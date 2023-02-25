@extends('web.home.cuenta')
@section('perfil')
    <div class="inner-box">

        <h1 class="title-seccion">Pagos efectuados</h1>
        @if(count($pedidos))
            <div class="table-responsive table-flow">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td><i class="fas fa-calendar-alt" aria-hidden="true"></i> Fecha</td>
                        <td colspan="2"><i class="fas fa-info-circle"  aria-hidden="true"></i> Detalles</td>
                        <td><i class="fas fa-shopping-cart" aria-hidden="true"></i> Método de pago</td>
                        <td><i class="fas fa-euro-sign" aria-hidden="true"></i> Importe</td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedidos->sortByDesc('updated_at') as $pedido)
                        <tr>
                            <td style="vertical-align: middle; font-size:1.0em;">
                                {{ date('d/m/Y', strtotime($pedido->created_at)) }}
                            </td>
                            <td class="pagos-detalles no-pd">

                                <i class="icon-envios texto-corporativo"></i>
                                <br>

                                @if($pedido->embalajes != 0.00)
                                    <i class="fas fa-shopping-bag texto-corporativo" aria-hidden="true"></i>
                                    <br>
                                @endif

                                <i class="icon-cobertura texto-corporativo" aria-hidden="true"></i>
                                <br>
                                <i class="fas fa-thumbs-up texto-corporativo" aria-hidden="true"></i>
                                <br>
                                @if($pedido->descuento != 0)
                                    <i class="far fa-money-bill-alt texto-corporativo" aria-hidden="true"></i>

                                @endif

                            </td>
                            <td style="text-align: left;font-size:0.9em;vertical-align: top;padding:8px 0 8px 0;">
                                <?php
                                $contadorEmbalajes = $pedido->envios()->whereHas('embalaje',function($query){
                                    $query->where('precio','>',0.0);
                                })->get()->count();
                                $contadorCoberturas = $pedido->envios()->whereHas('cobertura',function($query){
                                    $query->where('id','>',0);
                                })->get()->count();
                                $contadorEnvios = $pedido->envios()->count();
                                ?>
                                {{$contadorEnvios>1?'Envíos: ':'Envío: '}} {{ $pedido->base }} €
                                    <br>
                                @if($pedido->embalajes != 0.00)
                                    {{$contadorEmbalajes>1?'Embalajes: ':'Embalaje: '}} {{ $pedido->embalajes }} €

                                    <br>
                                @endif
                                {{$contadorCoberturas>1?'Coberturas: ':'Cobertura: '}} {{ $pedido->coberturas }} €
                                <br>
                                Gestión: {{ $pedido->gestion }} €
                                <br>
                                @if($pedido->descuento != 0)
                                    Descuento: {{ $pedido->descuento }} €
                                @endif

                            </td>
                            <td style="vertical-align: middle;font-size:1.0em;">
                                @if($pedido->estado)
                                    {{$pedido->metodo ? $pedido->metodo->descripcion :'' }}
                                @else
                                    Estado de pago no disponible
                                @endif
                            </td>
                            <td style="vertical-align: middle;font-size: 1.8em;">
                               <span class="texto-corporativo"><strong>{{ number_format($pedido->base + $pedido->gestion + $pedido->embalajes + $pedido->coberturas - $pedido->descuento, 2)}} €</strong></span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>


                </table>

                {{ $pedidos->links() }}

            </div>

        @else
            <div class="m-nomensajes col-xs-12" style="display: table;">
                <h3>No has realizado ningún pago todavía</h3>
                <p class="texto-gris">Por ahora no has realizado ningún pago.</p>
                <div class="text-center">
                    <a href="{{ route('formulario_envio') }}" class="btn-app btn-sm">Realiza un envío</a>
                </div>
            </div>

        @endif
        @if(isset($finalizado))
            <div id="l-modal-finalizacion">
                @include('web.partials.modales.lightbox-finalizacion')
            </div>

        @endif

    </div>
@endsection