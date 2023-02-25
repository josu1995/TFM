<?php

//Datos origen
$peso = old('peso') ? old('peso') : (isset($envio) ? $envio->paquete->peso : null);
$alto = old('alto') ? old('alto') : (isset($envio) ? $envio->paquete->alto : null);
$ancho = old('ancho') ? old('ancho') : (isset($envio) ? $envio->paquete->ancho : null);
$largo = old('largo') ? old('largo') : (isset($envio) ? $envio->paquete->largo : null);
$coberturaSession = Session::has('temp_cobertura') ? Session::get('temp_cobertura')->descripcion : (isset($envio->cobertura) ? $envio->cobertura->descripcion : 'Tipo de cobertura');
$coberturaVDeclarado = Session::has('temp_valor_declarado') ? Session::get('temp_valor_declarado') : isset($envio) ? $envio->valor_declarado : null;
$embalaje = !is_null(old('embalaje')) ? old('embalaje') : (isset($envio) ? $envio->embalaje->id : null);
$embalajeSession = !is_null($embalaje) && !empty($embalaje) ? \App\Models\Embalaje::find($embalaje)->descripcion : 'Tipo de embalaje';
$localidadEntrega = old('localidadEntrega') ? \App\Models\Localidad::find(old('localidadEntrega'))  :  $envio->puntoEntrega->localidad;
//Datos Destino
$nombre = old('nombre') ? old('nombre') : (isset($envio) ? $envio->destinatario->nombre : null);
$apellidos = old('apellidos') ? old('apellidos') : (isset($envio) ? $envio->destinatario->apellidos : null);
$email = old('email') ? old('email') : (isset($envio) ? $envio->destinatario->email : null);
$telefono = old('telefono') ? old('telefono') : (isset($envio) ? $envio->destinatario->telefono : null);
$contenido = old('descripcion') ? old('descripcion') : (isset($envio) ? $envio->descripcion : null);
$localidadRecogida = old('localidadRecogida') ? \App\Models\Localidad::find(old('localidadRecogida'))  :  $envio->puntoRecogida->localidad;
?>
@extends('layouts.web')
@section('title', 'Tu envío al mejor precio')

{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularPrecio')
@inject('cobertura', '\App\Models\Cobertura')


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

                    @if( session('error') )
                        <div class="alert alert-danger" role="alert">
                            <p class="text-danger">
                                {!! session('error') !!}
                            </p>
                        </div>
                    @endif

                    @if(count($errors))
                        <div class="alert alert-danger bloque-errores" role="alert">
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
                    @if(Session::has('mensaje'))
                        <div class="alert alert-success" role="alert">
                            <p>
                                Envío actualizado con éxito
                            </p>
                        </div>
                    @endif
                </div>
                {{-- Fin de errores --}}
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="h-seccion"><i class="icon-paquete icono-naranja"></i> Edición del envío: <strong>{{ $envio->descripcion }}</strong></h2>
                </div>
            </div>
                <div class="row">
                    <div class="col-md-7 bloque-izquierdo">
                        <h4 class="h-form"><i class="icon-envio-recogida texto-envio"></i> Datos del <strong>Paquete</strong></h4>

                    {{-- Creación de paquete, destinatario y envío --}}
                    <form class="form-horizontal" method="post"
                          action="{{ route('editar_envio', ['codigo' => $envio->codigo]) }}" name="enviar-form"
                          id="enviar-form">
                        {{-- Creación de paquete --}}
                        <div class="bloque-envio">
                            @if($envio -> paquete)
                                <div class="form-group{{ $errors->has('peso') ? ' has-error' : '' }} col-md-3 no-pd-l">
                                    <label for="peso">Peso: </label>
                                    <div class="bloque-input">
                                        <span class="icon-peso texto-corporativo"></span>
                                        <input type="text" class="form-control" id="peso" name="peso"
                                               value="{{ $peso }}"
                                               placeholder="Introduce el peso">
                                    </div>

                                </div>
                                <div class="form-group {{ $errors->has('alto') || $errors->has('ancho') || $errors->has('largo') || $errors->has('dimensiones') ? ' has-error' : '' }} col-md-9 pd-r-40">
                                    <label>Dimensiones:</label>
                                    <div class="col-4 col-4xs">
                                        <span class="texto-corporativo icon-tamano dimensiones-icon"></span>
                                        <input type="text" class="form-control dimensiones" id="alto" name="alto" value="{{ $alto }}" placeholder="Alto">
                                    </div>
                                    <div class="col-4 col-4xs">
                                        <input type="text" class="form-control dimensiones" id="ancho" name="ancho" value="{{ $ancho}}" placeholder="Ancho">
                                    </div>
                                    <div class="col-4 col-4xs no-pd">
                                        <input type="text" class="form-control dimensiones pd-r-35" id="largo" name="largo" value="{{ $largo }}" placeholder="Largo">
                                        <span class="input--medida"><strong>cm</strong></span>
                                    </div>
                                    <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip" data-placement="right" title=""
                                          data-original-title="Recuerda que las dimensiones del paquete no pueden superar los 150cm sumando alto, ancho y largo."></span>
                                    <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip" data-placement="left" title=""
                                          data-original-title="Recuerda que las dimensiones del paquete no pueden superar los 150cm sumando alto, ancho y largo."></span>
                                </div>
                                <input type="hidden" name="paquete_id" value="{{ $envio -> paquete ->id }}">
                            @endif

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

                            @if((old('cobertura') && \App\Models\Cobertura::find(old('cobertura'))->valor > 0) || ($envio->cobertura && $envio->cobertura->valor > 0))

                                <div id="valorDeclarado" class="col-sm-12 no-pd form-group">
                                    <label for="valorDeclarado">Valor declarado:</label>
                                    <div class="bloque-input pull-right bloque-contenido {{ $errors->has('valorDeclarado') ? ' has-error' : '' }}">
                                        <input type="text" class="form-control pd-r-35 pd-l-15" name="valorDeclarado"
                                               value="{{ $coberturaVDeclarado }}"
                                               placeholder="Introduce el valor en Euros del contenido">
                                        <span class="input--euro"><strong>€</strong></span>
                                    </div>
                                    <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip"
                                          data-placement="right" title=""
                                          data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>
                                    <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip"
                                          data-placement="left" title=""
                                          data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>
                                </div>

                            @else

                                <div id="valorDeclarado" class="col-sm-12 hidden no-pd form-group {{ $errors->has('valorDeclarado') ? ' has-error' : '' }}">
                                    <label for="valorDeclarado">Valor declarado:</label>
                                    <div class="bloque-input pull-right pd-r-40">
                                        <input type="text" class="form-control pd-r-35 pd-l-15" name="valorDeclarado"
                                               value="{{ old('valorDeclarado') ?  old('valorDeclarado') : "" }}"
                                               placeholder="Introduce el valor en Euros del contenido">
                                        <span class="input--euro"><strong>€</strong></span>
                                    </div>
                                    <span class="btn--info btn--info--right icon-info hide-xs" data-toggle="tooltip"
                                          data-placement="right" title=""
                                          data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>
                                    <span class="btn--info btn--info--right icon-info show-xs" data-toggle="tooltip"
                                          data-placement="left" title=""
                                          data-original-title="La cuantía deberá poder ser evidenciada con facturas de la mercancía en caso de reclamación"></span>


                                </div>
                            @endif
                            <div class="col-md-12 no-pd form-group {{ $errors->has('localidad') ? ' has-error' : '' }} bloque-desplegable">
                                <label for="localidad">Ciudad:</label>
                                <div class="pull-right bloque-input bloque-origen">
                                    <span class="icon-cp texto-inverso"></span>
                                    <ul class="lista-corporativo lista-form" id="localidad">
                                        <li>{{ $localidadEntrega->nombre}}
                                        </li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-localidades localidades-entrega">
                                            @foreach ($localidades as $localidad)
                                                <li id="{{ $localidad->id }}">{{ $localidad->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-12 no-pd form-group{{ $errors->has('origen') ? ' has-error' : '' }}">
                                <label for="punto_entrega">Store de origen:</label>
                                <div class="pull-right bloque-input">
                                    <span class="icon-punto texto-envio"></span>
                                    <input class="lista-inverso form-control lista-form" id="punto_entrega_input"
                                           type="text" name="punto_entrega_input" disabled
                                           value="{{ old('punto_entrega') ? old('punto_entrega') : $envio ->puntoEntrega ->nombre }}">
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
                            <div class="col-lg-12 no-pd">
                                <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }} col-lg-6 col-xs-12 no-pd-l">
                                    <label for="nombre">Nombre:</label>
                                    <div class="pull-right bloque-input bloque-nombre">
                                        <span class="icon-usuario texto-corporativo"></span>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                               value="{{$nombre}}"
                                               placeholder="Introduce el nombre">
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('apellidos') ? ' has-error' : '' }} col-lg-6 col-xs-12 pull-right no-pd-r">
                                    <label for="apellidos">Apellido:</label>
                                    <div class="pull-right bloque-input">
                                        <span class="icon-usuario texto-corporativo"></span>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                                               value="{{ $apellidos }}"
                                               placeholder="Introduce los apellidos">
                                    </div>
                                </div>
                            </div>

                            <!--  Email y Teléfono de destinatario -->
                            <div class="col-lg-12 no-pd">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} col-lg-6 col-xs-12 no-pd-l">
                                    <label for="email">E-mail:</label>
                                    <div class="bloque-input bloque-email">
                                        <span class="icon-contacto texto-corporativo"></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="{{ $email }}"
                                               placeholder="Introduce el correo electrónico">
                                    </div>

                                </div>
                                <div class="form-group{{ $errors->has('telefono') ? ' has-error' : '' }} col-lg-6 col-xs-12 pull-right no-pd-r">
                                    <label for="telefono">Móvil:</label>
                                    <div class="pull-right bloque-input bloque-movil">
                                        <span class="icon-movil texto-corporativo"></span>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                               value="{{ $telefono }}"
                                               placeholder="Introduce el teléfono">
                                    </div>
                                </div>
                            </div>

                                <!--  Ciudad y Punto de Recogida Final de destinatario -->

                            <div class="col-md-12 no-pd form-group{{ $errors->has('destinatario_localidad') ? ' has-error' : '' }} bloque-desplegable">
                                <label for="localidad">Ciudad:</label>
                                {{-- <input type="text" class="form-control" id="destinatario_localidad" name="destinatario_localidad" value="{{ old('destinatario_localidad') }}"> --}}
                                <div class="pull-right bloque-input bloque-destino">

                                    <span class="icon-cp texto-inverso"></span>

                                    <ul class="lista-corporativo lista-form" id="destinatario_localidad">
                                        <li>{{ $localidadRecogida -> nombre}}</li>
                                        <span class="open-lista icon-flecha-down texto-corporativo mensaje-inverso"></span>
                                        <ul class="sublista-form sublista-localidades localidades-recogida">
                                            @foreach ($localidades as $localidad)
                                                <li id="{{ $localidad->id }}">{{ $localidad->nombre }}</li>
                                            @endforeach
                                        </ul>
                                    </ul>
                                </div>
                            </div>

                            <div class="no-mg col-md-12 no-pd form-group{{ $errors->has('destino') ? ' has-error' : '' }}">
                                <label for="punto_recogida">Store de destino:</label>
                                <div class="pull-right bloque-input">
                                    <span class="icon-punto texto-recogida"></span>
                                    <input class="lista-inverso form-control lista-form" disabled
                                           id="punto_recogida_input" type="text" name="punto_recogida_input"
                                           value="{{  old('punto_recogida') ? old('punto_recogida') : $envio->puntoRecogida->nombre }}">
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
                        <input type="hidden" name="destino" id="punto_recogida"
                               value="{{ old('destino', $envio->puntoRecogida->id),session('temp_destino') }}">
                        <input type="hidden" name="origen" id="punto_entrega"
                               value="{{ old('origen',$envio->puntoEntrega->id,session('temp_origen'))}}">
                        <input type="hidden" name="cobertura" id="tipo_cobertura"
                               value="{{ old('tipo_cobertura', $envio->cobertura->id) }}">
                        <input type="hidden" name="embalaje" id="tipo_embalaje"
                               value="{{ old('tipo_embalaje', $embalaje) }}">
                        <input type="hidden" name="localidad_entrega_latitud" id="buscador_inicio_latitud"
                               value="{{old('localidad_entrega_latitud',$envio->puntoEntrega->latitud )}}">
                        <input type="hidden" name="localidad_entrega_longitud" id="buscador_inicio_longitud"
                               value="{{old('localidad_entrega_longitud',$envio->puntoEntrega->longitud)}}">
                        <input type="hidden" name="localidad_recogida_latitud" id="buscador_fin_latitud"
                               value="{{ old('localidad_recogida_latitud',$envio->puntoRecogida->latitud) }}">
                        <input type="hidden" name="localidad_recogida_longitud" id="buscador_fin_longitud"
                               value="{{ old('localidad_recogida_longitud', $envio->puntoRecogida->longitud )}}">
                        <input type="hidden" id="localidadEntregaName" name="localidadEntregaName"
                               value="{{ old('localidadEntregaName') ? old('localidadEntregaName') : $envio -> puntoEntrega ->localidad -> nombre }}">

                        <input type="hidden" id="localidadRecogidaName" name="localidadRecogidaName"
                               value="{{ old('localidadRecogidaName') ? old('localidadRecogidaName') : $envio -> puntoRecogida ->localidad -> nombre}}">
                        <input type="hidden" id="localidadEntrega" name="localidadEntrega"
                               value="{{ $localidadEntrega->id  }}">
                        <input type="hidden" id="localidadRecogida" name="localidadRecogida"
                               value="{{ $localidadRecogida->id }}">

                            {{-- Envio de form por put para actualización --}}
                            {!! method_field('put') !!}
                            <div class="row">
                                <div class="col-lg-12 text-center">
                                    <div class="form-group text-center mg-t-35 col-md-12">
                                        <button type="submit" class="form-control btn-app btn-sm centrado mg-b-35" id="enviar" name="enviar" value="Crear Envío">Actualizar envío </button>
                                        @if(Session::has('mensaje'))
                                            <a href="{{ route('crear_pago', ['codigo' => $envio->codigo])}}" class="form-control btn-app btn-sm centrado">Pagar el envío</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Fin formulario -->
                    </div>
                    <div class="col-md-5">
                        <div id="mapa_envios" class="bloque-mapa">
                            <div class="mensaje-corporativo mensaje-mapa">Selecciona los <strong>puntos de origen/destino</strong></div>
                            {{-- <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2376.5111506079666!2d2.1654696384377825!3d41.369381958857204!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12a49816718e30e5%3A0x44b0fb3d4f47660a!2sBarcelona!5e0!3m2!1ses!2ses!4v1457007002742" width="100%" height="100%" frameborder="0" style="border:0"></iframe> --}}
                            <div class="google-maps" id="map">
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>

    {{-- Lighbox dinámico de punto, click desde marker --}}
    <div id="lightbox"></div>

@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
    <script type="text/javascript" src="{{mix('js/vendor/clustered.js')}}"></script>
    <script type="text/javascript" src="{{mix('js/web/envio.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&callback=crearMapa" async defer></script>
@endpush
