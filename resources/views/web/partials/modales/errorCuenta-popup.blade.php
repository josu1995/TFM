<?php
    if(!isset($errors)){
        $errors = [];
    };
?>

{{-- Modal de registro --}}
<div class="modal fade" id="modalcondiciones" role="dialog" aria-labelledby="modalRegistro" tabindex="-1">
  <div class="modal-dialog m-login-registro m-registro" role="document">
    <div class="modal-content">
      <div class="form-horizontal">
         <div class="form-group">
            <div class="col-md-12 no-pd">
                <p >Hemos detectado que ya existe una cuenta con tu email. Puedes entrar a tu cuenta o si no recuerdas tu contraseña puedes recurperarla.</p>
                <button type="button" class="btn-app btn-lg btn-condiciones" data-dismiss="modal">
                    <strong>Iniciar sesión</strong>
                </button>
                <button type="button" class="btn-app btn-lg btn-condiciones" data-dismiss="modal">
                    <strong>Recuperar contraseña</strong>
                </button>
            </div>
         </div>
      </div>
    </div>
  </div>
    <input type="hidden" class="policy" value="{{$policy}}">
</div>

@push('javascripts-footer')
    <script type="text/javascript" src="{{ asset('js/dist/pwstrength.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/web/login-registro.js')}}"></script>

    <script type="text/javascript">

        $(function() {
            if($('.policy').val() == 1) {
                $('#modalcondiciones').modal('show');
            }
        });

    </script>

@endpush
