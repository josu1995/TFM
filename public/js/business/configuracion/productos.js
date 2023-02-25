var checkedRows = [];

var parseExcelAndPost = function(file) {
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        if(!workbook.Sheets["Productos"] || workbook.Sheets["Productos"].A1.v !== "SKU / Referencia" || workbook.Sheets["Productos"].B1.v !== "Item / Nombre Producto" || workbook.Sheets["Productos"].C1.v !== "Precio (€)"|| workbook.Sheets["Productos"].D1.v !== "Reembolsable (S / N)" || workbook.Sheets["Productos"].E1.v !== "Peso (kg)") {
            new PNotify({
                title: 'Transporter',
                text: 'El excel introducido no es correcto. Prueba a descargar nuestra plantilla desde el menú de acciones.',
                addclass: 'transporter-alert',
                icon: 'icon-transporter',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
            $('.import-from-excel-form > input[type="file"]').val('');
        } else {
            var excelJson = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[workbook.SheetNames[0]], {
                header: ["referencia", "nombre", "precio", "reembolsable", "peso"],
                range: 1
            });

            $.ajax({
                url: rutaImportarExcel,
                headers: { 'X-CSRF-TOKEN': csrf },
                type: 'POST',
                data: {'data' : excelJson},
                success: function (data) {
                    $('.import-from-excel-form > input[type="file"]').val('');
                    $('.import-omit-form input[name="rows[]"]').remove();
                    location.reload();
                },
                error: function (data) {
                    $('.import-from-excel-form > input[type="file"]').val('');
                    $('.import-omit-form input[name="rows[]"]').remove();
                    var json = data.responseJSON;
                    var keys = Object.keys(json);
                    $('#modal-error-importacion .alert > ul').empty();
                    keys.forEach(function(val, i) {
                        var row = parseInt(val.split('.')[1]) + 2;
                        var plainRow = row - 2;
                        $('.import-omit-form').append('<input type="hidden" name="rows[]" value="' + plainRow + '">');
                        $('#modal-error-importacion .alert > ul').append('<li>Fila ' + row + ': ' + json[val] + '</li>');
                    });
                    $('#modal-error-importacion').modal();
                }
            });
        }
    };

    reader.onerror = function(ex) {
        console.log(ex);
    };

    reader.readAsBinaryString(file);
};

function initPaginationListener() {
    $('.pagination li > a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var busquedaText = $('.buscar-input').val();
        if(busquedaText !== '') {
            url += '&t=' + busquedaText;
        }
        $('.business-table-row').load(url, function () {

            initCheckboxes();
            initPaginationListener();
            var newPage = getCurrentPage();
            if(checkedRows[newPage] && checkedRows[newPage].length) {
                if(checkedRows[newPage].length === 10) {
                    $('.header-checkbox').iCheck('check');
                } else {
                    checkedRows[newPage].forEach(function (val) {
                        $('input[value="' + val + '"]').iCheck('check');
                    });
                }
            }
        });
    });
}

function getCurrentPage() {
    var elem = $('.pagination > li.active > span');
    if(!elem.length) {
        return "1";
    } else {
        return $('.pagination > li.active > span').text();
    }
}

function showEliminar() {
    $('.eliminar-productos, .eliminar-divider').show();
}

function hideEliminar() {
    $('.eliminar-productos, .eliminar-divider').hide();
}

function addToChecked(val) {
    var page = getCurrentPage();
    if(!checkedRows[page]) {
        checkedRows[page] = [];
    }
    if(checkedRows[page].indexOf(val) === -1) {
        checkedRows[page].push(val);
    }
}

function removeFromChecked(val) {
    var page = getCurrentPage();
    var index = checkedRows[page].indexOf(val);
    checkedRows[page].splice(index, 1);
    if(!checkedRows[page].length) {
        delete checkedRows[page];
    }
}

function initCheckboxes() {
    $('input[type="checkbox"]').not('.switch').iCheck({
        checkboxClass: 'icheckbox_square-yellow',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });

    $('.header-checkbox').on('ifChecked', function (event) {
        $('input[type="checkbox"]').not('.header-checkbox').iCheck('check');
    });

    $('.header-checkbox').on('ifUnchecked', function (event) {
        $('input[type="checkbox"]').not('.header-checkbox').iCheck('uncheck');
    });

    $('.table-checkbox').on('ifChecked', function (event) {
        showEliminar();
        addToChecked($(this).val());
    });

    $('.table-checkbox').on('ifUnchecked', function (event) {
        removeFromChecked($(this).val());
        if(!Object.keys(checkedRows).length) {
            hideEliminar();
        }
    });

    $('.switch').bootstrapSwitch();
}

function initPopovers() {
    $('.popover-precio').popover({
        trigger: 'hover',
        placement: 'top',
        html: true,
        content: 'IVA no incl.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('.popover-reembolsable').popover({
        trigger: 'hover',
        placement: 'top',
        html: true,
        content: 'Apto para ser devuelto por el cliente en caso de no estar satisfecho. Se aplicará la política de devoluciones definida en Ajustes de devolución.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
}

function initEditable() {
    $('.editable').click(function() {

        var id = $(this).parent().children().first().attr('id');
        var route = $('#modal-anadir-producto').find('form').attr('action');

        var editModal = $('#modal-editar-producto');

        editModal.find('form').attr('action', route + '/' + id);

        $(this).parent().find('.field').each(function() {
            var text = $(this).find('.value').text().trim();
            var name = $(this).attr('data-edit-name');
            var input = editModal.find('input[name="' + name + '"]')
            if(input.attr('type') === 'checkbox') {
                input.bootstrapSwitch('state', text === 'Sí', true);
            } else {
                input.val(text);
            }
        });
        editModal.modal();
    });
}

$(function() {

    if(productoError) {
        $('#modal-anadir-producto').modal();
    }

    if(productoEditError) {
        $('#modal-editar-producto').modal();
    }

    $('.import-from-excel-btn').click(function() {
        $('.import-from-excel-form > input').click();
    });

    $('.import-from-excel-form > input').change(function() {
        parseExcelAndPost($('.import-from-excel-form > input[type="file"]').prop('files')[0]);
    });

    $('.export-pdf-btn, .export-xls-btn').click(function(e) {
        e.preventDefault();
        var url = $(this).children('a').attr('href');
        var data = [];
        checkedRows.forEach(function(arr) {
            arr.forEach(function(val) {
                data.push(val);
            });
        });
        if(data.length) {
            $.ajax({
                url: rutaSeleccionProductos,
                headers: {'X-CSRF-TOKEN': csrf},
                type: 'POST',
                data: {'data': data},
                success: function (data) {
                    window.open(url);
                }
            });
        } else {
            window.open(url);
        }
    });

    $('.eliminar-productos').click(function(e) {
        e.preventDefault();
        $('.delete-products-form > div').empty();
        checkedRows.forEach(function(arr) {
            arr.forEach(function(val) {
                $('.delete-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
            });
        });
        $('.delete-products-form').submit();
    });

    $('.buscar-input').keyup(function() {
        var text = $(this).val();
        $('.business-table-row').load(rutaSearchProductos + '?t=' + encodeURIComponent(text), function () {
            initCheckboxes();
            initPaginationListener();
            initPopovers();
            initEditable();
            checkedRows = [];
            hideEliminar();
        });
    });



    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initEditable();
});