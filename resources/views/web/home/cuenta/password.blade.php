@extends('web.home.cuenta')

@section('perfil')
    <h1 class="title-seccion">Cambio de contraseña</h1>
    <div class="box-perfil">
      <h4>Datos de contraseña</h4>
      <div class="inner-box-perfil">
        <form name="password" id="password" method="post" action='{{ route('perfil_actualizacion') }}'>
            @if(Auth::user()->password)
                <div class="form-group">
                  <label for="password">Contraseña actual</label>
                  <input type="password" class="form-control" name="password" placeholder="Contraseña">
                </div>
            @endif

          <div class="form-group">
            <label for="nuevo">Nueva contraseña</label>
            <input type="password" class="form-control password" id="password" name="nuevo" placeholder="Nueva contraseña">
          </div>
          <div class="form-group">
            <label for="nuevo_confirmation">Confirmar contraseña nueva</label>
            <input type="password" class="form-control" id="nuevo_confirmation" name="nuevo_confirmation" placeholder="Confirmar contraseña nueva">
          </div>
          <div class="form-group">
              <div id="pwd-container" no-pd col-md-12 bloque-input input-login>
                <div class="col-md-12">
                  <div class="pwstrength_viewport_progress"></div>
                </div>
              </div>
          </div>

          <div class="form-group">
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              <input type="hidden" name="update" value="password">
              <button type="submit" class="btn btn-app from-control">Guardar</button>
          </div>
        </form>
      </div>
    </div>
@endsection

@push('javascripts-footer')
<script type="text/javascript" src="{{ asset('js/dist/pwstrength.js') }}"></script>
@endpush
