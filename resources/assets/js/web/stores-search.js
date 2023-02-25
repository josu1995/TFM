'use strict';
// Mapa
let mapa;
// Iconos de marker
let imagenMarker = '/img/maps/transporter-marker-grey.png';
let markerActivo = '/img/maps/transporter-marker-active.png';
let markerEnvio = '/img/maps/transporter-marker-origen.png';
let markerRecogida = '/img/maps/transporter-marker-destino.png';
let markers = [];
let visibleMarkers = [];
let autocompleteInput;

let cercanaLat, cercanaLon, cercanaNombre;
let selectedLocalidad;
let visiblePuntos;
let mobile;
let scrolling = false;
const storesList = $('.stores-list');
const error = '<div class="alert alert-danger mg-15">' +
                '<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>' +
                '<p>No existen localidades para el texto introducido. Por favor, introduce una ciudad o código postal válido.</p>' +
                '</div>';

let scrollStartPos = 0;

$(function() {

    mobile = screen.width < 768;

    $('.linkCercana').on('click', function() {
        buscarLocalidad(cercanaNombre, cercanaLat, cercanaLon);
    });

    $('.btn-location').click(function() {
        getLocation();
    });

    $('.btn-buscar').click(function() {
        buscarLocalidad($('#autocomplete').val(), '', '');
    });

    if(mobile) {
        $('body').on('touchstart ', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    } else {
        $('body').on('mousedown', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    }

    storesList.on('touchstart', function(e) {
        scrollStartPos = e.originalEvent.touches[0].clientX;
    });

    storesList.on('touchmove', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if(e.originalEvent.touches[0].clientX < scrollStartPos) {
            $(this)[0].scrollLeft = scrollStartPos - e.originalEvent.touches[0].clientX + storesList.scrollLeft();
        } else {
            $(this)[0].scrollLeft = scrollStartPos - e.originalEvent.touches[0].clientX + storesList.scrollLeft();
        }
        scrollStartPos = e.originalEvent.touches[0].clientX;
    });

    storesList.on('touchend', function(e) {
        elementInViewport();
    });
});

function elementInViewport() {
    const scrolledLeft = storesList.scrollLeft();
    const viewPortWidth = storesList.width();
    const children = storesList.children();
    const elemWidth = children.first().width();

    let valToChange = 0;
    let elemToChange = null;

    for(let i = 0 ; i < children.length ; i++) {

        let visiblePixels = elemWidth - (scrolledLeft - (i * elemWidth));
        let percentVisible = visiblePixels * 100 / elemWidth;

        if(percentVisible > 100) {
            let hiddenPixels = elemWidth * (i + 1) - (scrolledLeft + viewPortWidth);
            percentVisible = (elemWidth - hiddenPixels) * 100 / elemWidth;
        }

        if(percentVisible < 0) {
            percentVisible = 0;
        } else if(percentVisible > 100) {
            percentVisible = 100;
        }

        if(percentVisible > valToChange) {
            valToChange = percentVisible;
            elemToChange = children.eq(i);
        }
    }

    hoverStore(elemToChange);
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            buscarLocalidad('', position.coords.latitude, position.coords.longitude);
        });
    } else {
        alert('Este navegador no soporta la geolocalización.');
    }
}

function mapsCallback() {
    initAutocomplete();
    crearMapa();
}

function initAutocomplete() {
    autocompleteInput = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
        {types: ['geocode'], componentRestrictions: {country: 'es'}});

    autocompleteInput.addListener('place_changed', fillInAddress);
}

function fillInAddress() {
    let place = autocompleteInput.getPlace();

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
                storesList.empty();
                $('.sin-paquetes').show();

            } else {
                selectedLocalidad = data.localidad;
                $('#autocomplete').val(data.localidad.nombre);
                // Creamos mapa en localidad recibida
                crearMapaLocalidad(data.localidad);
                // Creamos markers en puntos de localidad
                crearMarkersFin(data.localidad.puntos);

                showResultadoBusqueda(data.localidad.puntos, data.paquetes, data.ahorro);

            }
        },
        error: function() {
            showError();
        }
    });

}

// Crear mapa inicial
function crearMapa() {

    let opcionesMapa = {
        center: { lat: 40.4381311, lng: -3.8196195 },
        zoom: 5,
        disableDefaultUI: true,
        zoomControl: true,
    };

    mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);

    if(city !== null) {
        buscarLocalidad(city, '', '');
    }

}

const crearMapaLocalidad = function(localidad) {

    let center = new google.maps.LatLng(parseFloat(localidad.latitud), parseFloat(localidad.longitud));

    mapa.panTo(center);
    mapa.setZoom(12);

};

// Creación de markers por lista de puntos para elegir punto de inicio
const crearMarkersFin = function(puntos) {
    removeMarkers();
    markers = [];
    for (let punto in puntos) {
        // Creamos marker
        let marker = new google.maps.Marker({
            position: {
                lat: parseFloat(puntos[punto].latitud),
                lng: parseFloat(puntos[punto].longitud)
            },
            map: mapa,
            title: puntos[punto].nombre,
            icon: imagenMarker,
            animation: google.maps.Animation.DROP
        });

        markers.push(marker);

        crearMarkerListener(marker);
    }
};

function crearMarkerListener(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        hoverStore($('.punto-list-item').eq(markers.indexOf(marker)));
    });
}

function removeMarkers() {
    for(let i = 0 ; i < markers.length ; i++) {
        markers[i].setMap(null);
    }
}

function showResultadoBusqueda(puntos, paquetes, ahorro) {

    visiblePuntos = puntos;

    storesList.empty();

    $('.sin-paquetes').hide();
    storesList.show();

    for(let i = 0 ; i<puntos.length ; i++) {
        let punto = puntos[i];
        let imagePath = punto.imagen ? punto.imagen.path : '/img/home/store-no-img.png';

        let horario = '';
        if((punto.hoy.length == 2 && punto.hoy[0].cerrado && punto.hoy[1].cerrado) || (punto.hoy.length == 1 && punto.hoy[0].cerrado)) {
            horario = '<strong class="texto-recogida"> &nbsp' + 'Cerrado' + '</strong>';
        } else {
            if(punto.hoy[0] && !punto.hoy[0].cerrado) {
                horario += '<strong> &nbsp' + punto.hoy[0].inicio + '-' + punto.hoy[0].fin;
            }
            if(punto.hoy[1] && !punto.hoy[1].cerrado) {
                horario += ' ' + punto.hoy[1].inicio + '-' + punto.hoy[1].fin;
            }
            horario += '</strong>';
        }

        let listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item">' +
                '<img class="col-md-4 col-xs-12" src="' + imagePath + '" width="200">' +
                '<div class="col-md-8 col-xs-11 no-pd punto-datos">' +
                '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                '<label class="direccion-label"><i class="icon-punto texto-envio"> </i> ' + punto.nombre + '</label> <strong>(' + selectedLocalidad.nombre + ')</strong><br>' +
                '<i class="icon-ubicacion icono-naranja"> </i><label class="direccion-label">' + punto.direccion + '</label><br>' +
                '<i class="far fa-clock"> </i> <small class="text-grey">&nbspHORARIO</small><small class="text-secondary"> &nbspHOY</small><small>' + horario + '</small><br>' +
                '<a id="' + punto.id + '" class="ver-mas-link small text-secondary" data-toggle="popover" data-placement="top">Ver más</a>' +
                '</div>' +
                '</div>');

        let popover = $('<div class="horario-punto horario-table-' + punto.id + '">' +
            '<table class="table table-condensed table-hover table-horario-popover">' +
            '<tbody>' +
            '<tr class="1"><td class="day">Lunes</td></tr>' +
            '<tr class="2"><td class="day">Martes</td></tr>' +
            '<tr class="3"><td class="day">Miércoles</td></tr>' +
            '<tr class="4"><td class="day">Jueves</td></tr>' +
            '<tr class="5"><td class="day">Viernes</td></tr>' +
            '<tr class="6"><td class="day">Sábado</td></tr>' +
            '<tr class="7"><td class="day">Domingo</td></tr>' +
            '</tbody>' +
            '</table>' +
            '</div>');

        for(let i = 0 ; i < punto.horarios.length ; i++) {
            let element = null;

            if(punto.horarios[i].cerrado) {
                element = '<td class="texto-recogida">Cerrado</td>';
            } else if(punto.horarios[i].inicio && punto.horarios[i].fin) {
                element = '<td>' + punto.horarios[i].inicio.substr(0, 5) + '-' + punto.horarios[i].fin.substr(0, 5) + '</td>';
            }
            popover.find('tr.' + punto.horarios[i].dia).append(element);
        }

        storesList.append(listItem);

        $('body').append(popover);
    }

    if(!mobile) {
        customScroll(storesList);
    }

    storesList.find('img').on('load', function() {
        storesList.show();
    });
    setTimeout(function() {
        storesList.show();
    }, 2000);

    // Listener para hover
    if(!mobile) {
        $('.punto-list-item').hover(function () {
            hoverStore($(this));
        });
    } else {
        $('.punto-list-item').on('click', function () {
            hoverStore($(this));
        });
    }

    // Inicializamos popover
    $("[data-toggle=popover]").each(function() {
        $(this).popover({
            trigger: 'manual',
            html: true,
            content: function() {
                let id = $(this).attr('id');
                return $('.horario-table-' + id).html();
            },
            container: 'body'
        });
    });

    // Seleccionamos el primer elemento
    hoverStore($('.punto-list-item').first());
}

function hoverStore(elem) {
    let index = $('.punto-list-item').index(elem);
    let punto = visiblePuntos[index];
    let marker = findMarker(punto);

    if(!mobile) {
        clearStoresSelection();
        elem.addClass('store-active');
    } else {
        if($('.store-active') !== elem) {
            clearStoresSelection();
            elem.addClass('store-active');
        }
        if(index !== 0 && index !== $('.punto-list-item').length - 1) {
            storesList.animate({
                scrollLeft: elem.width() * index - ((storesList.width() - elem.width()) / 2),
            }, {duration: 400, queue: false});
        } else {
            storesList.animate({
                scrollLeft: elem.width() * index,
            }, {duration: 400, queue: false});
        }
    }

    marker.setIcon(markerActivo);
    let center = new google.maps.LatLng(parseFloat(punto.latitud), parseFloat(punto.longitud));
    mapa.panTo(center);

}

function buscarLocalidad(nombre, lat, lon) {

    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/localidad/search?nombre=' + nombre + '&lat=' + lat + '&long=' + lon + '&t=1',
        success: function(data) {
            selectedLocalidad = data.localidad;
            // Creamos mapa en localidad recibida
            crearMapaLocalidad(data.localidad);
            // Creamos markers en puntos de localidad
            crearMarkersFin(data.localidad.puntos, true);

            showResultadoBusqueda(data.localidad.puntos);

            $('#autocomplete').val(data.localidad.nombre);

        },
        error: function() {
            showError();
        }
    });

}

function showError() {
    if(!$('.alert.alert-danger').length) {
        $('.stores-search-container').prepend(error);
    }
}

function findMarker(punto) {
    for(let i = 0 ; i < markers.length ; i++) {
        let marker = markers[i];
        if(marker.position.lat().toFixed(6) == punto.latitud && marker.position.lng().toFixed(6) == punto.longitud) {
            return marker;
        }
    }
}

function findPunto(marker) {
    for(let i = 0 ; i < visiblePuntos.length ; i++) {
        let punto = visiblePuntos[i];
        if(marker.position.lat().toFixed(6) == punto.latitud && marker.position.lng().toFixed(6) == punto.longitud) {
            return punto;
        }
    }
}

function clearStoresSelection() {
    $('.store-active').removeClass('store-active');
    for(let i = 0 ; i < markers.length ; i++) {
        let marker = markers[i];
        marker.setIcon(imagenMarker);
    }
}


const customScroll = function(elemento) {
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