'use strict';
var inicio = true;
// Mapa
var mapa;
// Iconos de marker
var imagenMarker = '/img/maps/transporter-marker-grey.png';
var markerActivo = '/img/maps/transporter-marker-active.png';
var markerEnvio = '/img/maps/transporter-marker-origen.png';
var markerRecogida = '/img/maps/transporter-marker-destino.png';
var markers = [];
var cluster;
var directionsDisplay;
var directionsService;
var puntosAll;
var visiblePuntos = [];
var visibleMarkers = [];
var cercanaLat = null;
var cercanaLon = null;
var cercanaNombre = null;
var selectedLocalidad = null;
var selectedElement = null;

var mobile;

var lastY;

function mapsCallback() {
    initAutocomplete();
    crearMapa();
}

// Maps autocomplete
var placeSearch, autocomplete;
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function initAutocomplete() {
    // Create the autocomplete object, restricting the search to geographical
    // location types.
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
    var place = autocomplete.getPlace();

    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/localidad/search?nombre=' + place.address_components[1].short_name + '&lat=' + place.geometry.location.lat() + '&long=' + place.geometry.location.lng() + '&t=1',
        success: function(data) {

            if(data.localidad == null) {
                // Mostramos modal de seleccion
                $('.nombre-ciudad').text(data.cercana.nombre);
                cercanaLat = data.cercana.latitud;
                cercanaLon = data.cercana.longitud;
                cercanaNombre = data.cercana.nombre;
                $('#modalCercana').modal();
            } else {
                selectedLocalidad = data.localidad;
                // Creamos mapa en localidad recibida
                crearMapaLocalidad(data.localidad);
                // Creamos markers en puntos de localidad
                crearMarkersFin(data.localidad.puntos, true);

                showResultadoBusqueda(data.localidad.puntos);

                $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de destino</strong>');
            }
        }
    });

}

function buscarLocalidad(nombre, lat, lon) {

    var destinos = '';
    $('.destinos').each(function () {
        if(destinos == '') {
            destinos = $(this).val();
        } else {
            destinos += ','+$(this).val();
        }
    });

    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/localidad/search?nombre=' + nombre + '&lat=' + lat + '&long=' + lon + '&destinos=' + destinos + '&t=1',
        success: function(data) {
            selectedLocalidad = data.localidad;
            // Creamos mapa en localidad recibida
            crearMapaLocalidad(data.localidad);
            // Creamos markers en puntos de localidad
            crearMarkersFin(data.localidad.puntos, true);

            showResultadoBusqueda(data.localidad.puntos);

            $('.destinos').each(function() {
                findPuntoInMap($('.punto-id[value='+$(this).val()+']').parents('.punto-list-item')).setIcon(markerRecogida);
            });

            $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de destino</strong>');
        }
    });

}

function showResultadoBusqueda(puntos) {

    visiblePuntos = puntos;

    $('.destinoInput').prop('disabled', true);

    var list = $('.punto-list');

    var container = $('.bloque-paquetes');

    container.find('.sin-paquetes').hide();

    container.find('.unselected').remove();

    var destinos = [];
    if($('.destinos').length > 0) {
        $('.destinos').each(function () {
            destinos.push($(this).val());
        });
    }

    for(var i = 0 ; i<puntos.length ; i++) {
        var punto = puntos[i];
        var imagePath = punto.imagen ? punto.imagen.path : puntoImg;
        var listItem;
        if(punto.cerrado) {
            listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item unselected">' +
                '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                '<label class="direccion-label"><i class="icon-punto texto-recogida"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                '<i class="icon-ubicacion icono-naranja"> </i>' + punto.direccion + '<br>' +
                '<i class="fa fa-lock texto-recogida pd-l-2"> </i><label class="label-disponibles texto-recogida pd-t-5 pd-l-10">Este punto está cerrado</label>' +
                '</div>' +
                '<div class="col-md-2 col-xs-2 add-punto-container"><a class="btn-app-black add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                '</div>');
        } else {
            if (destinos.indexOf(punto.id + '') != -1) {
                listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item">' +
                    '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                    // '<div class="col-md-2 col-xs-2 add-punto-container show-xs hidden-md hidden-lg"><i class="fa fa-check-circle fa-3x check-punto" aria-hidden="true" style="color: green;"></i></div>' +
                    '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                    '<input type="hidden" class="punto-id" name="puntos[]" value="' + punto.id + '">' +
                    '<label class="direccion-label"><i class="icon-punto texto-recogida"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                    '<i class="icon-ubicacion icono-naranja"> </i>' + punto.direccion +
                    '<div class="checkbox no-mg delete">' +
                    '<input type="checkbox" id="' + punto.id + '" class="envios_eliminados">' +
                    '<label for="' + punto.id + '" class="texto-recogida viaje-eliminar-check">Eliminar</label>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-2 col-xs-2 add-punto-container"><i class="far fa-check-square fa-3x check-punto texto-envio" aria-hidden="true"></i></div>' +
                    '</div>');
                $('.continuarContainer').show();
            } else {
                listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item unselected">' +
                    '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                    // '<div class="col-md-2 col-xs-3 add-punto-container show-xs hidden-md hidden-lg"><a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                    '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                    '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                    '<label class="direccion-label"><i class="icon-punto texto-recogida"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                    '<i class="icon-ubicacion icono-naranja"> </i>' + punto.direccion +
                    '</div>' +
                    '<div class="col-md-2 col-xs-2 add-punto-container"><a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                    '</div>');
            }
        }
        list.append(listItem);
    }

    if(!mobile) {
        customScroll(list);
    }

    list.find('img').on('load', function() {
        list.show();
    });
    setTimeout(function() {
        list.show();
    }, 2000);

    $('.punto-list-item').not('.unselected').find('.checkbox label').on('click', function() {
        var element = $(this).parents('.punto-list-item');
        $(this).parent().fadeOut('slow', function() {
            findPuntoInMap(element).setIcon(imagenMarker);
            // $(this).parents('.punto-list-item').find('.punto-icon i').prop('style', '');
            element.find('.add-punto-container').html('<a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a>');
            $(this).parents('.punto-list-item').addClass('unselected');
            $(this).parents('.punto-list-item').find('.add-punto').on('click', function() { puntoSelectionListener($(this).parents('.punto-list-item')) });
            element.find('.punto-id').prop('name', '');
            if($('.punto-list-item').not('.unselected').length == 0) {
                $('.continuarContainer').hide();
            }
            $(this).remove();
        });
    });

    $('.punto-list-item.unselected .add-punto').on('click', function() {
        puntoSelectionListener($(this).parents('.punto-list-item'));
        $('#punto_entrega_seleccion').html('<strong>Stores de destino</strong> seleccionados');
    });

}


function findPuntoInMap(elem) {
    var id = elem.find('.punto-id').val();
    var punto = null;
    for(var i = 0 ; i<visiblePuntos.length ; i++) {
        if(visiblePuntos[i].id == id) {
            punto = visiblePuntos[i];
            break;
        }
    }

    for(var j = 0 ; j<visibleMarkers.length ; j++) {
        var marker = visibleMarkers[j];
        if(marker.position.lat().toFixed(6) == punto.latitud && marker.position.lng().toFixed(6) == punto.longitud) {
            return marker;
        }
    }
}

function findPuntoByMarker(marker) {
    for(var i = 0 ; i<visiblePuntos.length ; i++) {
        if(marker.position.lat().toFixed(6) == visiblePuntos[i].latitud && marker.position.lng().toFixed(6) == visiblePuntos[i].longitud) {
            return visiblePuntos[i];
        }
    }
}


function puntoSelectionListener(element) {
    var marker = findPuntoInMap(element);
    marker.setIcon(markerActivo);
    marker.setZIndex(9999);
    selectedElement = element;
    disableLightboxButton(selectedElement);
    $('#lightbox').load('/punto/' + element.find('.punto-id').val() + '/viaje', function(){
        $('#l-envio-popup').modal().on('shown.bs.modal', function(e) {
            disableScrollMobile();
            $('.agregar-origen-destino button').on('click', function(event) {
                // for(var i = 0 ; i<visibleMarkers.length ; i++) {
                //     visibleMarkers[i].setIcon(imagenMarker);
                // }
                marker.setIcon(markerRecogida);
                puntoSelectionActionPerformed(element);
            });
        });

        $('#l-envio-popup').modal().on('hidden.bs.modal', function(e) {
            enableLightboxButton(selectedElement);
            enableScrollMobile();
        });

    });

}

function puntoSelectionActionPerformed(element) {
    element.find('.add-punto-container').html('<i class="far fa-check-square fa-3x check-punto texto-envio" aria-hidden="true"></i>');
    var eliminarButton = $('<div class="checkbox no-mg delete">' +
        '<input type="checkbox" id="'+element.find('.punto-id').val()+'" class="envios_eliminados">' +
        '<label for="'+element.find('.punto-id').val()+'" class="texto-recogida viaje-eliminar-check">Eliminar</label>' +
        '</div>');
    element.find('.punto-datos').append(eliminarButton);
    element.removeClass('unselected');
    element.unbind('click');
    element.find('.punto-id').prop('name', 'puntos[]');
    $('.continuarContainer').show();

    element.find('.punto-datos .checkbox label').on('click', function() {
        $(this).parent().fadeOut('slow', function() {
            findPuntoInMap(element).setIcon(imagenMarker);
            // $(this).parents('.punto-list-item').find('.punto-icon i').prop('style', '');
            element.find('.add-punto-container').html('<a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a>');
            $(this).parents('.punto-list-item').addClass('unselected');
            $(this).parents('.punto-list-item').find('.add-punto').on('click', function() { puntoSelectionListener($(this).parents('.punto-list-item')) });
            element.find('.punto-id').prop('name', '');
            if($('.punto-list-item').not('.unselected').length == 0) {
                $('.continuarContainer').hide();
                $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de destino</strong>');
            }
            $(this).remove();
        });
    });
}

// Maps map
// Crear mapa inicial (carga página)
var crearMapa = function() {
    // Mapa zoom España
    var opcionesMapa = {
        center: {
            lat: 40.4381311,
            lng: -3.8196195
        },
        zoom: 5
    };
    mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);

    directionsDisplay = new google.maps.DirectionsRenderer({ suppressMarkers : true });
    directionsService = new google.maps.DirectionsService();

    google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
        mapa.setZoom(mapa.getZoom()-1);
    });

};

// Creación de mapa centrado en localidad
var crearMapaLocalidad = function(localidad) {
    var opcionesMapa = {
        center: {
            lat: parseFloat(localidad.latitud),
            lng: parseFloat(localidad.longitud)
        },
        zoom: 12
    };
    // Creamos mapa nuevo
    mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);
};

// Bias the autocomplete object to the user's geographical location,
// as supplied by the browser's 'navigator.geolocation' object.
function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    }
}

// Creación de markers por lista de puntos para elegir punto de inicio
var crearMarkersFin = function(puntos, listener) {
    visibleMarkers = [];
    if(markers.length == 0) {
        for (var punto in puntos) {
            // Creamos marker
            var marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(puntos[punto].latitud),
                    lng: parseFloat(puntos[punto].longitud)
                },
                // map: mapa,
                title: puntos[punto].nombre,
                icon: imagenMarker,
                animation: google.maps.Animation.DROP
            });

            markers.push(marker);
            visibleMarkers.push(marker);
            // Creamos listener de click en marker
            if(listener) {
                crearListenerFin(marker, puntos[punto].id, puntos[punto].nombre, visibleMarkers);
            }
        }

    } else {
        for (var punto in puntos) {
            // Creamos marker
            var marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(puntos[punto].latitud),
                    lng: parseFloat(puntos[punto].longitud)
                },
                // map: mapa,
                title: puntos[punto].nombre,
                icon: imagenMarker,
                animation: google.maps.Animation.DROP
            });

            visibleMarkers.push(marker);
            // Creamos listener de click en marker
            if(listener) {
                crearListenerFin(marker, puntos[punto].id, puntos[punto].nombre, visibleMarkers);
            }
        }

    }

    // Cluster de markers
    var mcOptions = {gridSize: 30, maxZoom: 14, imagePath: '/img/maps/clustered/m'};
    cluster = new MarkerClusterer(mapa, visibleMarkers, mcOptions);


    // Si hay paquetes seleccionados calculamos automaticamente la ruta
    if(inicio && $('.seleccionados .resumen-paquete:visible').length > 0) {
        // loadOrigenesDestinos();
        inicio = false;
    }

}

// Creación de listener de infowindow de marker
var crearListenerFin = function(marker, idPunto, nombrePunto, markers) {
    google.maps.event.addListener(marker, 'click', function() {
        marker.setIcon(markerActivo);
        marker.setZIndex(9999);
        $('#lightbox').load('/punto/' + idPunto + '/viaje', function(){
            $('#l-envio-popup').modal().on('shown.bs.modal', function(e) {
                disableScrollMobile();
                $('.agregar-origen-destino button').on('click', function(event) {
                    marker.setIcon(markerRecogida);
                    var currentPunto = findPuntoByMarker(marker);
                    var domElement = $('.punto-id[value=' + currentPunto.id + ']').parents('.punto-list-item');
                    if(domElement.hasClass('unselected')) {
                        puntoSelectionActionPerformed(domElement);
                    }
                });
            });

            $('#l-envio-popup').modal().on('hidden.bs.modal', function(e) {
                enableScrollMobile();
            });

        });
    });
}

function disableScrollMobile() {
    $('body').css('overflow', 'hidden');
    $(document).on('touchmove', function(e) {
        var currentY = e.originalEvent.changedTouches[0].pageY;
        if(!$('.m-mapa-popup').find($(e.target)).length || ($('.m-mapa-popup').scrollTop() >= $('.m-mapa-popup')[0].scrollHeight - $('.m-mapa-popup').height() && currentY < lastY) || ($('.m-mapa-popup').scrollTop() == 0 && currentY > lastY)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function enableScrollMobile() {
    $('body').css('overflow', 'auto');
    $(document).off('touchmove');
}

function disableLightboxButton(element) {
    element.find('.add-punto').off('click');
}

function enableLightboxButton(element) {
    element.find('.add-punto').on('click', function() {
        puntoSelectionListener(element);
    });
}

var customScroll = function(elemento) {
    $(elemento).mCustomScrollbar({
        theme : 'dark-thick',
        live: true,
        alwaysShowScrollbar: 0,
        scrollbarPosition: "inside",
        advanced:{
            autoUpdateTimeout: 1
        }
    });
};


//
// Ejecución
//
$(document).ready(function() {

    mobile = screen.width < 768;

    $(document).on('touchstart', function(e) {
        lastY = e.originalEvent.changedTouches[0].pageY;
    });

    $('.destinoForm').submit(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    $('.limpiarButton').on('click', function() {
        $('.punto-list').empty();
        $('.sin-paquetes').show();
        $('.destinoInput').val('').prop('disabled', false);
        for(var i = 0 ; i<visibleMarkers.length ; i++) {
            visibleMarkers[i].setIcon(imagenMarker);
        }
        $('#punto_entrega_seleccion').html('Selecciona primero tu <strong>ciudad de destino</strong>');
        $('.continuarContainer').hide();
        crearMapa();
        crearMarkersFin(puntosAll, false);
    });

    $('.continuarContainer button').on('click', function () {
        $('.destinosForm').submit();
    });

    // Carga inicial de mapa y de todos los puntos disponibles
    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/punto',
        success: function(data) {
            // Creamos mapa inicial
            // crearMapa();

            puntosAll = data.puntos.data;

            // Creamos markers en puntos de localidad
            crearMarkersFin(data.puntos.data, false);
        }
    });

    $('.linkCercana').on('click', function() {
        buscarLocalidad(cercanaNombre, cercanaLat, cercanaLon);
        $('#autocomplete').val(cercanaNombre);
    });


    if($('#localidad_lat').length != 0) {
        buscarLocalidad($('#autocomplete').val(), $('#localidad_lat').val(), $('#localidad_lon').val());
    } else {
        $('#autocomplete').prop('disabled', false).val('');
    }

});