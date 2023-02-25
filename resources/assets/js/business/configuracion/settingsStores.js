$(function () {
    buildTooltips();
    
});

/**
 *  Build tooltips for the info icons.
 */

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
    buildTooltip('default-store-help-icon', '<strong>Almacén por defecto</strong> para entregas E-commerce');
    buildTooltip('entregas-ecommerce-info', 'Envíos a domicilio o punto de recogida con plazo de entrega de 24h o más');
    buildTooltip('ecomm-recogidas-peticion-info', 'Desde Envíos pendientes de expedición podrás solicitar recogidas');
    buildTooltip('ecomm-recogidas-automatizadas-info', 'Siempre que haya envíos pendientes de expedición, un transportista acudirá los días de la semana (laborables) seleccionados');
    buildTooltip('entregas-qcommerce-info', 'Entregas <i>lastmile</i> en el mismo día');
    buildTooltip('qcommerce-click-n-collect-info', 'Recogida inmediata en tu tienda');
    buildTooltip('qcommerce-delivery-info', 'Envío a domicilio en < 1h o franja horaria seleccionada');
    buildTooltip('qcommerce-delivery-timetable', 'Tiempo que necesitas para preparar un pedido antes de llegar el repartidor. Según esto, variará el tiempo de entrega mostrado en tu tienda online (30min a 2h)');
    buildTooltip('horario-apertura-info', 'Disponible para entregar y/o preparar los pedidos');
    buildTooltip('qcommerce-image-btn', 'Elige tu imagen (300x120px o proporcional)');
    buildTooltip('clickcollect-transportista-help-icon', 'Contrato propio');
}