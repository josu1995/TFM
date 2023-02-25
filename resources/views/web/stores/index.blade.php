@extends('layouts.web')
@section('title', 'Encuentra tu punto de recogida Transporter Store')
@section('meta_description', 'Encuentra tu Transporter Store preferido para la entrega o recogida de tus envíos.')
@section('content')
<section class="stores-search-section">
    <div class="container">

        <h2 class="stores-search-title mg-t-30"><i class="material-icons icono-naranja">store</i></button> Encuentra tu Store</h2>

        <div class="row stores-search-container">
            <div class="col-md-6 col-xs-12">
                <div class="map-search-bar">
                    <span class="location"><button class="floating-button btn-grey btn-location"><i class="material-icons">my_location</i></button></span>
                    <span class="input"><input id="autocomplete" class="search-input pd-10" name="ciudad" placeholder="Ciudad o CP"></span>
                    <span class="search"><button class="btn btn-corporativo btn-corporativo square-button btn-buscar">BUSCAR</button></span>
                </div>
                <div class="stores-search-map" id="map"></div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="card stores-container">
                    <div id="stores-list" class="stores-list"></div>
                    <div class="t-paquetes sin-paquetes sin-stores">
                        <span class="icon-historial-paquetes texto-gris"></span>
                        <p class="text-center texto-gris"><strong>No hay ningún Store</strong><br>disponible</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('drivers.partials.modales.localidadCercana')
</section>


@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
    @if(isset($city))
        <script>
            const city = '{!! $city !!}';
        </script>
    @endif
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script type="text/javascript" src="{{asset('js/vendor/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script type="text/javascript" src="{{mix('js/web/stores-search.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&libraries=places&callback=mapsCallback" defer></script>
@endpush
