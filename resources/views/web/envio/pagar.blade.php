@extends('layouts.web')
@section('title', 'Validación de envío')

{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularPrecio')

@section('content')
    <section id="l-envios">
        <div class="container">
            @include('drivers.partials.progressbar',
                ['firstClass' => 'finalizado',
                'firstIcon' => 'icon-confirmar',
                'firstText' => 'Mi Envío',
                'secondClass' => 'activo-pago',
                'secondIcon' => 'icon-euros',
                'secondText' => '<strong>Confirmar y Pagar</strong>'])

       {{-- Mensajes de error (si usuario no tiene verificados email/móvil)--}}
       @if( session('error') )
           <div class="alert alert-danger mg-t-30" role="alert">
               <p class="text-danger">
                   {!! session('error') !!}
               </p>
           </div>
       @endif

       {{-- Mensajes de validacion --}}
       @if( session('message') )
           <div class="alert alert-success mg-t-30" role="alert">
               <p class="text-success">
                   {!! session('message') !!}
               </p>
           </div>
       @endif

       <div class="m-resumen-paquetes">
           <div class="row">
               <div class="col-xs-12">
                 <h2 class="h-seccion"><i class="icon-paquete icono-naranja"></i> Resumen de <span class="texto-corporativo"><strong>paquetes a enviar</strong></span></h2>
                 <p class="pull-left visible-xs"><strong>Total paquetes:</strong> <span class="pull-right texto-corporativo text-lg n--paquetes"> {{ count($envios) }} </span></p>
              </div>
           </div>
           <div class="row">
               <div class="col-xs-12">
                    <div class="lista-paquetes lista-pago">
                        <ul>
                            @foreach($envios as $envio)
                                @include('web.partials.modales.paquete-resumen')
                            @endforeach
                        </ul>

                    </div>
               </div>
           </div>
       </div>
       <div class="row">
            <div class="bloque-add">
               <div class="col-md-6 bloque-iz">
                   <div class="col-md-12">
                       <p class="pull-left hidden-xs"><strong>Total paquetes:</strong> <span class="pull-right texto-corporativo text-lg"> {{ count($envios) }} </span></p>
                       @if(count($envios))
                           <a href="{{ route('formulario_envio')}}" class="btn-app btn-sm pull-right">Añadir paquete <i class="icon-anadir texto-inverso"></i> </a>
                       @else
                           <a href="{{ route('formulario_envio')}}" class="btn-app btn-sm pull-right">Crear envío <i class="icon-anadir texto-inverso"></i> </a>
                       @endif
                   </div>
                   <div class="pd-r-15 pd-l-15 pd-t-30">
                   <div class="col-md-12 codigoDescuentoContainer card no-pd">
                       <div class="col-md-12 codigoDescuentoLeft">
                           <h4>¿Tienes un código de descuento?</h4>
                       </div>
                       <form method="post" class="formCodigo form-inline" action="{{route('codigo_validar')}}">
                           <div class="form-group">
                               <input type="text" name="codigo" class="form-control" placeholder="Código">
                           </div>

                           @foreach($envios as $envio)
                               <input type="hidden" name="envios[]" value="{{ $envio->codigo }}">
                           @endforeach
                           {{ csrf_field() }}
                           <div class="form-group btn-canjear-container mg-t-1">
                               <button type="submit" class="col-md-12 btn-app noBoxShadow">Canjear</button>
                           </div>
                       </form>
                   </div>
                   </div>
                </div>
               <div class="col-md-6 bloque-der">
                   <div class="resumen-pago">
                        <div class="partes-pago">
                            <p class="text-right">@if(count($envios) > 1)Precio envíos: @else Precio envío: @endif<span><strong>{{ $precio->calcularPrecioBase($envios)}}€</strong></span></p>
                            <div class="coberturas-item">
                                @if(count($envios) > 1)
                                    <div>
                                        <div>
                                            <p class="text-right"><a href="#collapseListGroup1" data-toggle="collapse"><i class="fas fa-plus-square cobertura-icon"></i></a> Coberturas: <span><strong>{{ $precio->calcularTotalCoberturas($envios)}}€</strong></span></p>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseListGroup1">
                                            <ul class="list-group">
                                                    @foreach($coberturas as $cobertura)
                                                        <p class="list-group-item text-right cobertura-item">{{$cobertura['nombre']}} x {{$cobertura['count']}}: <span><strong>{{$cobertura['valor']}}€</strong></span></p>
                                                    @endforeach

                                            </ul>
                                        </div>
                                    </div>
                                @elseif(count($envios) > 0)
                                    <p class="list-group-item text-right cobertura-item">Cobertura: <span><strong>{{ $precio->calcularCobertura($envio) }}€</strong></span></p>
                                @endif
                            </div>
                            @if($precio->calcularTotalEmbalajes($envios) != 0.00)
                            <div class="coberturas-item">
                                @if(count($envios) > 1)
                                    <div>
                                        <div>
                                            <p class="text-right"><a href="#collapseListGroup2" data-toggle="collapse"><i class="fas fa-plus-square cobertura-icon"></i></a> Embalajes: <span><strong>{{ $precio->calcularTotalEmbalajes($envios)}}€</strong></span></p>
                                        </div>
                                        <div class="panel-collapse collapse" id="collapseListGroup2">
                                            <ul class="list-group">
                                                @foreach($embalajes as $embalaje)
                                                    <p class="list-group-item text-right cobertura-item">{{$embalaje['nombre']}} x {{$embalaje['count']}}: <span><strong>{{$embalaje['precio']}}€</strong></span></p>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @elseif(count($envios) > 0)
                                    <p class="list-group-item text-right cobertura-item">Embalaje: <span><strong>{{ $precio->calcularEmbalaje($envio) }}€</strong></span></p>
                                @endif
                            </div>
                            @endif
                            <p class="text-right">Gastos de gestión: <span><strong>{{ $precio->calcularExtrasTotal($envios)}}€</strong></span></p>

                            @if(session('descuento') || isset($descuento))
                                @if(session('descuento'))
                                    {!! Form::open(
                                        array(
                                            'route' => array('codigo_eliminar'),
                                            'novalidate' => 'novalidate',
                                            'class' => 'codigoAplicado',
                                            'method' => 'delete')) !!}


                                    @foreach($envios as $envio)
                                        <input type="hidden" name="envios[]" value="{{ $envio->codigo }}">
                                    @endforeach

                                    <button type="submit" class="btn btn-link pull-left btn-xs btn-eliminar-descuento" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar descuento">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                    <p class="text-right">
                                        Descuento: <span><strong>- {!! session('descuento') !!}€</strong></span>
                                    </p>
                                    {!! Form::close() !!}
                                @else
                                    {!! Form::open(
                                        array(
                                            'route' => array('codigo_eliminar'),
                                            'novalidate' => 'novalidate',
                                            'class' => 'codigoAplicado',
                                            'method' => 'delete')) !!}


                                        @foreach($envios as $envio)
                                            <input type="hidden" name="envios[]" value="{{ $envio->codigo }}">
                                        @endforeach

                                        <button type="submit" class="btn btn-link pull-left btn-xs btn-eliminar-descuento" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="Eliminar descuento">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                        <p class="text-right">
                                             Descuento:
                                            <span><strong>- {!! $descuento !!}€</strong></span>
                                        </p>
                                    {!! Form::close() !!}
                                @endif
                            @endif
                        </div>
                        <div class="total-pago fondo-corporativo">
                                <p class="text-right texto-inverso">TOTAL:  <span><strong>{{ $precio->calcularPedido($envios)}}€</strong></span></p>
                        </div>
                   </div>
               </div>
            </div>
       </div>

        <hr style="margin:60px 0 30px 0">

        <div class="m-metodo-cobro">
            <div class="row">
                <div class="col-md-12">

                    <form action="{{ route('crear_pagos')}}" method="post">
                        @if($precio->calcularPedido($envios) == 0)
                            <h2 class="h-seccion"><i class="icon-pagos icono-naranja" aria-hidden="true"></i> ¡Tu envío es <span class="texto-corporativo"><strong>gratuito</strong></span>!</h2>
                            @foreach($envios as $envio)
                                <input type="hidden" name="envios[]" value="{{ $envio->codigo }}">
                            @endforeach
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-offset-3 col-md-6">
                                    <button class="btn-app btn-lg mg-t-35 btn-viaje-bottom" type="submit" name="name" value="Enviar">Finalizar</button>
                                </div>
                            </div>
                        @else
                            {{--<input type="hidden" name="metodo_cobro_select" class="metodo_cobro_select" value="2">--}}
                            {{--<h2 class="h-seccion"><i class="fas fa-credit-card icono-naranja" aria-hidden="true"></i> Efectúa el pago con tu <span class="texto-corporativo"><strong>tarjeta</strong></span></h2>--}}
                            {{--<div class="col-md-6 col-md-offset-3">--}}
                                {{--<p>Tarjeta de crédito / débito</p>--}}
                                {{--<div class="metodo-cobro tarjeta" data-value="2">--}}
                                    {{--<div class="inner-metodo"></div>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                                <input type="hidden" name="metodo_cobro_select" class="metodo_cobro_select" value="2">
                                <h2 class="h-seccion">Escoge el <span class="texto-corporativo"><strong>método de pago</strong></span></h2>
                                <div class="col-md-3 col-md-offset-3">
                                    <p class="pd-t-15"><span class="texto-corporativo">1. </span>Tarjeta de crédito / débito</p>
                                    <div class="metodo-cobro tarjeta" data-value="2">
                                        <div class="inner-metodo"></div>
                                    </div>

                                @if(count($metodosPago))
                                    <div class="g-cobro g-cobro-transfer">
                                        <label class="metodoPagoLabel">Selecciona tu tarjeta</label>

                                        @foreach($metodosPago as $key => $metodo)

                                            <br>
                                            <input type="radio" name="metodoPago" id="metodoPago{{$metodo->id}}" value="{{$metodo->id}}" {{ $key == 0 ? 'checked' : '' }}>
                                            &nbsp;
                                            <label class="tarjetaLabel" for="metodoPago{{$metodo->id}}">
                                                @if($metodo->tipoTarjeta->id == 1)
                                                    <img src="{{ asset('img/iconos-pagos/visa.png') }}" height="20px">
                                                @elseif($metodo->tipoTarjeta->id == 2)
                                                    <img src="{{ asset('img/iconos-pagos/master-card.png') }}" height="20px">
                                                @else
                                                    {{ $metodo->tipoTarjeta->nombre }} -
                                                @endif
                                                caduca en {{ substr($metodo->caducidad,2) }}/20{{ substr($metodo->caducidad,0,2) }}
                                            </label>

                                        @endforeach

                                        <br>
                                        <input type="radio" name="metodoPago" id="metodoPagoOtro" value="-1">
                                        &nbsp;
                                        <label class="tarjetaLabel" for="metodoPagoOtro">Nueva tarjeta</label>

                                    </div>

                                @endif

                            </div>
                            <div class="col-md-3">
                                <p class="pd-t-15"><span class="texto-corporativo">2. </span>Paypal </p>
                                <div class="metodo-cobro paypal" data-value="1">
                                    <div class="inner-metodo">
                                    </div>
                                </div>
                            </div>

                            {{-- Hiddens --}}
                            @foreach($envios as $envio)
                                <input type="hidden" name="envios[]" value="{{ $envio->codigo }}">
                            @endforeach
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-offset-3 col-md-6">
                                    <button class="btn-app btn-lg mg-t-35 btn-viaje-bottom" type="submit" name="name" value="Enviar">Pagar</button>
                                </div>
                            </div>

                        @endif
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
<script type="text/javascript" src="{{mix('js/web/pago.js')}}"></script>
@endpush