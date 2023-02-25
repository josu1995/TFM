{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularViaje')

@if($enviosCount)
    <div id="ver_paquetes" class="mensaje-oscuro mensaje text-left texto-inverso lista-paquetes-mensaje">
        @if($enviosCount < 1)
            <p>Este punto no tiene actualmente ningún envío para transportar</p>
        @else
            <p>Paquetes añadidos a tu viaje: <span class=""><strong>{{ $seleccionadosCount }}</strong></span></p>
        @endif
    </div>

    <ul>
        @foreach($envios as $key => $envio)
            <li class="resumen-paquete">
                <div class="icono-paquete"><span class="icon-paquete texto-corporativo"></span></div>
                <div class="datos-paquete">
                    <div>
                        @if($puntoOrigen->id !=  $envio->puntoEntrega->id && $envio->puntoEntrega->localidad->id != $puntoOrigen->localidad->id)
                            <p class="direccion-paquete"><i class="icon-punto texto-envio"></i> <strong>Origen:</strong> <span class="">{{$puntoOrigen->nombre}} <span class="">({{$puntoOrigen->localidad->nombre}})</span></span></p>
                        @else
                           <p class="direccion-paquete"><i class="icon-punto texto-envio"></i> <strong>Origen:</strong> <span class="">{{$envio->puntoEntrega->nombre}} <span class="">({{$envio->puntoEntrega->localidad->nombre}})</span></span></p>
                        @endif
                        @if(isset($envio->puntoIntermedio))
                                <p class="direccion-paquete"><i class="icon-punto texto-recogida"></i> <strong>Destino:</strong> <span class="">{{$envio->puntoIntermedio->nombre}} <span class="">({{$envio->puntoIntermedio->localidad->nombre}})</span></span></p>
                        {{--@elseif($puntoDestino->id !=  $envio->puntoRecogida->id && $envio->puntoRecogida->localidad->id != $puntoDestino->localidad->id)--}}
                            {{--<p class="direccion-paquete"><i class="icon-punto texto-recogida"></i> <strong>Destino:</strong> <span class="">{{$puntoDestino->nombre}} <span class="">({{$puntoDestino->localidad->nombre}})</span></span></p>--}}
                        @else
                            <p class="direccion-paquete"><i class="icon-punto texto-recogida"></i> <strong>Destino:</strong> <span class="">{{$envio->puntoRecogida->nombre}} <span class="">({{$envio->puntoRecogida->localidad->nombre}})</span></span></p>
                        @endif
                        <div class="datos-dos">
                            @if($envio->paquete)
                                <p><i class="icon-peso texto-corporativo"></i> <strong>Peso:</strong> <span>{{ $envio->paquete->peso }}</span></p>
                                <p><i class="icon-tamano texto-corporativo"></i> <strong>Dimensiones:</strong> <span class="alto">{{ $envio->paquete->alto }}</span> x <span class="ancho">{{ $envio->paquete->ancho }}</span> x <span class="largo">{{ $envio->paquete->largo }}</span>cm</p>
                            @endif
                            <div class="clear"></div>
                        </div>
                    </div>
              </div>
              <div class="ahorro-paquete">
                  @if(isset($envio->puntoIntermedio))
                      <p class="texto-corporativo text-center pull-left mg-r-45">Ahorro:<span class="block text-lg"><strong>{{ $precio->calcularEnvio($envio, $puntoOrigen, $envio->puntoIntermedio )}}€</strong></span></p>
                  @else
                    <p class="texto-corporativo text-center pull-left mg-r-45">Ahorro:<span class="block text-lg"><strong>{{ $precio->calcularEnvio($envio, $puntoOrigen, $envio->puntoRecogida )}}€</strong></span></p>
                  @endif
                  @if($envios->first() == $envio)
                    <input type="hidden" name="envio_codigo" value="{{ $envio->codigo }}">
                    <input type="hidden" name="envio_inicio" value="{{ $puntoOrigen->id }}">
                      @if(isset($envio->puntoIntermedio))
                        <input type="hidden" name="envio_fin" value="{{ $envio->puntoIntermedio->id }}">
                      @else
                        <input type="hidden" name="envio_fin" value="{{ $envio->puntoRecogida->id }}">
                      @endif
                    {!! csrf_field() !!}
                    <button class="btn-app add-paquete"><i class="icon-anadir texto-inverso"></i></button>
                @else
                    <button class="btn" disabled><i class="icon-anadir texto-inverso"></i></button>
                @endif
              </div>
            </li>
        @endforeach
    </ul>
@else
    <ul>
        <li>
            <p class="text-center mg-t-45 mg-b-45">
                No hay envíos para seleccionar en este punto
            </p>
        </li>
    </ul>
@endif
