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

    "seguimiento" => 'Seguimiento',
    'horario' => 'Horario',
	'hola' => 'Hola',

	'envioPagado.asunto' => 'Tu pedido de :nombre está listo',
	"envioPagado.titulo" => "¡Buenas noticias",
    "envioPagado.subtitulo1" => "Tu pedido está empaquetado y en breve saldrá hacia su destino.",
    "envioPagado.subtitulo2" => "Puedes seguir en tiempo real el estado y localización de tu envío en el siguiente enlace:",

    'destinoMR.asunto' => 'Tu pedido de :nombre está disponible para recoger',
    'destinoMR.titulo' => 'Hola',
    'destinoMR.subtitulo1' => 'Tu envío con nº <span class="text-orange"><strong>:localizador</strong></span> ya se encuentra en el punto <span class="text-orange"><strong>:nombre</strong></span> para que puedas ir a recogerlo.',
    'destinoMR.subtitulo2' => 'Tan sólo necesitas tu DNI y solicitar tu paquete a nombre de :nombre',
    'destinoMR.autorizacion' => 'Si no puedes retirar tu envío personalmente, otra persona lo puede recoger por ti presentando el formulario de autorización adjunto debidamente cumplimentado y firmado, y una fotocopia de tu DNI.',
    'destinoMR.autorizacion.titulo' => 'AUTORIZACIÓN DE RECOGIDA POR TERCEROS',
    'destinoMR.autorizacion.autorizante' => 'Yo, D/Dña..........................., con DNI nº..........................., autorizo a',
    'destinoMR.autorizacion.autorizado' => 'D/Dña..........................., con DNI nº..........................., a retirar en mi',
    'destinoMR.autorizacion.envio' => 'nombre el envío nº :numexp',
    'destinoMR.autorizacion.fecha' => 'Fecha:........................ Firma:',

    'avisoDestinatario.asunto' => 'Tu pedido lleva una semana en destino',
    'avisoDestinatario.subtitulo1' => 'Tu envío con nº <span style="color:#0097a7;font-weight:bold;">:localizador</span> se encuentra en el punto <span style="color:#0097a7;font-weight:bold;">:nombre</span> desde hace una semana. Recuerda que dispones de 7 días para recogerlo antes de ser devuelto al remitente.',

    'devolucion.asunto' => 'Tu solicitud de devolución a :nombre',
    'devolucion.subtitulo1' => 'Hemos recibido la solicitud de devolución de tu pedido <span class="text-orange"><strong>:localizador</strong></span>.',
    'devolucion.tramite.titulo' => 'Cómo tramitar la devolución:',
    'devolucion.tramite.paso1' => 'Coloca los artículos con su embalaje original en la caja, cierra el paquete y pega la nueva etiqueta encima de la antigua en el exterior de la caja.',
    'devolucion.tramite.paso1PreImpresa' => 'Coloca los artículos con su embalaje original en la caja, cierra el paquete y pega la nueva etiqueta que venía con el pedido encima de la antigua en el exterior de la caja.',
    'devolucion.tramite.paso2' => 'Lleva el paquete al punto de recogida seleccionado:',
    'devolucion.seguimiento' => 'Una vez entregado el paquete, podrás seguir en tiempo real el estado de tu envío en el siguiente enlace:',
    'devolucion.factura.nombre' => 'Factura-devolución-:nombre-:fecha.pdf',
    'devolucion.etiqueta.nombre' => 'Etiqueta-devolución-:nombre-:localizador.pdf',

    'etiquetaDevolucion.asunto' => 'Tu etiqueta de devolución a :nombre',
    'etiquetaDevolucion.text' => 'Tu devolución con código <strong class="text-orange">:localizador</strong> esta lista! Aquí tienes la etiqueta para tu paquete.',

    'devolucionDisponible.asunto' => 'Tu pedido de :nombre ha sido entregado',
    'devolucionDisponible.texto1' => 'Tu pedido :referencia ha sido entregado. Si deseas devolver alguno de los artículos, pulsa en el siguiente enlace.',
    'devolucionDisponible.boton' => 'Realizar devolución',
    'devolucionDisponible.footer' => 'Muchas gracias por confiar en nosotros.',


    'footer.titulo' => 'Si tienes cualquier duda o necesitas ayuda ponte en contacto con nosotros a través de nuestro chat online.',
    'footer.copyright' => 'Todos los derechos reservados',

);
