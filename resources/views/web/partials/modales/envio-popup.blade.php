<div class="modal fade" id="l-envio-popup" tabindex="-1" role="dialog" aria-labelledby="modalLogin">

<!-- Modal para confirmar cambiar punto de envio -->
<div class="modal fade" id="modalEnvio">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

  <div class="modal-dialog m-punto-popup" role="document">
    <div class="modal-content">
      <div class="barra-superior fondo-corporativo"> <!-- Barra superior del popup con info del punto -->
        <div class="icono-iz fondo-inverso"><span class="icon-punto texto-corporativo"></span></div>
        <div class="ubicacion">
          <p class="nombre-punto texto-inverso">{{ $punto->nombre}} <span class="nombre-ciudad"><strong>({{ $punto->localidad->nombre }})</strong></span></p>
          <p class="direccion-punto texto-inverso"><i class="icon-ubicacion"></i> {{ $punto->direccion }}</p>
        </div>
        <span class="cerrar-popup icon-cerrar texto-inverso" data-dismiss="modal"></span>
      </div>
      <div class="m-mapa-popup">
        <div class="mapa no-pd">
            <iframe src="https://maps.google.com/maps?q={{$punto->latitud}},{{$punto->longitud}}&hl=es;z=19&amp;output=embed" width="100%" height="100%" frameborder="0"></iframe>
        </div>
        <div class="m-info-punto no-pd">
            @if(count($punto->imagenes))
                <div class="foto-punto">
                  <img src="{{ $punto->imagenes->last()->path }}" alt="{{$punto->imagenes->last()->descripcion}}">
              </div>
            @endif
              {{-- <span class="ver-foto icon-buscar fondo-corporativo texto-inverso"></span> --}}
          <div class="titulo-horario fondo-corporativo">
            <p class="texto-inverso"><i class="icon-horario"></i> Horario de <strong>apertura</strong></p>
          </div>
          <div class="horario-punto">
              @include('web.partials.modales.horario')
          </div>
        </div> <!-- Contenedor mapa popup -->
      </div>
      <div class="agregar-origen-destino">
        <ul>
            @if($punto->completo || $punto->cerrado)
                <li class="mensaje-oscuro mensaje text-left texto-inverso">
                    <p>Este punto se encuentra cerrado temporalmente</p>
                </li>
            @else
                @if (!$fin)
                    <li class="inicio">
                        <button type="button" class="texto-inverso fondo-envio" data-dismiss="modal" data-punto='envio' data-id="{{ $punto->id }}" value="{{ $punto->nombre }}">
                            <i class="icon-punto"></i> Añadir como store de <strong>origen</strong>
                        </button>
                    </li>
                @else
                    <li class="fin">
                        <button type="button" class="texto-inverso fondo-recogida" data-dismiss="modal" data-punto='recogida' data-id="{{ $punto->id }}" value="{{ $punto->nombre }}">
                            <i class="icon-punto" data-punto='envio'></i> Añadir como store de <strong>destino</strong>
                        </button>
                    </li>
                @endif
            @endif
        </ul>
      </div>
    </div>
  </div>
</div>
