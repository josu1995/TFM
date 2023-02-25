'use strict';


// Mapa
var mapa;
// Iconos de marker
var imagenMarker = '/img/maps/transporter-marker-grey.png';
var markerActivo = '/img/maps/transporter-marker-active.png';
var markerEnvio = '/img/maps/transporter-marker-origen.png';
var markerRecogida = '/img/maps/transporter-marker-destino.png';

var markerCorreosExpressActivo = '/img/maps/correos-marker-active.png';
var markerCorreosExpress = '/img/maps/correos-marker-inactive.png';
var markerInPostActivo = '/img/maps/inpost-marker-active.png';
var markerInPost = '/img/maps/inpost-marker-inactive.png';
var markerUPSActivo = '/img/maps/ups-marker-active.png';
var markerUPS = '/img/maps/ups-marker-inactive.png';

var markers = [];
var myMarker;
var visibleMarkers = [];
var autocompleteInput;

var cercanaLat, cercanaLon, cercanaNombre;
var selectedLocalidad;
var visiblePuntos;
var mobile;
var scrolling = false;
var storesList;
var error = '<div class="alert alert-danger mg-15">' +
                '<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>' +
                '<p>No existen localidades para el texto introducido. Por favor, introduce una ciudad o código postal válido.</p>' +
                '</div>';
var geocoder;

var scrollStartPos = 0;
var scrollStartPosY = 0;

var predicciones = [];
var prediccion = false;
var mirarPrecio = true;
var mirarPrediccion = true;

$(function() {
    mobile = screen.width < 768;

    if(mobile) {
        $('body').off('touchstart');
        $('body').on('touchstart ', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    } else {
        $('body').off('mousedown');
        $('body').on('mousedown', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    }
    // crearMapa();

});

function initTouchHandlerApi() {

    storesList = $('.stores-list');

    storesList.on('touchstart', function(e) {
        scrollStartPos = e.originalEvent.touches[0].clientX;
        scrollStartPosY = e.originalEvent.touches[0].clientY;
    });

    storesList.on('touchmove', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if(e.originalEvent.touches[0].clientX < scrollStartPos) {
            $(this)[0].scrollLeft = scrollStartPos - e.originalEvent.touches[0].clientX + storesList.scrollLeft();
        } else {
            $(this)[0].scrollLeft = scrollStartPos - e.originalEvent.touches[0].clientX + storesList.scrollLeft();
        }
        $('.modal-body')[0].scrollTop = scrollStartPosY - e.originalEvent.touches[0].clientY + $('.modal-body').scrollTop();
        scrollStartPos = e.originalEvent.touches[0].clientX;
        scrollStartPosY = e.originalEvent.touches[0].clientY;
    });

    storesList.on('touchend', function(e) {
        elementInViewportApi();
    });
}

function initListenersApi() {
    $('.linkCercana').on('click', function() {
        buscarLocalidadApi(cercanaNombre, cercanaLat, cercanaLon);
    });

    $('.btn-location').click(function() {
        getLocationApi();
    });

    $('.btn-buscar').click(function() {
        searchUserLocationApi();
    });

    $('#autocomplete').keyup(function(e) {
        var code = e.which;
        if(code===13)e.preventDefault();
        if(code===13||code===188||code===186){
            searchUserLocationApi();
        }
    });
}

function updateDistancesApi(location) {
    var parent = $('.punto-list-item').parent();

    var temparr = [];

    $('.punto-list-item').each(function(index) {
        $(this).attr('data-distance', getDistanceApi(location.lat(), location.lng(), $(this).attr('data-lat'), $(this).attr('data-lng')));
        temparr.push({'item' : $(this)[0], 'punto': visiblePuntos[index]});
    });

    var items = parent.children();

    temparr.sort(function(a,b){
        var adist = a.item.getAttribute('data-distance'),
            bdist = b.item.getAttribute('data-distance');

        if(adist > bdist) {
            return 1;
        }
        if(adist < bdist) {
            return -1;
        }
        return 0;
    });

    var tempMarkers = [];

    temparr.forEach(function(val, index) {
        items[index] = val.item;
        visiblePuntos[index] = val.punto;
        tempMarkers.push(findMarkerApi(val.punto));
    });

    markers = tempMarkers;

    items.detach().appendTo(parent);

}

function getDistanceApi(lat1,lon1,lat2,lon2) {
    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2-lat1);  // deg2rad below
    var dLon = deg2rad(lon2-lon1);
    var a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon/2) * Math.sin(dLon/2)
    ;
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c; // Distance in km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI/180)
}

function searchUserLocationApi() {

    let regexp = /android|iphone|kindle|ipad/i;
    let isMobileDevice = regexp.test(details);
    if (isMobileDevice) {
        var input = $('.mobileInput').val();
    }else{
        var input = $('.pcInput').val();
    }
    geocoder.geocode({
        address: input + ', ' + $('.ciudad-input').val().split('-')[0].trim() + ', ' + $('.ciudad-input').val().split('-')[1].trim() + ', ES',
        componentRestrictions: {
            country: 'ES'
        }
    }, function(results, status) {

        if (status == google.maps.GeocoderStatus.OK && results[0].types[0] !== 'postal_code' && results[0].types[0] !== 'locality' && results[0].types[0] !== 'establishment') {
            if(myMarker && (myMarker.position.lat() !== results[0].geometry.location.lat() || myMarker.position.lng() !== results[0].geometry.location.lng()) ) {
                myMarker.setMap(null);
            }
            if(!myMarker || (myMarker && (myMarker.position.lat() !== results[0].geometry.location.lat() || myMarker.position.lng() !== results[0].geometry.location.lng()))) {
                myMarker = new google.maps.Marker({
                    map: mapa,
                    position: results[0].geometry.location,
                    icon: '/img/maps/transporter-business-marker.png'
                });
                mapa.panTo(myMarker.position);
                mapa.setZoom(13);

                updateDistancesApi(results[0].geometry.location);
            }
        } else {
            if(myMarker) {
                myMarker.setMap(null);
                myMarker = null;
            }

          
            if (isMobileDevice) {

                $('.mobileInput').popover('show');
                $('.error-popover .popover-content strong').text($('.ciudad-input').val());
                $('#modal-stores-search').one('click keyup', function() {
                    if($('.error-popover').length) {
                        $('.mobileInput').popover('hide');
                    }
                });
            }else{
                $('.pcInput').popover('show');
                $('.error-popover .popover-content strong').text($('.ciudad-input').val());
                $('#modal-stores-search').one('click keyup', function() {
                    if($('.error-popover').length) {
                        $('.pcInput').popover('hide');
                    }
                });

            }

        }
    });
}

function searchUserLocationApiByLatLngApi(latLng) {
    geocoder.geocode({
        location: latLng
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK && results[0].types[0] !== 'postal_code') {
            if(myMarker && (myMarker.position.lat() !== results[0].geometry.location.lat() || myMarker.position.lng() !== results[0].geometry.location.lng()) ) {
                myMarker.setMap(null);
            }
            if(!myMarker || (myMarker && (myMarker.position.lat() !== results[0].geometry.location.lat() || myMarker.position.lng() !== results[0].geometry.location.lng()))) {
                myMarker = new google.maps.Marker({
                    map: mapa,
                    position: results[0].geometry.location,
                    icon: '/img/maps/transporter-business-marker.png'
                });
                mapa.panTo(myMarker.position);
                mapa.setZoom(13);

                updateDistancesApi(results[0].geometry.location);
            }
        } else {
            if(myMarker) {
                myMarker.setMap(null);
                myMarker = null;
            }
            let regexp = /android|iphone|kindle|ipad/i;
            let isMobileDevice = regexp.test(details);
            if (isMobileDevice) {

                $('.mobileInput').popover('show');
                $('.error-popover .popover-content strong').text($('.ciudad-input').val());
                $('#modal-stores-search').one('click keyup', function() {
                    if($('.error-popover').length) {
                        $('.mobileInput').popover('hide');
                    }
                });
            }else{
                $('.pcInput').popover('show');
                $('.error-popover .popover-content strong').text($('.ciudad-input').val());
                $('#modal-stores-search').one('click keyup', function() {
                    if($('.error-popover').length) {
                        $('.pcInput').popover('hide');
                    }
                });

            }
        }
    });
}

function elementInViewportApi() {
    var scrolledLeft = storesList.scrollLeft();
    var viewPortWidth = storesList.width();
    var children = storesList.children();
    var elemWidth = children.first().width();

    var valToChange = 0;
    var elemToChange = null;

    for(var i = 0 ; i < children.length ; i++) {

        var visiblePixels = elemWidth - (scrolledLeft - (i * elemWidth));
        var percentVisible = visiblePixels * 100 / elemWidth;

        if(percentVisible > 100) {
            var hiddenPixels = elemWidth * (i + 1) - (scrolledLeft + viewPortWidth);
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

    hoverStoreApi(elemToChange);
}

function getLocationApi() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latLng = {lat: position.coords.latitude, lng: position.coords.longitude};
            searchUserLocationApiByLatLngApi(latLng);
        });
    } else {
        alert('Este navegador no soporta la geolocalización.');
    }
}

function mapsCallback() {
    crearMapaApi();
}

// Crear mapa inicial
function crearMapaApi() {
   
    $('.close-mobile-btn').click(function() {
        $('#modal-stores-search').modal('hide');
    });

    var city = $('.ciudad-input').val();

    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': city
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

            processGeocoderResultApi(results, city);

        } else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
            geocoder.geocode({
                'address': city.split('-')[1].trim()
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    processGeocoderResultApi(results, city);
                }
            });
        }
    });

}

function processGeocoderResultApi(results, city) {
    myMarker = null;

    initListenersApi();

    var opcionesMapa = {
        center: { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() },
        zoom: 5,
        disableDefaultUI: true,
        zoomControl: true,
        gestureHandling: 'cooperative'
    };

    mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);

    buscarLocalidadApi(city, '', '');
}

var crearMapaLocalidadApi = function(localidad) {

    var center = new google.maps.LatLng(parseFloat(localidad.latitud), parseFloat(localidad.longitud));

    mapa.panTo(center);
    mapa.setZoom(13);

};

// Creación de markers por lista de puntos para elegir punto de inicio
var crearMarkersFinApi = function(puntos) {
    removeMarkers();
    markers = [];
    for (var punto in puntos) {
        
        
        if(puntos[punto].metodoEnvio == 'Correos Express'){
            var imagen = markerCorreosExpress;
        }else if(puntos[punto].metodoEnvio == 'InPost'){
            var imagen = markerInPost;
        }else if(puntos[punto].metodoEnvio == 'DHL'){
            var imagen = markerUPS;
        }else{
            var imagen = imagenMarker;
        }
        
        // Creamos marker
        var marker = new google.maps.Marker({
            position: {
                lat: parseFloat(puntos[punto].latitud),
                lng: parseFloat(puntos[punto].longitud)
            },
            map: mapa,
            title: puntos[punto].nombre,
            icon: imagen,
            animation: google.maps.Animation.DROP
        });

        markers.push(marker);

        crearMarkerListenerApi(marker);
    }
};

function crearMarkerListenerApi(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        var elem = $('.punto-list-item').eq(markers.indexOf(marker));
        storesList.mCustomScrollbar("scrollTo", elem, {});
        hoverStoreApi(elem);
    });
}

function removeMarkers() {
    for(var i = 0 ; i < markers.length ; i++) {
        markers[i].setMap(null);
    }
}

function showResultadoBusquedaApi(puntos, paquetes, ahorro) {

    storesList = $('.stores-list');

    visiblePuntos = puntos;

    $('.horario-punto').remove();

    storesList.empty();
    $('.sin-paquetes').hide();
    storesList.show();

    for(var i = 0 ; i<puntos.length ; i++) {    
        
        var punto = puntos[i];

        var imagePath = punto.imagen ? punto.imagen.path : '/img/home/store-no-img.png';

        var horario = '';
        console.log(punto);
        if(!punto.hasOwnProperty('hoy') || punto.hoy[0].length == 0){
            horario = '<strong class="texto-recogida"> &nbsp' + 'Cerrado' + '</strong>';
        }else if((punto.hoy.length == 2 && punto.hoy[0].cerrado && punto.hoy[1].cerrado) || (punto.hoy.length == 1 && punto.hoy[0].cerrado)) {
            horario = '<strong class="texto-recogida"> &nbsp' + 'Cerrado' + '</strong>';
        } else {
            if(punto.hoy[0].inicio == ':' && punto.hoy[1].inicio == ':'){
                horario += '<strong class="texto-recogida"> &nbsp Cerrado';
            }else{
                if(punto.hoy[0] && !punto.hoy[0].cerrado) {
                    if(punto.hoy[0].inicio == ':'){
                        horario += '<strong class="texto-recogida"> &nbsp Cerrado';
                    }else{
                        horario += '<strong> &nbsp' + punto.hoy[0].inicio + '-' + punto.hoy[0].fin;
                    }
                    
                }
                if(punto.hoy[1] && !punto.hoy[1].cerrado) {
                    if(punto.hoy[1].inicio == ':'){
                        horario += '';
                    }else{
                        horario += ' ' + punto.hoy[1].inicio + '-' + punto.hoy[1].fin;
                    }
                    
                }
            }

            horario += '</strong>';
        }
        var contrareembolso;
        var existsContrareembolso = true;
        for(var pr = 0;pr<prediccionesRecogida.length;pr++){
            if(prediccionesRecogida[pr][2] == '0' && punto.metodoEnvio == prediccionesRecogida[pr][0]){
                existsContrareembolso = false;
            }
        }

        var color = 'black';
        for(var pr = 0;pr<prediccionesRecogida.length;pr++){
            if(punto.metodoEnvio == prediccionesRecogida[pr][0]){
                color = prediccionesRecogida[pr][3];
            }
        }


        if(!existsContrareembolso){
            contrareembolso = '';
        }else{
            contrareembolso = '<i id="contrareembolsoMapa" data-tooltip="contrareembolsoMapa" class="fas fa-money-bill-alt" style="position: absolute;top: 2%;left: 12%;color:'+color+';"></i>';
        }

        if(punto.metodoEnvio == 'DHL'){
            if(punto.reembolso == 1){
                contrareembolso = '<i id="contrareembolsoMapa" data-tooltip="contrareembolsoMapa" class="fas fa-money-bill-alt" style="position: absolute;top: 2%;left: 12%;color:'+color+';"></i>';
            }else{
                contrareembolso = '';
            }
        }
       



        var mirarPred = true;
        var mirarPrec = true;
        var pre = null;
        var prec = null;
        var contMetodos = 0;

        //Prediccion
        for(var pr = 0;pr<prediccionesRecogida.length;pr++){
            if(punto.metodoEnvio == prediccionesRecogida[pr][0]){
               
                if(pre == null){
                    
                    pre = prediccionesRecogida[pr][1];
                    
                }else{
                    contMetodos++;
                    if(pre != prediccionesRecogida[pr][1]){
                        mirarPred = false;
                        break;
                    }
                }

            }
        }
        //Precio
        for(var pr = 0;pr<prediccionesRecogida.length;pr++){
            if(punto.metodoEnvio == prediccionesRecogida[pr][0]){
               
                if(prec == null){
                    
                    prec = prediccionesRecogida[pr][5];
                    
                }else{
                    
                    if(prec != prediccionesRecogida[pr][5]){
                        mirarPrec = false;
                        break;
                    }
                }

            }
        }

        
        let regexp = /android|iphone|kindle|ipad/i;
        let isMobileDevice = regexp.test(details);

        if (isMobileDevice && contMetodos > 0) {
            var boton = '<i class="material-icons seleccionar-store-btn" id="iconoMobil" style="display:none;color:'+color+';margin-right: 4px;position:absolute;z-index:1000;margin-top: 7%;margin-left: 2%;">local_shipping</i><select style="padding-top: 5px;color:'+color+';border-color:'+color+';padding-left: 25px;height: 42px;width: 85%;" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" id="seleccionar-store-btn">';
            boton = boton.concat('<option  value="0">Ver opciones de entrega</option>');
        }else{
            var boton = ' ';
        }

        var precio = '';
        if(prediccion){
            for(var pr = 0;pr<prediccionesRecogida.length;pr++){
                if(punto.metodoEnvio == prediccionesRecogida[pr][0]){
                    for(var p = 0;p<preciosRecogida.length;p++){
                        if(preciosRecogida[p][1] == prediccionesRecogida[pr][4]){
                            precio = preciosRecogida[p][0];
                            break;
                        }
                    }
                    if (!isMobileDevice) {
                        if(mirarPrediccion && mirarPrecio){
                            
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">add_circle_outline</i>Seleccionar punto de recogida</button>' );
                            break;
                        }else if(mirarPrecio){
                            
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+'</button>' );
                        }else if(mirarPrediccion){
                            
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="font-weight: bold;color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+precio+'</button>' );
                        }else{
                            if(contMetodos > 0){

                                if(mirarPrec && mirarPred){
                                    
                                    boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">add_circle_outline</i>Seleccionar punto de recogida</button>' );
                                    break;
                                }else if(mirarPrec){
                                    boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+'</button>' );
                                }else if(mirarPred){
                                    boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="font-weight: bold;color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+precio+'</button>' );
                                }else{
                                    boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+': <span style="font-weight: bold;">'+precio+'</span></button>' );
                                }

                            }else{
                                boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+': <span style="font-weight: bold;">'+precio+'</span></button>' );
                            }
                        
                        }
                    }else{
                        
                        //Parte movil
                        if(mirarPrediccion && mirarPrecio){
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">add_circle_outline</i>Seleccionar punto de recogida</button>' );
                            break;
                        }else if(mirarPrecio){
                            
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+'</button>' );
                        }else if(mirarPrediccion){
                            
                            boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="font-weight: bold;color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+precio+'</button>' );
                        }else{
                            if(contMetodos > 0){

                                if(mirarPrec && mirarPred){
                                    boton = '';
                                    boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">add_circle_outline</i>Seleccionar punto de recogida</button>' );
                                    break;
                                }else if(mirarPrec){
                                    boton = boton.concat(' <option disabled value="'+prediccionesRecogida[pr][1]+'" >'+prediccionesRecogida[pr][1]+'</option>');
                                }else if(mirarPred){
                                    boton = boton.concat(' <option  disabled value="'+precio+'">'+precio+'</option>');
                                }else{
                                    boton = boton.concat(' <option disabled value="'+prediccionesRecogida[pr][1]+'">'+prediccionesRecogida[pr][1]+': '+precio+'</option>');
                                }

                            }else{
                                boton = boton.concat('<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">local_shipping</i>'+prediccionesRecogida[pr][1]+': <span style="font-weight: bold;">'+precio+'</span></button>' );
                            }
                        
                        }


                    }
                   
                }
            }

        }else{
           
            boton =  '<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="font-weight: bold;color:'+color+';border-color:'+color+';"><i class="material-icons">add_circle_outline</i>Seleccionar punto de recogida</button>'  ;
     
        }
        if(isMobileDevice && contMetodos > 0 && (!mirarPrec || !mirarPred)){
            boton = boton.concat('</select>');
        }
        
       
        var listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item" data-lat="' + punto.latitud + '" data-lng="' + punto.longitud + '">' +
                '<div class="col-md-4 col-xs-12">'+
                '<img id="imagenMapaCheck" style="width: 100%;" src="' + imagePath + '" width="200" onerror="this.onerror=null;this.src=\'/img/home/store-no-img.png\';">' +
                    contrareembolso+
                '</div>'+
                '<div class="col-md-8 col-xs-11 no-pd punto-datos">' +
                '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                '<input type="hidden" class="punto-tipo" value="' + punto.tipo + '">' +
                '<label class="direccion-label nombre"><i class="icon-punto texto-envio" style="color:'+color+'"> </i><strong> ' + punto.nombre + '</strong></label> <small>(' + punto.id + ')</small><br>' +
                '<i style="color: #3d3d3d" class="icon-ubicacion icono-naranja"> </i><label class="direccion-label direccion">' + punto.direccion + '</label> <br>' +
                '<i style="color: #3d3d3d;" class="fas fa-map icono-naranja"> </i> <label class="direccion-label direccion">&nbsp&nbsp' + punto.codigo_postal.codigo_postal + ', ' + punto.codigo_postal.ciudad + ', ' + punto.codigo_postal.codigo_pais + '</label><br>' +
                '<i class="far fa-clock"> </i><small class="text-secondary" style="color:#333;"> &nbspHOY</small><small class="horario-hoy">' + horario + '</small>&nbsp&nbsp&nbsp' +
                '<a id="' + punto.id + '" class="ver-mas-link small text-secondary" data-toggle="popover" data-placement="left" style="color:'+color+'">Ver más</a><br>' +
                boton +
                '</div>' +
                '</div>');

        var popover = $('<div class="horario-punto horario-table-' + punto.id + ' hidden">' +
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

        for(var j = 0 ; j < punto.horarios.length ; j++) {
            var element = null;

            if(punto.horarios[j].cerrado) {
                if(j % 2 === 0) {
                    element = '<td class="texto-recogida text-nowrap">Cerrado</td>';
                } else {
                    element = '<td></td>';
                }
            } else if(punto.horarios[j].inicio && punto.horarios[j].fin) {
               
                
                if(punto.horarios[j].inicio == ':'){
                    element = '<td class="texto-recogida text-nowrap">Cerrado</td>';
                }else{
                    
                    element = '<td class="text-nowrap">' + punto.horarios[j].inicio.substr(0, 5) + '-' + punto.horarios[j].fin.substr(0, 5) + '</td>';
                }
               
            }
            popover.find('tr.' + punto.horarios[j].dia).append(element);
        }

        storesList.append(listItem);

        $('body').append(popover);

        buildTooltipApi('contrareembolsoMapa', 'Pago contra-reembolso disponible');
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
            hoverStoreApi($(this));
        });
    } else {
        $('.punto-list-item').on('click', function () {
            hoverStoreApi($(this));
        });
    }

    // Inicializamos popover
    $("[data-toggle=popover]").each(function() {
        $(this).popover('destroy');
        $(this).popover({
            trigger: 'manual',
            placement: mobile ? 'top' : 'left',
            html: true,
            content: function() {
                var id = $(this).attr('id');
                return $('.horario-table-' + id).html();
            },
            container: 'body'
        });
    });

    $('.seleccionar-store-btn').click(function() {
        //seleccionarStore($(this).parent().children('.punto-id').val(), $(this).parent().children('.punto-tipo').val());
    });

    // Seleccionamos el primer elemento
    hoverStoreApi($('.punto-list-item').first());
}

function hoverStoreApi(elem) {

    var index = $('.punto-list-item').index(elem);
    var punto = visiblePuntos[index];
    var marker = findMarkerApi(punto);
    var color = 'black';
        for(var pr = 0;pr<prediccionesRecogida.length;pr++){
            if(punto.metodoEnvio == prediccionesRecogida[pr][0]){
                color = prediccionesRecogida[pr][3];
            }
        }
   
        
    if(!mobile) {
        
        clearStoresSelectionApi(punto.metodoEnvio,marker);
        elem.addClass('store-change');
        
        elem.find('button').css('display','block');

        elem.find('button').mouseenter(function () {

            $(this).css('color','white');
            $(this).css('background-color',color);
            $(this).css('border-color',color);
          });

        elem.find('button').mouseleave(function () {

            $(this).css('color',color);
            $(this).css('background-color','white');
            $(this).css('border-color',color);
        });
        
        elem.css('border-left','6px solid '+color+'');
    } else {
        
        if($('.store-change') !== elem) {
            
            
            clearStoresSelectionApi(punto.metodoEnvio,marker);
            elem.addClass('store-change');
           

            elem.find('button').focusin(function () {
                
                $(this).css('color','white');
                $(this).css('background-color',color);
                $(this).css('border-color',color);
                
              });
    
            elem.find('button').focusout(function () {
    
                $(this).css('color',color);
                $(this).css('background-color','white');
                $(this).css('border-color',color);
            });
           
            elem.find('select').focusin(function () {
                
                $(this).css('color','white');
                $(this).css('background-color',color);
                $(this).css('border-color',color);
                elem.find('select').prev().css('color','white');
                
              });
    
            elem.find('select').focusout(function () {
    
                $(this).css('color',color);
                $(this).css('background-color','white');
                $(this).css('border-color',color);
                elem.find('select').prev().css('color',color);
                
            });
            

            

            
            elem.find('button').css('display','block');
            elem.find('select').css('display','block');
            elem.find('select').prev().css('display','block');

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

    if(punto.metodoEnvio == 'InPost'){
        marker.setIcon(markerInPostActivo);
    }else if(punto.metodoEnvio == 'Correos Express'){
        marker.setIcon(markerCorreosExpressActivo);
    }else if(punto.metodoEnvio == 'DHL'){
        marker.setIcon(markerUPSActivo);
        
    }else{
        marker.setIcon(imagenMarker);
    }
   
    var center = new google.maps.LatLng(parseFloat(punto.latitud), parseFloat(punto.longitud));
    mapa.panTo(center);

}

function buscarLocalidadApi(nombre, lat, lon) {


    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': $('.ciudad-input').val()
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            processBusquedaLocalidadResultApi(results, nombre, lat, lon);
        } else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
            geocoder.geocode({
                'address': $('.ciudad-input').val().split('-')[1].trim()
            }, function(results, status) {
                processBusquedaLocalidadResultApi(results, nombre, lat, lon);
            });
        }
    });

}

function processBusquedaLocalidadResultApi(results, nombre, lat, lon) {
    lat = results[0].geometry.location.lat();
    lon = results[0].geometry.location.lng();

    var pais = null;

    results[0].address_components.forEach(function(val, i) {
        if(val.types.indexOf('country') !== -1) {
            pais = val.short_name;
        }
    });

    var cp = nombre.split('-')[0].trim();
    if(nombre.includes('-')) {
        nombre = nombre.split('-')[1].trim();
    }
    
    
    $.ajax({
        dataType: "json",
        url: '/api/tstore/v1/localidad/search?nombre=' + nombre + '&lat=' + lat + '&long=' + lon + '&cp=' + cp + '&pais=' + pais + '&t=2',
        success: function(data) {
            
            if(data.localidad.puntos) {
               //hacer N llamadas seguidas y juntarlos en front
           
                
               console.log(prediccionesRecogida);
                 
                for(var i = 0;i<prediccionesRecogida.length;i++){
                    prediccion = true;
                    if(predicciones.includes(prediccionesRecogida[i][1])){
                        
                    }else{
                        predicciones.push(prediccionesRecogida[i][1])
                    }
                
                }

                
                for(var i = 0;i<prediccionesRecogida.length;i++){
                    for(var p = 0;p<preciosRecogida.length;p++){
                        if(prediccionesRecogida[i][4] == preciosRecogida[p][1]){
                            prediccionesRecogida[i].push(preciosRecogida[p][0]);
                        }
                    }
                }
                console.log(prediccionesRecogida);
                //que las preddciones sean iguales

                if(prediccionesRecogida.length > 1){
                    var pred= prediccionesRecogida[0][1];
                    for(var i = 1;i<prediccionesRecogida.length;i++){
                        if(pred != prediccionesRecogida[i][1]){
                            mirarPrediccion = false;
                            break;
                        }
                    }
                }else{
                    mirarPrediccion = true;
                }

                //los precios sean iguales
                if(preciosRecogida.length > 1){
                    var precio = preciosRecogida[0][0];
                    for(var i = 1;i<preciosRecogida.length;i++){
                        if(precio != preciosRecogida[i][0]){
                            mirarPrecio = false;
                            break;
                        }
                    }

                }else{
                    mirarPrecio = true;
                }
                
                
               
                selectedLocalidad = data.localidad;
                // Creamos mapa en localidad recibida
                crearMapaLocalidadApi(data.localidad);
                // Creamos markers en puntos de localidad
                crearMarkersFinApi(data.localidad.puntos, true);

                showResultadoBusquedaApi(data.localidad.puntos);

                $('#modal-stores-search').modal();

                initTouchHandlerApi();

            } else {
                new PNotify({
                    title: 'Transporter',
                    text: 'No tenemos Stores en el código postal indicado. Prueba con otra ciudad o código postal.',
                    addclass: 'transporter-alert',
                    icon: 'icon-transporter',
                    autoDisplay: true,
                    hide: true,
                    delay: 5000,
                    closer: false,
                });
                $('#data').empty();
                $('input[name^="tipo_recogida"], input[name^="tipo_entrega"]').val('');
            }

        },
        error: function() {
            showErrorApi();
        }
    });
}

function showErrorApi() {
    if(!$('.alert.alert-danger').length) {
        $('.stores-search-container').prepend(error);
    }
}

function findMarkerApi(punto) {

    for(var i = 0 ; i < markers.length ; i++) {
        var marker = markers[i];
        if(parseFloat(parseFloat(marker.position.lat()).toFixed(6)) == parseFloat(parseFloat(punto.latitud).toFixed(6)) && parseFloat(parseFloat(marker.position.lng()).toFixed(6)) == parseFloat(parseFloat(punto.longitud).toFixed(6))) {
            return marker;
        }
    }
}

function findPuntoApi(marker) {
    for(var i = 0 ; i < visiblePuntos.length ; i++) {
        var punto = visiblePuntos[i];
        if(fixDecimalsApi(marker.position.lat()) === fixDecimalsApi(parseFloat(punto.latitud)) && fixDecimalsApi(marker.position.lng()) === fixDecimalsApi(parseFloat(punto.longitud))) {
            return punto;
        }
    }
}

function fixDecimalsApi(number) {
    var split = number.toString().split('.');
    return split[0] + '.' + split[1].substr(0, 6);
}

function clearStoresSelectionApi(metodoEnvio,markerOriginal) {
    
    $('.store-change').css('border-left','');
    $('.store-change').find('button').css('display','none');
    $('.store-change').find('select').css('display','none');
    $('.store-change').find('select').prev().css('display','none');
    
    $('.store-change').removeClass('store-change');
   
    
    
    for(var i = 0 ; i < markers.length ; i++) {
        var marker = markers[i];

            if(marker.icon == '/img/maps/inpost-marker-active.png'){
                marker.setIcon(markerInPost);
            }else if(marker.icon == '/img/maps/correos-marker-active.png'){
                marker.setIcon(markerCorreosExpress);
                
            }else if(marker.icon == '/img/maps/ups-marker-active.png'){

                marker.setIcon(markerUPS);
                
            }else{
                marker.setIcon(marker.icon);
            }
            
        
            
    }
}


var customScroll = function(elemento) {
    $(elemento).mCustomScrollbar({
        theme : 'minimal-dark',
        live: false,
        alwaysShowScrollbar: 0,
        scrollbarPosition: "inside",
        advanced:{
            autoUpdateTimeout: 1
        }
    });
};


function buildTooltipApi(ref, message) {
    $('[data-tooltip="' + ref + '"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: message,
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
}





//# sourceURL=stores-search.js