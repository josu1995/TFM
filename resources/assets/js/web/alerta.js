'use strict';

var autocompleteOrigenPuntual, autocompleteDestinoPuntual, autocompleteOrigenHabitual, autocompleteDestinoHabitual;
var autocompleteOrigenPuntualEditar, autocompleteDestinoPuntualEditar, autocompleteOrigenHabitualEditar, autocompleteDestinoHabitualEditar;
var placeOrigenPuntual, placeDestinoPuntual, placeOrigenHabitual, placeDestinoHabitual;
var placeOrigenPuntualEditar, placeDestinoPuntualEditar, placeOrigenHabitualEditar, placeDestinoHabitualEditar;
var localidadOrigen, localidadDestino;


function initAutocompletes() {
    // Inicializamos origen
    autocompleteOrigenPuntual = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteOrigenPuntual')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteOrigenPuntual.addListener('place_changed', fillInAddressOrigenPuntual);

    // Inicializamos destino
    autocompleteDestinoPuntual = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteDestinoPuntual')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteDestinoPuntual.addListener('place_changed', fillInAddressDestinoPuntual);

    autocompleteOrigenHabitual = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteOrigenHabitual')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteOrigenHabitual.addListener('place_changed', fillInAddressOrigenHabitual);

    autocompleteDestinoHabitual = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteDestinoHabitual')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteDestinoHabitual.addListener('place_changed', fillInAddressDestinoHabitual);


    // EDITAR

    // Inicializamos origen
    autocompleteOrigenPuntualEditar = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteOrigenPuntualEditar')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteOrigenPuntualEditar.addListener('place_changed', fillInAddressOrigenPuntualEditar);

    // Inicializamos destino
    autocompleteDestinoPuntualEditar = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteDestinoPuntualEditar')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteDestinoPuntualEditar.addListener('place_changed', fillInAddressDestinoPuntualEditar);

    autocompleteOrigenHabitualEditar = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteOrigenHabitualEditar')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteOrigenHabitualEditar.addListener('place_changed', fillInAddressOrigenHabitualEditar);

    autocompleteDestinoHabitualEditar = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocompleteDestinoHabitualEditar')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});
    autocompleteDestinoHabitualEditar.addListener('place_changed', fillInAddressDestinoHabitualEditar);

}

function fillInAddressOrigenPuntual() {
    placeOrigenPuntual = autocompleteOrigenPuntual.getPlace();

    if(placeOrigenPuntual.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeOrigenPuntual.address_components[1].short_name + '&lat=' + placeOrigenPuntual.geometry.location.lat() + '&long=' + placeOrigenPuntual.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {

                    $('#autocompleteOrigenPuntual').val(data.localidad.nombre);

                    $('#origenPuntual').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#origenPuntual').val('error');
                }
            }
        });
    }
}

function fillInAddressDestinoPuntual() {
    placeDestinoPuntual = autocompleteDestinoPuntual.getPlace();

    if(placeDestinoPuntual.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeDestinoPuntual.address_components[1].short_name + '&lat=' + placeDestinoPuntual.geometry.location.lat() + '&long=' + placeDestinoPuntual.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteDestinoPuntual').val(data.localidad.nombre);

                    $('#destinoPuntual').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#destinoPuntual').val('error');
                }
            }
        });
    }
}

function fillInAddressOrigenHabitual() {
    placeOrigenHabitual = autocompleteOrigenHabitual.getPlace();

    if(placeOrigenHabitual.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeOrigenHabitual.address_components[1].short_name + '&lat=' + placeOrigenHabitual.geometry.location.lat() + '&long=' + placeOrigenHabitual.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteOrigenHabitual').val(data.localidad.nombre);

                    $('#origenHabitual').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#origenHabitual').val('error');
                }
            }
        });
    }
}

function fillInAddressDestinoHabitual() {
    placeDestinoHabitual = autocompleteDestinoHabitual.getPlace();

    if(placeDestinoHabitual.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeDestinoHabitual.address_components[1].short_name + '&lat=' + placeDestinoHabitual.geometry.location.lat() + '&long=' + placeDestinoHabitual.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteDestinoHabitual').val(data.localidad.nombre);

                    $('#destinoHabitual').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#destinoHabitual').val('error');
                }
            }
        });
    }
}

// Listeners de autocomplete para editar

function fillInAddressOrigenPuntualEditar() {
    placeOrigenPuntualEditar = autocompleteOrigenPuntualEditar.getPlace();

    if(placeOrigenPuntualEditar.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeOrigenPuntualEditar.address_components[1].short_name + '&lat=' + placeOrigenPuntualEditar.geometry.location.lat() + '&long=' + placeOrigenPuntualEditar.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {

                    $('#autocompleteOrigenPuntualEditar').val(data.localidad.nombre);

                    $('#origenPuntualEditar').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#origenPuntualEditar').val('error');
                }
            }
        });
    }
}

function fillInAddressDestinoPuntualEditar() {
    placeDestinoPuntualEditar = autocompleteDestinoPuntualEditar.getPlace();

    if(placeDestinoPuntualEditar.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeDestinoPuntualEditar.address_components[1].short_name + '&lat=' + placeDestinoPuntualEditar.geometry.location.lat() + '&long=' + placeDestinoPuntualEditar.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteDestinoPuntualEditar').val(data.localidad.nombre);

                    $('#destinoPuntualEditar').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#destinoPuntualEditar').val('error');
                }
            }
        });
    }
}

function fillInAddressOrigenHabitualEditar() {
    placeOrigenHabitualEditar = autocompleteOrigenHabitualEditar.getPlace();

    if(placeOrigenHabitualEditar.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeOrigenHabitualEditar.address_components[1].short_name + '&lat=' + placeOrigenHabitualEditar.geometry.location.lat() + '&long=' + placeOrigenHabitualEditar.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteOrigenHabitualEditar').val(data.localidad.nombre);

                    $('#origenHabitualEditar').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#origenHabitualEditar').val('error');
                }
            }
        });
    }
}

function fillInAddressDestinoHabitualEditar() {
    placeDestinoHabitualEditar = autocompleteDestinoHabitualEditar.getPlace();

    if(placeDestinoHabitualEditar.address_components) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/search?nombre=' + placeDestinoHabitualEditar.address_components[1].short_name + '&lat=' + placeDestinoHabitualEditar.geometry.location.lat() + '&long=' + placeDestinoHabitualEditar.geometry.location.lng() + '&t=1',
            success: function (data) {

                if(data.localidad != null) {
                    $('#autocompleteDestinoHabitualEditar').val(data.localidad.nombre);

                    $('#destinoHabitualEditar').val(data.localidad.id);
                } else {
                    // Error localidad
                    $('#destinoHabitualEditar').val('error');
                }
            }
        });
    }
}


$.datepicker.regional['es'] = {
    closeText: 'Cerrar',
    prevText: '< Ant',
    nextText: 'Sig >',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
    weekHeader: 'Sm',
    dateFormat: 'dd-mm-yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: '',
    minDate: 1
};
$.datepicker.setDefaults($.datepicker.regional['es']);

$(function() {

    $( "#fecha" ).datepicker(
        $.datepicker.regional['es'],
        {
        dateFormat: 'dd-mm-yy',
        startDate: '1d'
    });

    $( "#fechaEditar" ).datepicker(
        $.datepicker.regional['es'],
        {
            dateFormat: 'dd-mm-yy',
            startDate: '1d'
        });

    $('form input').focus(function() {
        $(this).parent('.has-error').removeClass('has-error');
    });

    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('input').not('[name="_token"]').val('');
    });

    // Submits de creacion
    $('#submitPuntual').on('click', function(e) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/drivers/inicio/alertas/puntual',
            _token: $('input[name="_token"]').val(),
            data: $('.formPuntual').serializeArray(),
            success: function (data) {

                if (data == 200) {
                    // ok
                    location.reload();
                } else {
                    // error
                    if (data.origenPuntual) {
                        var origenInput = $('#autocompleteOrigenPuntual');
                        origenInput.parent().addClass('has-error');
                        origenInput.val('').attr('placeholder', data.origenPuntual[0]);
                        $('#origenPuntual').val('');
                    }
                    if (data.destinoPuntual) {
                        var destinoInput = $('#autocompleteDestinoPuntual');
                        destinoInput.parent().addClass('has-error');
                        destinoInput.val('').attr('placeholder', data.destinoPuntual[0]);
                        $('#destinoPuntual').val('');
                    }
                    if (data.fecha) {
                        var fecha = $('#fecha');
                        fecha.parent().addClass('has-error');
                        fecha.val('').attr('placeholder', data.fecha[0]);
                    }
                }
            }
        });

    });

    $('#submitHabitual').on('click', function (e) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/drivers/inicio/alertas/habitual',
            _token: $('input[name="_token"]').val(),
            data: $('.formHabitual').serializeArray(),
            success: function (data) {

                if (data == 200) {
                    // ok
                    location.reload();
                } else {
                    // error
                    if (data.origenHabitual) {
                        var origenInput = $('#autocompleteOrigenHabitual');
                        origenInput.parent().addClass('has-error');
                        origenInput.val('').attr('placeholder', data.origenHabitual[0]);
                        $('#origenHabitual').val('');
                    }
                    if (data.destinoHabitual) {
                        var destinoInput = $('#autocompleteDestinoHabitual');
                        destinoInput.parent().addClass('has-error');
                        destinoInput.val('').attr('placeholder', data.destinoHabitual[0]);
                        $('#destinoHabitual').val('');
                    }
                    if (data.dias) {
                        var fecha = $('#dias');
                        fecha.addClass('has-error');
                        fecha.find('.feedback-alertas').text(data.dias[0]);
                    }
                }
            }
        });

    });

    // Submits de edicion
    $('#submitPuntualEditar').on('click', function(e) {

        $.ajax({
            type: 'put',
            dataType: 'json',
            url: '/drivers/inicio/alertas/'+$('#alertaIdPuntualEditar').val(),
            _token: $('input[name="_token"]').val(),
            data: $('.formPuntualEditar').serializeArray(),
            success: function (data) {

                if (data == 200) {
                    // ok
                    location.reload();
                } else {
                    // error
                    if (data.origenPuntual) {
                        var origenInput = $('#autocompleteOrigenPuntualEditar');
                        origenInput.parent().addClass('has-error');
                        origenInput.val('').attr('placeholder', data.origenPuntual[0]);
                    }
                    if (data.destinoPuntual) {
                        var destinoInput = $('#autocompleteDestinoPuntualEditar');
                        destinoInput.parent().addClass('has-error');
                        destinoInput.val('').attr('placeholder', data.destinoPuntual[0]);
                    }
                    if (data.fecha) {
                        var fecha = $('#fechaEditar');
                        fecha.parent().addClass('has-error');
                        fecha.val('').attr('placeholder', data.fecha[0]);
                    }
                }
            }
        });

    });

    $('#submitHabitualEditar').on('click', function (e) {

        $.ajax({
            type: 'put',
            dataType: 'json',
            url: '/drivers/inicio/alertas/'+$('#alertaIdHabitualEditar').val(),
            _token: $('input[name="_token"]').val(),
            data: $('.formHabitualEditar').serializeArray(),
            success: function (data) {

                if (data == 200) {
                    // ok
                    location.reload();
                } else {
                    // error
                    if (data.origenHabitual) {
                        var origenInput = $('#autocompleteOrigenHabitualEditar');
                        origenInput.parent().addClass('has-error');
                        origenInput.val('').attr('placeholder', data.origenHabitual[0]);
                    }
                    if (data.destinoHabitual) {
                        var destinoInput = $('#autocompleteDestinoHabitualEditar');
                        destinoInput.parent().addClass('has-error');
                        destinoInput.val('').attr('placeholder', data.destinoHabitual[0]);
                    }
                    if (data.dias) {
                        var fecha = $('#diasEditar');
                        fecha.addClass('has-error');
                        fecha.find('.feedback-alertas').text(data.dias[0]);
                    }
                }
            }
        });

    });


    $('.btn-editar-alerta').on('click', function() {

        var alerta = $(this).parents('.alerta');
        if(alerta.hasClass('puntual')) {
            var alertaId = alerta.attr('id');
            var origenId = alerta.find('.origen').attr('id');
            var origenNombre = alerta.find('.origen').text();
            var destinoId = alerta.find('.destino').attr('id');
            var destinoNombre = alerta.find('.destino').text();
            var alertaFecha = alerta.find('.fecha').text().replace(/\//g,'-');

            $('#alertaIdPuntualEditar').val(alertaId);
            $('#autocompleteOrigenPuntualEditar').val(origenNombre);
            $('#origenPuntualEditar').val(origenId);
            $('#autocompleteDestinoPuntualEditar').val(destinoNombre);
            $('#destinoPuntualEditar').val(destinoId);
            $('#fechaEditar').val(alertaFecha);

            $('#modalEditarAlertaPuntual').modal();

        } else if(alerta.hasClass('habitual')) {


            var alertaId = alerta.attr('id');
            var origenId = alerta.find('.origen').attr('id');
            var origenNombre = alerta.find('.origen').text();
            var destinoId = alerta.find('.destino').attr('id');
            var destinoNombre = alerta.find('.destino').text();

            alerta.find('.dias').children().each(function() {
                $('#'+$(this).text()).prop('checked', true);
                $('#'+$(this).text()).parent().addClass('active');
            });

            $('#alertaIdHabitualEditar').val(alertaId);
            $('#autocompleteOrigenHabitualEditar').val(origenNombre);
            $('#origenHabitualEditar').val(origenId);
            $('#autocompleteDestinoHabitualEditar').val(destinoNombre);
            $('#destinoHabitualEditar').val(destinoId);

            $('#modalEditarAlertaHabitual').modal();

        }
    });

    $('#modalNuevaAlerta, #modalEditarAlertaPuntual, #modalEditarAlertaHabitual').on('show.bs.modal', function (e) {
        $('body, html').css('overflow', 'hidden');
    });

    $('#modalNuevaAlerta, #modalEditarAlertaPuntual, #modalEditarAlertaHabitual').on('hide.bs.modal', function (e) {
        $('body, html').css('overflow', 'inherit');
    });

});