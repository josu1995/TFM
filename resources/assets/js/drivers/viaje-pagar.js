var inicio = true;
// Mapa
var mapa;
// Iconos de marker
var imagenMarker = '/img/maps/transporter-marker-grey.png';
var markerActivo = '/img/maps/transporter-marker-active.png';
var markerEnvio = '/img/maps/transporter-marker-origen.png';
var markerRecogida = '/img/maps/transporter-marker-destino.png';
var markers = [];
var visibleMarkers = [];
// Cluster de markers
var cluster;

var mobile = false;

var directionsDisplay;
var directionsService;

var destinos = [];

var lastY;

var showingMap = false;
var mapReady = false;

var mapIsReady = function() {
    mapReady = true;
}

var crearMapa = function() {

    var origenLat = $('.localidad-origen-lat').val();
    var origenLng = $('.localidad-origen-lng').val();

    var destinoLat = $('.localidad-destino-lat').val();
    var destinoLng = $('.localidad-destino-lng').val();

    var start = new google.maps.LatLng(origenLat, origenLng);
    var end = new google.maps.LatLng(destinoLat, destinoLng);

    // Mapa zoom España
    var opcionesMapa = {
        center: {
            lat: 40.4381311,
            lng: -3.8196195
        },
        zoom: 6,
        draggable: false, zoomControl: false, scrollwheel: false, disableDoubleClickZoom: true, disableDefaultUI: true
    };
    mapa = new google.maps.Map(document.getElementById('map-resumen'), opcionesMapa);

    directionsDisplay = new google.maps.DirectionsRenderer({ suppressMarkers : true });
    directionsService = new google.maps.DirectionsService();

    google.maps.event.addListener(directionsDisplay, 'directions_changed', function() {
        mapa.setZoom(mapa.getZoom()-1);
    });

    directionsDisplay.setMap(mapa);

    // Creamos markers
    var markerOrigen = new google.maps.Marker({
        position: {
            lat: parseFloat(origenLat),
            lng: parseFloat(origenLng)
        },
        // map: mapa,
        title: 'Origen',
        icon: markerEnvio,
        animation: google.maps.Animation.DROP
    });

    var markerDestino = new google.maps.Marker({
        position: {
            lat: parseFloat(destinoLat),
            lng: parseFloat(destinoLng)
        },
        // map: mapa,
        title: 'Destino',
        icon: markerRecogida,
        animation: google.maps.Animation.DROP
    });

    markerOrigen.setMap(mapa);
    markerDestino.setMap(mapa);

    request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING
    };

    directionsService.route(request, function(result, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(result);
        } else {
            alert("couldn't get directions:" + status);
        }
    });

};

//
// Ejecución
//
$(document).ready(function() {

    $('.redsysForm').submit(function (e) {
        e.preventDefault();
        var self = this;
        var result = true;
        $.get("/drivers/viajar/seleccionar/actualizar", function (data) {
            for (var i = 0; i < data.length; i++) {
                var elem = data[i];
                if (elem['envio'].estado_id != 7 && elem['envio'].estado_id != 5) {
                    result = false;
                }
            }

            if (result) {
                self.submit();
            } else {
                window.location.href = '/drivers/viajar';
            }
        });
        return false;
    });

    var metodoCobro = $('.metodo-cobro');
    // if(metodoCobro.length > 0){
    //     $('.metodo-cobro[data-value=3]').addClass('metodo-activo');
    // }
    metodoCobro.click(function(event) {
        var metodoSelected = $(this)
            .attr('data-value');
        // metodoCobro.each(function(index, el) {
        //     $(this)
        //         .removeClass('metodo-activo');
        // });
        $('.metodo_cobro_select').val(metodoSelected);
        // $(this)
        //     .toggleClass('metodo-activo');
    });

    $('.ibanNuevo').on('focus', function() {
        $('.ibanNuevoCheck').prop('checked', true);
    });
    $('.paypalNuevo').on('focus', function() {
        $('.paypalNuevoCheck').prop('checked', true);
    });

    $("#modalMapa").on("shown.bs.modal", function () {
        if(mapReady && !showingMap) {
            crearMapa();
            showingMap = true;
        }
        $('html, body').css('overflow', 'hidden');
    });

    $("#modalMapa").on("hidden.bs.modal", function () {
        $('html, body').css('overflow', 'auto');
    });

});