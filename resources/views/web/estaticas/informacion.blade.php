@extends('layouts.estaticas', [ 'fixed' => true ])
@section('title', 'Citystock | ' . (isset($pagina) ? $pagina->meta_title : "Información"))
@section('meta_description', 'Citystock | ' . (isset($pagina) ? $pagina->meta_description : "Información"))
@section('meta_keywords', 'Citystock | ' . (isset($pagina) ? $pagina->meta_keywords : "Información"))

@section('content')

    <div class="m-principal-ayuda">
        <div class="container">
            <div class="row">

                {{-- Presentación de datos --}}
                <div class="col-md-12 pd-l-35">
                    {{-- Si hay página la mostramos, sino una portada general --}}
                    @if(isset($pagina))
                        {!! $pagina->texto !!}
                    @else
                        <div class="temas-populares">
                            <h2 class="titulo-ayuda text-left">Temas <strong> de {{ $categoria->nombre }}</strong></h2>
                            @foreach($categoria->paginas as $pagina)
                                <a href="{{ route('muestra_pagina_informacion', ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                                    <div class="col-md-3 b-popular">
                                        <div class="inner-popular">
                                            <span class="{{ $pagina->icono }}"></span><p> {{ $pagina->titulo }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            @foreach($categoria->categoriasHija as $categoriaHija)
                                @foreach($categoriaHija->paginas as $pagina)
                                    <a href="{{ route('muestra_pagina_informacion', ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                                        <div class="col-md-3 b-popular">
                                            <div class="inner-popular">
                                                <span class="{{ $pagina->icono }}"></span><p> {{ $pagina->titulo }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            @endforeach
                        </div>
                        <hr>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascripts-footer')
    <script>
        $(document).ready(function() {
            // Buscador ayuda
            $('.input-ayuda').on('keyup', function() {
                var ruta = "{{route('buscador_paginas')}}";
                var datos = { 'texto': $(this).val()};

                if($(this).val().length > 3 ) {
                    $.get(ruta, datos)
                        .done(function(response) {
                            $('#resultados-buscador').html(response).fadeIn();
                        })
                        .fail(function() {
                            console.log( "error" );
                        })
                } else {
                    $('#resultados-buscador').fadeOut();
                }

            });
        });
    </script>
@endpush
