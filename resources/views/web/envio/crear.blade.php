<?php

// Prioridad mayor a menor: modificado, prelogin, buscador

/* Datos Paquete
-------------------*/
// Peso (tipo input)
$peso = Session::has('peso') ? Session::get('peso') : old('peso');
$peso = Session::has('envio_guest') ? Session::get('envio_guest')['peso'] : $peso;
$peso = old('peso') ? old('peso') : $peso;

// Alto (tipo input)
$alto = old('alto') ? old('alto') : null;
$alto = Session::has('envio_guest') ? Session::get('envio_guest')['alto'] : $alto;
$alto = Session::has('alto') ? Session::get('alto') : $alto;

// Ancho (tipo input)
$ancho = old('ancho') ? old('ancho') : null;
$ancho = Session::has('envio_guest') ? Session::get('envio_guest')['alto'] : $ancho;
$ancho = Session::has('ancho') ? Session::get('ancho') : $ancho;

// Largo (tipo input)
$largo = old('largo') ? old('largo') : null;
$largo = Session::has('envio_guest') ? Session::get('envio_guest')['largo'] : $largo;
$largo = Session::has('largo') ? Session::get('largo') : $largo;

// Embalaje (tipo desplegable)
$embalaje = !is_null(old('embalaje')) ? old('embalaje') : null;
$embalaje = Session::has('envio_guest') ? Session::get('envio_guest')['embalaje'] : $embalaje;
$embalajeSession = !is_null($embalaje) && $embalaje != '' ? \App\Models\Embalaje::find($embalaje)->descripcion : 'Tipo de embalaje';

// Contenido (tipo input)
$contenido = old('descripcion') ? old('descripcion') : null;
$contenido = Session::has('envio_guest') ? Session::get('envio_guest')['descripcion'] : $contenido;
$contenido = Session::has('contenido') ? Session::get('contenido') : $contenido;

// Cobertura (tipo desplegable)
$cobertura =  old('cobertura') ? old('cobertura') : null;
$cobertura = Session::has('envio_guest') ? Session::get('envio_guest')['cobertura'] : $cobertura;
$coberturaSession = $cobertura ? \App\Models\Cobertura::find($cobertura)->descripcion : 'Tipo de cobertura';

// Valor declarado (tipo sub-desplegable)
$valorDeclarado = old('valorDeclarado') ? old('valorDeclarado') : null;
$valorDeclarado = Session::has('envio_guest') ? Session::get('envio_guest')['valorDeclarado'] : $valorDeclarado;
$valorDeclarado = Session::has('valorDeclarado') ? Session::get('valorDeclarado') : $valorDeclarado;

// Localidad Origen (tipo desplegable)
$localidadEntrega = Session::has('localidad_entrega') ? Session::get('localidad_entrega') :null;
$localidadEntrega = Session::has('envio_guest') ? \App\Models\Localidad::where('nombre',Session::get('envio_guest')['localidad_entrega'])->first() : $localidadEntrega;
$localidadEntrega = Session::has('localidadEntrega') ? Session::get('localidadEntrega') : $localidadEntrega;
$localidadEntregaName = Session::has('localidad_entrega') ? Session::get('localidad_entrega')->nombre : null;
$localidadEntregaName = Session::has('envio_guest') ? Session::get('envio_guest')['localidadEntregaName'] : $localidadEntregaName;
$localidadEntregaName = Session::has('localidadEntregaName') ? Session::get('localidadEntregaName') : $localidadEntregaName;
$localidadEntregaName = $localidadEntregaName ? $localidadEntregaName : 'Selecciona una localidad de origen';

// Punto de Origen (tipo desactivado)
$puntoEntrega = old('origen') ? old('origen') : null;
$puntoEntrega = Session::has('origen') ? Session::get('origen')->nombre : $puntoEntrega;
$puntoEntrega = Session::has('envio_guest') ? Session::get('envio_guest')['punto_entrega_guest'] : $puntoEntrega;
$puntoEntregaSession = $puntoEntrega ? $puntoEntrega : 'Selecciona el punto en el mapa';
//$puntoEntrega = Session::has('origen') ? Session::get('origen') : $puntoEntrega;

/* Datos Destinatario
------------------------*/

// Nombre (tipo input)
$nombre = old('nombre') ? old('nombre') : null;
$nombre = Session::has('envio_guest') ? Session::get('envio_guest')['nombre'] : $nombre;
$nombre = Session::has('nombre') ? Session::get('nombre') : $nombre;

// Apellido (tipo input)
$apellido = old('apellido') ? old('apellido') : null;
$apellido = Session::has('envio_guest') ? Session::get('envio_guest')['apellidos'] : $apellido;
$apellido = Session::has('apellido') ? Session::get('apellido') : $apellido;

// Email (tipo input)
$email = old('email') ? old('email') : null;
$email = Session::has('envio_guest') ? Session::get('envio_guest')['email'] : $email;
$email = Session::has('email') ? Session::get('email') : $email;

// Movil (tipo input)
$movil = old('movil') ? old('movil') : null;
$movil = Session::has('envio_guest') ? Session::get('envio_guest')['telefono'] : $movil;
$movil = Session::has('movil') ? Session::get('movil') : $movil;

// Localidad Destino (tipo desplegable)
$localidadRecogida = Session::has('localidad_recogida') ? Session::get('localidad_recogida') :null;
$localidadRecogida = Session::has('envio_guest') ? \App\Models\Localidad::where('nombre',Session::get('envio_guest')['localidad_recogida'])->first() : $localidadRecogida;
$localidadRecogida = Session::has('localidadRecogida') ? Session::get('localidadRecogida') : $localidadRecogida;
$localidadRecogidaName = Session::has('localidad_recogida') ? Session::get('localidad_recogida')->nombre : null;
$localidadRecogidaName = Session::has('envio_guest') ? Session::get('envio_guest')['localidadRecogidaName'] : $localidadRecogidaName;
$localidadRecogidaName = Session::has('localidadRecogidaName') ? Session::get('localidadRecogidaName') : $localidadRecogidaName;
$localidadRecogidaName = $localidadRecogidaName ? $localidadRecogidaName : 'Selecciona una localidad de destino';

// Punto Destino (tipo desactivado)
$puntoRecogida = old('destino') ? old('destino') : null;
$puntoRecogida = Session::has('destino') ? Session::get('destino')->nombre : $puntoRecogida;
$puntoRecogida = Session::has('envio_guest') ? Session::get('envio_guest')['punto_recogida_guest'] : $puntoRecogida;
$puntoRecogidaSession = $puntoRecogida ? $puntoRecogida : 'Selecciona el punto en el mapa';

// Punto entrega Latitud y Longitud
$puntoEntregaLat = Session::has('origen') ? Session::get('origen')->latitud : null;
$puntoEntregaLat = Session::has('envio_guest') ? Session::get('envio_guest')['puntoEntregaLat'] : $puntoEntregaLat;
$puntoEntregaLon = Session::has('origen') ? Session::get('origen')->longitud : null;
$puntoEntregaLon = Session::has('envio_guest') ? Session::get('envio_guest')['puntoEntregaLon'] : $puntoEntregaLon;

// Punto Recogida Latitud y Longitud
$puntoRecogidaLat = Session::has('destino') ? Session::get('destino')->latitud : null;
$puntoRecogidaLat = Session::has('envio_guest') ? Session::get('envio_guest')['puntoRecogidaLat'] : $puntoRecogidaLat;
$puntoRecogidaLon = Session::has('destino') ? Session::get('destino')->longitud : null;
$puntoRecogidaLon = Session::has('envio_guest') ? Session::get('envio_guest')['puntoRecogidaLon'] : $puntoRecogidaLon;

?>

@extends('layouts.web')
@section('title', 'Tu envío al mejor precio')
@section('meta_description', 'Envía tu paquete al precio más barato del mercado. Entrega y recoge tu envío en nuestra red de Transporter Stores con amplios horarios (sábados y domingos).')

{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularPrecio')

@section('content')

    <section id="l-envios">
        <div class="container">
            @include('drivers.partials.progressbar',
                ['firstClass' => 'activo-envio',
                'firstIcon' => 'icon-paquete',
                'firstText' => '<strong>Mi Envío</strong>',
                'secondClass' => 'para-hacer',
                'secondIcon' => 'icon-euros',
                'secondText' => 'Confirmar y Pagar'])
            <div class="row">
                {{-- Errores --}}
                <div class="col-md-12">
                    @if(Session::has('localidad_entrega') && Session::get('localidad_entrega') === 0)
                        <div class="alert alert-danger mg-t-30">
                            No se pueden realizar envíos desde la localidad indicada. Selecciona una de las localidades
                            disponibles.
                        </div>
                    @endif
                    @if(Session::has('localidad_recogida') && (Session::get('localidad_recogida') === 0))
                        <div class="alert alert-danger mg-t-30">
                            No se pueden realizar envíos a la localidad indicada. Selecciona una de las localidades
                            disponibles.
                        </div>
                    @endif

                    @if( session('error') )
                        <div class="alert alert-danger mg-t-30" role="alert">
                            <p class="text-danger">
                                {!! session('error') !!}
                            </p>
                        </div>
                    @endif

                    @if(count($errors))
                        <div class="alert alert-danger bloque-errores mg-t-30" role="alert">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            <strong><h5 class="text-center">Se han encontrado los siguientes errores al validar los datos</h5></strong>
                            <ul class="lista-errores">
                                @foreach($errors->all() as $error)
                                    <li class="help-block">
                                        <strong>{{ $error }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                {{-- Fin de errores --}}
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="h-seccion"><i class="icon-paquete icono-naranja"></i> Datos del <strong>Envío</strong></h2>
                </div>
            </div>
        <div class="row">
            <div class="col-md-7 bloque-izquierdo">
                <h4 class="h-form"><i class="icon-envio-recogida texto-envio"></i> Datos del <strong>Paquete</strong></h4>

                    {{-- Creación de paquete, destinatario y envío --}}
                    <form class="form-horizontal" method="post" action="" name="enviar-form" id="enviar-form">
                        {{-- Creación de paquete --}}
                        <div class="bloque-envio">
                            <div class="form-group{{ $errors->has('peso') ? ' has-error' : '' }} col-sm-3 no-pd-l">
                                <label for="peso">Peso: </label>
                                <div class="bloque-input">
                                    <span class="icon-peso texto-corporativo"></span>
                                    <input type="text" class="form-control" id="peso" name="peso" value="{{ $peso }}"
                                           placeholder="kg">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('alto') || $errors->has('ancho') || $errors->has('largo') || $errors->has('dimensiones') ? ' has-error' : '' }} col-sm-9 bloque-dimensiones">
                                <label>Dimensiones:</label>
                                <div class="col-4 col-4xs">
                                    <span class="texto-corporativo icon-tamano dimensiones-icon"></span>
                                    <input type="text" class="form-control dimensiones" id="alto" name="alto" value="{{ $alto}}" placeholder="Alto">
                                </div>
                                <div class="col-4 col-4xs">
                                    <input type="text" class="form-control dimensiones" id="ancho" name="ancho" value="{{ $ancho }}" placeholder="Ancho"></div>
                                <div class="col-4 col-4xs no-pd">
                                    <input type="text" class="form-control dimensiones pd-r-35" id="largo" name="largo" value="{{ $largo}}" placeholder="Largo">
                                    <span class="input--medida"><strong>cm</strong></span>
                                </div>
                                <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip" data-placement="right" title="" data-original-title="Recuerda que las dimensiones del paquete no pueden superar los 150cm sumando alto, ancho y largo."></span>
                                <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip" data-placement="left" title="" data-original-title="Recuerda que las dimensiones del paquete no pueden superar los 150cm sumando alto, ancho y largo."></span>
                            </div>

                            {{--Embalajes--}}
                            <div class="col-sm-12 no-pd form-group bloque-desplegable">
                                <label for="embalaje"> Embalaje:</label>
                                <div class="pull-right bloque-input {{ $errors->has('embalaje') ? ' has-error' : '' }} bloque-embalaje">
                                    <span class="fas fa-shopping-bag texto-inverso embalaje-icon"
                                          aria-hidden="true"></span>
                                    <ul class="lista-corporativo lista-form form-control" id="embalaje">
                                        <li class="pd-l-45">{{ $embalajeSession }}</li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-form-embalaje">
                                            @foreach ($embalajes as $tempEmbalaje)
                                                <li data-value="{{ $tempEmbalaje->id }}">
                                                    {{ $tempEmbalaje->descripcion }}
                                                    @if($tempEmbalaje->texto)
                                                        <i class="fas fa-info-circle right" aria-hidden="true"
                                                           data-toggle="tooltip" data-placement="top"
                                                           title="{{ $tempEmbalaje->texto }}"></i>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-sm-12 no-pd form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                                <label for="descripcion">Contenido:</label>
                                <div class="bloque-input pull-right bloque-contenido">
                                    <input type="text" class="form-control pd-l-15" id="descripcion" name="descripcion"
                                           value="{{$contenido}}"
                                           placeholder="Introduce el contenido del paquete">
                                </div>
                                <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip"
                                      data-placement="right" title=""
                                      data-original-title="Recuerda que debes mostrar el contenido de su paquete en el store de origen. Ten en cuenta que hay restricción de cierto tipo de mercancías."></span>
                                <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip"
                                      data-placement="left" title=""
                                      data-original-title="Recuerda que debes mostrar el contenido de su paquete en el store de origen. Ten en cuenta que hay restricción de cierto tipo de mercancías."></span>
                            </div>

                            <div class="col-sm-12 no-pd form-group bloque-desplegable">
                                <label for="cobertura"> Cobertura:</label>
                                <div class="pull-right bloque-input {{ $errors->has('cobertura') ? ' has-error' : '' }} bloque-cobertura">
                                    <span class="icon-lg icon-cobertura texto-inverso pd-t-5"></span>
                                    <ul class="lista-corporativo lista-form form-control" id="cobertura">
                                        <li class="pd-l-45">{{$coberturaSession}}</li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>

                                        <ul id="select" class="sublista-form">
                                            @foreach ($coberturas as $tempCobertura)
                                                <li data-value=" {{$tempCobertura->id }}">
                                                    {{ $tempCobertura->descripcion }}
                                                    <i class="fas fa-info-circle right" aria-hidden="true"
                                                       data-toggle="tooltip" data-placement="top"
                                                       title=" {{$tempCobertura->texto }}"></i>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>

                            <div id="valorDeclarado"
                                 class="col-sm-12 no-pd form-group {{($cobertura == 1 ?? $cobertura ==  0)?'hidden':''}} {{ $errors->has('valorDeclarado') ? ' has-error' : '' }}">
                                <label for="valorDeclarado">Valor declarado: </label>
                                <div class=" bloque-input pull-right bloque-contenido">
                                    <input type="text" class="form-control pd-r-35 pd-l-15" id="valor_declarado" name="valorDeclarado" value="{{$valorDeclarado}}" placeholder="Introduce el valor en Euros del contenido">
                                    <span class="input--euro"><strong>€</strong></span>
                                </div>
                                <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip"
                                      data-placement="right" title=""
                                      data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>
                                <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip"
                                      data-placement="left" title=""
                                      data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>
                            </div>


                            {{-- Puntos y localidades --}}
                            <div class="col-sm-12 no-pd form-group{{ $errors->has('localidad') ? ' has-error' : '' }} bloque-desplegable">
                                <label for="localidad">Ciudad:</label>
                                <div class="pull-right bloque-input bloque-origen">
                                    <span class="icon-cp texto-inverso"></span>
                                    <ul class="lista-corporativo lista-form" id="localidad">
                                        <li>
                                            {{ $localidadEntregaName }}
                                        </li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-localidades localidades-entrega">
                                            @foreach ($localidades as $localidad)
                                                <li id="{{ $localidad->id }}" data-value="{{ $localidad->id }}">{{ $localidad->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-12 no-pd form-group {{ $errors->has('origen') ? ' has-error' : '' }}">
                                <label for="punto_entrega">Store de origen:</label>
                                <div class="pull-right bloque-input">
                                    <span class="icon-punto texto-envio"></span>
                                    <input class="lista-inverso form-control lista-form"
                                           disabled
                                           placeholder="Selecciona el punto en el mapa"
                                           id="punto_entrega_input"
                                           type="text"
                                           name="punto_entrega_input"
                                           value="{{ $puntoEntregaSession }}">
                                    {{--Session::has('origen') ? Session::get('origen')->nombre : old('punto_entrega_input')--}}
                                    <ul class="lista-puntos-origen lista-form oculto" name="punto_entrega_input">
                                        <li>
                                            {{ 'Selecciona un store de origen' }}
                                        </li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-puntos-origen">

                                        </ul>
                                    </ul>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        {{-- Fin de paquete de envío --}}
                        <hr>

                    <!-- Persona encargada de recogida de envío //////////////////////////////////////////-->

                    <h4 class="h-form"><i class="icon-envio-recogida texto-recogida"></i> Datos del <strong>Destinatario</strong></h4>

                    <!--  Nombre y Apellidos de destinatario -->
                    <div class="bloque-envio">
                        <div class="col-sm-12 no-pd">
                            <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }} col-sm-6 col-xs-12 no-pd-l">
                                <label for="nombre">Nombre:</label>
                                <div class="pull-right bloque-input bloque-nombre">
                                    <span class="icon-usuario texto-corporativo"></span>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ Session::get('envio_guest') ? Session::get('envio_guest')['nombre'] : old('nombre') }}" placeholder="Introduce el nombre">
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('apellidos') ? ' has-error' : '' }} col-sm-6 col-xs-12 pull-right no-pd-r">
                                <label for="apellidos">Apellido:</label>
                                <div class="pull-right bloque-input">
                                    <span class="icon-usuario texto-corporativo"></span>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ Session::get('envio_guest') ? Session::get('envio_guest')['apellidos'] : old('apellidos') }}" placeholder="Introduce el apellido">
                                </div>
                            </div>
                        </div>

                        <!--  Email y Teléfono de destinatario -->
                        <div class="col-sm-12 no-pd">
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-sm-6 col-xs-12 no-pd-l">
                                <label for="email">E-mail:</label>
                                <div class="bloque-input bloque-email">
                                    <span class="icon-contacto texto-corporativo"></span>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ Session::get('envio_guest') ? Session::get('envio_guest')['email'] : old('email') }}" placeholder="Introduce el e-mail">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('telefono') ? ' has-error' : '' }} col-sm-6 col-xs-12 pull-right no-pd-r">
                                <label for="telefono">Móvil:</label>
                                <div class="pull-right bloque-input bloque-movil">
                                    <span class="icon-movil texto-corporativo"></span>
                                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ Session::get('envio_guest') ? Session::get('envio_guest')['telefono'] : old('telefono') }}" placeholder="Introduce el teléfono">
                                </div>
                            </div>
                        </div>

                            <!--  Ciudad y Punto de Recogida Final de destinatario -->
                            <div class="col-sm-12 no-pd form-group{{ $errors->has('destinatario_localidad') ? ' has-error' : '' }} bloque-desplegable">
                                <label for="destinatario_localidad">Ciudad:</label>
                                {{--<input type="text" class="form-control" id="destinatario_localidad" name="destinatario_localidad" value="{{ old('destinatario_localidad') }}"--}}
                                {{--placeholder="{{$localidadRecogidaName}}">--}}
                                <div class="pull-right bloque-input bloque-destino">
                                    <span class="icon-cp texto-inverso"></span>
                                    <ul class="lista-corporativo lista-form" id="destinatario_localidad">
                                        <li>{{ $localidadRecogidaName  }}</li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-localidades localidades-recogida">
                                            @foreach ($localidades as $localidad)
                                                <li id="{{ $localidad->id }}" data-value="{{ $localidad->id }}">{{ $localidad->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>

                        <div class="no-mg col-sm-12 no-pd form-group{{ $errors->has('destino') ? ' has-error' : '' }}">
                            <label for="punto_recogida">Store de destino:</label>
                            {{-- <input type="text" class="form-control" id="punto_recogida" name="punto_recogida" value="{{ old('punto_recogida') }}"> --}}
                            <div class="pull-right bloque-input">
                                <span class="icon-punto texto-recogida"></span>
                                <input class="lista-inverso form-control lista-form"
                                    id="punto_recogida_input"
                                    disabled
                                    placeholder="Selecciona el punto en el mapa"
                                    type="text"
                                    name="punto_recogida_input"
                                    value="{{ $puntoRecogidaSession }}">
                                <ul class="lista-puntos-destino lista-form oculto" name="punto_recogida_input">
                                    <li>
                                        {{ 'Selecciona un store de destino' }}
                                    </li>
                                    <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                    <ul class="sublista-form sublista-puntos-destino">

                                    </ul>
                                </ul>
                            </div>

                        </div>
                        <div class="clear"></div>
                    </div>

                        {{-- Hiddens --}}
                        {{ csrf_field() }}
                        <input type="hidden" name="punto_entrega_guest" id="punto_entrega_guest"
                               value="{{ Session::has('envio_guest') ? Session::get('envio_guest')['punto_entrega_guest'] : '' }}">
                        <input type="hidden" name="valor_declarado_guest" id="valor_declarado_guest"
                               value="{{ Session::has('envio_guest') ? Session::get('envio_guest')['valorDeclarado'] : '' }}">
                        <input type="hidden" name="punto_recogida_guest" id="punto_recogida_guest"
                               value="{{ Session::has('envio_guest') ? Session::get('envio_guest')['punto_recogida_guest'] : '' }}">
                        {{--<input type="hidden" name="cobertura_guest" id="cobertura_guest"--}}
                               {{--value="{{ Session::has('envio_guest') ? Session::get('envio_guest')['cobertura_guest'] : '' }}">--}}
                        {{--<input type="hidden" name="embalaje_guest" id="embalaje_guest"--}}
                               {{--value="{{ Session::has('envio_guest') ? Session::get('envio_guest')['embalaje_guest'] : '' }}">--}}

                        <input type="hidden" name="destino" id="punto_recogida"
                               value="{{ old('destino') ? old('destino') : (Session::has('envio_guest') ? Session::get('envio_guest')['destino']:'')}}">
                        <input type="hidden" name="origen" id="punto_entrega"
                               value="{{ old('origen') ? old('origen') :(Session::has('envio_guest') ? Session::get('envio_guest')['origen']:'')}}">
                        <input type="hidden" name="cobertura" id="tipo_cobertura"
                               value="{{ $cobertura }}">
                        <input type="hidden" name="embalaje" id="tipo_embalaje"
                               value="{{ $embalaje }}">
                        <input type="hidden" name="localidad_entrega_latitud" id="buscador_inicio_latitud"
                               value="{{old('localidad_entrega_latitud')?old('localidad_entrega_latitud'):($localidadEntrega? $localidadEntrega->latitud:'')}}">
                        <input type="hidden" name="localidad_entrega_longitud" id="buscador_inicio_longitud"
                               value="{{ old('localidad_entrega_longitud')?old('localidad_entrega_longitud'):($localidadEntrega?$localidadEntrega->longitud:'')}}">
                        <input type="hidden" name="localidad_recogida_latitud" id="buscador_recogida_latitud"
                               value="{{ old('localidad_recogida_latitud')?old('localidad_recogida_latitud'):($localidadRecogida?$localidadRecogida->latitud:'')}}">
                        <input type="hidden" name="localidad_recogida_longitud" id="buscador_recogida_longitud"
                               value="{{ old('localidad_recogida_longitud')?old('localidad_recogida_longitud'):($localidadRecogida?$localidadRecogida->longitud:'')}}">

                        <input type="hidden" id="localidadEntrega" name="localidadEntrega"
                               value="{{ $localidadEntrega?$localidadEntrega->id :''}}">
                        <input type="hidden" id="localidadRecogida" name="localidadRecogida"
                               value="{{ $localidadRecogida?$localidadRecogida->id:''}}">

                        <input type="hidden" id="localidadOrigenName" name="localidadEntregaName"
                               value="{{ $localidadEntregaName?$localidadEntregaName:''}}">
                        <input type="hidden" id="localidadDestinoName" name="localidadRecogidaName"
                               value="{{ $localidadRecogidaName?$localidadRecogidaName:''}}">
                        {{--<input type="hidden" id="origenID" name="origenID" value="">--}}
                        {{--<input type="hidden" id="destinoID" name="destinoID" value="">--}}

                        <input type="hidden" id="puntoEntregaLat" name="puntoEntregaLat" value="{{$puntoEntregaLat}}">
                        <input type="hidden" id="puntoEntregaLon" name="puntoEntregaLon" value="{{$puntoEntregaLon}}">

                        <input type="hidden" id="puntoRecogidaLat" name="puntoRecogidaLat"
                               value="{{$puntoRecogidaLat}}">
                        <input type="hidden" id="puntoRecogidaLon" name="puntoRecogidaLon"
                               value="{{$puntoRecogidaLon}}">

                    <input type="hidden" name="crear_envio" value="{{ csrf_token() }}">
                </form>
                {{-- Fin de formulario --}}
            </div>

            <div class="col-md-5 col-xs-12 mg-t-25">
                <div class="row">
                    <div id="mapa_envios" class="bloque-mapa">
                        <div class="mensaje-corporativo mensaje-mapa">Selecciona los <strong>puntos de origen/destino</strong></div>
                        <div class="google-maps" id="map"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-7 text-center">
                <div class="form-group text-center mg-t-35">
                    <button type="submit" class="form-control btn-app btn-sm centrado" id="enviar" name="crear_envio" value="Crear Envío" onclick="document.forms[0].submit();">Confirmar <i class="icon-confirmar"></i></button>
                </div>
            </div>
            <div class="col-md-5 right">
                @if(Session::has('pendientes'))
                    <div class="form-group text-center mg-t-35">
                        <a href="{{ route('crear_pagos')}}" class="form-control btn-app btn-sm centrado center active"><i class="fas fa-shopping-cart"></i> Pagar <span class="badge">{{ Session::get('pendientes')}}</span></a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Lighbox dinámico de punto, click desde marker --}}
<div id="lightbox"></div>


@endsection


{{-- Push de scripts --}}
@push('javascripts-footer')
    <script type="text/javascript" src="{{mix('js/web/envio.js')}}"></script>
    <script type="text/javascript" src="{{mix('js/vendor/clustered.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&callback=crearMapa" async defer></script>
@endpush