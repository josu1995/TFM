@extends('web.home.perfil')

@section('perfil')
    <div class="box-perfil">
      <h4>Certificados</h4>
      <div class="inner-box-perfil">
        {{-- Validación de email --}}
        @if($usuario->configuracion->mail_certificado)
          <p>
              <i class="icon-anadido texto-envio mg-r-5"></i> E-mail validado: <strong> {{$usuario->email}}</strong>
          </p>
        @else
          <h6>
            Validar correo
          </h6>
          @if(session('validacion_email') && !session('validacion_exito'))
            <p class="text-success">
              {{ session('validacion_email') }}
            </p>
          @endif
          @if(session('validacion_exito'))
            <p class="text-success">
              {{ session('validacion_exito') }}
            </p>
          @endif

          @if(session('validacion_error'))
            <p class="text-danger">
              {!! session('validacion_error') !!}
            </p>
          @endif

          <form class="form-group" action="{{ route('enviar_validacion_mail') }}" method="post">
              @if(Auth::user()->email)

              @endif
            <button type="submit" name="validar" class="btn btn-app">
                {{ Session::has('validacion_email') ? 'Reenviar email de validación' : "Enviar email de validación" }}</button>
                {!! csrf_field() !!}
            </form>
          @endif
          {{-- Fin de validación de email --}}
          <hr>


          {{-- Validación de móvil --}}
          @if($usuario->configuracion->movil_certificado)
            <p>
                <i class="icon-anadido texto-envio mg-r-5"></i> Móvil validado: <strong> {{$usuario->configuracion->telefonoModificado()}}</strong>
            </p>
          @else
            <h6>
              Validar móvil
            </h6>
            {{-- @if(session('validacion_movil'))
              <p class="text-success">
                {{ session('validacion_movil') }}
              </p>
            @endif --}}

            @if(session('validacion_exito_movil'))
              <p class="text-success">
                {{ session('validacion_exito_movil') }}
              </p>
              <form class="form-group  {{ Session::has('validacion_error_movil') ?  'has-error' : ''}}" action="{{ route('validacion_movil') }}" method="post">
                <input type="text" name="codigo" value="" placeholder="Introduce el código" class="form-control">
                <button type="submit" name="validar" class="btn btn-app" style="margin-top: 15px;">Validar móvil</button>
                {!! csrf_field() !!}
              </form>
            @endif

            <form class="" name="codigo" action="{{ route('enviar_validacion_movil') }}" method="get">
              <button type="submit" class="btn btn-app">{{ Session::has('validacion_exito_movil') ? "Reenviar código" : "Enviar código de validación"}}</button>
              {!! csrf_field() !!}
            </form>

            @if(session('validacion_error_movil'))
              <p class="text-danger mg-t-10">
                {!! session('validacion_error_movil') !!}
              </p>
            @endif


          @endif
          {{-- Fin de validación de móvil --}}
        </div>
      </div>
    </li>
@endsection
