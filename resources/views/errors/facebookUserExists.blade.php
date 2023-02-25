@extends('layouts.exceptions')
@section('title', 'Parece que ya estás registrado en Transporter')

@section('content')
  <div id="pagina_error">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h1 class="texto-corporativo titulo-error"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i></h1>
          <p class="texto-error">Parece que ya estás registrado en Transporter</p>
          <p class="texto-error pd-t-20">
            Si te has registrado con tu correo, no es posible iniciar sesión con Facebook. Te sugerimos que inicies sesión <a class="texto-corporativo" href="{{ route('auth.login') }}">aquí</a>.
          </p>
          <p class="texto-error">
            Si no recuerdas tu contraseña, puedes <a class="texto-corporativo" href="{{ route('auth.password.reset') }}">cambiarla</a> o contactar con nosotros a través de nuestro chat online.
          </p>
          <ul class="enlaces-error no-pd">
            <li><a class="texto-corporativo" href="/">Volver a la página principal</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
