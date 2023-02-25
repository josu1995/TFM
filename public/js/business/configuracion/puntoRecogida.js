'use strict';

var autocompleteInputCp = $('.autocomplete-input');
var businessAutocomplete = '';
var mapsready = false;
var mobile = screen.width < 768;

function mapsLoaded() {
    mapsready = true;
    if($('#modal-stores-search').length || $('#store-seleccionado-container').length || $('#domicilio-seleccionado-container').length) {
        crearMapa();
    }
}

function highlightValue(val, text) {
    if(val.toLowerCase().includes(text.toLowerCase())) {
        var pos = val.toLowerCase().search(text.toLowerCase());
        var newVal = '';
        if(pos !== 0) {
            newVal += val.substr(0, pos);
        }
        newVal += '<span class="highlight">' + val.substr(pos, text.length) + '</span>';
        if(pos + text.length !== val.length) {
            newVal += val.substr(pos + text.length);
        }
        return newVal;
    }
    return val;
}

$(function() {

    $(window).keydown(function(event){
        if(event.keyCode === 13) {
            event.preventDefault();
            return false;
        }
    });

    autocompleteInputCp.keyup(function(e) {
        if(e.keyCode === 8) {
            $('#codigo-postal-input').val('');
        }
        var text = $(this).val();
        var self = $(this).parent();
        setTimeout(function() {

            if(text === autocompleteInputCp.val() && autocompleteInputCp.is(':focus')) {

                $.ajax({
                    type: "get",
                    url: "/codigos-postales/search?t=" + text + '&p=64',
                    success: function (data) {
                        if (data.length && autocompleteInputCp.val() !== '') {
                            var autocomplete = '<div class="business-autocomplete">';
                            data.forEach(function (val) {
                                var cp = highlightValue(val.codigo_postal, text);
                                var ciudad = highlightValue(val.ciudad, text);

                                autocomplete += '<div class="autocomplete-item">' +
                                    '<p id="' + val.id + '">' + cp + ' - ' + ciudad + '</p>' +
                                    '</div>';
                            });
                            autocomplete += '</div>';
                            if (businessAutocomplete.length) {
                                businessAutocomplete.remove();
                            }
                            self.append(autocomplete);
                            businessAutocomplete = $('.business-autocomplete');

                            businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                                var id = $(this).children('p').attr('id');
                                var text = $(this).text();
                                businessAutocomplete.parent().children('input').val(text);
                                $('#codigo-postal-input').val(id);
                                $('input[name="cp_backup"]').val(text);
                            });
                        } else if (businessAutocomplete.length || autocompleteInputCp.val() === '') {
                            if(businessAutocomplete && businessAutocomplete !== '') {
                                businessAutocomplete.remove()
                            }
                        }
                    }
                });
            }

        }, 200);
    });

    autocompleteInputCp.blur(function() {
        if(businessAutocomplete && businessAutocomplete !== '') {
            businessAutocomplete.hide();
        }
        if($('#codigo-postal-input').val() === '') {
            $('.ciudad-input').val('');
            if(businessAutocomplete && businessAutocomplete !== '') {
                businessAutocomplete.remove();
            }
        } else if($('.ciudad-input') !== $('input[name="cp_backup"]')) {
            $('.ciudad-input').val($('input[name="cp_backup"]').val());
        }
    });

    autocompleteInputCp.focus(function() {
        if(businessAutocomplete.length) {
            if($(this).val() === '') {
                businessAutocomplete.remove();
            } else {
                businessAutocomplete.show();
            }
        }
    });

});
/** !AUTOCOMPLETE **/

function seleccionarStore(puntoId, tipoId) {
    var idInput = '<input type="hidden" class="punto-id-input" name="punto_id" value="' + puntoId + '">';
    var tipoInput = '<input type="hidden" class="punto-tipo-input" name="punto_tipo" value="' + tipoId + '">';
    var direccionInput = '<input type="hidden" class="punto-direccion-input" name="direccion" value="' + $('.store-active').find('.direccion').text() + '">';
    var form = $('form.selection-form');
    var oldInput = form.children('.punto-id-input');
    if(oldInput.length) {
        oldInput.remove();
    }
    var oldTipoInput = form.children('.punto-tipo-input');
    if(oldTipoInput.length) {
        oldTipoInput.remove();
    }
    var oldDireccionInput = form.children('.punto-direccion-input');
    if(oldDireccionInput.length) {
        oldDireccionInput.remove();
    }
    form.append(idInput);
    form.append(tipoInput);
    form.append(direccionInput);

    $('#tipo_recogida, .ciudad-input').prop('disabled', false);
    form.submit();
}

$(function() {
    $('#seleccionar-btn').click(function() {
        var ciudadInput = $('.ciudad-input');
        var cpInput = $('#codigo-postal-input');
        var tipoRecogidaInput = $('#tipo_recogida');

        var invalid = false;

        if(ciudadInput.val().trim() === '') {
            $('.ciudad-input + .invalid-feedback').text('Debes seleccionar un elemento de la lista para completar tu búsqueda');
            ciudadInput.addClass('is-invalid');
            invalid = true;
        } else if(cpInput.val() === '') {
            $('.ciudad-input + .invalid-feedback').text('Debes seleccionar un elemento de la lista para completar tu búsqueda');
            ciudadInput.addClass('is-invalid');
            invalid = true;
        }

        if(tipoRecogidaInput.val() === '') {
            $('#tipo_recogida + .invalid-feedback').text('Este campo es obligatorio');
            tipoRecogidaInput.addClass('is-invalid');
            invalid = true;
        }

        if(!invalid) {
            ciudadInput.prop('disabled', 'disabled');
            tipoRecogidaInput.prop('disabled', 'disabled');
            $('#seleccionar-btn').addClass('hidden');
            $('#cambiar-seleccion-btn').removeClass('hidden');
            $.ajax({
                type: "post",
                url: '/configuracion/punto-recogida/selection',
                data: {
                    cp_id: cpInput.val(),
                    tipo_recogida: tipoRecogidaInput.val(),
                    codigo_postal: ciudadInput.val(),
                    _token: $('input[name="_token"]').val()
                },
                success: function(data) {
                    $('#data').empty();
                    if(typeof mapa !== 'undefined' && tipoRecogidaInput.val() == 2) {
                        $('#data').append(data);
                        $('#modal-stores-search').on('hide.bs.modal', function() {
                            $('.ciudad-input, #tipo_recogida').prop('disabled', false);
                            $('#cambiar-seleccion-btn').addClass('hidden');
                            $('#seleccionar-btn').removeClass('hidden');
                        });
                    } else {
                        $.when(
                            $.getScript(storesSearchJs),
                            $.getScript(customScrollJs),
                            $.Deferred(function (deferred) {
                                $(deferred.resolve);
                            })
                        ).done(function () {
                            $('#data').append(data);
                            $('#modal-stores-search').on('hide.bs.modal', function() {
                                $('.ciudad-input, #tipo_recogida').prop('disabled', false);
                                $('#cambiar-seleccion-btn').addClass('hidden');
                                $('#seleccionar-btn').removeClass('hidden');
                                $('.search-input').popover('hide');
                            });
                        });
                    }
                }
            });
        }
    });

    $('#cambiar-seleccion-btn').click(function() {
        $('#data').empty();
        $('.ciudad-input, #tipo_recogida, #codigo-postal-input').prop('disabled', false);
        $('#cambiar-seleccion-btn').addClass('hidden');
        $('#seleccionar-btn').removeClass('hidden');
    });

    $('input, select').focus(function() {
        $(this).removeClass('is-invalid');
    });
});