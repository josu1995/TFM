'use strict';
// Mapa
let mapa;
let markers = [];

let directionsDisplay;
let directionsService;

$(document).ready(function() {

    const mobile = screen.width < 768;

    if(mobile) {
        let sL = $('.estados-info').width();
        $('.estados-info').animate({
            scrollLeft: 1000
        }, 2000);
    } else {
        customScroll('.estados-info');
    }
});


// Crear mapa inicial
function crearMapa() {

    if(!mapa) {

        const lineSymbol = {
            path: google.maps.SymbolPath.CIRCLE,
            fillOpacity: 1,
            scale: 3
        };

        const polylineDotted = {
            strokeColor: '#71b0df',
            strokeOpacity: 0,
            fillOpacity: 0,
            icons: [{
                icon: lineSymbol,
                offset: '0',
                repeat: '10px'
            }],
        };

        let opcionesMapa = {
            zoom: 5,
            disableDefaultUI: true,
            zoomControl: true,
            gestureHandling: 'cooperative'
        };
        if (locations.length === 1) {
            opcionesMapa.center = {lat: Number(locations[0].latitud), lng: Number(locations[0].longitud)};
        }

        mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);

        if (state === 9 || state === 11) {
            directionsDisplay = new google.maps.DirectionsRenderer({
                suppressMarkers: true
            });
        } else {
            directionsDisplay = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                polylineOptions: polylineDotted
            });
        }
        directionsService = new google.maps.DirectionsService();

        google.maps.event.addListener(directionsDisplay, 'directions_changed', function () {
            setTimeout(function() {
                setBounds();
            }, 200);

        });
    } else {
        $('#map').replaceWith(mapa.getDiv());
    }

    crearMarkers();
}

function setBounds() {
    var bounds = new google.maps.LatLngBounds();
    markers.forEach(function(marker) {
        bounds.extend(marker.getPosition());
    });
    mapa.fitBounds(bounds);
}

function deleteMarkers() {
    markers.forEach(function(marker) {
        marker.setMap(null);
    });
    markers = [];
}

// Creación de markers por lista de puntos
const crearMarkers = function() {
    deleteMarkers();
    locations.forEach(function(location) {
        // Creamos marker
        let marker = new google.maps.Marker({
            position: {
                lat: parseFloat(location.latitud),
                lng: parseFloat(location.longitud)
            },
            map: mapa,
            title: 'Transporter Store',
            icon: location.imagen,
            category: "Store",
            animation: google.maps.Animation.DROP
        });
        markers.push(marker);
    });

    if(markers.length === 2 || markers.length === 3) {
        directionsDisplay.setMap(mapa);

        const start = new google.maps.LatLng(markers[0].position.lat(), markers[0].position.lng());
        const end = new google.maps.LatLng(markers[1].position.lat(), markers[1].position.lng());

        let request = {
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        };

        if(markers.length === 3) {
            let intermedio = new google.maps.LatLng(markers[2].position.lat(), markers[2].position.lng());
            request.waypoints = [{location: intermedio}];
        }

        directionsService.route(request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);
            } else {
                alert("couldn't get directions:" + status);
            }
        });
    } else {
        directionsDisplay.setMap(null);
        mapa.setCenter(markers[0].getPosition());
        mapa.setZoom(12);
    }
};

const customScroll = function(elemento) {
    $(elemento).mCustomScrollbar({
        theme : 'minimal-dark',
        live: true,
        alwaysShowScrollbar: 0,
        scrollbarPosition: "inside",
        advanced:{
            autoUpdateTimeout: 1
        }
    });
    setTimeout(function() {
        $(elemento).mCustomScrollbar("scrollTo","bottom");
    }, 200);
};