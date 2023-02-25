@extends('layouts.auth')
@section('title', 'Vincula tu cuenta')
{{-- Modal de registro --}}
@section('content')

    <?php
        $cliente = $rol == \App\Models\Rol::CLIENTE_POTENCIAL;
            ?>

    <div id="l-login">
        <div class="logo-login">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{asset('img/identidad/citystock-white-logo.png') }}" alt="transporter logo white" width="257" height="66">
            </a>
        </div>

    </div>
<div class="modal fade" id="modalVinculacion" role="dialog" aria-labelledby="modalVinculacion" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog small-modal" role="document">
    <div class="modal-content">
      <form action="{{ route('post_vinculacion_cuenta') }}" method="POST" class="form-horizontal">
         <div class="form-group">
            <div class="col-md-12 pd-30 center">
                <i class="fas fa-link mg-b-20" aria-hidden="true"></i>
                <p class="pd-t-10 pd-b-10">Vincula tu cuenta {{ $cliente ? 'Driver' : 'cliente' }} para actuar como {{ $cliente ? 'cliente' : 'Driver' }} particular. Sólo necesitamos la contraseña con la que te has registrado previamente.</p>
                <div class="form-group">
                    <div class="input-group col-sm-6 col-sm-offset-3">
                        <div class="input-group-addon"><span class="icon-contrasena icono"></span></div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                    </div>
                </div>
                <input type="hidden" name="state" value="{{ $state }}">
                {{ csrf_field() }}
                <button type="submit" class="btn-app btn-vincular mg-t-20">
                    <strong>Vincular</strong>
                </button>
            </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('javascripts-footer')

<script>
    $(function() {
        $('#modalVinculacion').modal();
    });
</script>

@endpush
