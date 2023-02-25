<div class="modal fade" id="l-envio-popup" tabindex="-1" role="dialog" aria-labelledby="modalLogin">
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
          <iframe src = "https://maps.google.com/maps?q={{$punto->latitud}},{{$punto->longitud}}&hl=es;z=14&amp;output=embed" width="100%" height="100%" frameborder="0"></iframe>
        </div>
        <div class="m-info-punto no-pd">
            @if(count($punto->imagenes))
                <div class="foto-punto">
                  <img src="{{ $punto->imagenes->last()->path }}" alt="{{$punto->imagenes->last()->descripcion}}">
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
      @if($punto->cerrado)
        <div class="mensaje-oscuro mensaje text-left texto-inverso">
          <p>Este punto se encuentra cerrado temporalmente</p>
        </div>
      @else
        <div class="agregar-origen-destino viaje">
            <button type="button" class="fondo-recogida btn btn-block btn-flat" data-dismiss="modal" data-punto='envio' data-id="{{ $punto->id }}" value="{{ $punto->nombre }}"><i class="icon-punto"></i> Seleccionar punto <strong>{{ $punto->nombre }}</strong></button>
        </div>
      @endif
    </div>
  </div>
</div>
