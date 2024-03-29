@extends('layouts.exceptions')
@section('title', '500 - Error interno del servidor')

@section('content')
  <div id="pagina_error">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h1 class="texto-corporativo titulo-error">500</h1>
          <p class="texto-error">Oops... Vaya, parece que nuestro servidor no responde <br> Tenemos algún problema en nuestros servidores, pero ya lo estamos solucionando</p>
          <ul class="enlaces-error no-pd">
            <p>Quizás con alguno de estos enlaces puedas encontrar el camino de vuelta</p>
            <li><a class="texto-corporativo" href="{{ route('business_landing_index') }}">Inicio</a></li>
            <li><a class="texto-corporativo" href="{{ route('business_register') }}">Registrarse</a></li>
            <li><a class="texto-corporativo" href="#" data-toggle="modal" data-target="#modalLogin">Iniciar sesión</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
