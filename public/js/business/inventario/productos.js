var checkedRows = [];

function loadImagePreview(input, id) {  
    //id = id || 'preview_imagen';
    let result = false;
    if (input.files && input.files[0]) {
        if (input.files[0].type.match('image.*')) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#'+id).attr('src', e.target.result);
            };
     
            reader.readAsDataURL(input.files[0]);
            result = true;
        } else {
            $('#'+id).attr('src', rutaImagenPorDefecto);
            $('[id^=ruta_imagen_temp]').val("");
        }
    }
    return result;
 }


 var parseExcelAndPost = function(file) {
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        if(!workbook.Sheets["Productos"] || workbook.Sheets["Productos"].A1.v !== "SKU / Referencia" || workbook.Sheets["Productos"].B1.v !== "Item / Nombre Producto" 
        || workbook.Sheets["Productos"].C1.v !== "Precio (€)"|| workbook.Sheets["Productos"].D1.v !== "Reembolsable (S / N)" || workbook.Sheets["Productos"].E1.v !== "Peso (kg)"
        || workbook.Sheets["Productos"].F1.v !== "Largo (cm)" || workbook.Sheets["Productos"].G1.v !== "Alto (cm)"
        || workbook.Sheets["Productos"].H1.v !== "Ancho (cm)" || workbook.Sheets["Productos"].I1.v !== "Tipo C. Barras"
        || workbook.Sheets["Productos"].J1.v !== "Código de Barras" || workbook.Sheets["Productos"].K1.v !== "Activo (S / N)"
        ) {
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
                header: ["referencia", "nombre", "precio", "reembolsable", "peso", "largura", "altura", "anchura", "tipo_codigo_barras", "codigo_barras", "activo"],
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
        console.log('cambio?');
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
                $('.header-checkbox').iCheck('check');  
                if(checkedRows[newPage].length === 10) {
                   
                } else {
                    checkedRows[newPage].forEach(function (val) {
                        $('input[value="' + val + '"]').iCheck('check');
                    });
                }
            }
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

// function showEliminar() {
//     $('.eliminar-productos, .eliminar-divider').show();
// }

// function hideEliminar() {
//     $('.eliminar-productos, .eliminar-divider').hide();
// }

function showActivar() {
    $('.activar-productos, .activar-divider').show();
}

function showDesactivar() {
    $('.desactivar-productos, .desactivar-divider').show();
}

function hideActivar() {
    $('.activar-productos, .activar-divider').hide();
}

function hideDesactivar() {
    $('.desactivar-productos, .desactivar-divider').hide();
}

function getPages(){
    var elem = $('.pagination > li > span');
    var elem = $('.pagination > li > span');
    if(elem.length == 0){
      return [1];
    }else{
      return elem.length;
    }
  }

function addToChecked(val) {
    var pages = getPages();
  
    for(var i = 1; i<=pages;i++){
      if (!checkedRows[i]) {
        checkedRows[i] = [];
      }
    }
    if ($('input.header-checkbox').is(':checked')) {
      for(var i = 1; i<=pages;i++){
        if (checkedRows[i].indexOf(val) === -1) {
          checkedRows[i].push(val);
        }
      }
      
    }else{

      var page = getCurrentPage();
      if (!checkedRows[page]) {
        checkedRows[page] = [];
      }
      if (checkedRows[page].indexOf(val) === -1) {
        checkedRows[page].push(val);
      }
    }
   
}

function removeFromChecked(val) {
    var pages = getPages(); 
    var cont = 0;
  


    var page = getCurrentPage();
    var index = checkedRows[page].indexOf(val);
    checkedRows[page].splice(index, 1);

    for(var i = 1;i<=pages;i++){
        cont = cont + checkedRows[i].length;
      }
  
      if(cont == 0){
        hideActivar();
        hideDesactivar();
      }

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
        var pages = getPages();

        for(var i = 1; i<=pages;i++){
            checkedRows[i] = [];
        }
    });

    $('.table-checkbox').on('ifChecked', function (event) {
        // showEliminar();
        if ($('#filtro-activos').val() == 1){          
            showDesactivar();
        } else {
            showActivar();
        }
        addToChecked($(this).val());
    });

    $('.table-checkbox').on('ifUnchecked', function (event) {
        removeFromChecked($(this).val());
        if(!Object.keys(checkedRows).length) {
            // hideEliminar();
            hideActivar();
            hideDesactivar();
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
    // $('.editable').click(function() {
    $('.box-body').on('click', '.business-table .editable', function(e) {
        e.preventDefault();
        var id = $(this).parent().children().first().attr('id');
        var route = $('#modal-anadir-producto').find('form').attr('action');

        var editModal = $('#modal-editar-producto');

        editModal.find('form').attr('action', route + '/' + id);

        $(this).parent().find('.field').each(function() {
            var text = $(this).find('.value').text().trim();
            var name = $(this).attr('data-edit-name');
            var input = editModal.find('input[name="' + name + '"]')
            // if(input.attr('type') === 'checkbox') {
            //     input.bootstrapSwitch('state', text === 'Sí', true);
            // } else {
            //     input.val(text);
            // }


            if(name == "imagen_edit"){              
                let image = editModal.find('#preview_imagen_edit');
                let link = $(this).find('img').attr('src');             
                if(typeof(link) === 'undefined'){                   
                    link = rutaImagenPorDefecto;               
                }
                image.attr('src', link);

                (editModal.find('#ruta_imagen_guardada')).val(link);

            } else if (name == "dimensiones_edit"){
                let dimensions = text.split('x');
                if (dimensions.length == 3){
                    var input = editModal.find('input[name="largura_edit"]');
                    input.val(dimensions[0].trim());

                    var input = editModal.find('input[name="altura_edit"]');
                    input.val(dimensions[1].trim());

                    var input = editModal.find('input[name="anchura_edit"]');
                    input.val(dimensions[2].trim());  
                }
            } else if (name == "tipo_codigo_barras_id_edit"){
                var itemId = $(this).attr('data-edit-value');
                var select = editModal.find('select[name="tipo_codigo_barras_id_edit"]');
                select.val(itemId);
            } else if(input.attr('type') === 'checkbox') {
                input.bootstrapSwitch('state', text === 'Sí', true);
            } else {
                input.val(text);
            }
        });
        editModal.modal();
    });
}

function filterListData(filter, text) {
    $('.business-table-row').load(rutaSearchProductos + '?f=' + filter + '&t=' + encodeURIComponent(text), function () {
        initCheckboxes();
        initPaginationListener();
        initPopovers();
        initEditable();
        checkedRows = [];
        // hideEliminar();
        hideActivar();
        hideDesactivar();
    });
}

$(function() {

    if(productoError) {
        if ($('#ruta_imagen_temp').val() != ""){
            $('#preview_imagen').attr('src', rutaBaseImagenTemp+$('#ruta_imagen_temp').val() );
        }
        $('#modal-anadir-producto').modal();
    }

    if(productoEditError) {
        if ($('#ruta_imagen_temp_edit').val() != ""){
        // if ($('#ruta_imagen_temp_edit').val() != "" && $('#ruta_imagen_temp_edit').val() != rutaImagenPorDefecto){
            $('#preview_imagen_edit').attr('src', rutaBaseImagenTemp+$('#ruta_imagen_temp_edit').val() );
            $('#image_changed_control_edit').val("1");
        } else if($('#ruta_imagen_guardada').val() != ""){
            $('#preview_imagen_edit').attr('src', $('#ruta_imagen_guardada').val() );
        }
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
        var filter = $('#filtro-activos').val();
        
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
                data: {'data': data, 'filter': filter},
                success: function (data) {
                    window.open(url);
                }
            });
        } else {
            // window.open(url);          
            $.ajax({
                url: rutaFiltroProductos,
                headers: {'X-CSRF-TOKEN': csrf},
                type: 'POST',
                data: {'filter': filter},
                success: function (data) {
                    window.open(url);
                }
            });
        }

        // if(data.length) {
        //     $.ajax({
        //         url: rutaSeleccionProductos,
        //         headers: {'X-CSRF-TOKEN': csrf},
        //         type: 'POST',
        //         data: {'data': data},
        //         success: function (data) {
        //             window.open(url);
        //         }
        //     });
        // } else {
        //     window.open(url);
        // }
    });

    // $('.eliminar-productos').click(function(e) {
    //     e.preventDefault();
    //     $('.delete-products-form > div').empty();
    //     checkedRows.forEach(function(arr) {
    //         arr.forEach(function(val) {
    //             $('.delete-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
    //         });
    //     });
    //     $('.delete-products-form').submit();
    // });

    $('.activar-productos').click(function(e) {
        e.preventDefault();
        $('.activate-products-form > div').empty();
        checkedRows.forEach(function(arr) {
            arr.forEach(function(val) {
                $('.activate-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
            });
        });
        $('.activate-products-form').submit();
    });

    $('.desactivar-productos').click(function(e) {
        e.preventDefault();
        $('.deactivate-products-form > div').empty();
        checkedRows.forEach(function(arr) {
            arr.forEach(function(val) {
                $('.deactivate-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
            });
        });
        $('.deactivate-products-form').submit();
    });


    $('.buscar-input').keyup(function() {
        let text = $(this).val();
        let filter = $('#filtro-activos').val();
        
        filterListData(filter, text);
    });


    $('#filtro-activos').change(function(e) {
        e.preventDefault();
        let filter = $(this).val();
        let text = $('.buscar-input').val();
 
        filterListData(filter, text);
    });



    $('.image-input-file').click(function(e) {
        $(this).val("");  
        // let previewId = 'preview_imagen'; 
        // let controlItem = $(this).data('control');
        // if (typeof(controlItem) !== 'undefined' && controlItem != ""){
        //     previewId = previewId + controlItem;
        // }

        // $('#'+previewId).attr('src', rutaImagenPorDefecto);
    });


    $('.image-input-file').change(function(e) {
        let controlItem = $(this).data('control');
        let previewId = 'preview_imagen';
        let previewError = 'preview_imagen_error';
        let tempRouteId = 'ruta_imagen_temp';

        if (typeof(controlItem) !== 'undefined' && controlItem != ""){
            previewId = previewId + controlItem;
            previewError= previewError + controlItem;
            tempRouteId = tempRouteId + controlItem;
        } 

        //guardar ruta en hidden para control temporal
        if (this.files[0].type.match('image.*')) {
        //    let imageFilename = this.files[0].name;           
           let tempRoute = `/usuario_${idUsuario}/${this.files[0].name}`;
            $('#'+tempRouteId).val(tempRoute);
        }
     
        $('#'+previewError).hide();
        let result = loadImagePreview(this, previewId);
        if(!result){
            $('#'+previewError).show();
        }

        $('#image_changed_control_edit').val("1");
    });
    



    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initEditable();

    hideActivar();
    hideDesactivar();
});