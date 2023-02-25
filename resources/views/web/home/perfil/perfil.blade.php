@extends('web.home.perfil')

@section('perfil')
        <div class="box-perfil">
      <h4>Datos de usuario</h4>
      <div class="inner-box-perfil">
        <form name="configuracion" id="configuracion" method="post" action='{{ route('perfil_actualizacion') }}'>
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre', $usuario->configuracion->nombre) }}">
          </div>
          <div class="form-group">
            <label for="apellidos">Apellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos', $usuario->configuracion->apellidos) }}">
          </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email', $usuario->email) }}">
            </div>
          <div class="form-group">
            <label for="telefono">Teléfono móvil</label>
            <div class="input-telefono">
              <input type="tel" class="form-control" id="telefono" placeholder="6XX XXX XXX" value="{{ old('telefono') ? strlen(old('telefono')) != 2 ? substr(old('telefono'),2) : '' : $usuario->configuracion->telefono }}">
              <input id="hidden-phone" name="telefono" type="hidden" name="phone-full">
              <span id="valid-msg" class="hide" style="color: #3c763d;">✓ Válido</span>
              <span id="error-msg" class="hide" style="color: #a94442;">Móvil no válido</span>
            </div>
          </div>
          <div class="form-group">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <div class="row">
            {{-- <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="Fecha de nacimiento"  value="{{ old('fecha_nacimiento', $usuario->configuracion->fecha_nacimiento) }}"> --}}
                <div class="col-md-2">
                    <select name="dia" class="form-control">
                        @for ($i = 1; $i <= 31; $i++)
                            <option value="{{sprintf('%02d', $i)}}" {{ Carbon\Carbon::parse($usuario->configuracion->fecha_nacimiento)->format('d') == sprintf('%02d', $i) ? 'selected' : '' }}>{{sprintf('%02d', $i)}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="mes" class="form-control">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ sprintf('%02d', $i) }}" {{ Carbon\Carbon::parse($usuario->configuracion->fecha_nacimiento)->format('m') == sprintf('%02d', $i) ? 'selected' : '' }}>{{sprintf('%02d', $i)}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="ano" class="form-control">
                        @for ($i = $carbon->year; $i >= 1900 ; $i--)
                            <option value="{{ $i }}" {{ Carbon\Carbon::parse($usuario->configuracion->fecha_nacimiento)->format('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
          </div>
          <div class="form-group">
            <label for="fecha_nacimiento">DNI</label>
            <input type="text" class="form-control" id="dni" name="dni" placeholder="DNI/NIE. Ej.: 78803925M"  value="{{ old('dni', $usuario->configuracion->dni) }}">
          </div>
          {{--<div class="form-group">--}}
             {{--<p>--}}
                {{--<small>* Campo obligatorio</small>--}}
             {{--</p>--}}
          {{--</div>--}}
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="update" value="configuracion">
          <button type="submit" class="btn btn-app">Guardar</button>
        </form>
      </div>
    </div>


@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')

<link rel="stylesheet" type="text/css" href="{{asset('css/intltelinput/intlTelInput.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{asset('css/intltelinput/intl.custom.css') }}"/>

@endpush

