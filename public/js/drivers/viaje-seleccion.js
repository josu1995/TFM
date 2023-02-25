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
var numPaquetesSeleccionados = 0;
var numPaquetesSeleccionadosLocal = 0;
var cercanaLat = null;
var cercanaLon = null;
var cercanaNombre = null;
var selectedLocalidad = null;
var selectedElement = null;
var mobile;
var paqueteAdded = false;

var lastY;

var currentPunto = null;

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
        url: '/api/tstore/v1/localidad/search?nombre=' + place.address_components[1].short_name + '&lat=' + place.geometry.location.lat() + '&long=' + place.geometry.location.lng() + '&destinos='+destinos + '&t=1',
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
                $('#autocomplete').val(data.localidad.nombre);
                // Creamos mapa en localidad recibida
                crearMapaLocalidad(data.localidad);
                // Creamos markers en puntos de localidad
                crearMarkersFin(data.localidad.puntos, true);

                showResultadoBusqueda(data.localidad.puntos, data.paquetes, data.ahorro);

                $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de origen</strong>');
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

    var selected = '';
    $('.resumen-paquete').each(function() {
        if(selected == '') {
            selected = $(this).find('.idPaquete').val();
        } else {
            selected += ','+$(this).find('.idPaquete').val();
        }
    });

    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/localidad/search?nombre=' + nombre + '&lat=' + lat + '&long=' + lon + '&destinos=' + destinos + '&selected='+selected + '&t=1',
        success: function(data) {
            selectedLocalidad = data.localidad;
            // Creamos mapa en localidad recibida
            crearMapaLocalidad(data.localidad);
            // Creamos markers en puntos de localidad
            crearMarkersFin(data.localidad.puntos, true);

            showResultadoBusqueda(data.localidad.puntos);

            $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de origen</strong>');
        }
    });

}

function showResultadoBusqueda(puntos, paquetes, ahorro) {

    visiblePuntos = puntos;

    $('.destinoInput').prop('disabled', true);

    var list = $('.punto-list');

    var lista_paquetes = $('.punto-list-2');

    var container = $('#puntos');

    container.find('.no-puntos').hide();

    container.find('.unselected').remove();

    var enviosDisponibles = 0;

    for(var i = 0 ; i<puntos.length ; i++) {
        var punto = puntos[i];
        enviosDisponibles += punto.envios;
        var imagePath = punto.imagen ? punto.imagen.path : puntoImg;
        var listItem;
        if(punto.cerrado) {
            listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item unselected">' +
                '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                '<label class="direccion-label"><i class="icon-punto texto-envio"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                '<i class="icon-ubicacion icono-naranja"> </i><label class="direccion-label">' + punto.direccion + '</label><br>' +
                '<i class="fa fa-lock texto-recogida pd-l-2"> </i><label class="label-disponibles texto-recogida pd-t-5 pd-l-10">Este punto está cerrado</label>' +
                '</div>' +
                '<div class="col-md-2 col-xs-2 add-punto-container"><a class="btn-app-black add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                '</div>');
        } else {
            if (punto.envios > 0) {
                listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item unselected">' +
                    '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                    '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                    '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                    '<label class="direccion-label"><i class="icon-punto texto-envio"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                    '<i class="icon-ubicacion icono-naranja"> </i><label class="direccion-label">' + punto.direccion + '</label><br>' +
                    '<i class="icon-paquete icono-naranja"> </i><label class="texto-corporativo label-disponibles">Envíos disponibles: ' + punto.envios + '</label>' +
                    '</div>' +
                    '<div class="col-md-2 col-xs-2 add-punto-container"><a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                    '</div>');
            } else {
                if (punto.selected) {
                    listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item">' +
                        '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                        '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                        '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                        '<label class="direccion-label"><i class="icon-punto texto-envio"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                        '<i class="icon-ubicacion icono-naranja"> </i><label class="direccion-label">' + punto.direccion + '</label><br>' +
                        '<i class="icon-paquete icono-naranja"> </i><label class="label-disponibles">No hay más envíos</label>' +
                        '</div>' +
                        '<div class="col-md-2 col-xs-2 add-punto-container"><i class="far fa-check-square fa-3x check-punto texto-envio" aria-hidden="true"></i></div>' +
                        '</div>');
                } else {
                    listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item unselected">' +
                        '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                        '<div class="col-md-6 col-xs-9 no-pd punto-datos">' +
                        '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                        '<label class="direccion-label"><i class="icon-punto texto-envio"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                        '<i class="icon-ubicacion icono-naranja"> </i><label class="direccion-label">' + punto.direccion + '</label><br>' +
                        '<i class="icon-paquete icono-naranja"> </i><label class="label-disponibles">No hay envíos disponibles</label>' +
                        '</div>' +
                        '<div class="col-md-2 col-xs-2 add-punto-container"><a class="btn-app-black add-punto"><i class="icon-anadir texto-inverso"></i></a></div>' +
                        '</div>');
                }
            }
        }
        list.append(listItem);

        if(punto.selected && punto.envios == 0) {
            findPuntoInMap(listItem).setIcon(markerEnvio);
        }
    }

    var errores = $('.errorSeleccion');
    var seleccionados = $('.seleccionados .resumen-paquete');

    if(enviosDisponibles == 0 && paquetes != undefined && paquetes != 0 && ahorro != undefined && ahorro != 0 && errores.length == 0 && seleccionados.length == 0) {
        $('#localidadOrigenAlerta').text(selectedLocalidad.nombre);
        $('#numPaquetesAlerta').text(paquetes);
        $('#precioAlerta').text(ahorro+'€');
        $('#modalAlerta').modal();
    } else if(enviosDisponibles == 0 && errores.length == 0 && seleccionados.length == 0) {
        $('#autocompleteOrigenPuntual').val(selectedLocalidad.nombre);
        $('#origenPuntual').val(selectedLocalidad.id);
        $('#autocompleteOrigenHabitual').val(selectedLocalidad.nombre);
        $('#origenHabitual').val(selectedLocalidad.id);

        $('#modalNuevaAlerta').modal();
    }

    // container.append(list);

    if(!mobile) {
        customScroll(list);
        customScroll(lista_paquetes);
    }

    list.find('img').on('load', function() {
        list.show();
    });
    setTimeout(function() {
        list.show();
    }, 2000);

    $('.punto-list-item.unselected .add-punto').on('click', function() {
        puntoSelectionListener($(this).parents('.punto-list-item'));
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

    var destinos = null;
    $('.destinos').each(function () {
        if(destinos == null) {
            destinos = $(this).val();
        } else {
            destinos += ','+$(this).val();
        }
    });

    currentPunto = element.find('.punto-id').val();

    selectedElement = element;
    disableLightboxButton(element);

    $('#lightbox').load('/punto/' + destinos +'/desde/' + currentPunto, function() {


        customScroll('.lista-paquetes--modal .lista-paquetes.paquetes-scroll');

        $('#l-punto-popup').modal().on('hidden.bs.modal', function(e) {
            enableScrollMobile();
            enableLightboxButton(selectedElement);

            // Editamos mensaje de envios disponibles
            var currentElement = $('.punto-id[value='+currentPunto+']').parents('.punto-list-item');
            var disponibles = currentElement.find('.label-disponibles').text().split(': ')[1];

            if(disponibles) {
                if (disponibles - numPaquetesSeleccionados == 0) {
                    currentElement.find('.add-punto-container').html('<i class="far fa-check-square fa-3x check-punto texto-envio" aria-hidden="true"></i>');
                    findPuntoInMap(currentElement).setIcon(markerEnvio);
                    currentElement.find('.label-disponibles').text('No hay más envíos');
                    currentElement.find('.label-disponibles').removeClass('texto-corporativo');
                    currentElement.removeClass('unselected');
                    $('#punto_entrega_seleccion').html('<strong>Stores de origen</strong> seleccionados');
                    currentElement.unbind('click');
                } else {
                    currentElement.addClass('unselected');
                    if($('.punto-list-item').not('.unselected').length == 0) {
                        $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de origen</strong>');
                    }
                    currentElement.find('.label-disponibles').text('Envíos disponibles: ' + (disponibles - numPaquetesSeleccionados));
                    if(!currentElement.find('.label-disponibles').hasClass('texto-corporativo')) {
                        currentElement.find('.label-disponibles').addClass('texto-corporativo');
                    }
                }
                numPaquetesSeleccionados = 0;
                numPaquetesSeleccionadosLocal = 0;
            }
        });

        $('#l-punto-popup').modal().on('shown.bs.modal', function(e) {
            disableScrollMobile();
            $('.agregar-origen-destino button').on('click', function(event) {
                marker.setIcon(markerRecogida);
            });


            // Añadir paquete a viaje
            $(this).on('click', '.add-paquete', function(event) {
                event.preventDefault();

                var selfElement = $(this).parents('.resumen-paquete');

                $('.spinner.hidden').removeClass('hidden');
                var datos = {
                    'codigo': selfElement.find('input[name="envio_codigo"]').val(),
                    'inicio': selfElement.find('input[name="envio_inicio"]').val(),
                    'fin': selfElement.find('input[name="envio_fin"]').val(),
                    '_token': selfElement.find('input[name="_token"]').val()
                };

                var destinos = null;

                $('.destinos').each(function() {
                    if(destinos == null) {
                        destinos = $(this).val();
                    } else {
                        destinos += ','+$(this).val();
                    }
                });

                // Añadir por post el envío y mostrar lista resultante
                    // Recargamos la lista de paquetes
                var jqxhr = $.post('/drivers/viajar', datos, function(datos) {
                    if(!datos.error) {
                        $('#lista-paquetes').load('/punto/' + destinos + '/desde/' + element.find('.punto-id').val() + '/lista', function () {
                            // Actualizamos el div con la lista de paquetes seleccionados
                            $('#paquetes').load(document.URL + ' #paquetes', function () {
                                // Volvemos a recargar scroll al actualizar
                                if(!mobile) {
                                    customScroll('.lista-paquetes--modal .lista-paquetes.paquetes-scroll');
                                    customScroll($('#paquetes').find('.paquetes-scroll'));
                                }
                                paqueteAdded = true;
                                refreshEliminadosListener();
                                $('.spinner').addClass('hidden');
                            });
                            // Recargamos botón de finalizar
                            // $('.continuarContainer').show();
                        });

                        // Cambiamos las pestañas
                        if($('#puntosTab').hasClass('active')) {
                            $('#puntosTab').removeClass('active');
                            $('#paquetesTab').addClass('active');
                            $('#puntos').removeClass('active');
                            $('#paquetes').addClass('active');
                        }

                        numPaquetesSeleccionados++;
                        numPaquetesSeleccionadosLocal++;

                    } else {
                        setTimeout( function() {
                            $('.spinner').addClass('hidden');
                        }, 1000);
                    }
                });




            });
        });
    });

}

function puntoSelectionActionPerformed(element) {
    element.find('.punto-icon i').prop('style','color: green');
    var eliminarButton = $('<div class="checkbox no-mg delete">' +
        '<input type="checkbox" id="'+element.find('.punto-id').val()+'" class="envios_eliminados">' +
        '<label for="'+element.find('.punto-id').val()+'" class="texto-recogida">Eliminar</label>' +
        '</div>');
    element.find('.punto-datos').append(eliminarButton);
    element.removeClass('unselected');
    element.unbind('click');
    element.find('.punto-id').prop('name', 'puntos[]');
    $('.continuarContainer').show();

    element.find('.punto-datos .checkbox label').on('click', function() {
        $(this).parent().fadeOut('slow', function() {
            findPuntoInMap(element).setIcon(imagenMarker);
            $(this).parents('.punto-list-item').find('.punto-icon i').prop('style', '');
            $(this).parents('.punto-list-item').addClass('unselected');
            $(this).parents('.punto-list-item .add-punto').on('click', function() { puntoSelectionListener($(this).parents('.punto-list-item')) });
            element.find('.punto-id').prop('name', '');
            if($('.punto-list-item').not('.unselected').length == 0) {
                $('.continuarContainer').hide();
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

var cargarContenido = function(idPunto, marker) {
    // Cargamos contenido en modal

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
        var currentPunto = findPuntoByMarker(marker);
        puntoSelectionListener($('.punto-id[value='+currentPunto.id+']').parents('.punto-list-item'));
    });
};

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
}

function deseleccionarEnviosAll() {
    var data = {
        '_token' : $('.lista-paquetes input[name="_token"]').val()
    };

    // Borrado de envíos
    $.ajax({
        url: '/drivers/viajar/seleccion',
        type: 'DELETE',
        data: data,
        success: function(result) {

        },
        error: function(error) {

        }
    });
}

function refreshEliminadosListener() {
    $('#paquetes').find('input[name="envios_eliminados[]"]').each(function() {
        $(this).off('change');
        $(this).on('change', function(event) {
            var self = $(this);

            $(this).off('change');

            var resumen = $(this).parents('.resumen-paquete');
            var idPunto = resumen.find('.idPuntoOrigenPaquete').val();

            // Editamos mensaje de envios disponibles
            var currentElement = $('.punto-id[value='+idPunto+']').parents('.punto-list-item');
            var disponibles = currentElement.find('.label-disponibles').text().split(': ')[1];

            if(!disponibles || disponibles == 0) {
                currentElement.find('.add-punto-container').html('<a class="btn-app add-punto"><i class="icon-anadir texto-inverso"></i></a>');
                findPuntoInMap(currentElement).setIcon(imagenMarker);
                if(!disponibles) {
                    disponibles = 0;
                }
                currentElement.find('.label-disponibles').text('Envíos disponibles: '+(parseInt(disponibles)+1));
                currentElement.addClass('unselected');

                if($('.punto-list-item').not('.unselected').length == 0) {
                    $('#punto_entrega_seleccion').html('Selecciona los <strong>stores de origen</strong>');
                }

                currentElement.find('.add-punto').on('click', function() {
                    puntoSelectionListener(currentElement);
                });

            } else {
                currentElement.find('.label-disponibles').text('Envíos disponibles: '+(parseInt(disponibles)+1));
            }

            if(!currentElement.find('.label-disponibles').hasClass('texto-corporativo')) {
                currentElement.find('.label-disponibles').addClass('texto-corporativo');
            }

            event.preventDefault();
            var data = {
                'envios_eliminados' : [$(this).attr('id')],
                '_token' : $('.lista-paquetes input[name="_token"]').val()
            };

            // Borrado de envío
            $.ajax({
                url: '/drivers/viajar',
                type: 'DELETE',
                data: data,
                success: function(result) {
                    self.closest('.resumen-paquete').fadeOut('slow', function() {
                        $('#paquetes').load(document.URL +  ' #paquetes', function() {
                            refreshEliminadosListener();
                            if(!mobile) {
                                customScroll($('#paquetes').find('.paquetes-scroll'));
                            }
                        });

                    });
                },
                error: function(error) {
                    console.log('No ha sido posible eliminar el envío');
                }
            });
        });

    });
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
    yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['es']);


//
// Ejecución
//
$(document).ready(function() {

    mobile = screen.width < 768;

    $.ajaxSetup ({
        // Disable caching of AJAX responses
        cache: false
    });

    $(document).on('touchstart', function(e) {
        lastY = e.originalEvent.changedTouches[0].pageY;
    });

    $( "#fecha" ).datepicker(
        $.datepicker.regional['es'],
        {
            dateFormat: 'dd-mm-yy',
            startDate: '0d'
        });

    $('.destinoForm').submit(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });

    // $('.limpiarButton').on('click', function() {
    //     $('.punto-list').empty();
    //     $('.no-puntos').show();
    //     $('.destinoInput').val('').prop('disabled', false);
    //     for(var i = 0 ; i<visibleMarkers.length ; i++) {
    //         visibleMarkers[i].setIcon(imagenMarker);
    //     }
    // });

    $('.confirmLimpiar').on('click', function() {
        $('.punto-list').empty();
            $('.no-puntos').show();
            $('#autocomplete').val('').prop('disabled', false);
            for(var i = 0 ; i<visibleMarkers.length ; i++) {
                visibleMarkers[i].setIcon(imagenMarker);
            }

            deseleccionarEnviosAll();
            $('#form-seleccion').remove();
            var paquetesDiv = $('#paquetes');
            if(paquetesDiv.find('.sin-paquetes').length == 0) {
                var noPaquetes = $('<div class="t-paquetes sin-paquetes no-puntos" style="position: absolute; margin: 0 auto; right: 0; left: 0; top: 50%; transform: translateY(-50%); text-align: center;">'+
                    '<span class="icon-historial-paquetes texto-gris" style="font-size: 8em;display: block;margin-bottom: 10px;"></span>'+
                    '<p class="text-center texto-gris"><strong>No hay ningún Store</strong> <br>seleccionado</p>'+
                '</div>');
                paquetesDiv.append(noPaquetes);
            }

            $('#punto_entrega_seleccion').html('Selecciona primero tu <strong>ciudad de origen</strong>');

            crearMapa();
            crearMarkersFin(puntosAll, false);
    });

    $('.continuarContainer button').on('click', function () {
        $('.destinosForm').submit();
    });

    $(document).on('click','#ver_paquetes.show-paquetes-viaje-button',function(){
        $('.lista-paquetes--modal').show();
    });

    $(document).on('click','#lista-paquetes--cerrar',function(){
        if(paqueteAdded) {
            $('.enviosRestantes').text($('.enviosRestantes').text() - numPaquetesSeleccionadosLocal);
            numPaquetesSeleccionadosLocal = 0;
            if ($('.enviosRestantes').text() <= 0) {
                $('#ver_paquetes > p').empty().text('Este punto no tiene actualmente ningún envío para transportar');
                $('#ver_paquetes').removeClass('show-paquetes-viaje-button').addClass('mensaje-oscuro');
            }
            paqueteAdded = false;
        }
        $('.lista-paquetes--modal').hide();
    });

    // Submit alerta

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
                    location.href = '/drivers/inicio/alertas';
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
                    location.href = '/drivers/inicio/alertas';
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

    // Carga inicial de mapa y de todos los puntos disponibles
    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/punto',
        success: function(data) {
            puntosAll = data.puntos.data;

            // Creamos markers en puntos de localidad
            crearMarkersFin(data.puntos.data, false);
        }
    });

    refreshEliminadosListener();

    $('.linkCercana').on('click', function() {
        buscarLocalidad(cercanaNombre, cercanaLat, cercanaLon);
        $('#autocomplete').val(cercanaNombre);
    });

    //Buscar si existe localidad
    if($('#localidad_lat').length != 0) {
        buscarLocalidad($('#autocomplete').val(), $('#localidad_lat').val(), $('#localidad_lon').val());
    } else {
        $('#autocomplete').prop('disabled', false).val('');
    }

    if($('#form-seleccion').length != 0 && !mobile) {
        customScroll('.paquetes-scroll');
    }

});