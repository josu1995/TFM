'use strict';
// Mapa
var mapa;
// Iconos de marker
var imagenMarker = '/img/maps/transporter-marker-grey.png';
var markerActivo = '/img/maps/transporter-marker-active.png';
var markerEnvio = '/img/maps/transporter-marker-origen.png';
var markerRecogida = '/img/maps/transporter-marker-destino.png';

var markerStoreActivo = '/img/maps/store-marker-active.png';
var markerUbicacionActivo = '/img/maps/store-marker-activec.png';
var markerStore = '/img/maps/store-marker-inactive.png';

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

var mirarPrecioClick = true;
var mirarPrediccionClick = true;

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

function initTouchHandler() {

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
        elementInViewport();
    });
}

function initListeners() {
    $('.linkCercana').on('click', function() {
        buscarLocalidad(cercanaNombre, cercanaLat, cercanaLon);
    });

    $('.btn-location').click(function() {
        getLocation();
    });

    $('.btn-buscar').click(function() {
        searchUserLocation();
    });

    $('#autocomplete').keyup(function(e) {
        var code = e.which;
        if(code===13)e.preventDefault();
        if(code===13||code===188||code===186){
            searchUserLocation();
        }
    });
}

function updateDistances(location) {
    var parent = $('.punto-list-item').parent();

    var temparr = [];

    $('.punto-list-item').each(function(index) {
        $(this).attr('data-distance', getDistance(location.lat(), location.lng(), $(this).attr('data-lat'), $(this).attr('data-lng')));
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
        tempMarkers.push(findMarker(val.punto));
    });

    markers = tempMarkers;

    items.detach().appendTo(parent);

}

function getDistance(lat1,lon1,lat2,lon2) {
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

function searchUserLocation() {
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
                mapa.setZoom(12);

                updateDistances(results[0].geometry.location);
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

function searchUserLocationByLatLng(latLng) {
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
                mapa.setZoom(12);

                updateDistances(results[0].geometry.location);
            }
        } else {
            if(myMarker) {
                myMarker.setMap(null);
                myMarker = null;
            }
            $('.search-input').popover('show');
            $('.error-popover .popover-content strong').text($('.ciudad-input').val());
            $('#modal-stores-search').one('click keyup', function() {
                if($('.error-popover').length) {
                    $('.search-input').popover('hide');
                }
            });
        }
    });
}

function elementInViewport() {
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

    hoverStore(elemToChange);
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latLng = {lat: position.coords.latitude, lng: position.coords.longitude};
            searchUserLocationByLatLng(latLng);
        });
    } else {
        alert('Este navegador no soporta la geolocalización.');
    }
}

function mapsCallback() {
    crearMapa();
}

// Crear mapa inicial
function crearMapa() {
    $('.close-mobile-btn').click(function() {
        $('#modal-stores-search').modal('hide');
    });

    var city = $('.ciudad-input').val();

    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': city
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

            processGeocoderResult(results, city);

        } else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
            geocoder.geocode({
                'address': city.split('-')[1].trim()
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    processGeocoderResult(results, city);
                }
            });
        }
    });

}

function processGeocoderResult(results, city) {
    myMarker = null;

    initListeners();

    var opcionesMapa = {
        center: { lat: results[0].geometry.location.lat(), lng: results[0].geometry.location.lng() },
        zoom: 5,
        disableDefaultUI: true,
        zoomControl: true,
        gestureHandling: 'cooperative'
    };

    mapa = new google.maps.Map(document.getElementById('map'), opcionesMapa);

    buscarLocalidad(city, '', '');
}

var crearMapaLocalidad = function(localidad) {

    var center = new google.maps.LatLng(parseFloat(localidad.latitud), parseFloat(localidad.longitud));

    mapa.panTo(center);
    mapa.setZoom(12);

};

// Creación de markers por lista de puntos para elegir punto de inicio
var crearMarkersFin = function(puntos) {
    removeMarkers();
    markers = [];
    for (var punto in puntos) {
        // Creamos marker
        var marker = new google.maps.Marker({
            position: {
                lat: parseFloat(puntos[punto].latitud),
                lng: parseFloat(puntos[punto].longitud)
            },
            map: mapa,
            title: puntos[punto].nombre,
            icon: markerStore,
            animation: google.maps.Animation.DROP
        });

        markers.push(marker);

        crearMarkerListener(marker);
    }
};

function crearMarkerListener(marker) {
    google.maps.event.addListener(marker, 'click', function() {
        var elem = $('.punto-list-item').eq(markers.indexOf(marker));
        storesList.mCustomScrollbar("scrollTo", elem, {});
        hoverStore(elem);
    });
}

function removeMarkers() {
    for(var i = 0 ; i < markers.length ; i++) {
        markers[i].setMap(null);
    }
}

function showResultadoBusqueda(puntos, paquetes, ahorro) {

    storesList = $('.stores-list');

    visiblePuntos = puntos;

    $('.horario-punto').remove();

    storesList.empty();
    $('.sin-paquetes').hide();
    storesList.show();

    for(var i = 0 ; i<puntos.length ; i++) {    
        
        var punto = puntos[i];
        if(punto.metodo == "store"){
            var imagePath = punto.imagen ? '/img/transportistas/'+punto.imagen.path : '/img/home/store-no-img.png';
        }else{
            var imagePath = punto.imagen ? punto.imagen.path : '/img/home/store-no-img.png';
        }
     
        
        
        var horario = '';
        if(!punto.hasOwnProperty('hoy') || punto.hoy.length == 0){
            horario = '<strong class="texto-recogida"> &nbsp' + 'Cerrado' + '</strong>';
        }else if((punto.hoy.length == 2 && punto.hoy[0].cerrado && punto.hoy[1].cerrado) || (punto.hoy.length == 1 && punto.hoy[0].cerrado)) {
            horario = '<strong class="texto-recogida"> &nbsp' + 'Cerrado' + '</strong>';
        } else {
            if(punto.hoy[0].inicio == ':' && punto.hoy[1].inicio == ':'){
                horario += '<strong class="texto-recogida"> &nbsp Cerrado';
            }if(punto.hoy[0].inicio == 'Festivo' && punto.hoy[1].inicio == 'Festivo'){
                horario += '<strong class="texto-recogida"> &nbsp Festivo';
            }else{
                if(punto.hoy[0] && !punto.hoy[0].cerrado) {
                    if(punto.hoy[0].inicio == ':'){
                       
                    }else{
                        if(punto.hoy[0].inicio == 'festivo'){
                            horario += '<strong class="texto-recogida"> &nbsp Festivo';
                        }else{
                            horario += '<strong> &nbsp' + punto.hoy[0].inicio.substring(0, 5) + '-' + punto.hoy[0].fin.substring(0, 5);
                        }
                       
                    }
                    
                }
                if(punto.hoy[1] && !punto.hoy[1].cerrado) {
                    if(punto.hoy[1].inicio == ':' ){
                        horario += '';
                    }else{
                        if(punto.hoy[1].inicio == 'festivo'){
                            horario += '';
                        }else{
                            horario += ' ' + punto.hoy[1].inicio.substring(0, 5) + '-' + punto.hoy[1].fin.substring(0, 5);
                        }
                      
                    }
                    
                }
            }

            horario += '</strong>';
        }
        
        var color = 'black';
    
        if(punto.metodo == "store"){
            color = '#0097a7';
        }else if(punto.metodo == "tienda"){
            color = '#CED7DB';
        }

        var boton = '';
        console.log(prediccionesClick);
        for(var pr = 0;pr<prediccionesClick.length;pr++){
      
            if(punto.metodo == prediccionesClick[pr][0]){
                if(mirarPrediccionClick && mirarPrecioClick){
                    boton = '<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">add_circle_outline</i>Seleccionar punto de recogida</button>';
                }else if(mirarPrediccionClick){
                    boton = '<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">store</i>Entrega <span style="font-weight: bold;">'+prediccionesClick[pr][3]+'</span></button>';
                }else if(mirarPrecioClick){
                    boton = '<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">store</i>'+prediccionesClick[pr][1]+'</button>';
                }else{
                    boton = '<button id="seleccionar-store-btn" type="button" class="btn btn-warning business-btn-outline with-icon-left mg-t-20 seleccionar-store-btn" style="color:'+color+';border-color:'+color+';padding-left: 5px;"><i class="material-icons" style="margin-right: 4px;">store</i>'+prediccionesClick[pr][1]+': <span style="font-weight: bold;">'+prediccionesClick[pr][3]+'</span></button>';
                }
              
            }
        }

        var listItem = $('<div class="col-md-12 col-xs-12 no-pd pd-t-15 pd-b-15 punto-list-item" data-lat="' + punto.latitud + '" data-lng="' + punto.longitud + '">' +
                '<div class="col-md-4 col-xs-12">'+
                '<img id="imagenMapaCheck" style="width: 100%;" src="' + imagePath + '" width="200" onerror="this.onerror=null;this.src=\'/img/home/store-no-img.png\';">' +
                '</div>'+
                '<div class="col-md-8 col-xs-11 no-pd punto-datos">' +
                '<input type="hidden" class="punto-id" value="' + punto.id + '">' +
                '<input type="hidden" class="punto-tipo" value="' + punto.tipo + '">' +
                '<label class="direccion-label nombre"><i class="icon-punto texto-envio" style="color:'+color+'"> </i><strong> ' + punto.nombre + '</strong></label><br>' +
                '<i style="color: #3d3d3d" class="icon-ubicacion icono-naranja"> </i><label class="direccion-label direccion">' + punto.direccion + '</label><br>' +
                '<i class="fas fa-map icono-naranja" style="color: #3d3d3d"> </i> <label class="direccion-label direccion">&nbsp&nbsp' + punto.codigo_postal.codigo_postal + ', ' + punto.codigo_postal.ciudad + ', ' + punto.codigo_postal.codigo_pais + '</label><br>' +
                '<i class="far fa-clock"> </i><small class="text-secondary" style="color:#333;"> &nbspHOY</small><small class="horario-hoy">' + horario + '</small>&nbsp&nbsp&nbsp' +
                '<a id="' + punto.id + '" style="color:'+color+'" class="ver-mas-link small text-secondary" data-toggle="popover" data-placement="left">Ver más</a><br>' +
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
                console.log(punto.horarios[j].inicio);
                if(punto.horarios[j].inicio == ':'){
                    element = '<td class="texto-recogida text-nowrap">Cerrado</td>';
                }else{
                    if(punto.horarios[j].inicio == 'festivo'){
                        element = '<td class="texto-recogida text-nowrap">Festivo</td>';
                    }else{
                        element = '<td class="text-nowrap">' + punto.horarios[j].inicio.substr(0, 5) + '-' + punto.horarios[j].fin.substr(0, 5) + '</td>';
                    }
                  
                }
               
            }
            popover.find('tr.' + punto.horarios[j].dia).append(element);
        }

        storesList.append(listItem);

        $('body').append(popover);

        buildTooltip('contrareembolsoMapa', 'Pago contra-reembolso disponible');
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
    hoverStore($('.punto-list-item').first());
}

function hoverStore(elem) {

    var index = $('.punto-list-item').index(elem);
    var punto = visiblePuntos[index];
    var marker = findMarker(punto);

    var color = 'black';
    
    if(punto.metodo == "store"){
        color = '#0097a7';
    }else if(punto.metodo == "tienda"){
        color = '#CED7DB';
    }
    $('.btn-warning:active').css('background-color',color);
    if(!mobile) {
        clearStoresSelection();
        //elem.addClass('store-active');
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
        if($('.store-active') !== elem) {
            clearStoresSelection();
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
    if(punto.metodo == "store"){
        marker.setIcon(markerUbicacionActivo);
    }else if(punto.metodo == "tienda"){
        marker.setIcon(markerStoreActivo);
    }
    
    
    var center = new google.maps.LatLng(parseFloat(punto.latitud), parseFloat(punto.longitud));
    mapa.panTo(center);

}

function buscarLocalidad(nombre, lat, lon) {


    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        'address': $('.ciudad-input').val()
    }, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            processBusquedaLocalidadResult(results, nombre, lat, lon);
        } else if(status == google.maps.GeocoderStatus.ZERO_RESULTS) {
            geocoder.geocode({
                'address': $('.ciudad-input').val().split('-')[1].trim()
            }, function(results, status) {
                processBusquedaLocalidadResult(results, nombre, lat, lon);
            });
        }
    });

}

function processBusquedaLocalidadResult(results, nombre, lat, lon) {
 
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
        url: obtenerPuntosMapa,
        headers: { 'X-CSRF-TOKEN': csrf },
        type: 'GET',
        data: { 
            'nombre': nombre,
            'lat': lat,
            'long': lon,
            'cp': cp,
            'pais': pais
        },
        success: function(data) {
           
            if(data.puntos) {

                
                for(var i = 0;i<prediccionesClick.length;i++){
                    for(var p = 0;p<preciosClick.length;p++){
                        if(prediccionesClick[i][2] == preciosClick[p][1]){
                            if(prediccionesClick[i][1] == ''){
                                prediccionesClick[i].push('');
                            }else{
                                prediccionesClick[i].push(preciosClick[p][0]);
                            }
                            
                        }
                    }
                }
               
                if(prediccionesClick.length > 1){
                    var pred= prediccionesClick[0][1];
                    for(var i = 1;i<prediccionesClick.length;i++){
                        if(pred != prediccionesClick[i][1]){
                            mirarPrediccionClick = false;
                            break;
                        }
                    }
                }else{
                    mirarPrediccionClick = true;
                }

                //los precios sean iguales
                if(preciosClick.length > 1){
                    var precio = preciosClick[0][0];
                    for(var i = 1;i<preciosClick.length;i++){
                        if(precio != preciosClick[i][0]){
                            mirarPrecioClick = false;
                            break;
                        }
                    }

                }else{
                    mirarPrecioClick = true;
                }

                
                selectedLocalidad = data;
                // Creamos mapa en localidad recibida
                crearMapaLocalidad(data);
                // Creamos markers en puntos de localidad
                crearMarkersFin(data.puntos, true);

                showResultadoBusqueda(data.puntos);

                $('#modal-stores-search').modal();

                initTouchHandler();

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

    for(var i = 0 ; i < markers.length ; i++) {
        var marker = markers[i];
        if(parseFloat(parseFloat(marker.position.lat()).toFixed(6)) == parseFloat(parseFloat(punto.latitud).toFixed(6)) && parseFloat(parseFloat(marker.position.lng()).toFixed(6)) == parseFloat(parseFloat(punto.longitud).toFixed(6))) {
            return marker;
        }
    }
}

function findPunto(marker) {
    for(var i = 0 ; i < visiblePuntos.length ; i++) {
        var punto = visiblePuntos[i];
        if(fixDecimals(marker.position.lat()) === fixDecimals(parseFloat(punto.latitud)) && fixDecimals(marker.position.lng()) === fixDecimals(parseFloat(punto.longitud))) {
            return punto;
        }
    }
}

function fixDecimals(number) {
    var split = number.toString().split('.');
    return split[0] + '.' + split[1].substr(0, 6);
}

function clearStoresSelection() {
    $('.store-change').css('border-left','');
    $('.store-change').find('button').css('display','none');

    $('.store-change').removeClass('store-change');
    for(var i = 0 ; i < markers.length ; i++) {
        var marker = markers[i];
        marker.setIcon(markerStore);
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


function buildTooltip(ref, message) {
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