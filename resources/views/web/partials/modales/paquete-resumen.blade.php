<li class="resumen-paquete paquete-pago">
  <div class="icono-paquete"><span class="icon-paquete texto-corporativo"></span></div>
  <div class="datos-paquete">
    <ul>
      <li>
        <p class="direccion-paquete">
          <i class="icon-punto texto-envio"></i>
          <span class="texto-oscuro">{{ $envio->puntoEntrega->nombre }} <strong>( {{ $envio->puntoEntrega->localidad->nombre }} )</strong></span>
        </p>
      </li>
      <li class="flecha-puntos"><span class="icon-siguiente texto-corporativo"></span></li>
      <li>
         <p class="direccion-paquete">
          <i class="icon-punto texto-recogida"></i>
          <span class="texto-oscuro">{{ $envio->puntoRecogida->nombre }} <strong>( {{ $envio->puntoRecogida->localidad->nombre }} )</strong></span>
        </p>
      </li>
    </ul>

    <div class="datos-dos">
      <p><i class="icon-peso texto-corporativo"></i>
      <strong>Peso:</strong>
      @if ($envio->paquete)
          {{ $envio->paquete->peso }}kg
      @endif
      </p>
      <p>
        <i class="icon-tamano texto-corporativo"></i>
        <strong>Dimensiones:</strong>
        @if ($envio->paquete)
            {{ $envio->paquete->alto }} x {{ $envio->paquete->ancho }} x {{ $envio->paquete->largo }} cm
        @endif
      </p>
      <p>
        <i class="icon-cobertura texto-corporativo"></i>
        <strong>Cobertura:</strong>
        {{ $envio->cobertura->descripcion }}
      </p>
      <div class="clear"></div>

    </div>
    <div class="datos-dos">
      <p class="contenido-paquete"><i class="icon-contenido texto-corporativo"></i> <strong>Contenido:</strong> <span>{{ $envio->descripcion }}</span></p>
      @if($envio->embalaje->precio != 0.00)
        <p>
          <i class="fas fa-shopping-bag texto-corporativo" aria-hidden="true"></i>
          <strong>Embalaje:</strong>
          {{ $envio->embalaje->descripcion }}
        </p>
      @endif
    </div>
  </div>
  @if($envio->estado->id != 2)
  <div class="pagar-mas-tarde">
    <form class="retrasarPagoForm" action="{{ route('retrasar_pago') }}" method="post">
        <button class="btn btn-default btn-xs" type="submit" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="Editar datos y pagar más tarde">
            <i class="fas fa-cart-plus" aria-hidden="true"></i>
        </button>
        <input type="hidden" name="codigo_envio" id="codigo_envio" value="{{ $envio->codigo }}">
        {{ csrf_field() }}
    </form>
  </div>
  @endif
  <div class="precio-paquete fondo-corporativo">
    <p>Precio:<span><strong>{{ round($precio->calcularPrecio($envio),2) }}€</strong></span></p>
  </div>
</li>
