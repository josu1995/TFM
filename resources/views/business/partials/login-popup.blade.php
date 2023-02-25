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
        <p class="login-title text-center mg-b-20">Entra en tu cuenta Citystock</p>
          <form id="login-form" class="form-horizontal" role="form" method="POST" action="{{ route('business_login') }}">
             {!! csrf_field() !!}
             <div class="form-group">
                <div class="col-md-12 bloque-input input-login login no-pd">
                   <input type="email" class="form-control-lg form-control" name="email" id="login-email" value="{{ old('email') }}" placeholder="Correo electrónico" aria-describedby="emailStatus">
                   <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                   <span id="emailStatus" class="sr-only">(error)</span>
                   <span class="icon-contacto texto-gris icono"></span>
                </div>
             </div>
             <div class="form-group">
                <div class="col-md-12 bloque-input input-login login no-pd">
                   <input type="password" class="form-control-lg form-control" id="login-password" name="password" placeholder="Contraseña" aria-describedby="passwordStatus">
                   <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                   <span id="passwordStatus" class="sr-only">(error)</span>
                   <span class="icon-contrasena texto-gris icono"></span>
                </div>
             </div>

              <div id="login-error" class="col-xs-12 mensaje-error mg-t-15 text-left"></div>

             <div class="form-group no-mg">
                <div class="col-xs-12 no-pd">
                   <div class="col-md-6 col-sm-6 col-xs-12 no-pd-l">
                      <div class="checkbox">
                         <input type="checkbox" name="remember" id="remember">
                         <label for="remember">Mantener sesión activa</label>
                      </div>
                   </div>
                   <div class="col-md-6 col-sm-6 col-xs-12 pull-right no-pd no-mg">
                      <a href="{{ route('auth.password.reset.request') }}" class="texto-corporativo pass-olvidada"><strong>¿Has olvidado la contraseña?</strong></a>
                   </div>
                </div>
             </div>
             <div class="form-group">
                <div class="col-xs-12 no-pd">
                   <button type="submit" class="btn-app btn-lg link-sesion" data-popup="m-login">
                   Iniciar sesión
                   </button>
                </div>
             </div>
          </form>
        <hr>
          <div class="form-group text-center">
             <p>¿Todavía no tienes cuenta? <a href="{{ route('business_register') }}" class="texto-corporativo link-registro"><strong>Regístrate</strong></a></p>
          </div>
    </div>
  </div>
</div>

{{-- Bloque recordar contraseña --}}
<div class="modal fade" id="modalOlvidar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog m-login-registro m-olvidar" role="document">
    <div class="modal-content">
    <p>Introduce la dirección de correo electrónico asociada a tu cuenta y te enviaremos un enlace para restablecer tu contraseña.</p>
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
               <button type="submit" class="btn-app btn-lg btn-registro link-sesion link-login" data-popup="m-registro-datos">Enviar enlace</button>
            </div>
         </div>
      </form>
    </div>
  </div>
</div>
