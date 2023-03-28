<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ee8026" />

    <title>Citystock | @yield('title')</title>

    <meta name="description" content="@yield('meta_description')">
    <meta name="keywords" content="@yield('meta_keywords')">

    <link rel="shortcut icon" type="image/ico" href="{{asset('img/identidad/favicon.ico')}}" />
    <meta property="og:image" content="{{asset('img/identidad/citystock.png')}}">

    {{-- CSS Transporter --}}
    <link rel="stylesheet" href="{{ mix('css/idiograbber.css') }}" media="screen" title="no title" charset="utf-8">

    {{-- Llamadas dinámicas a js por view --}}
    @stack('javascripts-head')
</head>

<body id="app-layout" class="estaticas">

    @include('drivers.partials.header-inverso')

    @yield('content')

    @include('drivers.partials.footer')
</body>

{{-- Start of Zopim Live Chat Script--}}
<script defer type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
                d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
        _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
            $.src="//v2.zopim.com/?3dFUiB17IFQTlGyKEvqodFDcsjr60z58";z.t=+new Date;$.
                    type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
{{-- End of Zopim Live Chat Script--}}

{{--DESCOMENTAR EN PROD--}}
{{--<script>
    --}}
    {{--(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){--}}
    {{--(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),--}}
    {{--m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)--}}
    {{--})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');--}}

    {{--ga('create', 'G-6G416JV0PT', 'auto');--}}
    {{--ga('send', 'pageview');--}}

    {{--
</script>--}}


{{-- Llamadas dinámicas a js de footer --}}
<script src="{{ mix('js/dist/jquery-bootstrap.js') }}"></script>

{{-- Llamadas dinámicas a js de footer --}}
@stack('javascripts-footer')

</html>