@extends('layouts.web')
@section('title', Lang::get('tracking.meta.title'))
@section('meta_description', Lang::get('tracking.meta.description'))
@section('content')
<section class="tracking-section">
    @if(isset($envio))
    <div class="container">

        <h2 class="tracking-title mg-t-30"><img src="{{ asset('img/home/map-orange.png') }}" height="35px"/> Seguimiento <span>({{ str_replace('(D)', '', $envio->localizador) }})</span></h2>

        <div class="row tracking-container">

            <div class="col-md-6 col-xs-12">
                <div class="tracking-map" id="map"></div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="card pd-20 text-center estado-actual-container no-mg">
                    <button class="floating-button {{ $estadoActual['buttonClass'] }}">{!! $estadoActual['icon'] !!}</button>
                    <h4>{{ $estadoActual['titulo'] }}</h4>
                    <p>{{ $estadoActual['subtitulo'] }}</p>
                </div>

                <div class="card pd-20 row no-mg estados-info">
                    @foreach($estados as $key => $estado)
                        <div class="estado-container {{ $key % 2 == 0 ? 'even' : 'odd' }} {{ $key == 0 ? 'first' : '' }} {{ $key == count($estados) - 1 && !$proximoEstado ? 'last' : '' }} {{ $key == count($estados) - 1 ? 'ruta' : '' }}">
                            <p>
                                <span class="titulo">{{ $estado['titulo'] }}</span>
                                @if($estado['fecha'] != '')
                                    <br>
                                    <span class="fecha">{{ $estado['fecha'] }}</span>
                                @endif
                                <br>
                                <span class="direccion">{{ $estado['direccion'] }}</span>
                            </p>
                            <button class="floating-button {{ $estado['buttonClass'] }}">{!! $estado['icon'] !!}</button>
                        </div>
                    @endforeach
                    @if($proximoEstado)
                        <div class="estado-container last ruta {{ count($estados) % 2 == 0 ? 'even' : 'odd' }}">
                            <p>
                                <span class="titulo">{{ $proximoEstado['titulo'] }}</span>
                                <br>
                                <span class="direccion">{{ $proximoEstado['direccion'] }}</span>
                            </p>
                            <button class="floating-button {{ $proximoEstado['buttonClass'] }}">{!! $proximoEstado['icon'] !!}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @else
        <div class="tracking-error">
            <div class="alert alert-danger text-center">
                <p>No existen pedidos con el localizador indicado.<br><br>Si tienes problemas para seguir tu envío, puedes contactar con nosotros a través de nuestro chat online.</p>
                <br>
                <a href="{{ route('business_landing_index') }}" class="alert-link">Volver al inicio</a>
            </div>
        </div>
    @endif

</section>
@endsection

{{-- Push de scripts --}}
@push('javascripts-head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
@endpush
@push('javascripts-footer')
    @if(isset($envio))
        <script>
            const locations = {!! $markers !!};
            const state = {!! $envio->estado_id !!};
        </script>
        <script type="text/javascript" src="{{mix('js/web/tracking.js')}}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&callback=crearMapa" defer></script>
        <script type="text/javascript" src="{{asset('js/vendor/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    @endif
@endpush
