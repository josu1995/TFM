var mobile = screen.width < 768;

$(function () {

    // Color picker
    $('#colorpicker').colorpicker({
        color: color,
        format: 'hex'
    }).on('changeColor', function (e) {
        var color = e.value;
        $('.upload-logo-btn').css('background', color);
    }).on('showPicker', function (e) {
        if (mobile) {
            $('.colorpicker.dropdown-menu').css('left', '10px');
        }
    });

    $('#colorpicker .input-group-addon').click(function () {
        if (!mobile) {
            $('.dropdown-menu input').focus();
        }
    });

    if (mobile) {
        $('body').on('touchmove', function (e) {
            var target = $(e.target);
            if ($('.colorpicker-visible').length && !target.parents('.colorpicker').length) {
                $('#colorpicker').colorpicker('hide');
            }
        });
    }
    // !Color picker

    // Popovers
    $('#logo-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Introduce tu logotipo y color corporativo para comunicarnos y gestionar las devoluciones de tus clientes por ti.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#dias-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Introduce el plazo que tienen tus clientes para devolver productos desde que reciben su pedido. El plazo mínimo legal establecido es de 14 días naturales.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#etiqueta-preimpresa-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Para aportar la máxima comodidad a tu cliente, se introduce la etiqueta de devolución dentro del pedido. La etiqueta se genera automáticamente junto con la del envío.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#etiqueta-email-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Cuando el cliente nos solicite y tramitemos la devolución, le enviaremos la etiqueta por email.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#motivos-help-icon').popover({
        trigger: 'hover',
        placement: 'top',
        html: true,
        content: 'Introduce los motivos de devolución que podrían tener tus clientes. Te informaremos de los motivos seleccionados cuando un cliente solicite una devolución.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#entrega-store-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Tu cliente podrá entregar su paquete en uno de nuestros 2.000 puntos de recogida.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#recogida-domicilio-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Disponible próximamente.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#coste-prepagado-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Tu cliente no tendrá que adelantar el pago del envío de devolución, aún así podrás descontar a posteriori la cuantía del importe a reembolsar.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#coste-pagado-help-icon').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Tu cliente deberá pagar el coste del envío para gestionar la devolución.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
    // !popovers

    $('.marca-input-group > .input-group-addon > button').click(function () {
        $('.marca-input-group > input[type="file"]').click();
    });

    $('#seleccion-logo-input').focus(function () {
        $(this).blur();
        // $('.marca-input-group > input[type="file"]').click();
    });

    $('#logo-file-input').change(function (e) {
        var split = e.target.value.split('\\');
        $('#seleccion-logo-input').val(split[split.length - 1]);
        uploadPhotos(e);
    });

    $('#btn-anadir-motivo').click(function () {
        var input = $('.motivo-input');
        var alertShowing = $('.transporter-alert').length;
        var repeated = 0;
        $('.motivo > p').each(function () {
            if ($(this).text().trim() === input.val().trim()) {
                repeated++;
            }
        });
        if (repeated !== 0 && alertShowing === 0) {
            new PNotify({
                title: 'Transporter',
                text: 'Este motivo ya está añadido.',
                addclass: 'transporter-alert',
                icon: 'icon-transporter',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
        } else if (repeated === 0) {
            if (input.val() !== '') {
                var template = '<div class="motivo">' +
                    '<p>' + input.val() + '</p>' +
                    '<i class="material-icons pull-right">close</i>' +
                    '<input type="hidden" name="motivos[]" value="' + input.val() + '"/>' +
                    '</div>';
                input.val('');
                $('.motivos-container').append(template);
                $('.motivo > i').click(function () {
                    $(this).parent().remove();
                });
            }
        }
    });

    $('#modalImagen').on('shown.bs.modal', function () {
        $('#imageCrop').cropper('replace', $('#imageCrop').prop('src'));
        $('#modalImagen .modal-content').css({
            'width': ($('.cropper-container').width() + 30) + 'px',
            'margin': 'auto'
        });
        $('#imagen').val('');
    });
    $('#modalImagen').on('hidden.bs.modal', function () {
        loaded = false;
        $('.cropper-container').css('visibility', 'hidden');
    });

    $('#imageCrop').cropper({
        aspectRatio: 200 / 100,
        viewMode: 0,
        dragMode: 'none',
        resizable: true,
        cropBoxResizable: true,
        zoomable: true,
        zoomOnWheel: false,
        zoomOnTouch: false,
        scalable: true,
        rotatable: true,
        multiple: false,
        checkOrientation: true,
        built: function () {
            $('.cropper-container').css('visibility', 'visible').hide().fadeIn('slow');
        }
    });

    $('.zoom-container > button').click(function () {
        if ($(this).hasClass('zoom-in')) {
            $('#imageCrop').cropper('zoom', '0.1');
        } else {
            $('#imageCrop').cropper('zoom', '-0.1');
        }
    });

    $('#imagenSave').on('click', function () {
        var originalData = $('#imageCrop').cropper("getCroppedCanvas");
        $('#base64image').val(originalData.toDataURL());
        $('.logo-container > img').attr('src', originalData.toDataURL());
        if ($('.logo-container > .image-replace').length) {
            $('.logo-container > .image-replace').addClass('hidden');
            $('.logo-container > img').removeClass('hidden');
        }
    });

    $('.motivo > i').click(function () {
        $(this).parent().remove();
    });

    function uploadPhotos(e) {
        // Read in file
        var file = e.target.files[0];

        // Ensure it's an image
        if (file.type.match(/image.*/)) {

            $('.global-spinner-container').addClass('spin');

            // Load the image
            var reader = new FileReader();
            reader.onload = function (readerEvent) {
                var image = new Image();
                var originalBase64 = readerEvent.target.result;
                image.onload = function (imageEvent) {

                    // Resize the image
                    var canvas = document.createElement('canvas'),
                        max_size = 700,
                        width = image.width,
                        height = image.height;
                    if (width > height) {
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                    } else {
                        if (height > max_size) {
                            width *= max_size / height;
                            height = max_size;
                        }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    canvas.getContext('2d').drawImage(image, 0, 0, width, height);
                    var dataUrl = '';
                    if (image.src.includes('image/jpeg')) {
                        dataUrl = canvas.toDataURL('image/jpeg');
                    } else {
                        dataUrl = canvas.toDataURL();
                    }

                    dataUrl = ExifRestorer.restore(originalBase64, dataUrl);

                    if (dataUrl == $('#imageCrop').prop('src')) {
                        $('.global-spinner-container').removeClass('spin');
                        $('#modalImagen').modal();
                    } else {

                        if (image.src.includes('image/jpeg')) {
                            $('#imageCrop').css('max-height', window.innerHeight - 145 + 'px').prop('src', 'data:image/jpeg;base64,' + dataUrl);
                        } else {
                            $('#imageCrop').css('max-height', window.innerHeight - 145 + 'px').prop('src', dataUrl);
                        }

                        $('#imageCrop').on('load', function () {
                            if (!loaded) {
                                loaded = true;
                                if (window.innerWidth > $('#imageCrop')[0].width) {
                                    $('#modalImagen .modal-content').css({
                                        'width': $('#imageCrop')[0].width + 'px',
                                        'margin': 'auto'
                                    });
                                }

                                $('.global-spinner-container').removeClass('spin');
                                $('#modalImagen').modal();

                            }
                        });
                    }

                };
                image.src = readerEvent.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
});

var ExifRestorer = (function () {

    var ExifRestorer = {};

    ExifRestorer.KEY_STR = "ABCDEFGHIJKLMNOP" +
        "QRSTUVWXYZabcdef" +
        "ghijklmnopqrstuv" +
        "wxyz0123456789+/" +
        "=";

    ExifRestorer.encode64 = function (input) {
        var output = "",
            chr1, chr2, chr3 = "",
            enc1, enc2, enc3, enc4 = "",
            i = 0;

        do {
            chr1 = input[i++];
            chr2 = input[i++];
            chr3 = input[i++];

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
                this.KEY_STR.charAt(enc1) +
                this.KEY_STR.charAt(enc2) +
                this.KEY_STR.charAt(enc3) +
                this.KEY_STR.charAt(enc4);
            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";
        } while (i < input.length);

        return output;
    };

    ExifRestorer.restore = function (origFileBase64, resizedFileBase64) {
        if (!origFileBase64.match("data:image/jpeg;base64,")) {
            return resizedFileBase64;
        }

        var rawImage = this.decode64(origFileBase64.replace("data:image/jpeg;base64,", ""));
        var segments = this.slice2Segments(rawImage);

        var image = this.exifManipulation(resizedFileBase64, segments);

        return this.encode64(image);

    };


    ExifRestorer.exifManipulation = function (resizedFileBase64, segments) {
        var exifArray = this.getExifArray(segments),
            newImageArray = this.insertExif(resizedFileBase64, exifArray),
            aBuffer = new Uint8Array(newImageArray);

        return aBuffer;
    };


    ExifRestorer.getExifArray = function (segments) {
        var seg;
        for (var x = 0; x < segments.length; x++) {
            seg = segments[x];
            if (seg[0] == 255 & seg[1] == 225) //(ff e1)
            {
                return seg;
            }
        }
        return [];
    };


    ExifRestorer.insertExif = function (resizedFileBase64, exifArray) {
        var imageData = resizedFileBase64.replace("data:image/jpeg;base64,", ""),
            buf = this.decode64(imageData),
            separatePoint = buf.indexOf(255, 3),
            mae = buf.slice(0, separatePoint),
            ato = buf.slice(separatePoint),
            array = mae;

        array = array.concat(exifArray);
        array = array.concat(ato);
        return array;
    };



    ExifRestorer.slice2Segments = function (rawImageArray) {
        var head = 0,
            segments = [];

        while (1) {
            if (rawImageArray[head] == 255 & rawImageArray[head + 1] == 218) { break; }
            if (rawImageArray[head] == 255 & rawImageArray[head + 1] == 216) {
                head += 2;
            }
            else {
                var length = rawImageArray[head + 2] * 256 + rawImageArray[head + 3],
                    endPoint = head + length + 2,
                    seg = rawImageArray.slice(head, endPoint);
                segments.push(seg);
                head = endPoint;
            }
            if (head > rawImageArray.length) { break; }
        }

        return segments;
    };



    ExifRestorer.decode64 = function (input) {
        var output = "",
            chr1, chr2, chr3 = "",
            enc1, enc2, enc3, enc4 = "",
            i = 0,
            buf = [];

        // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
        var base64test = /[^A-Za-z0-9\+\/\=]/g;
        if (base64test.exec(input)) {
            alert("There were invalid base64 characters in the input text.\n" +
                "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
                "Expect errors in decoding.");
        }
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        do {
            enc1 = this.KEY_STR.indexOf(input.charAt(i++));
            enc2 = this.KEY_STR.indexOf(input.charAt(i++));
            enc3 = this.KEY_STR.indexOf(input.charAt(i++));
            enc4 = this.KEY_STR.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            buf.push(chr1);

            if (enc3 != 64) {
                buf.push(chr2);
            }
            if (enc4 != 64) {
                buf.push(chr3);
            }

            chr1 = chr2 = chr3 = "";
            enc1 = enc2 = enc3 = enc4 = "";

        } while (i < input.length);

        return buf;
    };


    return ExifRestorer;
})();