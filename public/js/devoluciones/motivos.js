'use strict';

var loaded = false;
var selectedInput = null;

function initFileListener(component) {
    component.click(function() {
        $(this).blur();
        var hidden = component.parent().parent().find('input.image-file-input');
        hidden.click();
        hidden.change(function(e) {
            component.val(e.target.files[0].name);
            selectedInput = $(this);
            uploadPhotos(e);
        });
    });
}

$(function() {
    $('input[type="checkbox"]').change(function() {
        $('#selected-prods').text($('input[type="checkbox"]:checked:not(:disabled)').length);
        if(!$(this).prop('checked')) {
            $(this).parents('.producto-container').find('select option:selected').prop('selected', false);
            $(this).parents('.producto-container').find('select option:first').prop('selected', true);
            $(this).parents('.producto-container').find('.custom-input').remove()
        }
    });

    $('select').change(function() {

        $(this).parent().parent().find('input[type="checkbox"]').prop('checked', true);
        $('#selected-prods').text($('input[type="checkbox"]:checked:not(:disabled)').length);

        var component;

        var descTara = locale === 'es' ? 'Descripción de la tara...' : 'Defect\'s description...';
        var fotoTara = locale === 'es' ? 'Fotografía de la tara...' : 'Defect\'s picture...';
        var descDanos = locale === 'es' ? 'Descripción de los daños...' : 'Damages\' description...';
        var fotoDanos = locale === 'es' ? 'Fotografía de los daños...' : 'Damages\' picture...';
        var desc = locale === 'es' ? 'Descripción...' : 'Description...';
        var opcionInfo = locale === 'es' ? 'Información (dimensiones/cuidado, etc.)' : 'Information (dimensions, care, etc.)';
        var opcionCorresponde = locale === 'es' ? 'No se corresponde con los vídeos/imágenes' : 'Not corresponding to videos/images';

        switch ($(this).find('option:selected').val()) {
            case '1':
                component = '<input type="text" class="form-control mg-t-10 custom-input" placeholder="' + descTara + '" name="productos[' + $(this).parent().parent().attr('id') + '][descripcion]">' +
                    '<div class="icon-input-container custom-input"><input type="text" class="form-control mg-t-10 file-text-input" placeholder="' + fotoTara + '"><i class="fas fa-camera custom-color"></i></div>' +
                    '<input type="file" class="hidden image-file-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen]">' +
                    '<input type="hidden" class="image-data-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen_data]">';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                initFileListener($(this).parent().find('.file-text-input'));
                break;
            case '2':
                component = '<input type="text" class="form-control mg-t-10 custom-input" placeholder="' + descDanos + '" name="productos[' + $(this).parent().parent().attr('id') + '][descripcion]">' +
                    '<div class="icon-input-container custom-input"><input type="text" class="form-control mg-t-10 file-text-input" placeholder="' + fotoDanos + '"><i class="fas fa-camera custom-color"></i></div>' +
                    '<input type="file" class="hidden image-file-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen]">' +
                    '<input type="hidden" class="image-data-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen_data]">';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                initFileListener($(this).parent().find('.file-text-input'));
                break;
            case '3':
                component = '<input type="text" class="form-control mg-t-10 custom-input" placeholder="' + descDanos + '" name="productos[' + $(this).parent().parent().attr('id') + '][descripcion]">' +
                    '<div class="icon-input-container custom-input"><input type="text" class="form-control mg-t-10 file-text-input" placeholder="' + fotoDanos + '"><i class="fas fa-camera custom-color"></i></div>' +
                    '<input type="file" class="hidden image-file-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen]">' +
                    '<input type="hidden" class="image-data-input custom-input" name="productos[' + $(this).parent().parent().attr('id') + '][imagen_data]">';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                initFileListener($(this).parent().find('.file-text-input'));
                break;
            case '5':
                component = '<p class="radio-container no-mg-b mg-t-10 custom-input">' +
                    '<input id="opcion-info-' + $(this).parent().parent().attr('id') + '" name="productos[' + $(this).parent().parent().attr('id') + '][opcion]" type="radio" class="with-gap custom-color-radio" value="1" checked>' +
                    '<label for="opcion-info-' + $(this).parent().parent().attr('id') + '">' + opcionInfo + '</label>' +
                    '</p>' +
                    '<p class="radio-container no-mg-b custom-input">' +
                    '<input id="opcion-imagen-' + $(this).parent().parent().attr('id') + '" name="productos[' + $(this).parent().parent().attr('id') + '][opcion]" type="radio" class="with-gap custom-color-radio" value="2">' +
                    '<label for="opcion-imagen-' + $(this).parent().parent().attr('id') + '">' + opcionCorresponde + '</label>' +
                    '</p>';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                break;
            case '8':
                component = '<input type="text" class="form-control mg-t-10 custom-input" placeholder="' + desc + '" name="productos[' + $(this).parent().parent().attr('id') + '][descripcion]">';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                break;
            case '10':
                component = '<input type="text" class="form-control mg-t-10 custom-input" placeholder="' + desc + '" name="productos[' + $(this).parent().parent().attr('id') + '][descripcion]">';
                $(this).parent().find('.custom-input').remove();
                $(this).parent().append(component);
                break;
            default:
                $(this).parent().find('.custom-input').remove();
                break;
        }
    });

    $('input.file-text-input').each(function() {
        initFileListener($(this));
    });

    $('#modalImagen').on('shown.bs.modal', function() {
        $('#imageCrop').cropper('replace', $('#imageCrop').prop('src'));
        $('#modalImagen .modal-content').css({
            'width': ($('.cropper-container').width() + 30) + 'px',
            'margin': 'auto'
        });
        setTimeout(function() {
                $('#modalImagen .modal-content').css({
                    'width': ($('.cropper-container').width() + 30) + 'px',
                    'margin': 'auto'
                });
            }, 100
        );
    });
    $('#modalImagen').on('hidden.bs.modal', function() {
        loaded = false;
        $('.cropper-container').css('visibility','hidden');
    });

    $('#imageCrop').cropper({
        aspectRatio: 200/100,
        viewMode: 0,
        dragMode: 'none',
        resizable: false,
        cropBoxResizable: true,
        zoomable: true,
        zoomOnWheel: false,
        zoomOnTouch: false,
        scalable: true,
        rotatable: true,
        multiple: false,
        checkOrientation: true,
        built: function () {
            $('.cropper-container').css('visibility','visible').hide().fadeIn('slow');
        }
    });

    $('.zoom-container > button').click(function() {
        if($(this).hasClass('zoom-in')) {
            $('#imageCrop').cropper('zoom', '0.1');
        } else {
            $('#imageCrop').cropper('zoom', '-0.1');
        }
    });

    $('#imagenSave').on('click', function() {
        var originalData = $('#imageCrop').cropper("getCroppedCanvas");
        selectedInput.siblings('.image-data-input').val(originalData.toDataURL());
    });
});

function uploadPhotos(e){
    // Read in file
    var file = e.target.files[0];

    // Ensure it's an image
    if(file.type.match(/image.*/)) {

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
                if(image.src.includes('image/jpeg')) {
                    dataUrl = canvas.toDataURL('image/jpeg');
                } else {
                    dataUrl = canvas.toDataURL();
                }

                dataUrl = ExifRestorer.restore(originalBase64, dataUrl);

                if(dataUrl == $('#imageCrop').prop('src')) {
                    $('.global-spinner-container').removeClass('spin');
                    $('#modalImagen').modal();
                } else {

                    if(image.src.includes('image/jpeg')) {
                        $('#imageCrop').css('max-height', window.innerHeight - 145 + 'px').prop('src', 'data:image/jpeg;base64,' + dataUrl);
                    } else {
                        $('#imageCrop').css('max-height', window.innerHeight - 145 + 'px').prop('src', dataUrl);
                    }

                    $('#imageCrop').on('load', function () {
                        if(!loaded) {
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

var ExifRestorer = (function()
{

    var ExifRestorer = {};

    ExifRestorer.KEY_STR = "ABCDEFGHIJKLMNOP" +
        "QRSTUVWXYZabcdef" +
        "ghijklmnopqrstuv" +
        "wxyz0123456789+/" +
        "=";

    ExifRestorer.encode64 = function(input)
    {
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

    ExifRestorer.restore = function(origFileBase64, resizedFileBase64)
    {
        if (!origFileBase64.match("data:image/jpeg;base64,"))
        {
            return resizedFileBase64;
        }

        var rawImage = this.decode64(origFileBase64.replace("data:image/jpeg;base64,", ""));
        var segments = this.slice2Segments(rawImage);

        var image = this.exifManipulation(resizedFileBase64, segments);

        return this.encode64(image);

    };


    ExifRestorer.exifManipulation = function(resizedFileBase64, segments)
    {
        var exifArray = this.getExifArray(segments),
            newImageArray = this.insertExif(resizedFileBase64, exifArray),
            aBuffer = new Uint8Array(newImageArray);

        return aBuffer;
    };


    ExifRestorer.getExifArray = function(segments)
    {
        var seg;
        for (var x = 0; x < segments.length; x++)
        {
            seg = segments[x];
            if (seg[0] == 255 & seg[1] == 225) //(ff e1)
            {
                return seg;
            }
        }
        return [];
    };


    ExifRestorer.insertExif = function(resizedFileBase64, exifArray)
    {
        var imageData = resizedFileBase64.replace("data:image/jpeg;base64,", ""),
            buf = this.decode64(imageData),
            separatePoint = buf.indexOf(255,3),
            mae = buf.slice(0, separatePoint),
            ato = buf.slice(separatePoint),
            array = mae;

        array = array.concat(exifArray);
        array = array.concat(ato);
        return array;
    };



    ExifRestorer.slice2Segments = function(rawImageArray)
    {
        var head = 0,
            segments = [];

        while (1)
        {
            if (rawImageArray[head] == 255 & rawImageArray[head + 1] == 218){break;}
            if (rawImageArray[head] == 255 & rawImageArray[head + 1] == 216)
            {
                head += 2;
            }
            else
            {
                var length = rawImageArray[head + 2] * 256 + rawImageArray[head + 3],
                    endPoint = head + length + 2,
                    seg = rawImageArray.slice(head, endPoint);
                segments.push(seg);
                head = endPoint;
            }
            if (head > rawImageArray.length){break;}
        }

        return segments;
    };



    ExifRestorer.decode64 = function(input)
    {
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