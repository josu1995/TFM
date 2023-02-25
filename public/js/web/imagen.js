'use strict';

var loaded = false;
$(function() {

    // --
    // Archivo imágen seleccionado
    // --

    var imgPerfil = $('#imagen');
    var lblImagen = $('.cambiar-imagen label');

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
                    var dataUrl = canvas.toDataURL('image/jpeg');

                    dataUrl = ExifRestorer.restore(originalBase64, dataUrl);

                    if(dataUrl == $('#imageCrop').prop('src')) {
                        $('.global-spinner-container').removeClass('spin');
                        $('#modalImagen').modal();
                    } else {

                        $('#imageCrop').css('max-height', window.innerHeight - 145 + 'px').prop('src', dataUrl);

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
            var namePerfil = $(this)
                .val()
                .split('\\')
                .pop();
            lblImagen.text(namePerfil);
            $('.bloques-perfil .cambiar-imagen label').addClass('subida');
        }
    }

    $('#modalImagen').on('shown.bs.modal', function() {
        $('#imageCrop').cropper('replace', $('#imageCrop').prop('src'));
        $('#imagen').val('');
    });
    $('#modalImagen').on('hidden.bs.modal', function() {
        loaded = false;
        $('.cropper-container').css('visibility','hidden');
    });

    if (imgPerfil.length) {
        document.getElementById('imagen')
            .addEventListener('change', uploadPhotos, false);
    }



    $('#imageCrop').cropper({
        aspectRatio: 200/200,
        viewMode: 1,
        dragMode: 'none',
        resizable: false,
        cropBoxResizable: true,
        zoomable: false,
        scalable: true,
        rotatable: true,
        multiple: false,
        checkOrientation: true,
        built: function () {
            $('.cropper-container').css('visibility','visible').hide().fadeIn('slow');
        }
    });

    $('#imagenSave').on('click', function() {
        var originalData = $('#imageCrop').cropper("getCroppedCanvas");
        $('#base64image').val(originalData.toDataURL());
        $('#imageForm').submit();
    });

});

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