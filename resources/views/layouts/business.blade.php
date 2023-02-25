<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ Lang::get('dashboard.meta.title') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="theme-color" content="#0097a7" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- CSS -->
  <link rel="stylesheet" href="{{ mix('css/business/business.css') }}">

  <!-- Material icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
<style>
  input[type="time"]::-webkit-calendar-picker-indicator {

    background: linear-gradient(rgba(255,255,255,1), rgba(255,255,255,1)),url(https://mywildalberta.ca/images/GFX-MWA-Parks-Reservations.png) no-repeat;
    background-size: 3px 1px;
  }
</style>
  @stack('javascripts-head')
  @stack('styles')
</head>
<body class="hold-transition skin-yellow sidebar-mini">
<div class="wrapper">

  @include('business.partials.header')

  @include('business.partials.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    @yield('content')

  </div>
  <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

{{--<script>--}}
  {{--$.widget.bridge('uibutton', $.ui.button);--}}
{{--</script>--}}
<!-- Jquery & Bootstrap 3.3.7 -->
<script type="text/javascript" src="{{ mix('js/dist/jquery-bootstrap.js') }}"></script>
<!-- Alpine JS 3.10.2 -->
<script defer src="{{ mix('js/dist/alpine.min.js') }}"></script>

<!-- AdminLTE App -->
<script type="text/javascript" src="{{ mix('js/business/adminlte.js') }}"></script>
<script type="text/javascript" src="{{ mix('js/vendor/pnotify.js') }}"></script>
</body>

@stack('javascripts-footer')

<script>
  @if(session()->has('alert') or session()->has('success'))
      $(function() {
          new PNotify({
              title: '{{ session()->has("alert") ? "Aviso" : "Citystock" }}',
              text: '{!! session()->get("alert") ?? session()->get("success") !!}',
              addclass: 'transporter-alert',
              icon: 'icon-transporter',
              autoDisplay: true,
              hide: true,
              delay: 5000,
              closer: false,
          });
      });
  @endif
</script>

</html>
