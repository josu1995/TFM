'use strict';

function highlightValue(val, text) {
    if(val && val.toLowerCase().includes(text.toLowerCase())) {
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

function initProductosAutocompleteListener(component) {
    var businessAutocomplete = '';
    var originalInput = component.parent().find('.original-input');
    component.keyup(function(e) {
        if(e.keyCode === 8) {
            originalInput.val('');
        }
        var text = $(this).val();
        var self = $(this).parent();
        setTimeout(function() {

            if(text === component.val() && component.is(':focus')) {

                $.ajax({
                    type: "get",
                    url: "/configuracion/productos/search-data?t=" + text,
                    success: function (data) {
                        if (data.length && component.val() !== '') {
                            var autocomplete = '<div class="business-autocomplete">';
                            data.forEach(function (val) {
                                var referencia = highlightValue(val.referencia, text);
                                var nombre = highlightValue(val.nombre, text);
                                if(referencia) {
                                    autocomplete += '<div class="autocomplete-item">' +
                                        '<p id="' + val.id + '" peso="' + val.peso + '">' + referencia + ' - ' + nombre + '</p>' +
                                        '</div>';
                                } else {
                                    autocomplete += '<div class="autocomplete-item">' +
                                        '<p id="' + val.id + '" peso="' + val.peso + '">' + nombre + '</p>' +
                                        '</div>';
                                }
                            });
                            autocomplete += '</div>';
                            if (businessAutocomplete.length) {
                                businessAutocomplete.remove();
                            }
                            self.append(autocomplete);
                            businessAutocomplete = $('.business-autocomplete');

                            businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                                var id = $(this).children('p').attr('id');
                                var peso = $(this).children('p').attr('peso');
                                var text = $(this).text();
                                $(this).parent().parent().children('input').val(text);
                                $(this).parent().parent().parent().find('.peso-input').val(peso);
                                $(this).parent().parent().parent().find('.cantidad-input').val(1);
                                originalInput.val(id);
                            });
                        } else if (businessAutocomplete.length || component.val() === '') {
                            businessAutocomplete.remove();
                        }
                    }
                });
            }

        }, 200);
    });

    component.blur(function() {
        if(businessAutocomplete.length) {
            businessAutocomplete.hide();
        }
        if(originalInput.val() === '') {
            component.val('');
        }
    });

    component.focus(function() {
        if(businessAutocomplete.length) {
            if(component.val() === '') {
                businessAutocomplete.remove();
            } else {
                businessAutocomplete.show();
            }
        }
    });
}

function initPaisAutocompleteListener(component) {
    var businessAutocomplete = '';
    var originalInput = component.parent().find('.original-input');
    component.keyup(function(e) {
        originalInput.val('');
        var text = $(this).val();
        var self = $(this).parent();
        setTimeout(function() {

            if(text === component.val() && component.is(':focus')) {

                $.ajax({
                    type: "get",
                    url: "/paises/search?t=" + text,
                    success: function (data) {
                        if (data.length && component.val() !== '') {
                            var autocomplete = '<div class="business-autocomplete">';
                            data.forEach(function (val) {
                                var pais = highlightValue(val.nombre, text);

                                autocomplete += '<div class="autocomplete-item">' +
                                    '<p id="' + val.id + '">' + pais + '</p>' +
                                    '</div>';
                            });
                            autocomplete += '</div>';
                            if (businessAutocomplete.length) {
                                businessAutocomplete.remove();
                            }
                            self.append(autocomplete);
                            businessAutocomplete = component.parent().find('.business-autocomplete');

                            businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                                var id = $(this).children('p').attr('id');
                                var text = $(this).text();
                                businessAutocomplete.parent().children('input').val(text);
                                originalInput.val(id);
                                if(id == 172 || id == 185 || id == 16) {
                                    $('#tipo_entrega_destino > option[value="1"]').attr('disabled', false);
                                    $('#tipo_entrega_destino > option[value="2"]').attr('disabled', true);
                                } else if(id == 140) {
                                    $('#tipo_entrega_destino > option[value="1"]').attr('disabled', true);
                                    $('#tipo_entrega_destino > option[value="2"]').attr('disabled', false);
                                } else {
                                    $('#tipo_entrega_destino > option[value="1"]').attr('disabled', false);
                                    $('#tipo_entrega_destino > option[value="2"]').attr('disabled', false);
                                }
                                $('#cp-destino-autocomplete').val('');
                                $('#tipo_entrega_destino').val(0);
                                $('input[name="direccion_destino"]').val('');
                                $('input[name="store_destino"]').val('');
                            });
                        } else if (businessAutocomplete.length || component.val() === '') {
                            businessAutocomplete.remove()
                        }
                    }
                });
            }

        }, 200);
    });

    component.blur(function() {
        if(businessAutocomplete.length) {
            businessAutocomplete.hide();
        }
        if(originalInput.val() === '') {
            component.val('');
            $('#cp-destino-autocomplete').val('');
            $('#tipo_entrega_destino').val('');
            $('input[name="direccion_destino"]').val('');
            $('input[name="store_destino"]').val('');
        }
    });

    component.focus(function() {
        if(businessAutocomplete.length) {
            if(component.val() === '') {
                businessAutocomplete.remove();
            } else {
                businessAutocomplete.show();
            }
        }
    });
}

function initCpAutocompleteListener(component) {
    var businessAutocomplete = '';
    var originalInput = component.parent().find('.original-input');
    component.keyup(function(e) {
        originalInput.val('');
        var text = $(this).val();
        var self = $(this).parent();
        setTimeout(function() {

            if(text === component.val() && component.is(':focus')) {
                var pais = '';
                if(component.is($('#cp-destino-autocomplete')) && $('input[name="pais_destino_id"]').val() !== '') {
                    pais = '&p=' + $('input[name="pais_destino_id"]').val();
                } else if(component.is($('#cp-origen-autocomplete'))) {
                    pais = '&p=64';
                }

                $.ajax({
                    type: "get",
                    url: "/codigos-postales/search?t=" + text + pais,
                    success: function (data) {
                        if (data.length && component.val() !== '') {
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
                            businessAutocomplete = component.parent().find('.business-autocomplete');

                            businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                                var id = $(this).children('p').attr('id');
                                var text = $(this).text();
                                businessAutocomplete.parent().children('input').val(text);
                                originalInput.val(id);
                            });
                        } else if (businessAutocomplete.length || component.val() === '') {
                            businessAutocomplete.remove()
                        }
                    }
                });
            }

        }, 200);
    });

    component.blur(function() {
        if(businessAutocomplete.length) {
            businessAutocomplete.hide();
        }
        if(originalInput.val() === '') {
            component.val('');
            if(component.is('#cp-destino-autocomplete')) {
                $('#tipo_entrega_destino').val('');
                $('input[name="direccion_destino"]').val('');
                $('input[name="store_destino"]').val('');
            } else {
                $('#tipo_recogida_origen').val('');
                $('input[name="domicilio_origen"]').val('');
                $('input[name="store_origen"]').val('');
            }
        }
    });

    component.focus(function() {
        if(businessAutocomplete.length) {
            if(component.val() === '') {
                businessAutocomplete.remove();
            } else {
                businessAutocomplete.show();
            }
        }
    });
}

function seleccionarStore(puntoId, tipoId) {
    var inputs;
    if($('#data').hasClass('origen')) {
        inputs = '<input type="hidden" class="punto-origen-id-input" name=punto_origen_id value="' + puntoId + '">' +
            '<input type="hidden" class="punto-origen-tipo-input" name=punto_origen_tipo value="' + tipoId + '">';
        $('#origen-form').append(inputs);
        $('#direccion-origen-store > input').val($('.store-active').find('.nombre').text().trim() + ', ' + $('.store-active').find('.direccion').text().trim());
        $('#cp-origen-autocomplete, #tipo_recogida_origen, #direccion-origen-store > input').attr('disabled', 'disabled');
        $('#cambiar-origen-link').removeClass('hidden');
    } else if($('#data').hasClass('destino')) {
        inputs = '<input type="hidden" class="punto-destino-id-input" name=punto_destino_id value="' + puntoId + '">' +
            '<input type="hidden" class="punto-destino-tipo-input" name=punto_destino_tipo value="' + tipoId + '">';
        $('#destino-form').append(inputs);
        $('#direccion-destino-store > input').val($('.store-active').find('.nombre').text().trim() + ', ' + $('.store-active').find('.direccion').text().trim());
        $('#cp-destino-autocomplete, #pais-autocomplete, #tipo_entrega_destino, #direccion-destino-store > input').attr('disabled', 'disabled');
        $('#cambiar-destino-link').removeClass('hidden');
    }

    $('#modal-stores-search').modal('hide');
}

$(function() {

    var mapa;
    var menosProductosLink = $('#menos-productos-link');
    var direccionOrigenStore = $('#direccion-origen-store');
    var direccionOrigenDomicilio = $('#direccion-origen-domicilio');
    var cpOrigenAutocomplete = $('#cp-origen-autocomplete');
    var tipoRecogidaOrigen = $('#tipo_recogida_origen');
    var tipoRecogidaDestino = $('#tipo_entrega_destino');
    var direccionDestinoStore = $('#direccion-destino-store');
    var direccionDestinoDomicilio = $('#direccion-destino-domicilio');
    var cpDestinoAutocomplete = $('#cp-destino-autocomplete');

    $('#cp-destino-autocomplete').focus(function() {
        if($('input[name="pais_destino_id"]').val() === '') {
            $('#pais-autocomplete').focus();
            if(!$('.ui-pnotify-container').length) {
                new PNotify({
                    title: 'Transporter',
                    text: 'Primero debes seleccionar un país.',
                    addclass: 'transporter-alert',
                    icon: 'icon-transporter',
                    autoDisplay: true,
                    hide: true,
                    delay: 5000,
                    closer: false,
                });
            }
        }
    });

    $('#mas-productos-link').click(function() {
        var component = '<div class="row form-group producto">' +
            '<div class="col-lg-6 col-xs-12 form-group-md">' +
            '<label>SKU O NOMBRE PRODUCTO</label>' +
            '<input type="text" class="form-control autocomplete-input" name="nombre_producto[]" autocomplete="off" value="" required>' +
            '</div>' +
            '<div class="col-lg-3 col-xs-6">' +
            '<label>Nº</label>' +
            '<input type="number" min="1" class="form-control cantidad-input" name="num_productos[]" value="" required>' +
            '</div>' +
            '<div class="col-lg-3 col-xs-6">' +
            '<label class="text-nowrap">PESO (kg/ud)</label>' +
            '<input type="text" class="form-control peso-input" name="peso_producto[]" placeholder="0.0" value="" required>' +
            '</div>' +
            '</div>';
        menosProductosLink.removeClass('hidden');
        $(this).parent().before(component);
        initProductosAutocompleteListener($('.producto:last').find('.autocomplete-input'));
    });

    menosProductosLink.click(function() {
        $('.producto:last').remove();
        if($('.producto').length === 0) {
            menosProductosLink.addClass('hidden');
        }
    });

    $('#embalaje').change(function(e) {
        if(e.target.value > 0) {
            var elem = $(this).find('option[value="' + e.target.value + '"]');
            $('#alto-input').val(elem.attr('alto'));
            $('#ancho-input').val(elem.attr('ancho'));
            $('#largo-input').val(elem.attr('largo'));
            $('#alto-input, #ancho-input, #largo-input').prop('disabled', 'disabled');
        } else {
            $('#alto-input, #ancho-input, #largo-input').val('').prop('disabled', false);
        }
    });

    $('#cambiar-origen-link').click(function() {
        $('#origen-form input, #origen-form select').val('').prop('disabled', false);
        if(!direccionOrigenStore.hasClass('hidden')) {
            direccionOrigenStore.addClass('hidden');
        }
        direccionOrigenDomicilio.removeClass('hidden');
        $('.punto-origen-id-input, .punto-origen-tipo-input').remove();
        $(this).addClass('hidden');
    });

    tipoRecogidaOrigen.change(function() {
        var val = $(this).val();
        $('input[name="tipo_recogida_id"]').val(val);
        if(val == 2 && cpOrigenAutocomplete.val() === '') {
            $(this).val('');
            new PNotify({
                title: 'Transporter',
                text: 'Primero debes seleccionar un código postal o ciudad.',
                addclass: 'transporter-alert',
                icon: 'icon-transporter',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
            cpOrigenAutocomplete.focus();
        } else {
            if (val == 1 && !direccionOrigenStore.hasClass('hidden')) {
                direccionOrigenStore.addClass('hidden');
                direccionOrigenDomicilio.removeClass('hidden');
            } else if (val == 2 && !direccionOrigenDomicilio.hasClass('hidden')) {
                direccionOrigenDomicilio.addClass('hidden');
                direccionOrigenStore.removeClass('hidden');
            }
        }

        if(val == 2) {
            $('.ciudad-input').val($('.ciudad-origen-input').val());

            $.ajax({
                
                type: "post",
                url: '/configuracion/punto-recogida/selection',
                data: {
                    cp_id: $('#codigo-postal-origen-input').val(),
                    tipo_recogida: tipoRecogidaOrigen.val(),
                    codigo_postal: $('.ciudad-origen-input').val(),
                    _token: $('input[name="_token"]').val()
                },
                success: function(data) {
                    $('#data').empty();
                    $('#data').removeClass('origen').removeClass('destino').addClass('origen');
                    if(typeof mapa !== 'undefined' && tipoRecogidaOrigen.val() == 2) {
                        $('#data').append(data);
                        $('#modal-stores-search').on('hide.bs.modal', function () {
                            if (direccionOrigenStore.val() === '') {
                                $('#tipo_recogida_origen').val('');
                            }
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
                            $('#modal-stores-search').on('hide.bs.modal', function () {
                                if (direccionOrigenStore.find('input').val() === '') {
                                    $('#tipo_recogida_origen').val('');
                                }
                            });

                        });
                    }

                    $('.punto-origen-id-input, .punto-origen-tipo-input').remove();

                }
            });
        }
    });

    tipoRecogidaDestino.change(function() {
        var val = $(this).val();
        $('input[name="tipo_entrega_destino_id"]').val(val);
        if(val == 2 && cpDestinoAutocomplete.val() === '') {
            $(this).val('');
            new PNotify({
                title: 'Transporter',
                text: 'Primero debes seleccionar un país y un código postal o ciudad.',
                addclass: 'transporter-alert',
                icon: 'icon-transporter',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
            cpDestinoAutocomplete.focus();
        } else {
            if (val == 1 && !direccionDestinoStore.hasClass('hidden')) {
                direccionDestinoStore.addClass('hidden');
                direccionDestinoDomicilio.removeClass('hidden');
            } else if (val == 2 && !direccionDestinoDomicilio.hasClass('hidden')) {
                direccionDestinoDomicilio.addClass('hidden');
                direccionDestinoStore.removeClass('hidden');
            }
        }

        if(val == 2) {
            
            $('.ciudad-input').val($('.ciudad-destino-input').val());
            console.log($('.ciudad-destino-input').val());
            $.ajax({
                type: "post",
                url: '/configuracion/punto-recogida/selection',
                async: true,
                data: {
                    cp_id: $('#codigo-postal-destino-input').val(),
                    tipo_recogida: tipoRecogidaDestino.val(),
                    codigo_postal: $('.ciudad-destino-input').val(),
                    _token: $('input[name="_token"]').val()
                },
                success: function(data) {
                    $('#data').removeClass('origen').removeClass('destino').addClass('destino');
                    if(typeof mapa !== 'undefined' && tipoRecogidaDestino.val() == 2) {
                        $('#data').empty();
                        $('#data').append(data);
                        $('#modal-stores-search').on('hide.bs.modal', function() {
                            if(!$('.punto-destino-id-input').length) {
                                $('#tipo_entrega_destino').val('');
                            }
                        });
                    } else {
                        $.when(
                            $.getScript(storesSearchJs),
                            $.getScript(customScrollJs),
                            $.Deferred(function (deferred) {
                                $(deferred.resolve);
                            })
                        ).done(function () {
                            $('#data').empty();
                            $('#data').append(data);
                            $('#modal-stores-search').on('hide.bs.modal', function() {
                                if(!$('.punto-destino-id-input').length) {
                                    $('#tipo_entrega_destino').val('');
                                }
                            });
                        });
                    }

                    $('.punto-destino-id-input, .punto-destino-tipo-input').remove();

                }
            });
        }
    });

    $('#cambiar-destino-link').click(function() {
        $('#destino-form input, #destino-form select').val('').attr('disabled', false);
        $(this).addClass('hidden');
    });

    $('form').submit(function() {
        var form = $(this);
        $('input:disabled, select:disabled').each(function() {
            var input = '<input type="hidden" name="' + $(this).attr('name') + '" value="' + $(this).val() + '">';
            form.append(input);
        });
    });

    initProductosAutocompleteListener($('.autocomplete-input'));
    initCpAutocompleteListener($('#cp-origen-autocomplete'));
    initCpAutocompleteListener($('#cp-destino-autocomplete'));
    initPaisAutocompleteListener($('#pais-autocomplete'));

});