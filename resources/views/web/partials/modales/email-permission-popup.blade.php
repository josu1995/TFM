
{{-- Modal de registro --}}
<div class="modal fade" id="modalPermission" role="dialog" aria-labelledby="modalPermission" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog small-modal" role="document">
    <div class="modal-content">
      <div class="form-horizontal">
         <div class="form-group">
            <div class="col-md-12 pd-30 center">
                <i class="fas fa-envelope" aria-hidden="true"></i>
                <p class="pd-t-10">En Transporter necesitamos tu correo electrónico para finalizar tu registro. Por favor, revisa tus permisos de Facebook.</p>
                <a href="{{ route('login_facebook_permissions') }}" class="btn facebook-button mg-t-10">
                    <i class="fab fa-facebook-square left" aria-hidden="true"></i>
                    <strong>Revisar Permisos</strong>
                </a>
            </div>
         </div>
      </div>
    </div>
  </div>
</div>

@push('javascripts-footer')

<script>
    $(function() {
        $('#modalPermission').modal();
    });
</script>

@endpush
