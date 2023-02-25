{{-- Inyección de servicio para calcular el precio dinámicamente --}}
@inject('precio', 'App\Services\CalcularViaje')

<div class="modal fade" id="l-punto-popup" tabindex="-1" role="dialog" aria-labelledby="modalPunto" data-envios="{{ $enviosCount }}">
  <div class="modal-dialog m-punto-popup" role="document">
    <div class="modal-content">
      <div class="barra-superior fondo-corporativo"> <!-- Barra superior del popup con info del punto -->
        <div class="icono-iz fondo-inverso"><span class="icon-punto texto-corporativo"></span></div>
        <div class="ubicacion">
          <p class="nombre-punto texto-inverso">{{ $puntoOrigen->nombre}} <span class="nombre-ciudad"><strong>({{ $puntoOrigen->localidad->nombre }})</strong></span></p>
          <p class="direccion-punto texto-inverso"><i class="icon-ubicacion"></i> {{ $puntoOrigen->direccion }}</p>
        </div>
        <span class="cerrar-popup icon-cerrar texto-inverso" data-dismiss="modal"></span>
      </div>
      <div class="m-mapa-popup">
        <div class="mapa no-pd">
          <iframe src = "https://maps.google.com/maps?q={{$puntoOrigen->latitud}},{{$puntoOrigen->longitud}}&hl=es;z=14&amp;output=embed" width="100%" height="100%" frameborder="0"></iframe>
        </div>
        <div class="m-info-punto no-pd">

            @if(count($puntoOrigen->imagenes))
                <div class="foto-punto">
                  <img src="{{ $puntoOrigen->imagenes->last()->path }}" alt="{{$puntoOrigen->imagenes->last()->descripcion}}">
              </div>
            @endif
          <div class="titulo-horario fondo-corporativo">
            <p class="texto-inverso"><i class="icon-horario"></i> Horario de <strong>apertura</strong></p>
          </div>
          <div class="horario-punto">
              @include('web.partials.modales.horario')
          </div>
        </div> <!-- Contenedor mapa popup -->
      </div>
        @if($puntoOrigen->cerrado)
            <div class="mensaje-oscuro mensaje text-left texto-inverso">
                <p>Este punto se encuentra cerrado temporalmente</p>
            </div>
        @else
      @if($enviosCount < 1)
        <div id="ver_paquetes" class="mensaje-oscuro mensaje text-left texto-inverso pd-t-15">
            <p>Este punto no tiene actualmente ningún envío para transportar</p>
        </div>
      @else
        <div id="ver_paquetes" class="show-paquetes-viaje-button mensaje text-left texto-inverso">
            <p>Ver paquetes disponibles: <span class="enviosRestantes"><strong>{{ $enviosCount }}</strong></span> <i class="fas fa-chevron-up icono-negro" aria-hidden="true"></i></p>
        </div>
      @endif
      <div class="paquetes-punto lista-paquetes--modal">
        <div class="lista-paquetes--cerrar">
          <span class="cerrar-popup icon-cerrar texto-inverso" id="lista-paquetes--cerrar"></span>
        </div>
          <div class="spinner hidden">
              <div id="floatingBarsG">
                  <i class="fas fa-sync-alt fa-spin opinionesSpinner"></i>
              </div>
          </div>

                    <div class="lista-paquetes paquetes-scroll" data-mcs-theme="dark">
                        <div id="lista-paquetes">
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
                                                @elseif($puntoDestino->id !=  $envio->puntoRecogida->id && $envio->puntoRecogida->localidad->id != $puntoDestino->localidad->id)
                                                    <p class="direccion-paquete"><i class="icon-punto texto-recogida"></i> <strong>Destino:</strong> <span class="">{{$puntoDestino->nombre}} <span class="">({{$puntoDestino->localidad->nombre}})</span></span></p>
                                                @else
                                                    <p class="direccion-paquete"><i class="icon-punto texto-recogida"></i> <strong>Destino:</strong> <span class="">{{$envio->puntoRecogida->nombre}} <span class="">({{$envio->puntoRecogida->localidad->nombre}})</span></span></p>
                                                @endif

                                          <div class="datos-dos">
                                              @if($envio->paquete)
                                                  <p><i class="icon-peso texto-corporativo"></i> <strong>Peso:</strong> <span>{{ $envio->paquete->peso }} kg</span></p>
                                                  <p><i class="icon-tamano texto-corporativo"></i> <strong>Dimensiones:</strong> {{ $envio->paquete->dimensionesSinDecimalesString() }}</p>
                                              @endif
                                              <div class="clear"></div>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="ahorro-paquete">
                                      @if(isset($envio->puntoIntermedio))
                                          <p class="texto-corporativo text-center pull-left mg-r-45">Ahorro:<span class="block text-lg"><strong>{{ $precio->calcularEnvio($envio, $puntoOrigen, $envio->puntoIntermedio )}}€</strong></span></p>
                                      @elseif($puntoDestino->id !=  $envio->puntoRecogida->id && $envio->puntoRecogida->localidad->id != $puntoDestino->localidad->id)
                                          <p class="texto-corporativo text-center pull-left mg-r-45">Ahorro:<span class="block text-lg"><strong>{{ $precio->calcularEnvio($envio, $puntoOrigen, $puntoDestino )}}€</strong></span></p>
                                      @else
                                          <p class="texto-corporativo text-center pull-left mg-r-45">Ahorro:<span class="block text-lg"><strong>{{ $precio->calcularEnvio($envio, $puntoOrigen, $envio->puntoRecogida )}}€</strong></span></p>
                                      @endif
                                          @if($envios->first() == $envio)
                                              <input type="hidden" name="envio_codigo" value="{{ $envio->codigo }}">
                                              <input type="hidden" name="envio_inicio" value="{{ $puntoOrigen->id }}">
                                              @if(isset($envio->puntoIntermedio))
                                                  <input type="hidden" name="envio_fin" value="{{ $envio->puntoIntermedio->id }}">
                                              @elseif($puntoDestino->id !=  $envio->puntoRecogida->id && $envio->puntoRecogida->localidad->id != $puntoDestino->localidad->id)
                                                  <input type="hidden" name="envio_fin" value="{{ $puntoDestino->id }}">
                                              @else
                                                <input type="hidden" name="envio_fin" value="{{ $envio->puntoRecogida->id }}">
                                              @endif
                                              {!! csrf_field() !!}
                                              <button class="btn-app add-paquete"><i class="icon-anadir texto-inverso"></i></button>
                                          @else
                                              <button class="btn add-paquete" disabled><i class="icon-anadir texto-inverso"></i></button>
                                          @endif
                                        </div>
                                      </li>
                          @endforeach
                  </ul>

              </div>
          </div> <!-- Paquetes para añadir -->
      </div>
        @endif

    </div>
  </div>
</div>

@push('javascripts-footer')
<script>
    $(document).ready(function() {
        // --
        // Abrir contenedor de añadir paquetes
        // --
        var verPaquetes = $('#ver_paquetes');
        var bloquePaquetes = $('.paquetes-punto .lista-paquetes');

        verPaquetes.click(function(e) {
            bloquePaquetes.toggle();
        });
    });
</script>
@endpush