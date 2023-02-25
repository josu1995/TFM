'use strict';

var parseExcelAndPost = function(file) {
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        if(!workbook.Sheets["Envíos"]
            || workbook.Sheets["Envíos"].A1.v !== "Referencia Pedido"
            || workbook.Sheets["Envíos"].B1.v !== "CP Origen"
            || workbook.Sheets["Envíos"].C1.v !== "Recogida"
            || workbook.Sheets["Envíos"].D1.v !== "ID Store Origen"
            || workbook.Sheets["Envíos"].E1.v !== "Dirección Origen"
            || workbook.Sheets["Envíos"].F1.v !== "Nombre Destinatario"
            || workbook.Sheets["Envíos"].G1.v !== "Apellido Destinatario"
            || workbook.Sheets["Envíos"].H1.v !== "Email"
            || workbook.Sheets["Envíos"].I1.v !== "Teléfono"
            || workbook.Sheets["Envíos"].J1.v !== "País Destino"
            || workbook.Sheets["Envíos"].K1.v !== "CP Destino"
            || workbook.Sheets["Envíos"].L1.v !== "Entrega"
            || workbook.Sheets["Envíos"].M1.v !== "ID Store Destino"
            || workbook.Sheets["Envíos"].N1.v !== "Dirección Destino"
            || workbook.Sheets["Envíos"].O1.v !== "Embalaje"
            || workbook.Sheets["Envíos"].P1.v !== "Largo (cm)"
            || workbook.Sheets["Envíos"].Q1.v !== "Alto (cm)"
            || workbook.Sheets["Envíos"].R1.v !== "Ancho (cm)"
            || workbook.Sheets["Envíos"].S1.v !== "Producto1"
            || workbook.Sheets["Envíos"].T1.v !== "Cantidad1"
            || workbook.Sheets["Envíos"].U1.v !== "Peso1 (kg)") {
            new PNotify({
                title: 'Transporter',
                text: 'El excel introducido no es correcto. Prueba a descargar nuestra plantilla desde el enlace.',
                addclass: 'transporter-alert',
                icon: 'icon-transporter',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
            $('input[type="file"]').val('');
        } else {
            var excelJson = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[workbook.SheetNames[0]]);

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
                    keys.sort().forEach(function(val, i) {
                        var row = parseInt(val.split('.')[0]) + 2;
                        var plainRow = row - 2;
                        $('.import-omit-form').append('<input type="hidden" name="rows[]" value="' + plainRow + '">');
                        json[val].forEach(function(sentence) {
                            $('#modal-error-importacion .alert > ul').append('<li>Fila ' + row + ': ' + sentence + '</li>');
                        });
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


$(function () {
    $('.import-btn').click(function () {
        $('input[name="import_excel"]').click();
    });

    $('input[name="import_excel"]').change(function() {
        parseExcelAndPost($('input[name="import_excel"]').prop('files')[0]);
    });
});