<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, between) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

    "seguimiento" => 'Tracking',
    'horario' => 'Schedule',
	'hola' => 'Hello',

    'envioPagado.asunto' => 'Your :nombre order is ready',
	"envioPagado.titulo" => "Good news",
    "envioPagado.subtitulo1" => "Your order is packed and ready to go.",
    "envioPagado.subtitulo2" => "You can track your shipment's state and location in real time from the next link:",

    'destinoMR.asunto' => 'Your :nombre order is ready to collect',
    'destinoMR.titulo' => 'Hello',
    'destinoMR.subtitulo1' => 'Your shipment with code <span class="text-orange"><strong>:localizador</strong></span> is already in <span class="text-orange"><strong>:nombre</strong></span> ready for collection.',
    'destinoMR.subtitulo2' => 'You only need your ID number to ask for your package in the name of :nombre',
    'destinoMR.autorizacion' => 'If you can\'t collect your shipment personally, it can be taken by another person, showing up the attached authorization form properly filled and signed, with an ID card\'s copy.',
    'destinoMR.autorizacion.titulo' => 'THIRD PARTIES COLLECTION AUTHORIZATION',
    'destinoMR.autorizacion.autorizante' => 'I, Mr/Mrs..........................., with ID number ..........................., authorize',
    'destinoMR.autorizacion.autorizado' => 'Mr/Mrs..........................., with ID number..........................., to collect in my',
    'destinoMR.autorizacion.envio' => 'name the shipment nº :numexp',
    'destinoMR.autorizacion.fecha' => 'Date:........................ Signature:',

    'avisoDestinatario.asunto' => 'It\'s been one week since your order was at destination',
    'avisoDestinatario.subtitulo1' => 'Your shipment with code <span style="color:#ee8026;font-weight:bold;">:localizador</span> has been in <span style="color:#ee8026;font-weight:bold;">:nombre</span> for one week. Remember that you have 7 days to collect it before it\'s automatically returned to the sender.',

    'devolucion.asunto' => 'Your :nombre return request',
    'devolucion.subtitulo1' => 'We have received a request for the return of your order <span class="text-orange"><strong>:localizador</strong></span>.',
    'devolucion.tramite.titulo' => 'How to handle the return:',
    'devolucion.tramite.paso1' => 'Put the articles in the original package, close it and stick the new sticker over the older one in the outside of your package.',
    'devolucion.tramite.paso1PreImpresa' => 'Put the articles in the original package, close it and stick the new sticker over the older one in the outside of your package.',
    'devolucion.tramite.paso2' => 'Take the package to the selected point:',
    'devolucion.seguimiento' => 'Once the package is dropped off, you will be able to check in real time the state of your shipment in the next link:',
    'devolucion.factura.nombre' => 'Return-invoice-:nombre-:fecha.pdf',
    'devolucion.etiqueta.nombre' => 'Return-label-:nombre-:localizador.pdf',

    'etiquetaDevolucion.asunto' => 'Your return\'s label to :nombre',
    'etiquetaDevolucion.text' => 'Your return with code <strong class="text-orange">:localizador</strong> is ready! Here you have your package\'s label.',

    'devolucionDisponible.asunto' => 'Your order from :nombre has been delivered',
    'devolucionDisponible.texto1' => 'Your order :referencia has been delivered. If you\'d like to return any of your products, please click the next link.',
    'devolucionDisponible.boton' => 'Make your return',
    'devolucionDisponible.footer' => 'Thank you for trusting us.',

    'footer.titulo' => 'If you have any question or you need help, please contact us in our online support chat.',
    'footer.copyright' => 'All rights reserved',

);
