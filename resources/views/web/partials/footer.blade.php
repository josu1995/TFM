<footer class="footer">
    <div class="container">
        {{--
        <div class="row">
            @php
                $chunks = \App\Models\FooterLink::orderBy('orden', 'asc')->take(10)->get()->chunk(5);
            @endphp
            <div class="col-sm-4 col-md-3 col-xs-12 links">
                <h3 >Envíos nacionales</h3>
                @foreach($chunks[0] ?? [] as $link)
                    <p><a href="{{ url('/' . $link->slug) }}">{{ $link->titulo }}</a></p>
                @endforeach
            </div>
            <div class="col-sm-4 col-md-3 links hidden-xs mg-t-51">
                @if(count($chunks) > 1)
                    @foreach($chunks[1] as $link)
                        <p><a href="{{ url('/' . $link->slug) }}">{{ $link->titulo }}</a></p>
                    @endforeach
                @endif
            </div>
            <div class="col-sm-4 col-md-3 col-xs-12 links">
                <h3>Citystock</h3>
                <p><a href="{{ url('/ayuda') }}">Ayuda</a></p>
                <p><a href="{{ route('business_landing_index') }}">Empresas</a></p>
                <p><a href="{{ route('blog_get_index') }}">Blog</a></p>
                @php /*
                <p><a href="{{ route('drivers_landing_index') }}">Conductores</a></p>
                <p><a href="{{ route('stores_portada') }}">Stores</a></p>
                */ @endphp
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12 social-container">
                <h3>Síguenos</h3>
                <div class="social rrss">
                    <ul class="list-unstyled">
                        <li class="no-mg-l" data-wow-delay=".2s"><a href="https://www.facebook.com/transporter" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        <li data-wow-delay=".1s"><a href="https://twitter.com/transporter_es" target="_blank"><i class="fab fa-twitter"></i></a></li>
                    </ul>
                </div>
                <h3>Pagos admitidos</h3>
                <div class="pagos-admitidos">
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-paypal"></i>
                </div>
            </div>
        </div>
        --}}

        <div class="business-footer" style="padding:0">
            @if( ! in_array( Route::currentRouteName( ), [ 'tracking_index', 'muestra_inicio_ayuda', 'muestra_pagina_ayuda' ] ) )
            <div class="condiciones h-100"  style="float:unset; text-align:left;">
                <a class="mg-r-10" href="">Condiciones de uso</a>
                <a href="">Política de privacidad y cookies</a>
            </div>
            <div class="copyright-wrapper h-100">
                <p class="copyright">© 2022 <a href="{{env('APP_URL')}}" target="_blank"><strong>Citystock</strong></a>. Todos los derechos reservados</p>
            </div>
            @else
            <div class="copyright-wrapper h-100" style="display:flex !important; justify-content:center; align-items:center">
                <p class="copyright">© 2022 <a href="{{env('APP_URL')}}" target="_blank"><strong>Citystock</strong></a>. Todos los derechos reservados</p>
            </div>
            @endif
        </div>

    </div>

    <div id="cookies">
        <p>Citystock utiliza cookies para mejorar la experiencia de usuario y sus servicios. Si continúa navegando, consideramos que acepta su uso. Más información aquí <b><a href="">Política de Privacidad y Cookies</a></b> <a href="#" class="cookieClose"><span class="glyphicon glyphicon-remove"></span></a> </p>
    </div>

    @if (!Auth::user())
        <div id="l-login-registro">
            {{-- @include('auth.login-popup') --}}
            @include('business.partials.login-popup')
        </div>
    @endif
</footer>

@push('javascripts-footer')
    {{-- JavaScripts  --}}

    <script type="text/javascript" async defer>
        jQuery(document).ready(function(){
            if (leeCookie("mensajecookie")!="si") {
                jQuery("#cookies").fadeIn(200);
                $(document).on('click', function() {
                    setCookieState();
                });
            }

            setInterval('mueveSlide()',6000);

            jQuery('#menu-principal > .menu-item').click(function(e) {
                if (jQuery(window).width() < 466) {
                    e.preventDefault();
                }
            });

            $('.cookieClose').on('click', function() {
               ocultaCookie();
            });

        });

        function mueveSlide() {
            if (jQuery(document).scrollTop() < 50) {
                jQuery(".cinematic-right").click();
            }
        }
        $('#aceptar__cookies').on('click',function(){
            $('#cookies').fadeOut(200);
        });
        function leeCookie(nombre) {
            var cookies=document.cookie;
            if(!cookies) return false;

            var comienzo=cookies.indexOf(nombre);
            if(comienzo==-1) return false;
            comienzo=comienzo+nombre.length+1;
            cantidad=cookies.indexOf("; ", comienzo)-comienzo;

            if(cantidad<=0) cantidad=cookies.length;

            return cookies.substr(comienzo, cantidad);
        }

        function ocultaCookie() {
            jQuery("#cookies").fadeOut(200);
            setCookieState();
        }

        function setCookieState() {
            var date = new Date();
            date.setTime(date.getTime() + (60 * 24 * 60 * 60 * 1000));
            var expires = " expires=" + date.toGMTString();
            document.cookie="mensajecookie=si;"+expires+"; path=/";
        }

    </script>
@endpush
