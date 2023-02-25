<div class="row">
    <div class="datos-paquete col-xs-12">
        <div class="icono-paquete col-xs-12">
            {{--{!! QrCode::size(150)->generate($pedido->identificador) !!}--}}
            <p class="qr no-mg" id="{{$pedido -> identificador}}">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->backgroundColor(245,245,245)->generate($pedido->identificador)) !!} ">
            </p>
        </div>
        <div class="col-xs-12 mg-t-15">
            <ul class="list-unstyled list-inline">
                <li>
                    <i class="texto-corporativo fas fa-calendar-alt" aria-hidden="true"></i>
                    <span>{{ date('d/m/Y', strtotime($pedido->created_at)) }}</span>
                </li>
                <li>
                    <i class="texto-corporativo fas fa-paper-plane-o" aria-hidden="true"></i>
                    <span><strong>{{ $pedido->identificador }}</strong></span>
                </li>
                <li class="right" data-toggle="tooltip" title="Pago realizado con {{ $pedido->metodo->nombre == 'paypal' ? 'paypal' : 'tarjeta' }}">
                    <i class="fas fa-check-circle texto-envio" aria-hidden="true"></i>
                    <span>{{ $pedido->estado ? $pedido->estado->descripcion : 'Estado de pago no disponible'}}</span>
                </li>
            </ul>
            <ul class="list-unstyled list-inline">
                <li>Envío: {{ $pedido->base }}€</li>
                @if($pedido->embalajes != 0.00)
                    <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                    <li>Embalaje: {{ $pedido->embalajes }}€</li>
                @endif
                <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                <li>Cobertura: {{ $pedido->coberturas }}€</li>
                <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                <li>Gestión: {{ $pedido->gestion }}€</li>
                <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                @if($pedido->descuento != 0)
                    <li>Descuento: {{ $pedido->descuento }}€</li>
                    <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                @endif
                <li>Total: <strong>{{ number_format($pedido->base + $pedido->gestion + $pedido->embalajes + $pedido->coberturas - $pedido->descuento, 2)}}</strong>€</li>
            </ul>
            <button class="btn-app btn-sm mg-t-25 mg-b-10 btn-pagos"><i class="icon-ver-perfil"></i> Ver paquetes</button>
        </div>
        <div class="col-xs-12 g-pagos-paquetes">
            <h4><strong>Lista de envíos</strong></h4>
            @foreach ($pedido->envios as $envio)
                <div class="table-bordered pd-15 mg-b-15">
                    <ul class="list-inline">
                        <li>
                            <i class="icon-punto texto-envio" aria-hidden="true"></i>
                            {{ $envio->puntoEntrega->nombre}} ({{ $envio->puntoEntrega->localidad->nombre }})
                        </li>
                        <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
                        <li>
                            <i class="icon-punto texto-recogida" aria-hidden="true"></i>
                            {{ $envio->puntoRecogida->nombre}} ({{ $envio->puntoRecogida->localidad->nombre }})
                        </li>
                    </ul>

                    <ul class="list-inline list-unstyled">
                        <li>
                            <i class="texto-corporativo fas fa-user" aria-hidden="true"></i>
                            {{ $envio->destinatario->nombre}} {{ $envio->destinatario->apellidos}}
                        </li>
                        @if($envio->destinatario->telefono)
                        <li>
                            <i class="texto-corporativo fas fa-phone" aria-hidden="true"></i>
                            {{ $envio->destinatario->telefono}}
                        </li>
                        @endif
                        <li>
                            <i class="texto-corporativo fas fa-envelope" aria-hidden="true"></i>
                            {{ $envio->destinatario->email}}
                        </li>
                        <li>
                            <i class="texto-corporativo fas fa-barcode" aria-hidden="true"></i>
                            <strong>{{ $envio->localizador ?? 'Sin localizador todavía' }}</strong>
                        </li>
                    </ul>
                    <ul class="list-unstyled list-inline">
                        <li>
                            <i class="texto-corporativo fas fa-square" aria-hidden="true"></i>
                            {{ $envio->descripcion }}
                        </li>
                        @if($envio->paquete)
                            <li>
                                <i class="texto-corporativo fas fa-archive" aria-hidden="true"></i>
                                {{ $envio->paquete->alto }} x {{ $envio->paquete->ancho }} x {{ $envio->paquete->largo }} {{ $envio->paquete->peso }} kg
                            </li>
                        @endif
                    </ul>
                </div>
            @endforeach
        </div>
        </div>
</div>