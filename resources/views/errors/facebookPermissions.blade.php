@extends('layouts.auth')
@section('title', 'Sólo falta tu email')

@section('content')
  <div id="l-login">
    <div class="logo-login">
      <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{asset('img/identidad/citystock-white-logo.png') }}" alt="transporter logo white" width="257" height="66">
      </a>
    </div>

  </div>

  @include('web.partials.modales.email-permission-popup')
@endsection
