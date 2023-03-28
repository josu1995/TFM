<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#ee8026" />

        <title>Citystock | @yield('title')</title>

        <link rel="shortcut icon" type="image/ico" href="{{asset('img/identidad/favicon.ico')}}"/>
        <meta property="og:image" content="{{asset('img/identidad/citystock.png')}}">

        {{-- CSS Transporter --}}
        <link rel="stylesheet" href="{{ mix('css/idiograbber.css') }}" media="screen" title="no title" charset="utf-8">

        {{--  Llamadas dinámicas a js por view --}}
        @stack('javascripts-head')
    </head>

    <body id="app-layout">

        @include('web.partials.header', [ 'fixed' => true ])

        @yield('content')

        @include('web.partials.footer')
    </body>

    {{-- Start of Zopim Live Chat Script--}}
    <script type="text/javascript">
        window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
                d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
        _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
            $.src="//v2.zopim.com/?3dFUiB17IFQTlGyKEvqodFDcsjr60z58";z.t=+new Date;$.
                    type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
    </script>
    {{-- End of Zopim Live Chat Script--}}

    <script  src="{{ mix('js/dist/jquery-bootstrap.js') }}"></script>

    {{-- Llamadas dinámicas a js de footer --}}
    @stack('javascripts-footer')
</html>
