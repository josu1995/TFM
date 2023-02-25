@extends('layouts.web')
@section('title', 'Panel de Usuario')
@section('content')
<section id="wrapper_principal">
    <div class="row-fluid navegacion-home">
        <!-- Navegación -->
        @include('web.home.navegacion')
    </div>
    <div class="inner-perfil">
        <div class="container">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-md-3 col-perfil-l">
                        <div class="barra-perfil">
                            <div class="perfil-imagen">
                                <div class="inner-perfil-imagen">
                                    <a href="{{ route('perfil_imagen') }}">
                                        <div class="m-cambiar-imagen">
                                            <span class="fas fa-camera"></span>
                                            <p class="cambiar-imagen text-center">Cambiar imagen de perfil</p>
                                        </div>
                                        @if($usuario->imagen)
                                            <div id="preview-imagen" style="background-image: url({{ $usuario->imagen->path }}); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                                        @else
                                            <img src="/img/commons/transporter-default-user.png" alt="usuario transporter">
                                        @endif
                                    </a>
                                </div>
                                <div class="info-perfil">
                                    <h3 class="texto-inverso fondo-corporativo">{{ $usuario->configuracion->nombre }} {{ Auth::user()->configuracion->apellidos && !empty(Auth::user()->configuracion->apellidos) ? substr(Auth::user()->configuracion->apellidos,0 , 1) . '.' : '' }}</h3>
                                    <ul class="editar-perfil">
                                        <li><a href="{{ route('perfil_usuario') }}">Ver perfil</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="box-perfil box-l mg-t-25">
                            <h4>Certificaciones</h4>
                            <div class="inner-box-perfil">
                                {{-- Detalle de validaciones actuales --}}
                                @if($usuario->configuracion->mail_certificado)
                                    <p>
                                        <i class="icon-anadido texto-envio mg-r-5"></i><strong class="texto-envio">email validado</strong>
                                    </p>
                                @else

                                    <p>
                                        <a href="{{ route('perfil_certificados') }}"><span class="icon-reportar texto-recogida mg-r-5"></span><strong>Valida tu email</strong></a>
                                    </p>
                                @endif
                                <hr>
                                @if($usuario->configuracion->movil_certificado)
                                    <p>
                                        <i class="icon-anadido texto-envio mg-r-5"></i> <strong class="texto-envio">móvil validado</strong>
                                    </p>
                                @else
                                    <p>
                                        <a href="{{ route('perfil_certificados') }}"><span class="icon-reportar texto-recogida mg-r-5"></span><strong>Valida tu móvil</strong></a>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="box-perfil box-l mg-t-25">
                            <h4>Enlaces rápidos</h4>
                            <div class="inner-box-perfil">
                                <ul>
                                    <li><a href="{{ route('perfil_usuario') }}">Datos de usuario</a></li>
                                    <li><a class="text-nowrap" href="{{ route('perfil_metodo_pago') }}">Métodos de pago</a></li>
                                    <li><a href="{{ route('perfil_password') }}">Cambio de contraseña</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">

                         <div class="box-perfil">
                            <h4>Notificaciones</h4>
                            <div class="inner-box-perfil">
                                @if(count($mensajes))
                                    <table class="table-hover table-condensed text-left">
                                        <?php $nuevo_envio = null; ?>
                                    @foreach($mensajes->sortByDesc('created_at') as $mensaje)
                                        <?php $envio_id = $mensaje->envio_id; ?>

                                        <tr>
                                            <td>
                                                <span class="msg-fecha">{{ Carbon\Carbon::parse($mensaje->created_at )->format('d/m/Y H:i:s')}}</span>
                                            </td>
                                            <td>
                                                <span class="msg-paquete msg-{{ $mensaje->estado->nombre }}"><i class="icon-{{ $mensaje->estado->nombre }}"></i> {{ $mensaje->texto }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </table>
                                @else
                                    <p>No tienes ninguna notificación nueva</p>
                                @endif
                            </div>
                        </div>
                            {{ $mensajes->links() }}

                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($policy))
        <div id="l-login-registro">
            @include('web.partials.modales.condiciones-popup')
        </div>
    @endif

</section>
@endsection
