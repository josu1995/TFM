@extends('layouts.web')
@section('title', 'Mensajes')

@section('content')
    <section id="wrapper_principal">
        <div class="row-fluid navegacion-home">
            <!-- Navegación -->
            @include('web.home.navegacion')
        </div>
        <div class="inner-perfil inner-msg">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12" style="margin-bottom: 15px;">
                        @if(count($mensajes))
                            <h1 class="title-seccion">Notificaciones</h1>
                            <div class="row">
                                <div class="m-mensajes">
                                    <table class="table-hover table-condensed text-left table-msg">
                                        <?php $nuevo_envio = null; ?>
                                        @foreach($mensajes->sortByDesc('created_at') as $mensaje)
                                            <?php $envio_id = $mensaje->envio_id; ?>

                                            <tr>
                                                <td>
                                                    <span class="msg-fecha text-nowrap"><i class="icon-fecha texto-corporativo"></i> &nbsp;{{ Carbon\Carbon::parse($mensaje->created_at )->format('d/m/Y H:i:m')}} </span>
                                                </td>
                                                <td>
                                                    <span class="msg-paquete msg-{{ $mensaje->estado->nombre }}"><i class="icon-{{ $mensaje->estado->nombre }}"></i> {{ $mensaje->texto }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $mensajes->links() }}
                                </div>
                            </div>

                        @else
                            <div class="m-nomensajes col-md-12" style="display: table;">
                                <h3>No tienes notificaciones pendientes</h3>
                                <p class="texto-gris">En estos momentos no tienes ninguna notificación. Recibirás mensajes al enviar paquetes</p>
                                <p class="no-mg"><a href="{{ route('formulario_envio') }}" class="btn-app no-mg">Realiza un envío</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
