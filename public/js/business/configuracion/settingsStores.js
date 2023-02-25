$(function () {
    buildTooltips();
});

/**
 *  Build tooltips for the info icons.
 */
 


$('#direccion').focus(function() {
    if($('#cp').val() === '') {
        $('#cp').focus();
        if(!$('.ui-pnotify-container').length) {
            new PNotify({
                title: 'Citystock',
                text: 'Primero debes seleccionar una ciudad.',
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

    function searchAddress(elem) {
        
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            address: $('#direccion').val() + ', ' + $('#cp').val().split('-')[0].trim() + ', ' + $('#cp').val().split('-')[1].trim() + ', ES',
        }, function (results, status) {
            
            if (status == google.maps.GeocoderStatus.OK && results[0].types[0] !== 'postal_code' && results[0].types[0] !== 'locality') {
                if (marker && (marker.position.lat() !== results[0].geometry.location.lat() || marker.position.lng() !== results[0].geometry.location.lng())) {
                    marker.setMap(null);
                }
                if (!marker || (marker && (marker.position.lat() !== results[0].geometry.location.lat() || marker.position.lng() !== results[0].geometry.location.lng()))) {
                    marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        mapTypeControl:false,
                        streetViewControl: false,
                        icon: '/img/maps/transporter-business-marker.png'
                    });
                    map.panTo(marker.position);
                    map.setZoom(16);
                }
            } else {
                
                if (marker) {
                    marker.setMap(null);
                    marker = null;
                }
                $('#direccion').popover('show');
                $('.error-popover .popover-content strong').text($('#cp').val());
                $('body, body input, body button').one('mousedown keyup', function() {
                    if($('.error-popover').length) {
                        $('#direccion').popover('hide');
                    }
                });
            }
        });
    }

    $('#direccion').popover({
        trigger: 'manual',
        placement: 'top',
        html: true,
        content: 'No hemos podido encontrar tu dirección en <strong></strong>. Por favor, revisa que los datos sean correctos y vuelve a introducirlos.',
        container: 'body',
        template: '<div class="popover business-popover error-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    $('#direccion').keyup(function(e) {

        var code = e.which;
        if(code===13) {
            e.preventDefault();
        }
        if(code===13||code===188||code===186) {
            searchAddress();
        }
        return false;
    });

    $('.direccion-search-btn').click(function() {
        
        searchAddress();
    });

function buildTooltips() {
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

    buildTooltip('delivery-available-help-icon', 'Servicio <strong>Delivery</strong> disponible');
    buildTooltip('delivery-unavailable-help-icon', 'Servicio <strong>Delivery</strong> no disponible');
    buildTooltip('clickcollect-available-help-icon', 'Entrega <strong>Click&Collect</strong> disponible');
    buildTooltip('clickcollect-unavailable-help-icon', 'Entrega <strong>Click&Collect</strong> no disponible');
    buildTooltip('default-store-help-icon', '<strong>Almacén por defecto</strong> para entregas </br>E-commerce');
    buildTooltip('entregas-ecommerce-info', 'Envíos a domicilio o punto de recogida con </br>plazo de entrega de 24h o más');
    buildTooltip('ecomm-recogidas-peticion-info', 'Desde Envíos pendientes de expedición</br> podrás solicitar recogidas');
    buildTooltip('ecomm-recogidas-automatizadas-info', 'Siempre que haya envíos pendientes de expedición, un transportista acudirá los días de la semana (laborables) seleccionados');
    buildTooltip('entregas-qcommerce-info', 'Entregas <i>lastmile</i> en el mismo día');
    buildTooltip('qcommerce-click-n-collect-info', 'Recogida inmediata en tu tienda');
    buildTooltip('qcommerce-delivery-info', 'Envío a domicilio en < 1h o franja horaria</br> seleccionada');
    buildTooltip('qcommerce-delivery-timetable', 'Tiempo que necesitas para preparar un pedido antes de llegar el repartidor.<br> Según esto, variará la predicción del tiempo de entrega mostrado en tu tienda online (30min a 2h)');
    buildTooltip('ecommerce-tprep', 'Tiempo que necesitas para preparar un pedido antes de la recogida. Según esto, variará la predicción del tiempo de entrega mostrado en tu tienda online');
    buildTooltip('qcommerce-delivery-collect', 'Tiempo que necesitas para preparar un pedido antes de llegar el cliente.<br> Según esto, variará la predicción del tiempo de entrega mostrado en tu tienda online (30min a 2h)');
    buildTooltip('horario-apertura-info', 'Disponible para entregar y/o preparar los </br>pedidos');
    buildTooltip('qcommerce-image-btn', 'Elige tu imagen (300x120px o proporcional)');
    buildTooltip('checkout-image-btn', 'Elige tu imagen (185x60px o proporcional)');
    buildTooltip('clickcollect-transportista-help-icon', 'Contrato propio');
    buildTooltip('fecha-hora-checkout', 'Momento de compra del pedido');
    buildTooltip('fecha-hora-checkout1', 'Momento de devolución del pedido');
    buildTooltip('precio-checkout', 'Precio total del pedido en euros');
    buildTooltip('peso-checkout', 'Peso total del pedido en kg');
    buildTooltip('peso-modal-checkout', 'Por expedición');
    buildTooltip('prioridad-reglas', 'Las reglas se ordenan por prioridad.Se</br> aplicará antes la que tenga un número más bajo.');
    buildTooltip('cp-reglas', 'Para introducir múltiples Códigos Postales,sepáralos con una coma y sin espacios');
    buildTooltip('sku-reglas', 'Para introducir múltiples SKUs,sepáralos con una coma y sin espacios');
    buildTooltip('producto-reglas', 'Para introducir múltiples nombres de productos,sepáralos con una coma y sin espacios');
    buildTooltip('zonas-modal-checkout', 'Define precios para los rangos y zonas</br> seleccionadas. En caso de dejar la celda </br> vacía (sin valor), el método de envío no</br> estará disponible para el rango/zona</br> correspondiente');
    buildTooltip('calendario-almacen', 'Elige tus días festivos del año');
    buildTooltip('paquete', 'Las dimensiones de tu paquete por defecto</br> condicionarán los métodos de envío</br> disponibles en tu Checkout');

    buildTooltip('ordenar-checkout', 'La opción guardada se mostrará en </br>tu tienda online');
    buildTooltip('puntopack-checkout', 'Añade puntos de recogida de diferentes</br> transportistas en tu tienda online');
    buildTooltip('mapa-checkout', 'Simula un destino para ver el mapa de</br> puntos de recogida/tiendas disponibles');
}