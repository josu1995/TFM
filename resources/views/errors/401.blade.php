@extends('layouts.exceptions')
@section('title', '401 - No autorizado')

@section('content')
  <div id="pagina_error">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <h1 class="texto-corporativo titulo-error">401</h1>
          <p class="texto-error">No autorizado</p>
          <p class="texto-error">
              @if(isset($exception))
                  {{ $exception->getMessage() }}
              @endif
          </p>
          <ul class="enlaces-error no-pd">
            <p>Quizás con alguno de estos enlaces puedas encontrar el camino de vuelta</p>
            <li><a class="texto-corporativo" href="{{ route('business_landing_index') }}">Inicio</a></li>
            <li><a class="texto-corporativo" href="{{ route('business_register') }}">Registrarse</a></li>
            <li><a class="texto-corporativo" href="#" data-toggle="modal" data-target="#modalLogin">Iniciar sesión</a></li>
            <li><a class="texto-corporativo" href="{{ route('blog_get_index') }}">Blog</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
