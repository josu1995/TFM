@if (Auth::user())
    @if($route != 'muestra_pagina_ayuda' && $route != 'muestra_categoria' && $route != 'drivers_muestra_pagina_ayuda' && $route != 'drivers_muestra_categoria')
    <li class="pd-15"><a class="guia-ayuda" href="{{ route('muestra_categoria', ['slug' => 'ayuda']) }}">Ayuda</a></li>
    @endif
    @if($route == 'muestra_inicio_ayuda' ?? $route == 'muestra_pagina_ayuda' )
    <li class="pd-15">
        <a href="{{ route('inicio') }}" class="btn btn-corporativo" style="color:white">
            Volver a Citystock&nbsp&nbsp<i class="fas fa-chevron-right"></i>
        </a>
    </li>
    @endif
    <li class="dropdown header-user pd-15">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">

            {{ Auth::user()->configuracion->nombre }} {{ Auth::user()->configuracion->apellidos && !empty(Auth::user()->configuracion->apellidos) ? substr(Auth::user()->configuracion->apellidos,0 , 1) . '.' : '' }}
            @if(Auth::user()->imagen)
                <img class="img-circle header-user-img" src="{{Auth::user()->imagen->path}}" alt="usuario transporter" width="32px">
            @else
                <img class="img-circle header-user-img" src="/img/commons/transporter-default-user.png" alt="usuario transporter" width="32px">
            @endif

            <span class="caret header-user-caret"></span>
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
                    <h4><strong>{{ Auth::user()->configuracion->nombre }} {{ Auth::user()->configuracion->apellidos && !empty(Auth::user()->configuracion->apellidos) ? substr(Auth::user()->configuracion->apellidos,0 , 1) . '.' : '' }}</strong></h4>
                </div>
            </div>
            <ul class="enlaces-menu">
                <li>
                    <a href="{{ route('inicio') }}"><i class="icon-panel"></i>Panel de usuario</a>
                </li>
                <li>
                    <span>
                        <a href="{{ $pendientes ? route('envios_pendientes') : route('envios') }}">
                            <i class="icon-envios"></i>Envíos
                            @if($pendientes)
                                <span  href="{{ route('envios_pendientes') }}" class="badge badge-success pull-right mg-t-2">
                                    {{ $pendientes }}
                                </span>
                            @endif
                        </a>
                    </span>
                </li>
                <li>
                    <a href="{{ route('mensajes') }}">
                        <i class="icon-contacto"></i>Notificaciones @if($noLeidos)<span class="badge badge-success pull-right mg-t-2">{{$noLeidos}}</span>@endif
                    </a>
                </li>
                <li><a href="{{ route('perfil_usuario') }}"><i class="icon-usuario"></i>Perfil</a></li>
                <li><a href="{{ route('cuenta_usuario') }}"><i class="icon-pagos"></i>Cuenta</a></li>
                <li><a href="{{ url('/logout') }}"><i class="fas fa-sign-out-alt"></i>Salir</a></li>
            </ul>
        </div>
    </li>
@else
    @if($route != 'muestra_pagina_ayuda' && $route != 'muestra_categoria' && $route != 'drivers_muestra_pagina_ayuda' && $route != 'drivers_muestra_categoria')
        <li class="pd-15"><a class="guia-ayuda" href="{{ route('muestra_categoria', ['slug' => 'ayuda']) }}">Ayuda</a></li>
    @endif
    <li class="pd-15"><a href="/">Inicio</a></li>
    <li class="pd-15"><a href="#" class="link-sesion" data-toggle="modal" data-target="#modalLogin">Iniciar sesión</a></li>
    <li class="pd-15"><a href="{{ route('business_register') }}" class="link-sesion">Registrarse</a></li>
    <li class="pd-15"><a href="{{ route('blog_get_index') }}" class="link-sesion">Blog</a></li>
@endif