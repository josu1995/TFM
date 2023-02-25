@extends('web.home.cuenta')

@section('perfil')
    <h1 class="title-seccion">Métodos de pago</h1>
    @if(count($metodosPago))
        <div class="row-xs">
            <div class="table-responsive table-flow">
                <table class="table table-striped ">
                    <thead>
                        <tr>
                            <td><i class="fas fa-credit-card" aria-hidden="true"></i> Tarjeta</td>
                            <td><i class="fas fa-user" aria-hidden="true"></i> Titular</td>
                            <td><i class="fas fa-calendar-alt" aria-hidden="true"></i> Caducidad</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($metodosPago as $metodo)
                      <tr id="{{ $metodo->id }}">
                          {{--<td style="position: absolute;left: 20px;padding: 5px;">--}}
                          {{--@if($metodo->tipoTarjeta->id == \App\Models\TipoTarjeta::VISA)--}}
                              {{--<img src="{{ asset('img/iconos-pagos/visa.png') }}" height="25px" style="position: absolute;left: 20px;">--}}
                          {{--@elseif($metodo->tipoTarjeta->id == \App\Models\TipoTarjeta::MASTER_CARD)--}}
                              {{--<img src="{{ asset('img/iconos-pagos/master-card.png') }}" height="25px" style="position: absolute;left: 20px;">--}}
                          {{--@endif--}}
                          {{--</td>--}}
                          <td>
                              @if($metodo->tipoTarjeta->id == \App\Models\TipoTarjeta::VISA)
                                <img src="{{ asset('img/iconos-pagos/visa.png') }}" height="25px">
                              @elseif($metodo->tipoTarjeta->id == \App\Models\TipoTarjeta::MASTER_CARD)
                                <img src="{{ asset('img/iconos-pagos/master-card.png') }}" height="25px">
                              @else
                                  {{ $metodo->tipoTarjeta->nombre }}
                              @endif
                          </td>
                          <td>{{ $metodo->titular }}</td>
                          <td>{{ substr($metodo->caducidad,2) }}/20{{ substr($metodo->caducidad,0,2) }}</td>
                          <td>
                              <ul class="list-unstyled list-inline no-mg">
                                  <li style="display: inline-block;">
                                      <button type="button" value="eliminar" class="btn btn-block btn-xs btn-danger btn-eliminar-pago" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                          <i class="fas fa-trash" aria-hidden="true"></i>
                                      </button>
                                  </li>
                              </ul>
                          </td>
                      </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <button type="button" id="anadirButton" class="btn btn-app"><i class="icon-anadir texto-inverso"></i> Añadir método de pago</button>
    @else
        <div class="m-nomensajes col-md-12" style="display: table;">
            <h3>No tienes métodos de pago</h3>
            <p class="texto-gris">Añade un nuevo método para realizar tus pagos de forma más rápida y sencilla.</p><br>
            <p class="no-mg"><a href="#" data-toggle="modal" data-target="#modalAnadir" class="btn-app no-mg">Añadir método de pago</a></p>
        </div>
    @endif

    {{--Modal de añadir tarjeta--}}
    <div class="modal fade" tabindex="-1" role="dialog" id="modalAnadir" aria-labelledby="modalAnadir" aria-hidden="true">
        <div class="modal-dialog small-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Verificar tarjeta</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning">
                        Vamos a proceder a realizar la verificación de tu tarjeta. No realizaremos ningún cobro en la operación.
                    </div>

                    <form action="{{ route('cuenta_verificar_tarjeta') }}" class="text-center">
                        <button type="submit" class="btn btn-app modalCenter">Continuar</button>
                    </form>

                </div>

            </div>
        </div>
    </div>

    {{--Modal de confirmacion--}}
    <div class="modal fade" tabindex="-1" role="dialog" id="modalEliminar" aria-labelledby="modalEliminar" aria-hidden="true">
        <div class="modal-dialog small-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Eliminar método de pago</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres eliminar este método de pago?
                </div>
                <form id="eliminar-form" action="" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <a type="button" class="btn btn-link" data-dismiss="modal">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('javascripts-footer')

<script type="text/javascript" defer>

    $(function() {
        $('#anadirButton').on('click', function() {
           $('#modalAnadir').modal();
        });

        $('.btn-eliminar-pago').on('click', function() {
            var route = '{!! route('cuenta_metodo_pago') !!}';
            $('#eliminar-form').prop('action', route+'/'+$(this).parents('tr').attr('id'));
            $('#modalEliminar').modal();
        });
    });

</script>

@endpush
