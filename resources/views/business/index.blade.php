<!DOCTYPE html>
<html class="h-100">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>{{ Lang::get('business.meta.title') }}</title>

    <meta name="description" content="{{ Lang::get('business.meta.description') }}">

    <meta name="theme-color" content="#ee8026" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="">
    <meta name="twitter:creator" content="">
    <meta name="twitter:title" content="{{ Lang::get('business.meta.title') }}">
    <meta name="twitter:description" content="{{ Lang::get('business.meta.description') }}">
    <meta name="twitter:image" content="{{ asset('img/redes/citystock-redes.jpg') }}">

    <meta property="og:url" content="{{ route('business_landing_index') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ Lang::get('business.meta.title') }}" />
    <meta property="og:description" content="{{ Lang::get('business.meta.description') }}" />
    <meta property="og:image" content="{{asset('img/redes/citystock-redes.jpg')}}">

    <link rel="shortcut icon" type="image/ico" href="{{asset('img/identidad/favicon.ico')}}" />

    <style>
        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTa-j2U0lmluP9RWlSytm3ho.woff2) format('woff2');
            unicode-range: U+0460-052F, U+20B4, U+2DE0-2DFF, U+A640-A69F
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTZX5f-9o1vgP2EXwfjgl7AY.woff2) format('woff2');
            unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTRWV49_lSm1NYrwo-zkhivY.woff2) format('woff2');
            unicode-range: U+1F00-1FFF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTaaRobkAwv3vxw3jMhVENGA.woff2) format('woff2');
            unicode-range: U+0370-03FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTf8zf_FOSsgRmwsS7Aa9k2w.woff2) format('woff2');
            unicode-range: U+0102-0103, U+1EA0-1EF9, U+20AB
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTT0LW-43aMEzIO6XUTLjad8.woff2) format('woff2');
            unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 300;
            src: local('Open Sans Light'), local('OpenSans-Light'), url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTegdm0LZdjqr5-oayXSOefg.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/K88pR3goAWT7BTt32Z01mxJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+0460-052F, U+20B4, U+2DE0-2DFF, U+A640-A69F
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/RjgO7rYTmqiVp7vzi-Q5URJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/LWCjsQkB6EMdfHrEVqA1KRJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+1F00-1FFF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/xozscpT2726on7jbcb_pAhJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+0370-03FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/59ZRklaO5bWGqF5A9baEERJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+0102-0103, U+1EA0-1EF9, U+20AB
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/u-WUoqrET9fUeobQW7jkRRJtnKITppOI_IvcXXDNrsc.woff2) format('woff2');
            unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: local('Open Sans'), local('OpenSans'), url(https://fonts.gstatic.com/s/opensans/v13/cJZKeOuBrn4kERxqtaUH3VtXRa8TVwTICgirnJhmVJw.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzK-j2U0lmluP9RWlSytm3ho.woff2) format('woff2');
            unicode-range: U+0460-052F, U+20B4, U+2DE0-2DFF, U+A640-A69F
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzJX5f-9o1vgP2EXwfjgl7AY.woff2) format('woff2');
            unicode-range: U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzBWV49_lSm1NYrwo-zkhivY.woff2) format('woff2');
            unicode-range: U+1F00-1FFF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzKaRobkAwv3vxw3jMhVENGA.woff2) format('woff2');
            unicode-range: U+0370-03FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzP8zf_FOSsgRmwsS7Aa9k2w.woff2) format('woff2');
            unicode-range: U+0102-0103, U+1EA0-1EF9, U+20AB
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzD0LW-43aMEzIO6XUTLjad8.woff2) format('woff2');
            unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF
        }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: local('Open Sans Bold'), local('OpenSans-Bold'), url(https://fonts.gstatic.com/s/opensans/v13/k3k702ZOKiLJc3WVjuplzOgdm0LZdjqr5-oayXSOefg.woff2) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215
        }
    </style>
    <link rel="stylesheet" href="{{ mix('css/citystock.css') }}" media="screen" title="no title" charset="utf-8">

</head>

<body class="h-100">

    <div class="business-hero bg-transparent relative overflow-hidden">
        <video autoplay muted loop class="fullscreen">
            
        </video>
        <div class="business-header desktop-header">
            <a href="{{ route('business_landing_index') }}">
                <img class="business-logo" src="{{ asset('img/business/citystock-white-logo.png') }}" />
            </a>
            <div class="header-separator"></div>
            <div class="header-link">
                <a href="#" data-toggle="modal" data-target="#modalLogin" class="link">Iniciar sesión</a>
            </div>
            <div class="header-link">
                <a href="{{ route('business_register') }}" class="btn btn-corporativo">Registrarse&nbsp&nbsp<i
                        class="fas fa-chevron-right"></i></a>
            </div>
        </div>

        <div class="mobile-header-container">
            <div class="mobile-header business">
                <span>
                    <a>
                        <img width="160" class="business-logo white"
                            src="{{ asset('img/business/citystock-white-logo.png') }}" />
                        <img width="160" class="business-logo color"
                            src="{{ asset('img/business/citystock-white-color.png') }}" />
                    </a>
                </span>
                <span class="header-separator"></span>
                <span>
                    @include('partials.hamburger', ['class' => 'pull-right', 'function' => ''])
                </span>
            </div>
            <div class="mobile-header-content business">
                <div class="link">
                    <a href="#" data-toggle="modal" data-target="#modalLogin">Iniciar sesión
                        <i class="fas fa-arrow-right pull-right"></i></a>
                </div>
                <div class="bottom-button pd-20 text-center">
                    <a href="{{ route('business_register') }}" class="btn btn-corporativo">Registrarse</a>
                </div>
            </div>
        </div>

        <div class="content">
            <div>
                <h1 class="titulo">APRENDE IDIOMAS FÁCILMENTE</h1>
                <h2 class="subtitulo">Variedad de idiomas a tu alcance</h2>
                <a href="{{ route('business_register') }}" class="btn btn-corporativo"
                    style="padding: 1rem 1.5rem; font-weight: bold; font-size: 1.6rem;">
                    Pruébalo gratis<i class="fas fa-chevron-right" style="margin-left:1rem"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="business-content">

        <div class="section first">
            <div class="wrapper">
                <div class="col-text col-left">
                    <div>
                        <h1 class="title">
                            Entregas inmediatas para tu negocio
                        </h1>
                        <p class="text">Impulsa tus ventas ofreciendo la opción de envíos same day en menos de 1h o en
                            la franja horaria seleccionada y diferénciate de la competencia.</p>
                    </div>
                </div>
                <div class="col-img gif" style="position:relative">
                    <img style="max-width:350px" data-src="{{ asset('img/business/immediate-deliveries.png') }}"
                        width="100%">
                </div>
            </div>
        </div>

        <div class="section">
            <div class="wrapper reverse">
                <div class="col-text col-right">
                    <div>
                        <h1 class="title">
                            Conecta tu tienda <br> en minutos...
                        </h1>
                        <p class="text">Nuestra tecnología se integra perfectamente con todas las principales
                            plataformas de eCommerce y marketplaces utilizando nuestra API o módulos de tiendas online.
                        </p>
                    </div>
                </div>
                <div class="col-img stores">
                    <img data-src="{{ asset('img/business/connect.png') }}" width="100%"
                        style="border-radius: 0; box-shadow: none;" />
                </div>
            </div>
        </div>

        <div class="section">
            <div class="wrapper">
                <div class="col-text col-left">
                    <div>
                        <h1 class="title">
                            Administra todo en un mismo lugar
                        </h1>
                        <p class="text">Simplifica la gestión logística de tu empresa. Sincroniza tu inventario, importa
                            pedidos, sigue los envíos en tiempo real y controla tus gastos en un sólo panel fácil de
                            usar.</p>
                    </div>
                </div>
                <div class="col-img">
                    <img style="margin-bottom:-3rem" data-src="{{ asset('img/business/interface.png') }}"
                        width="100%" />
                </div>
            </div>
        </div>


        <div class="final-text">
            <h1 class="title">
                Date de alta en Citystock totalmente gratis
            </h1>
            <p class="text">
                Sin compromisos ni contratos ni volúmenes mínimos.<br>
                Ahorra tiempo y dinero creando una cuenta para tu negocio.
            </p>
            <a href="{{ route('business_register') }}" class="btn btn-corporativo">EMPEZAR&nbsp&nbsp<i
                    class="fas fa-chevron-right"></i></a>
        </div>

    </div>

    <footer class="business-footer">
        <div class="condiciones" style="float:unset; text-align:left;">
            <a class="mg-r-10"
                href="{{ route('muestra_pagina_informacion', ['slug2' => 'condiciones-de-uso']) }}">Condiciones de
                uso</a>
            <a href="{{ route('muestra_pagina_informacion', ['slug2' => 'politica-de-privacidad-y-cookies']) }}">Política
                de privacidad y cookies</a>
        </div>
        <div class="copyright-wrapper">
            <p class="copyright no-mg">© 2022 <a href="{{env('APP_URL')}}"
                    target="_blank"><strong>Citystock</strong></a>. {{ Lang::get('footer.copyright') }}</p>
        </div>
    </footer>

    <div id="cookies">
        <p>Citystock utiliza cookies para mejorar la experiencia de usuario y sus servicios. Si continúa navegando,
            consideramos que acepta su uso. Más información aquí <b><a
                    href="{{ route('muestra_pagina_informacion', ['slug2' => 'politica-de-privacidad-y-cookies']) }}">Política
                    de Privacidad y Cookies</a></b> <a href="#" class="cookieClose"><span
                    class="glyphicon glyphicon-remove"></span></a> </p>
    </div>

    @include('business.partials.login-popup')

</body>

{{-- Start of Zopim Live Chat Script--}}
<script type="text/javascript" async defer>
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
            d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
        _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
            $.src="//v2.zopim.com/?3dFUiB17IFQTlGyKEvqodFDcsjr60z58";z.t=+new Date;$.
                type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>





{{-- Llamadas dinámicas a js de footer --}}
<script src="{{ mix('js/dist/jquery-bootstrap.js') }}"></script>
<script src="{{ mix('js/dist/lazyload.min.js') }}"></script>

<script>
    const myLazyLoad = new LazyLoad();
        const close = document.getElementById("closebtn");

        $(function() {
            let anim = false;
            const header = $('.mobile-header');
            const baseHeight = header.height();
            const headerContent = $('.mobile-header-content');
            const burger = $('.hamburger');
            headerContent.css('top', '-' + headerContent.height() + 'px');
            burger.click(function() {
                if(!anim && !$('.line1.active').length) {
                    anim = true;
                    headerContent.css({'visibility': 'visible', 'top': '-' + headerContent.height() + 'px'});
                    setTimeout(function () {
                        header.removeClass('active').addClass('reverse');
                        anim = false;
                    }, 200);
                } else if(!anim) {
                    anim = true;
                    header.removeClass('reverse').addClass('active');
                    setTimeout(function () {
                        headerContent.css({'visibility': 'visible', 'top': baseHeight + 'px'});
                        anim = false;
                    }, 250);
                }
            });

            close.addEventListener("click", function() {
                let menuIcon = close.children;
                for (let i = 0; i < menuIcon.length; i++) {
                    menuIcon[i].classList.toggle("active");
                }
            });


            $('#login-form').on('submit', function(event) {
                event.preventDefault();
                $('#login-error').text('');
                $('.mensaje-error-login').html('');

                $.ajax({
                    type: "POST",
                    url: "{{ route('business_validar_login') }}", 
                    data:  {
                        'email': $('#login-email').val(),
                        'password': $('#login-password').val(),
                        '_token' : "{{ csrf_token() }}"
                    },
                    success: function(data) {
                    $('#login-form').unbind('submit').submit();
                    },
                    error: function(error) {
                        $('.login').addClass('has-error').addClass('has-feedback');
                        $('.login input').attr('placeholder', 'Credenciales incorrectas');
                        if(error.status === 403) {
                            $('#login-error').text(error.responseJSON);
                        }
                    }
                });
            });

        });



</script>

<script type="text/javascript" async defer>
    $(document).ready(function(){
            if (leeCookie("mensajecookie")!="si") {
                $("#cookies").fadeIn(200);
                $(document).on('click', function() {
                    setCookieState();
                });
            }

            setInterval('mueveSlide()',6000);

            $('#menu-principal > .menu-item').click(function(e) {
                if ($(window).width() < 466) {
                    e.preventDefault();
                }
            });

            $('.cookieClose').on('click', function() {
            ocultaCookie();
            });

        });

        function mueveSlide() {
            if ($(document).scrollTop() < 50) {
                $(".cinematic-right").click();
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
            $("#cookies").fadeOut(200);
            setCookieState();
        }

        function setCookieState() {
            var date = new Date();
            date.setTime(date.getTime() + (60 * 24 * 60 * 60 * 1000));
            var expires = " expires=" + date.toGMTString();
            document.cookie="mensajecookie=si;"+expires+"; path=/";
        }

</script>

</html>