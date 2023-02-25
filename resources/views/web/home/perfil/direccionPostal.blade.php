@extends('web.home.perfil')

@section('perfil')
    <div class="box-perfil">
        <h4>Dirección postal</h4>
        <div class="inner-box-perfil">
            <form name="configuracion" id="configuracion" method="post" action='{{ route('direccion_actualizacion') }}'>
                <div class="form-group">
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="{{ old('direccion', $usuario->configuracion->direccion) }}">
                    </div>
                    <div class="form-group">
                        <label for="codigo_postal">Código postal</label>
                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" placeholder="Código postal" value="{{ old('codigo_postal', $usuario->configuracion->codigo_postal) }}">
                    </div>
                    <label for="ciudad">Ciudad </label>
                    <input type="text" class="form-control" id="ciudad" name="ciudad"  placeholder="Ciudad" value="{{ old('ciudad', $usuario -> configuracion -> ciudad) }}">
                    <input type="hidden" class="form-control" id="ciudad_oculto" name="ciudad_oculto" placeholder="Código postal" value="{{ old('ciudad_oculto', $usuario -> configuracion -> ciudad) }}">
                </div>
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <input type="hidden" name="update" value="configuracion">
                <button type="submit" class="btn btn-app guardarDireccion">Guardar</button>
            </form>
        </div>
    </div>


@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
<script defer>
//$(function(){
//    $('#ciudad').change(function(){
//      alert($(this).val());
//    });
//});
</script>
<link rel="stylesheet" type="text/css" href="{{asset('css/intltelinput/intlTelInput.css') }}"/>
<link rel="stylesheet" type="text/css" href="{{asset('css/intltelinput/intl.custom.css') }}"/>

<script type="text/javascript" src="{{mix('js/web/direccion.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&libraries=places&callback=mapsCallback" async defer></script>

@endpush

