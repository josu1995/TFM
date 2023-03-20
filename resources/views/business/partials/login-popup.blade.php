<?php
    if(!isset($errors)){
        $errors = [];
    };
?>

<div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="modalLogin">
  <div class="modal-dialog m-login-registro popupHome business-login-popup" role="document">
    <div class="modal-content">
        <a class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </a>
        <p class="login-title text-center mg-b-20">{!! trans('usuario.entrar') !!}</p>
          <form id="login-form" class="form-horizontal" role="form" method="POST" action="{{ route('business_login') }}">
             {!! csrf_field() !!}
             <div class="form-group">
                <div class="col-md-12 bloque-input input-login login no-pd">
                   <input type="email" class="form-control-lg form-control" name="email" id="login-email" value="{{ old('email') }}" placeholder="{!! trans('usuario.correo') !!}" aria-describedby="emailStatus">
                   <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                   <span id="emailStatus" class="sr-only">(error)</span>
                   <span class="icon-contacto texto-gris icono"></span>
                </div>
             </div>
             <div class="form-group">
                <div class="col-md-12 bloque-input input-login login no-pd">
                   <input type="password" class="form-control-lg form-control" id="login-password" name="password" placeholder="{!! trans('usuario.contraseña') !!}" aria-describedby="passwordStatus">
                   <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                   <span id="passwordStatus" class="sr-only">(error)</span>
                   <span class="icon-contrasena texto-gris icono"></span>
                </div>
             </div>

              <div id="login-error" class="col-xs-12 mensaje-error mg-t-15 text-left"></div>

             <div class="form-group no-mg">
                <div class="col-xs-12 no-pd">
                   <div class="col-md-6 col-sm-6 col-xs-12 pull-right no-pd no-mg">
                      <a href="{{ route('auth.password.reset.request') }}" class="texto-corporativo pass-olvidada"><strong>{!! trans('usuario.olvidar') !!}</strong></a>
                   </div>
                </div>
             </div>
             <div class="form-group">
                <div class="col-xs-12 no-pd">
                   <button type="submit" class="btn-app btn-lg link-sesion" data-popup="m-login">
                   {!! trans('usuario.inicio') !!}
                   </button>
                </div>
             </div>
          </form>
        <hr>
          <div class="form-group text-center">
             <p>{!! trans('usuario.noCuenta') !!} <a href="{{ route('business_register') }}" class="texto-corporativo link-registro"><strong>{!! trans('usuario.registrarse') !!}</strong></a></p>
          </div>
    </div>
  </div>
</div>

{{-- Bloque recordar contraseña --}}
<div class="modal fade" id="modalOlvidar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog m-login-registro m-olvidar" role="document">
    <div class="modal-content">
    <p>{!! trans('usuario.olvidar1') !!}</p>
      <form class="form-horizontal" role="form" method="POST" action="{{ route('business_validar_login') }}">
         {!! csrf_field() !!}
         <div class="form-group">
            <div class="no-pd col-md-12 bloque-input input-login">
               <span class="icon-contacto texto-gris"></span>
               <input type="email" class="form-control-lg form-control" name="email" value="{{ old('email') }}" placeholder="Correo electrónico">
                @if ($errors->has('email'))
                    <span class="help-block">
                       <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
         </div>
         <hr>
         <div class="form-group">
            <div class="col-md-12 no-pd">
               <button type="submit" class="btn-app btn-lg btn-registro link-sesion link-login" data-popup="m-registro-datos">{!! trans('usuario.olvidar2') !!}</button>
            </div>
         </div>
      </form>
    </div>
  </div>
</div>
