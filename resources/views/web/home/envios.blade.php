@extends('layouts.web')
@section('title', 'Envíos')

{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularPrecio')

@section('content')
    <section id="wrapper_principal">
        <div class="row-fluid navegacion-home">
            <!-- Navegación -->
            @include('web.home.navegacion')
        </div>
        <div class="inner-perfil">
            <div class="container">

                <div class="row">
                    <div class="col-xs-12">
                        <h1 class="title-seccion">Lista de envíos </h1>

                        <!-- Tabs -->
                        <ul class="nav nav-tabs tabs-perfil" id="myTab" role="tablist">
                            <li role="presentation" class="activos {{$paginaActiva == 'activos' || $paginaActiva == ''?'active':''}} "><a data-target="#confirmados" aria-controls="confirmados" role="tab" data-toggle="tab">Activos</a></li>
                            <li role="presentation" class="pendientes {{$paginaActiva == 'pendientes'?'active':''}}"><a data-target="#pendientes" aria-controls="pendientes" role="tab" data-toggle="tab">Pendientes</a></li>
                            <li role="presentation" class="historial {{$paginaActiva == 'historial'?'active':''}}"><a data-target="#historial" aria-controls="historial" role="tab" data-toggle="tab">Historial</a></li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade {{$paginaActiva == 'activos' || $paginaActiva == ''?'in active':''}} " id="confirmados" style="margin-bottom: 15px;">
                                @if(count($pedidosActivos))
                                    <div class="row-xs">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <td class="text-nowrap"><i class="fas fa-qrcode"></i> Pago (QR - Fecha)</td>
                                                    <td><i class="icon-paquete"></i> Paquetes</td>
                                                    <td><i class="icon-usuario"></i> Destinatario</td>
                                                    <td><i class="fas fa-info-circle" aria-hidden="true"></i> Estado</td>
                                                </tr>
                                                </thead>

                                                @foreach ($pedidosActivos as $pedido)
                                                        <tr>
                                                            <td rowspan="{{count($pedido->envios)}}" style="border-right: 1px dashed lightgrey;white-space: unset;">

                                                                <p class="qr no-mg" id="{{$pedido -> identificador}}">
                                                                    {!! QrCode::format('svg')->size(120)->generate($pedido->identificador) !!}
                                                                </p>
                                                                <p>
                                                                    <i class="fas fa-calendar-alt texto-corporativo" aria-hidden="true"></i> {{ Carbon\Carbon::parse($pedido->created_at )->format('d/m/Y')}}
                                                                </p>
                                                                <p>
                                                                    <i class="fas fa-check-circle" aria-hidden="true" style="color: #9be56b"></i> Pagado:
                                                                    <strong>

                                                                        {{ number_format(($pedido -> base + $pedido -> embalajes + $pedido -> coberturas + $pedido -> gestion) - ($pedido -> descuento ), 2)}}
                                                                    </strong> €
                                                                </p>
                                                            </td>
                                                            @foreach($pedido->envios as $key => $envio)
                                                                @if($envio == $pedido->envios->first() )
                                                                    <td class="pd-l-25">
                                                                        <span class="text-left">
                                                                            <p class="text-nowrap">
                                                                                <i class="fas fa-balance-scale fa-fw texto-corporativo"></i>

                                                                                {{ $envio->paquete->peso }} kg
                                                                                &nbsp
                                                                                <i class="icon-tamano texto-corporativo"></i> {{$envio-> paquete -> altoSinDecimales() }}x{{$envio-> paquete->anchoSinDecimales() }}x{{ $envio->paquete->largoSinDecimales() }}cm
                                                                            </p>

                                                                            @if($envio->embalaje_id > 0)
                                                                                <p class="text-nowrap">

                                                                                    <i class="fas fa-shopping-bag fa-fw texto-corporativo" aria-hidden="true"></i>

                                                                                    {{DB::table('embalajes')->where('id', $envio -> embalaje_id)->first()->descripcion}}


                                                                                </p>
                                                                            @endif

                                                                            <p class=" text-nowrap">
                                                                                <i class="icon-contenido2  texto-corporativo" aria-hidden="true"></i>{{$envio->descripcion}}
                                                                            </p>
                                                                            <p class="text-nowrap">
                                                                                <i class="icon-cobertura fs-1-2 texto-corporativo " aria-hidden="true"></i> {{$envio->cobertura->descripcion}}
                                                                            </p>
                                                                        </span>
                                                                    </td>


                                                                    <td class="datos-destinatario-pedido pd-l-25" valign="top">
                                                                        <span>
                                                                            <p>
                                                                                <i class="fas fa-user fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->nombre}} {{$envio->destinatario->apellidos}}
                                                                            </p>
                                                                            <p>
                                                                                <i class="fas fa-envelope fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->email}}
                                                                            </p>
                                                                            @if($envio->destinatario->telefono)
                                                                                <p>
                                                                                    <i class="fas fa-phone fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->telefono}}
                                                                                </p>
                                                                            @endif
                                                                        </span>
                                                                    </td>
                                                                    <td class="td--msg">
                                                                        <div>
                                                                            <p>
                                                                                <span class="icon-punto texto-envio"></span>
                                                                                {{ $envio->puntoEntrega->nombre }}
                                                                                <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                            </p>
                                                                            <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                            <p>
                                                                                <span class="icon-punto texto-recogida"></span>
                                                                                {{ $envio->puntoRecogida->nombre }}
                                                                                <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                            </p>
                                                                        </div>

                                                                        <a type="button" id="{{$envio->localizador}}" class="btn msg-paquete msg-app estado-popover" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content="

                                                                         <div class='inner-popover'>

                                                                            <p class='msg-paquete msg-{{ $envio->estado->nombre }}'>
                                                                                <i class='icon-{{ $envio -> estado -> nombre }}'></i>

                                                                                {{ $envio -> estado ->descripcion }}
                                                                            </p>

                                                                         </div>
                                                                         @if(count($envio->posicionesSinCancelar))
                                                                                <p>
                                                                                <small>
                                                                                El envío pasará por:
@foreach($envio->posicionesSinCancelar as $posicion)
                                                                        @if(!is_null($posicion->punto_destino_id))
                                                                                <span class='envio-intermedio'><strong>{{ $posicion->puntoDestino->nombre }}</strong> ({{ $posicion->puntoDestino->localidad->nombre}})</span>
                                                                         @endif
                                                                        @endforeach
                                                                                </small>
                                                                                </p>
@endif
                                                                                ">
                                                                            <i class="icon-ver-perfil"></i> Ver estado

                                                                        </a>


                                                                    </td>

                                                        </tr>

                                                    @else

                                                        <tr data-paquete=1>
                                                            <td class="pd-l-25">
                                                                <span class="text-left">
                                                                    <p class="text-nowrap">
                                                                        <i class="fas fa-balance-scale fa-fw texto-corporativo"></i> {{ $envio->paquete->peso }} kg
                                                                        <i class="icon-tamano texto-corporativo "></i> {{$envio-> paquete -> altoSinDecimales() }}x{{$envio-> paquete->anchoSinDecimales() }}x{{ $envio->paquete->largoSinDecimales() }}cm
                                                                    </p>
                                                                    @if($envio->embalaje_id > 0)
                                                                        <p class="text-nowrap">
                                                                            <i class="fas fa-shopping-bag fa-fw texto-corporativo " aria-hidden="true"></i>
                                                                            {{$envio->embalaje->descripcion}}
                                                                        </p>
                                                                    @endif
                                                                    <p class=" text-nowrap">
                                                                        <i class="icon-contenido2 texto-corporativo " aria-hidden="true"></i>{{$envio->descripcion}}
                                                                    </p>
                                                                    <p class="text-nowrap">
                                                                        <i class="icon-cobertura fs-1-2 texto-corporativo" aria-hidden="true"></i>{{$envio->cobertura->descripcion}}
                                                                    </p>
                                                                </span>
                                                            </td>
                                                            <td class="pd-l-25 datos-destinatario-pedido" valign="top">
                                                                <span>
                                                                    <p>
                                                                        <i class="fas fa-user fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->nombre}} {{$envio->destinatario->apellidos}}
                                                                    </p>
                                                                    <p>
                                                                        <i class="fas fa-envelope fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->email}}
                                                                    </p>
                                                                    @if($envio->destinatario->telefono)
                                                                        <p>
                                                                            <i class="fas fa-phone fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->telefono}}
                                                                        </p>
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td class="td--msg">
                                                                @if($envio->posicionesSinCancelar && $envio->posicionesSinCancelar->isEmpty())
                                                                    <p>
                                                                        <span class="icon-punto texto-envio"></span>
                                                                        {{ $envio->puntoEntrega->nombre }}
                                                                        <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                    </p>
                                                                    <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                    <p>
                                                                        <span class="icon-punto texto-recogida"></span>
                                                                        {{ $envio->puntoRecogida->nombre }}
                                                                        <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                    </p>
                                                                @else

                                                                    <?php
                                                                    $puntoInicial = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                        $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                    })->whereNotNull('punto_origen_id')->orderBy('created_at')->first();
                                                                    $puntoFinal = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                        $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                    })->whereNotNull('punto_destino_id')->orderBy('created_at')->first()
                                                                    ?>
                                                                    <p>
                                                                        <span class="icon-punto texto-envio"></span>
                                                                        @if($puntoInicial)
                                                                            {{ $puntoInicial->puntoOrigen->nombre }}
                                                                            <strong>({{ $puntoInicial->puntoOrigen->localidad->nombre }})</strong>
                                                                        @else
                                                                            {{ $envio->puntoEntrega->nombre }}
                                                                            <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                        @endif
                                                                    </p>
                                                                    <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                    <p>
                                                                        <span class="icon-punto texto-recogida"></span>
                                                                        @if($puntoFinal)
                                                                            {{ $puntoFinal->puntoDestino->nombre }}
                                                                            <strong>({{ $puntoFinal->puntoDestino->localidad->nombre }})</strong>
                                                                        @else
                                                                            {{ $envio->puntoRecogida->nombre }}
                                                                            <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                        @endif
                                                                    </p>
                                                                @endif
                                                                <a type="button" id="{{$envio->localizador}}" class="btn msg-paquete msg-app estado-popover" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content="

                                                                         <div class='inner-popover'>

                                                                            <p class='msg-paquete msg-{{ $envio->estado->nombre }}'>
                                                                             <i class='icon-{{ $envio -> estado -> nombre }}'></i>

                                                                                                                             {{ $envio -> estado ->descripcion }}
                                                                        </p>

                                                                        </div>
@if(count($envio->posicionesSinCancelar))
                                                                        <p>
                                                                        <small>
                                                                        El envío pasará por:
@foreach($envio->posicionesSinCancelar as $posicion)
                                                                @if(!is_null($posicion->punto_destino_id))
                                                                        <span class='envio-intermedio'><strong>{{ $posicion->puntoDestino->nombre }}</strong> ({{ $posicion->puntoDestino->localidad->nombre}})</span>
                                                                         @endif
                                                                @endforeach
                                                                        </small>
                                                                        </p>
@endif
                                                                        ">
                                                                    <i class="icon-ver-perfil"></i> Ver estado

                                                                </a>


                                                            </td>

                                                        </tr>
                                                    @endif

                                                @endforeach
                                                @endforeach
                                            </table>


                                        </div>
                                        <a href="{{ route('formulario_envio') }}" class="pull-left btn-app btn-sm">Enviar paquete</a>

                                        {{ $pedidosActivos->appends(['activos' => $pedidosActivos->currentPage()])->links() }}
                                    </div>

                                @else
                                    <div class="row-xs">
                                        <div class="m-nomensajes col-xs-12" style="display: table;">
                                            <h3>No tienes envíos activos</h3>
                                            <p class="texto-gris">En estos momentos no tienes ningún envío activo. ¡No esperes más y envía ahora!</p>
                                            <p class="no-mg"><a href="{{ route('formulario_envio') }}" class="btn-app ">Realizar envío</a></p>
                                        </div>
                                    </div>
                                @endif


                            </div>
                            <div role="tabpanel" class="tab-pane fade {{$paginaActiva === 'pendientes'?'in active':''}}" id="pendientes" style="margin-bottom: 15px;">
                                @if(count($pendientes))
                                    <div class="row-xs">
                                        <div class="table-responsive table-flow">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <td><i class="icon-fecha"></i>&nbsp;Fecha</td>
                                                    <td><i class="icon-contenido"></i> Paquete</td>
                                                    <td><i class="icon-usuario"></i> Destinatario</td>
                                                    <td><i class="fas fa-road" aria-hidden="true"></i> Ruta</td>
                                                    <td><i class="icon-paquete"></i> Acciones</td>
                                                </tr>
                                                </thead>
                                                @foreach ($pendientes->sortByDesc('created_at') as $envio)
                                                    <tr>
                                                        <td>{{date($envio->created_at->format('d/m/Y'))}}</td>
                                                        <td class="pd-l-25">
                                                            <span class="text-left">
                                                                <p>
                                                                    <i class="fas fa-balance-scale fa-fw texto-corporativo "></i> {{ $envio->paquete->peso }} kg
                                                                    <i class="icon-tamano texto-corporativo "></i> {{$envio-> paquete->altoSinDecimales() }}x{{$envio-> paquete->anchoSinDecimales() }}x{{ $envio->paquete->largoSinDecimales() }}cm

                                                                </p>
                                                                @if($envio->embalaje_id > 0)
                                                                    <p class="text-nowrap">
                                                                        <i class="fas fa-shopping-bag fa-fw texto-corporativo" aria-hidden="true"></i>

                                                                        {{$envio->embalaje->descripcion}}

                                                                    </p>
                                                                @endif
                                                                <p class="text-nowrap">
                                                                    <i class="icon-contenido2 texto-corporativo" aria-hidden="true"></i>{{$envio->descripcion}}
                                                                </p>
                                                                <p class="text-nowrap">
                                                                    <i class="icon-cobertura fs-1-2 texto-corporativo" aria-hidden="true"></i>{{$envio->cobertura->descripcion}}
                                                                </p>
                                                            </span>
                                                        </td>
                                                        <td class="pd-l-25 datos-destinatario-pedido" valign="top">
                                                            <span>
                                                                <p>
                                                                    <i class="fas fa-user fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->nombre}} {{$envio->destinatario->apellidos}}
                                                                </p>
                                                                <p>
                                                                    <i class="fas fa-envelope fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->email}}
                                                                </p>
                                                                @if($envio->destinatario->telefono)
                                                                    <p>
                                                                        <i class="fas fa-phone fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->telefono}}
                                                                    </p>
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($envio->posicionesSinCancelar && $envio->posicionesSinCancelar->isEmpty())
                                                                <p>
                                                                    <span class="icon-punto texto-envio"></span>
                                                                    {{ $envio->puntoEntrega->nombre }}
                                                                    <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                </p>
                                                                <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                <p>
                                                                    <span class="icon-punto texto-recogida"></span>

                                                                    {{ $envio->puntoRecogida->nombre }}
                                                                    <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                </p>
                                                            @else

                                                                <?php
                                                                $puntoInicial = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                    $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                })->whereNotNull('punto_origen_id')->orderBy('created_at')->first();
                                                                $puntoFinal = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                    $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                })->whereNotNull('punto_destino_id')->orderBy('created_at')->first()
                                                                ?>
                                                                <p>
                                                                    <span class="icon-punto texto-envio"></span>
                                                                    @if($puntoInicial)
                                                                        {{ $puntoInicial->puntoOrigen->nombre }}
                                                                        <strong>({{ $puntoInicial->puntoOrigen->localidad->nombre }})</strong>
                                                                    @else
                                                                        {{ $envio->puntoEntrega->nombre }}
                                                                        <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                    @endif
                                                                </p>
                                                                <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                <p>
                                                                    <span class="icon-punto texto-recogida"></span>
                                                                    @if($puntoFinal)
                                                                        {{ $puntoFinal->puntoDestino->nombre }}
                                                                        <strong>({{ $puntoFinal->puntoDestino->localidad->nombre }})</strong>
                                                                    @else
                                                                        {{ $envio->puntoRecogida->nombre }}
                                                                        <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                    @endif
                                                                </p>
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            <ul class="list-unstyled list-inline btns--acciones">
                                                                <li>
                                                                    <button type="button" name="button" class="btn btn-info btn-xs btn-block" onclick="location.href='{{ route('editar_envio', ['codigo' =>$envio->codigo]) }}'" data-toggle="tooltip" data-placement="top" title="Editar">
                                                                        <i class="fas fa-pencil-alt " aria-hidden="true"></i>
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button type="button" name="button" class="btn fondo-envio texto-inverso btn-xs btn-block" onclick="location.href='{{ route('crear_pago', ['codigo' =>$envio->codigo]) }}'" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Pagar">
                                                                        <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    {{-- Borrar envío --}}
                                                                    <form action="{{ route('borrar_envio', ['codigo' =>$envio->codigo]) }}" method="post">
                                                                        {{ csrf_field() }}
                                                                        {{ method_field('DELETE') }}
                                                                        <button type="submit" name="name" value="eliminar" class="btn btn-block btn-xs btn-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>

                                        </div>
                                        <a href="{{ route('resumen_pagos_todos') }}" class="btn-app btn-sm pull-left mg-r-5">Pagar todos</a>
                                        <a href="{{ route('formulario_envio') }}" class="pull-left btn-app btn-sm">Enviar paquete</a>
                                        {{ $pendientes->links() }}
                                    </div>


                                @else
                                    <div class="m-nomensajes col-md-12" style="display: table;">
                                        <h3>No tienes envíos pedientes</h3>
                                        <p class="texto-gris">En estos momentos no tienes ningún envío pendiente. ¡No esperes más y envía tu paquete ahora!</p>
                                        <p class="no-mg"><a href="{{ route('formulario_envio') }}" class="btn-app no-mg">Realiza un envío</a></p>
                                    </div>
                                @endif

                            </div>
                            <div role="tabpanel" class="tab-pane fade {{$paginaActiva == 'historial'?'in active':''}}" id="historial" style="margin-bottom: 15px;">
                                @if(count($finalizados))
                                    <div class="row-xs">
                                        <div class="table-responsive table-flow">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <td><i class="icon-fecha"></i>&nbsp;Fecha finalización</td>
                                                    <td><i class="icon-contenido pd-r-5"></i>Paquete</td>
                                                    <td><i class="icon-usuario"></i> Destinatario</td>
                                                    <td><i class="fas fa-road" aria-hidden="true"></i> Ruta</td>
                                                    <td><i class="icon-info"></i> Estado</td>
                                                </tr>
                                                </thead>
                                                @foreach ($finalizados->sortByDesc('created_at') as $envio)
                                                    <tr>
                                                        <td>{{isset($envio->fecha_finalizacion)? date('d/m/Y', strtotime($envio->fecha_finalizacion)):'-' }}</td>
                                                        <td class="pd-l-25">
                                                            <span class="text-left">
                                                                <p>
                                                                    <i class="fas fa-balance-scale fa-fw texto-corporativo"></i> {{ $envio->paquete->peso }} kg
                                                                    <i class="icon-tamano texto-corporativo"></i> {{$envio-> paquete->altoSinDecimales() }}x{{$envio-> paquete->anchoSinDecimales() }}x{{ $envio->paquete->largoSinDecimales() }}cm

                                                                </p>
                                                                @if($envio->embalaje_id > 0)
                                                                    <p class="text-nowrap">
                                                                        <i class="fas fa-shopping-bag fa-fw texto-corporativo " aria-hidden="true"></i>

                                                                        {{$envio->embalaje->descripcion}}

                                                                    </p>
                                                                @endif
                                                                <p class=" text-nowrap">
                                                                    <i class="icon-contenido2 texto-corporativo" aria-hidden="true"></i>{{$envio->descripcion}}
                                                                </p>
                                                                <p class="text-nowrap">
                                                                    <i class="icon-cobertura fs-1-2 texto-corporativo " aria-hidden="true"></i>{{$envio->cobertura->descripcion}}
                                                                </p>
                                                            </span>
                                                        </td>
                                                        <td class="pd-l-25 datos-destinatario-pedido" valign="top">
                                                            <span>
                                                                <p>
                                                                    <i class="fas fa-user fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->nombre}} {{$envio->destinatario->apellidos}}
                                                                </p>
                                                                <p>
                                                                    <i class="fas fa-envelope fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->email}}
                                                                </p>
                                                                @if($envio->destinatario->telefono)
                                                                    <p>
                                                                        <i class="fas fa-phone fa-fw texto-corporativo" aria-hidden="true"></i> {{$envio->destinatario->telefono}}
                                                                    </p>
                                                                @endif
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($envio->posicionesSinCancelar && $envio->posicionesSinCancelar->isEmpty())
                                                                <p>
                                                                    <span class="icon-punto texto-envio"></span>
                                                                    {{ $envio->puntoEntrega->nombre }}
                                                                    <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                </p>
                                                                <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                <p>
                                                                    <span class="icon-punto texto-recogida"></span>
                                                                    {{ $envio->puntoRecogida->nombre }}
                                                                    <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                </p>
                                                            @else
                                                                <?php
                                                                $puntoInicial = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                    $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                })->whereNotNull('punto_origen_id')->orderBy('created_at')->first();
                                                                $puntoFinal = $envio->posiciones()->whereDoesntHave('viaje', function ($query) {
                                                                    $query->where('estado_id', \App\Models\EstadoViaje::CANCELADO)->orWhere('estado_id', \App\Models\EstadoViaje::CANCELADO_TRANSPORTER);
                                                                })->whereNotNull('punto_destino_id')->orderBy('created_at')->first()
                                                                ?>
                                                                <p>
                                                                    <span class="icon-punto texto-envio"></span>
                                                                    @if($puntoInicial)
                                                                        {{ $puntoInicial->puntoOrigen->nombre }}
                                                                        <strong>({{ $puntoInicial->puntoOrigen->localidad->nombre }})</strong>
                                                                    @else
                                                                        {{ $envio->puntoEntrega->nombre }}
                                                                        <strong>({{ $envio->puntoEntrega->localidad->nombre }})</strong>
                                                                    @endif
                                                                </p>
                                                                <i class="flecha-puntos"><span class="icon-abajo texto-corporativo"></span></i>
                                                                <p>
                                                                    <span class="icon-punto texto-recogida"></span>
                                                                    @if($puntoFinal)
                                                                        {{ $puntoFinal->puntoDestino->nombre }}
                                                                        <strong>({{ $puntoFinal->puntoDestino->localidad->nombre }})</strong>
                                                                    @else
                                                                        {{ $envio->puntoRecogida->nombre }}
                                                                        <strong>({{ $envio->puntoRecogida->localidad->nombre }})</strong>
                                                                    @endif
                                                                </p>
                                                            @endif
                                                        </td>
                                                        <td><span class="msg-paquete msg-{{ $envio->estado->nombre }}"><i class="icon-{{ $envio->estado->nombre }}"></i> {{ $envio->estado->descripcion }}</span></td>

                                                    </tr>
                                                @endforeach
                                            </table>

                                        </div>
                                        <a href="{{ route('formulario_envio') }}" class="pull-left btn-app btn-sm">Enviar paquete</a>

                                        {{ $finalizados->links() }}
                                    </div>
                                @else
                                    <div class="m-nomensajes col-md-12" style="display: table;">
                                        <h3>No tienes envíos en tu historial</h3>
                                        <p class="texto-gris">En estos momentos no tienes ningún envío en tu historial. ¡No esperes más y envía tu paquete ahora!</p>
                                        <p class="no-mg"><a href="{{ route('formulario_envio') }}" class="btn-app no-mg">Realiza un envío</a></p>
                                    </div>
                                @endif

                            </div>


                        </div>

                    </div>
                </div>
            </div>
            <div id="contenedor-modal-pedidos-qr"></div>

        </div>

        {{-- Modal qr --}}
        <div class="modal modal-center-vertical fade" id="modal-qr" role="dialog" style="text-align:-webkit-center;" aria-labelledby="modalQr" tabindex="-1" >
            <div class="modal-dialog modal-sm"  role="document" data-backdrop="true" data-keyboard="false">
                <div class="modal-content" style="width: 322px;">
                    <div class="qr-container pd-10">

                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection

@push('javascripts-head')
    <script type="text/javascript" src="{{ mix('js/dist/qrcode.min.js') }}"></script>
@endpush

@push('javascripts-footer')

<script type="text/javascript">


    $(function () {

        $('.estado-popover').popover({
            html: true,
            container: 'body',
            trigger: 'hover',
            title: function () {

                if ($(this).attr('id')) {
                    var title = '<div class="no-mg text-center"><span><strong>' + $(this).attr('id') + '</strong></span></div>';
                }
                else {
                    var title = '<div class="no-mg text-center"><span><strong>' + 'Localizador sin asignar todavía' + '</strong></span></div>';
                }
                return title
            }
        })
    });
    var qr = $('.qr');

    function flashQr() {

        var id = $(this).attr('id');
        var qrmodal = $('#modal-qr');
        var container = $('.qr-container');
        container.empty();
        new QRCode(container[0], {
            text: id,
            width: 300,
            height: 300,
            correctLevel : QRCode.CorrectLevel.H
        });
        qrmodal.modal('show');
    }

    qr.on('click', flashQr);

    if (screen.width < 768) {

        $('.estado-popover').each(function () {

            $(this).on("touchend", function () {

                if (!$('.popover').attr('id') || $('.popover').attr('id') != $(this).attr('aria-describedby')) {
                    $('.popover').popover('hide');
                    $(this).popover('show');

                } else if ($('.popover').attr('id') && $('.popover').attr('id') == $(this).attr('aria-describedby')) {
                    $('.popover').popover('hide');
                }

            });
        });


        $(document).on("touchend", function (e) {

            if ($('.popover') && (!$(e.target).hasClass('estado-popover') && !$(e.target).parent().hasClass('estado-popover'))) {

                $('.popover').popover('hide');

            }

        });
        $('.estado-popover').attr('data-placement', 'top');


        $('.estado-popover').popover({
            html: true,
            container: 'body',
            placement: 'top',
            trigger: 'manual',
            title: function () {
                if ($(this).attr('id')) {
                    var title = '<div class="no-mg text-center"><span><strong>' + $(this).attr('id') + '</strong></span></div>';
                }
                else {
                    var title = '<div class="no-mg text-center"><span><strong>' + 'Localizador sin asignar todavía' + '</strong></span></div>';
                }
                return title
            }
        })

    }


</script>
@endpush
