// Mapa
var mapa;

// Markers
var markersIcon = {};
var markers = [];
var visibleMarkers = [];
var markersCopy = [];
// Iconos de marker
var imagenMarker = '/img/maps/transporter-marker-grey.png';
var markerActivo = '/img/maps/transporter-marker-active.png';
var markerEnvio = '/img/maps/transporter-marker-origen.png';
var markerRecogida = '/img/maps/transporter-marker-destino.png';

// Cluster de markers
var cluster;

// Estado
var estado = false;

var markerOrigen;
var markerDestino;

var directionsDisplay;
var directionsService;

var mobile;

var lastY;

$(document).ready(function() {

    mobile = screen.width < 768;

    $(document).on('touchstart', function(e) {
        lastY = e.originalEvent.changedTouches[0].pageY;
    });

    // Carga de puntos filtrados por localidad (evento sobre lista localidades)
    $('.sublista-localidades li').on('click', function() {
        // mostrarMarkers();

        if($(this).parent().hasClass('localidades-entrega')) {
            estado = false;
            $('.mensaje-mapa').html('Selecciona el punto de <strong>origen</strong>');
        } else {
            estado = true;
            $('.mensaje-mapa').html('Selecciona el punto de <strong>destino</strong>');
        }

        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/localidad/' + $(this).attr('id'),
            success: function(data) {
                // Movemos la posicion del mapa a una localidad concreta
                cambiarLocalidad(data.localidad);
                // Cambiamos el nombre que se envia en el formulario
                if(estado) {
                    $('#localidadDestinoName').val(data.localidad.nombre);
                } else {
                    $('#localidadOrigenName').val(data.localidad.nombre);
                }
                directionsDisplay.setMap(null);
                if(mobile) {
                    if (estado) {
                        $('#localidadRecogida').val('');
                        cargarComboDestino(data.localidad.puntos);
                    } else {
                        cargarComboOrigen(data.localidad.puntos);
                    }
                }
            }
        });
    });


    // Seleción de cobertura en creación de envío
    $('#cobertura li').on('click', function() {
        $('#tipo_cobertura').val($(this).attr('data-value'));
        $('#cobertura_guest').val($(this).text());


        if ($(this).attr('data-value') == 1 || $('#tipo_cobertura').val() == undefined) {

            $('div#valorDeclarado').addClass('hidden');
            $('input[name="valorDeclarado"]').val(undefined);

        } else if ($(this).attr('data-value') > 1) {

            $('div#valorDeclarado').removeClass('hidden');
        }
    });

    // Seleción de embalaje en creación de envío
    $('#embalaje li').on('click', function() {
        $('#tipo_embalaje').val($(this).attr('data-value'));
        $('#embalaje_guest').val($(this).text());
    });
    $('body').on('click tap', function(e) {
        if(!$(e.target).hasClass('lista-form') && !$(e.target).hasClass('sublista-form') && !$(e.target).hasClass('bloque-input') && !$(e.target).hasClass('open-lista') && !$(e.target).parent().hasClass('lista-form')) {
            $('.lista-form').children('ul:visible').hide();
        }
    });

    if(mobile) {
        $('body').on('touchmove', function() { $('.sublista-form:visible').hide() })
    }

    // Desplegar Lista formularios
    $('.lista-form').on('click tap', function() {
        if($(this).children('ul:visible').length !== 0) {
            $('.lista-form').children('ul:visible').hide();
        } else {
            $('.lista-form').children('ul:visible').hide();
            $(this).children('ul').toggle();
        }
    });

    // Mostrar valor elegido lista
    $('.lista-form > ul li').click(function(e) {
        var padre = $(this).parent().parent().first();
        $('li:first', padre).text($(this).text());

        if (padre.attr('id') === 'destinatario_localidad') {
            //Reiniciar punto recogida

            $('#localidadRecogidaName').val(e.target.innerText);
            // $('#localidadOrigenID').val($(this).attr('id'));
            $('#localidadRecogida').val(e.target.id);
            $('#punto_recogida').val('');
            $('#punto_recogida_input').val('Seleccione un punto de destino');
            $.ajax({
                dataType: "json",
                url: '/api/tstore/v1/localidad/' + e.target.id,
                success: function (data) {
                    // Creamos markers en puntos de localidad
                    $('#buscador_fin_longitud').val(data.localidad.longitud);
                    $('#buscador_fin_latitud').val(data.localidad.latitud);
                }
            });

            $('.mensaje-mapa').html('Selecciona el punto de <strong>destino</strong>');
        }
        else if (padre.attr('id') === 'localidad') {
            $('#localidadEntregaName').val(e.target.innerText);
            // $('#localidadOrigenID').val($(this).attr('id'));
            $('#localidadEntrega').val(e.target.id);
            $('#punto_entrega').val('');
            $('#punto_entrega_input').val('Seleccione un punto de origen');

            $.ajax({
                dataType: "json",
                url: '/api/tstore/v1/localidad/' + e.target.id,
                success: function (data) {
                    // Creamos markers en puntos de localidad
                    $('#buscador_inicio_longitud').val(data.localidad.longitud);
                    $('#buscador_inicio_latitud').val(data.localidad.latitud);
                }
            });

            $('.mensaje-mapa').html('Selecciona el punto de <strong>origen</strong>');
        }
    });

    // Cambio estilos método de pago
    // $('.metodo-cobro').click(function(event) {
    //     var metodoSelected = $(this).attr('data-value');
    //     metodoCobro.each(function(index, el) {
    //         $(this).removeClass('metodo-activo');
    //     });
    //     $('#lista-pago option[selected="selected"]').attr('selected', false);
    //     $('#lista-pago option[value="' + metodoSelected + '"]').attr('selected', true);
    //     $(this).toggleClass('metodo-activo');
    // });

    if(value = $('#punto_recogida_guest').val()) {
        $('#punto_recogida_input').val(value);
    }
    if(value = $('#punto_entrega_guest').val()) {
        $('#punto_entrega_input').val(value);
    }
    // if (value = $('#punto_entrega').val()) {
    //     $('#punto_entrega').val(value);
    // }
    if (value = $('#cobertura_guest').val()) {
        $('#cobertura li:first').text(value);
    }
    if(value = $('#embalaje_guest').val()) {
        $('#embalaje li:first').text(value);
    }


// if($('#localidadOrigenName').val()) {
//     $('#localidad > li').text($('#localidadOrigenName').val());
// }
// if($('#localidadDestinoName').val()) {
//     $('#destinatario_localidad > li').text($('#localidadDestinoName').val());
// }

    $('input').on('blur', function(event) {
        $(this).val($(this).val().trim());
    });

});

// Crear mapa inicial
var crearMapa = function() {
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

    google.maps.event.addListener(directionsDisplay, 'directions_changed', function () {
        setTimeout(mapa.setZoom(mapa.getZoom() - 1), 500);
    });

    // Recuperamos puntos
    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/punto',
        success: function(data) {
            // Creamos markers en puntos de localidad
            crearMarkers(data.puntos.data);
            if(!$('#punto_entrega').val() || !$('#punto_recogida').val()) {
                if ($('#buscador_inicio_latitud').val().length) {
                    mostrarMarkersByLocation($('#localidadEntrega').val());
                    $('.mensaje-mapa').html('Selecciona el punto de <strong>origen</strong>');
                }
            }
        }
    });
};

function cargarComboOrigen(puntos) {
    $('.lista-puntos-origen li').text('Seleccione un punto de origen');
    $('.sublista-puntos-origen').empty();

    if(puntos) {
        for (var i = 0; i < puntos.length; i++) {
            var punto = puntos[i];
            $('.sublista-puntos-origen').append('<li id="' + punto.id + '" lat="' + punto.latitud + '" long="' + punto.longitud + '">' + punto.nombre + ' - ' + punto.direccion + '</li>');
        }

        $('#punto_entrega_input').hide();
        $('.icon-punto.texto-envio').css('color', '#fff');
        $('.lista-puntos-origen').show();

        $('.lista-puntos-origen > ul li').click(function (e) {
            var id = $(this).attr('id');
            var lat = $(this).attr('lat');
            var long = $(this).attr('long');
            var padre = $(this).parent().parent().first();
            $('li:first', padre).text($(this).text());
            estado = false;

            $('#lightbox').load('/punto/' + id + '?fin=' + estado, function () {
                $('#l-envio-popup').modal().on('shown.bs.modal', function () {
                    disableScrollMobile();
                    var marker = findMarker(lat, long);
                    tipoPunto(marker, markers, id); // Listeners de click
                    // Listener para el boton de cerrar
                    $('.cerrar-popup').on('click', function () {
                        $('.lista-puntos-origen li:first').text('Seleccione un punto de origen');
                        $('#punto_entrega_input').val('');
                        $('#punto_entrega_guest').val('');
                        $('#punto_entrega').val('');
                    });

                });

                $('#l-envio-popup').modal().on('hidden.bs.modal', function () {
                    enableScrollMobile();
                });


            });

            // Actualizar puntos
            var nombrePunto = $(this).text().split('-')[0].trim();
            $('#punto_entrega_input').val(nombrePunto);
            $('#punto_entrega_guest').val(nombrePunto);
            $('#punto_entrega').val(id);
        });
    }
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

function cargarComboDestino(puntos) {
    $('.lista-puntos-destino li').text('Seleccione un punto de destino');
    $('.sublista-puntos-destino').empty();

    if(puntos) {
        for (var i = 0; i < puntos.length; i++) {
            var punto = puntos[i];
            $('.sublista-puntos-destino').append('<li id="' + punto.id + '" lat="' + punto.latitud + '" long="' + punto.longitud + '">' + punto.nombre + ' - ' + punto.direccion + '</li>');
        }

        $('#punto_recogida_input').hide();
        $('.icon-punto.texto-recogida').css('color', '#fff');
        $('.lista-puntos-destino').show();

        $('.lista-puntos-destino > ul li').click(function (e) {
            var id = $(this).attr('id');
            var lat = $(this).attr('lat');
            var long = $(this).attr('long');
            var padre = $(this).parent().parent().first();
            $('li:first', padre).text($(this).text());
            estado = true;

            $('#lightbox').load('/punto/' + id + '?fin=' + estado, function () {
                $('#l-envio-popup').modal().on('shown.bs.modal', function () {
                    disableScrollMobile();
                    var marker = findMarker(lat, long);
                    tipoPunto(marker, markers, id); // Listeners de click
                    // Listener para el boton de cerrar
                    $('.cerrar-popup').on('click', function () {
                        $('.lista-puntos-destino li:first').text('Seleccione un punto de destino');
                        $('#punto_recogida_input').val('');
                        $('#punto_recogida_guest').val('');
                        $('#punto_recogida').val('');
                    });
                });

                $('#l-envio-popup').modal().on('hidden.bs.modal', function () {
                    enableScrollMobile();
                });

            });

            var nombrePunto = $(this).text().split('-')[0].trim();
            $('#punto_recogida_input').val(nombrePunto);
            $('#punto_recogida_guest').val(nombrePunto);
            $('#punto_recogida').val($(this).attr('id'));
        });
    }
}

function findMarker(latitud, longitud) {
    for(var i = 0 ; i<markers.length ; i++) {
        var marker = markers[i];
        if(marker.position.lat().toFixed(6) == parseFloat(latitud).toFixed(6) && marker.position.lng().toFixed(6) == parseFloat(longitud).toFixed(6)) {
            return marker;
        }
    }
}

// Limpia todos los markers del mapa
function limpiarMarkers() {
    for (var i = 0 ; i<markersCopy.length ; i++) {
        markersCopy[i].setMap(null);
    }
    markersCopy = [];
}

function hideAllMarkers() {
    visibleMarkers = [];
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(null);
        markers[i].setVisible(false);
    }
    // cluster.repaint();
}

function mostrarMarkersByLocation(location) {
    visibleMarkers = [];
    for (var i = 0; i < markers.length; i++) {
        if(markers[i].category != location) {
            markers[i].setMap(null);
            markers[i].setVisible(false);
        } else {
            visibleMarkers.push(markers[i]);
            markers[i].setMap(mapa);
            markers[i].setVisible(true);
        }
    }
    if(cluster) {
        cluster.clearMarkers();
        if (visibleMarkers.length > 1) {
            cluster.addMarkers(visibleMarkers);
        }
    }

}

function mostrarMarkers() {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(mapa);
    }
}

// Cambiar de localidad en el mapa
var cambiarLocalidad = function(localidad) {
    google.maps.event.addListener(mapa, 'center_changed', function() {
        google.maps.event.clearListeners(mapa, 'center_changed');
        mostrarMarkersByLocation(localidad.id);
    });
    var nuevoPunto = new google.maps.LatLng(parseFloat(localidad.latitud), parseFloat(localidad.longitud));
    mapa.panTo(nuevoPunto);
    mapa.setCenter(nuevoPunto);
    mapa.setZoom(12);

}

var cambiarLocalidadById = function(localidad) {
    // $('#buscador_recogida_latitud').val('');
    estado = true;
    google.maps.event.addListener(mapa, 'center_changed', function() {
        google.maps.event.clearListeners(mapa, 'center_changed');
        mostrarMarkersByLocation(localidad);
    });
    var nuevoPunto = new google.maps.LatLng(parseFloat($('#buscador_recogida_latitud').val()), parseFloat($('#buscador_recogida_longitud').val()));
    mapa.panTo(nuevoPunto);
    mapa.setCenter(nuevoPunto);
    mapa.setZoom(12);
}

// Creación de markers por lista de puntos
var crearMarkers = function(puntos) {
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
            category: puntos[punto].localidad_id,
            animation: google.maps.Animation.DROP
        });
        markersIcon[puntos[punto].nombre] = marker;
        markers.push(marker);
        // Creamos listener de click en marker
        crearListener(marker, puntos[punto].id, puntos[punto].nombre, markers);
    }
    cargarMarkers(puntos);
}

function cargarMarkers(puntos) {
    for(var i = 0 ; i<markers.length ; i++) {
        markers[i].setMap(mapa);
        markers[i].setVisible(false);
    }
    if (($('#punto_entrega').val() == '' && $('#localidadEntrega').val()!='') && ($('#punto_recogida').val() == '' && $('#localidadRecogida').val()!='')){
        var localidad_entrega_ID = $('#localidadEntrega').val();
        var longitud_entrega = $('#buscador_inicio_longitud').val();
        var latitud_entrega  = $('#buscador_inicio_latitud').val();
        estado = false;
        var localidad_entrega = {'id': localidad_entrega_ID, 'longitud': longitud_entrega, 'latitud': latitud_entrega};
        cambiarLocalidad(localidad_entrega);

        if(mobile) {
            cargarComboOrigen(puntos.filter(function (punto) {
                return punto.localidad_id == $('#localidadEntrega').val();
            }));
            cargarComboDestino(puntos.filter(function (punto) {
                return punto.localidad_id == $('#localidadRecogida').val();
            }));
        }

    }

    // Inicializamos si vienen las ciudades precargadas para móvil

    else if ($('#punto_entrega').val().length > 0 && $('#punto_recogida').val().length > 0) {
        $.ajax({
            dataType: "json",
            url: '/api/tstore/v1/punto',
            success: function (data) {
                // Creamos markers en puntos de localidad
                for (i = 0; i < data.puntos.data.length; i++) {
                    punto = data.puntos.data[i];
                    if (punto.id == $('#punto_entrega').val()) {
                        $('#buscador_inicio_latitud').val(punto.latitud);
                        $('#buscador_inicio_longitud').val(punto.longitud);
                    }
                    if (punto.id == $('#punto_recogida').val()) {
                        $('#buscador_fin_latitud').val(punto.latitud);
                        $('#buscador_fin_longitud').val(punto.longitud);
                    }
                }
            }

        });

        markerOrigen = findMarker($('#buscador_inicio_latitud').val(), $('#buscador_inicio_longitud').val());
        markerDestino = findMarker($('#buscador_fin_latitud').val(), $('#buscador_fin_longitud').val());

        if(!markerOrigen || !markerDestino) {
            markerOrigen = findMarker($('#puntoEntregaLat').val(), $('#puntoEntregaLon').val());
            markerDestino = findMarker($('#puntoRecogidaLat').val(), $('#puntoRecogidaLon').val());
        }

        calcularRuta();

    }
    else if ($('#punto_recogida').val() == '' && $('#localidadRecogida').val()!='') {

        var localidad_recogida_ID = $('#localidadRecogida').val();
        var longitud_recogida = $('#buscador_recogida_longitud').val();
        var latitud_recogida = $('#buscador_recogida_latitud').val();
        estado = true;
        var localidad_recogida = {'id': localidad_recogida_ID, 'longitud': longitud_recogida, 'latitud': latitud_recogida};
        cambiarLocalidad(localidad_recogida);

        if(mobile) {
            cargarComboDestino(puntos.filter(function (punto) {
                return punto.localidad_id == $('#localidadRecogida').val();
            }));
        }

    }
    else if ($('#punto_entrega').val() == '' && $('#localidadEntrega').val()!='') {
        var localidad_entrega_ID = $('#localidadEntrega').val();
        var longitud_entrega = $('#buscador_inicio_longitud').val();
        var latitud_entrega = $('#buscador_inicio_latitud').val();
        estado = false;
        var localidad_entrega = {'id': localidad_entrega_ID, 'longitud': longitud_entrega, 'latitud': latitud_entrega};
        cambiarLocalidad(localidad_entrega);

        if(mobile) {
            cargarComboOrigen(puntos.filter(function (punto) {
                return punto.localidad_id == $('#localidadEntrega').val();
            }));
        }

    }

    else {

        var mcOptions = {gridSize: 30, maxZoom: 14, imagePath: '/img/maps/clustered/m', ignoreHidden: true};
        cluster = new MarkerClusterer(mapa, visibleMarkers, mcOptions);

        if (mobile) {

            if ($('#localidadRecogida').val()) {
                $('#punto_recogida_input').hide();
                $('.icon-punto.texto-recogida').css('color', '#fff');
                $('.lista-puntos-destino').show();
                $.ajax({
                    dataType: "json",
                    url: '/api/tstore/v1/localidad/' + $('#localidadRecogida').val(),
                    success: function (data) {
                        // Movemos la posicion del mapa a una localidad concreta
                        cambiarLocalidad(data.localidad);
                        estado = true;
                        cargarComboDestino(data.localidad.puntos);
                    }
                });
            }

            if ($('#localidadEntrega').val()) {
                $('#punto_entrega_input').hide();
                $('.icon-punto.texto-envio').css('color', '#fff');
                $('.lista-puntos-origen').show();
                $.ajax({
                    dataType: "json",
                    url: '/api/tstore/v1/localidad/' + $('#localidadEntrega').val(),
                    success: function (data) {
                        // Movemos la posicion del mapa a una localidad concreta
                        cambiarLocalidad(data.localidad);
                        estado = false;
                        cargarComboOrigen(data.localidad.puntos);
                    }
                });
            }

        }
    }

}

// Creación de listener de infowindow de marker
var crearListener = function(marker, idPunto, markers) {
    var tipopuntoMapa = "";
    google.maps.event.addListener(marker, 'click', function() {
        toggleBounce(marker);
        setTimeout(function(){ marker.setAnimation(null)}, 1500);
        marker.setIcon(markerActivo);
        marker.setZIndex(9999);

        // Cargamos modal por ajax
        $('#lightbox').load('/punto/' + idPunto + '?fin='+estado, function() {
            $('#l-envio-popup').modal().on('shown.bs.modal', function(e){
                console.log(estado);
                tipoPunto(marker,markers,idPunto); // Listeners de click
            });
        });
    });
}

var tipoPunto = function (marker, markers, idPunto) {
    $('.agregar-origen-destino ul li button').click(function () {
        var btntipoPunto = $(this).attr('data-punto');
        var nombrePunto = $(this).val();
        var idtipoPunto = $(this).attr('data-id');

        if (btntipoPunto == 'envio') {
            //estado = true;
            for (var icon in markersIcon) {
                if (markersIcon[icon].icon == markerEnvio) {
                    markersIcon[icon].setIcon(imagenMarker);
                }
            }

            $('#punto_entrega_input').val(nombrePunto);
            $('#punto_entrega_guest').val(nombrePunto);
            $('#punto_entrega').val(idtipoPunto);
            $('#puntoEntregaLat').val(marker.position.lat());
            $('#puntoEntregaLon').val(marker.position.lng());
            marker.setIcon(markerEnvio);
            marker.set('tipo', 'envio');

            markerOrigen = marker;

            if(markerDestino) {
                calcularRuta();
            } else {
                if($('#localidadRecogida').val() != '' && $('#buscador_recogida_latitud').val()) {
                    cambiarLocalidadById($('#localidadRecogida').val());
                    $('#localidadRecogida').val('');
                    $('.mensaje-mapa').html('Selecciona el punto de <strong>destino</strong>');
                }
            }

        } else {

            for (var icon in markersIcon) {
                if (markersIcon[icon].icon == markerRecogida) {
                    markersIcon[icon].setIcon(imagenMarker);
                }
            }

            $('#punto_recogida_input').val(nombrePunto);
            $('#punto_recogida_guest').val(nombrePunto);
            $('#punto_recogida').val(idtipoPunto);
            $('#puntoRecogidaLat').val(marker.position.lat());
            $('#puntoRecogidaLon').val(marker.position.lng());
            marker.setIcon(markerRecogida);
            marker.set('tipo','recogida');

            markerDestino = marker;

            /** CALCULO DE RUTA FINAL **/
            if(markerOrigen) {
                calcularRuta();
            }
        }
    });
}

function calcularRuta() {
    hideAllMarkers();

    markerOrigen.setIcon(markerEnvio);
    markerOrigen.setMap(mapa);
    markerOrigen.setVisible(true);
    markerDestino.setIcon(markerRecogida);
    markerDestino.setMap(mapa);
    markerDestino.setVisible(true);

    visibleMarkers.push(markerOrigen);
    visibleMarkers.push(markerDestino);

    if(cluster) {
        cluster.clearMarkers();
        cluster.addMarkers(visibleMarkers);
    }

    directionsDisplay.setMap(mapa);

    var start = new google.maps.LatLng(markerOrigen.position.lat(), markerOrigen.position.lng());
    var end = new google.maps.LatLng(markerDestino.position.lat(), markerDestino.position.lng());

    var request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING
    };
    directionsService.route(request, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            markerOrigen.setMap(mapa);
            markerOrigen.setVisible(true);
            markerDestino.setMap(mapa);
            markerDestino.setVisible(true);
            directionsDisplay.setDirections(result);
            $('.mensaje-mapa').html('<strong>Resumen del envío</strong>');
        } else {
            alert("couldn't get directions:" + status);
        }
    });
}

function toggleBounce(marker) {
  if (marker.getAnimation() !== null) {
    marker.setAnimation(null);
  } else {
    marker.setAnimation(google.maps.Animation.BOUNCE);
  }
}


