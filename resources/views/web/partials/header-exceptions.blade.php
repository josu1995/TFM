@if (Auth::user())
<div id="menu_mobile">
    <div id="menu_body">
        <div class="body--menu">
            <div class="user--menu">
                <div class="user--imagen">
                    <figure class="pull-left">
                      @if(Auth::user()->imagen)
                        <img src="{{Auth::user()->imagen->path}}" alt="usuario transporter">
                      @else
                        <img src="/img/commons/transporter-default-user.png" alt="usuario transporter">
                      @endif
                    </figure>
                </div>
                <div class="user--datos">
                    <div class="datos--parte fondo-inverso">
                        <h3><strong>{{ Auth::user()->configuracion->nombre}}</strong></h3>
                        <span class="total--mensajes texto-corporativo">Mensajes <a href="{{ route('mensajes') }}"><span class='badge badge-success'><?php echo (count(Auth::user()->mensajes)); ?></span></a></span>
                    </div>
                    <div class="datos--parte">
                        <ul>
                            <li><a href="{{ route('perfil_usuario') }}"><i class="icon-ver-perfil"></i> Ver perfil</a></li>
                            <li><a href="{{ route('inicio') }}"><i class="icon-editar-perfil"></i> Panel de usuario</a></li>
                        </ul>
                    </div>
                </div>
                </div>
            </div>
            <div class="scroll-menu">
                <div class="scroll-content">
                    <div class="links--menu submenu">
                        <ul class="enlaces-menu">
                            <li><a href="{{ route('envios') }}"><i class="icon-envios"></i>Envíos</a></li>
                            <li><a href="{{ route('viajes') }}"><i class="icon-punto"></i>Viajes</a></li>
                            <li><a href="{{ route('mensajes') }}"><i class="icon-contacto"></i>Mensajes</a></li>
                            <li><a href="{{ route('alertas') }}"><i class="fas fa-bell"></i>Alertas</a></li>
{{--                            <li><a href="{{ route('opiniones') }}"><i class="icon-opiniones"></i>Opiniones</a></li>--}}
                            <li><a href="{{ route('resumen_pedidos') }}"><i class="icon-pagos"></i>Pagos</a></li>
                            <li><a href="{{ url('/ayuda') }}"><i class="icon-preguntas"></i>Ayuda</a></li>
                            <li><a href="{{ url('/logout') }}"><i class="icon-desconectarse"></i>Salir</a></li>
                          </ul>
                    </div>
                    <div class="btns--enviar-trans">
                        <a href="{{ route('formulario_envio') }}" class="btn btn-app">Enviar paquete</a>
                        <a href="{{ route('drivers_buscar_viaje') }}" class="btn btn-app">Viajar</a>
                    </div>
                </div>
            </div>
    </div>
</div>
@else
    <div id="menu_mobile">
        <div id="menu_body">
            <a class="navbar-brand logo--nouser" href="{{ url('/') }}">
                <img src="{{asset('img/identidad/citystock-white-logo.png') }}" alt="transporter logo" width="257" height="66">
            </a>
            <div class="links--menu links-menu--nouser submenu">
                <ul class="enlaces-menu">
                    <li><a href="{{ url('/login') }}"><i class="icon-usuario"></i> Iniciar sesión</a></li>
                    <li><a href="{{ url('/registro') }}"><i class="icon-contrasena"></i> Registrarse</a></li>
                    <li><a href="{{ url('/ayuda') }}"><i class="icon-preguntas"></i>Ayuda</a></li>
                </ul>
                <div class="btns--nouser">
                    <a href="{{ route('formulario_envio') }}" class="btn btn-app">Enviar paquete</a>
                    <a href="{{ route('drivers_buscar_viaje') }}" class="btn btn-app">Viajar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<header>
    <div class="inner-header">
        <div class="container">
            <span class="burger_icon icon-burger-menu col-xs-3"></span>
            <div class="logo pull-left col-md-5 col-xs-9">
                 <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{asset('img/identidad/citystock-white-logo.png') }}" alt="transporter logo" width="257" height="66" title="">
                </a>
            </div>
            <nav class="pull-right">
                <ul class="list-unstyled">
                    @if (Auth::user())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="icon-usuario"></i> Panel de usuario <span class="caret"></span>
                            </a>

                                <div class="dropdown-menu submenu" role="menu">
                                    <div class="submenu-datos">
                                        <figure class="pull-left">
                                            @if(Auth::user()->imagen)
                                                <img src="{{Auth::user()->imagen->path}}" alt="usuario transporter">
                                            @else
                                                <img src="/img/commons/transporter-default-user.png" alt="usuario transporter">
                                            @endif
                                        </figure>
                                        <div class="nombre-usuario">
                                            <h4><strong>{{ Auth::user()->configuracion->nombre}}</strong></h4>
                                            <ul>
                                                <li><a href="{{ route('perfil_usuario') }}">Ver perfil</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <ul class="enlaces-menu">
                                        <li><a href="{{ route('inicio') }}"><i class="icon-panel"></i>Panel de usuario</a></li>
                                        <li><a href="{{ route('envios') }}"><i class="icon-envios"></i>Envíos</a></li>
                                        <li><a href="{{ route('viajes') }}"><i class="icon-punto"></i>Viajes</a></li>
                                        <li><a href="{{ route('mensajes') }}"><i class="icon-contacto"></i>Mensajes</a></li>
                                        <li><a href="{{ route('alertas') }}"><i class="fas fa-bell"></i>Alertas</a></li>
                                        {{--<li><a href="{{ route('opiniones') }}"><i class="icon-opiniones"></i>Opiniones</a></li>--}}
                                        <li><a href="{{ route('resumen_pedidos') }}"><i class="icon-pagos"></i>Pagos</a></li>
                                        <li><a href="{{ route('formulario_envio') }}"><i class="icon-paquete-anadido"></i>Crear envío</a></li>
                                        <li><a href="{{ route('drivers_buscar_viaje') }}"><i class="icon-transporte"></i>Viajar</a></li>
                                        <li><a href="{{ url('/logout') }}"><i class="fas fa-sign-out-alt"></i>Salir</a></li>
                                    </ul>
                                </div>
                        </li>
                    @endif
                    <li><a href="{{ route('muestra_categoria', ['slug' => 'ayuda']) }}">Ayuda</a></li>
                </ul>
            </nav>

        </div>
    </div>

</header>