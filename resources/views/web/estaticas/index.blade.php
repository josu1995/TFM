@extends('layouts.estaticas')

@section('title', 'Citystock | ' . (isset($pagina) ? $pagina->meta_title : "Centro de Ayuda"))
@section('meta_description', 'Citystock | ' . (isset($pagina) ? $pagina->meta_description : "Centro de Ayuda"))
@section('meta_keywords', 'Citystock | ' . (isset($pagina) ? $pagina->meta_keywords : "Centro de Ayuda"))

@section('content')
    <div class="m-busqueda-ayuda fondo-corporativo">
        <div class="container">
            <div class="row">
                <div class="col-md-3 text-left"><p class="texto-inverso">Centro de <strong>ayuda</strong></p></div>
                <div class="col-md-6 no-pd">
                    <input type="text" class="input-ayuda text-center" placeholder="&#xe903 ¿Cómo podemos ayudarte?">
                    <div class="hidden-ayuda" id="resultados-buscador">
                    </div>
                </div>
                <div class="col-md-3 text-right"><a href="#"><p class="texto-inverso"><i class="icon-contacto"></i> Contáctanos</p></a> </div>
            </div>
            <div class="row">
            </div>
        </div>
    </div>

    <div class="m-principal-ayuda">
        <div class="container">
            <div class="row">
                <div class="col-md-3 text-center no-pd col-principal-ayuda">
                    <div class="inner-principal-ayuda">
                        {{-- Se pasa con nombre categoriaHija para poder seguir accediendo a $catetoria padre --}}
                        @include('web.estaticas.partials.menu', ['categoriasHija' => $categoria->categoriasHija, 'categoria' => $categoria])
                    </div>
                </div>

                {{-- Presentación de datos --}}
                <div class="col-md-9 pd-l-35">
                    {{-- Si hay página la mostramos, sino una portada general --}}
                    @if(isset($pagina))
                        {!! $pagina->texto !!}
                    @else
                        <div class="temas-populares">
                            <h2 class="titulo-ayuda text-left">Temas <strong>populares</strong></h2>

                            @foreach($populares as $pagina)
                                <a href="{{ route('muestra_pagina_'.$categoria->slug, ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                                    <div class="col-md-3 b-popular">
                                        <div class="inner-popular">
                                            <span class="{{ $pagina->icono }}"></span><p> {{ $pagina->titulo }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                        </div>
                        <hr>
                        <div class="guia-ayuda">
                            <h2 class="titulo-ayuda">Guía para <strong>empezar</strong></h2>

                            @foreach($portada as $pagina)
                                <div class="col-md-4 inner-guia">
                                    <span class="{{ $pagina->icono }}"></span>
                                    <h3>{{ $pagina->titulo }}</h3>
                                    <p>{{ str_limit($pagina->extracto, 110) }}</p>
                                    <a href="{{ route('muestra_pagina_'.$categoria->slug, ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                                        Seguir Leyendo
                                    </a>
                                </div>
                            @endforeach

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('javascripts-footer')
    <script>
        $(document).ready(function() {
            // --
            // Al pulsar enlaces del menu página "Ayuda"
            // --
            var nowScroll = 0;
            var menu = $('.col-principal-ayuda');
            var bloqueAyuda = $('.lista-temas').outerWidth();
            var ipAyuda = $('.inner-principal-ayuda').children().length;
            $('.inner-principal-ayuda').css('width', bloqueAyuda * ipAyuda);
            $('.sublista-temas').css('width', bloqueAyuda);
            var classSublista = '';
            var listaPrevia = '';
            var vAtras = 0;

            $('.categoria a, .atras-ayuda').on('click', function (event) {
                event.preventDefault();
                nowScroll = menu.scrollLeft();
                if ($(this).hasClass('atras-ayuda')) {
                    vAtras -= 1;
                    var listaOcultar = $('.lista-temas[data="' + $(this).attr('data') + '"]');
                    listaPrevia = $(this).closest('.sublista-temas').siblings('.lista-temas');
                    classSublista = listaPrevia.attr('class');
                    if (classSublista == 'sublista-temas') {
                        listaPrevia.children('ul').show();
                    } else {
                        if (vAtras < 1) {
                            $('.sublista-temas ul').each(function () {
                                $(this).hide();
                            });
                        }
                    }
                    menu.animate({
                        scrollLeft: nowScroll - bloqueAyuda + 'px'
                    }, 150, function () {

                    });

                } else {
                    vAtras += 1;
                    var listaMostrar = $('ul[data="' + $(this).attr('data-siguiente') + '"]');
                    if ($(this).attr('data-anterior')) {
                        var listaOcultar = $('ul[data="' + $(this).attr('data-anterior') + '"]');
                        listaMostrar.show();
                        menu.animate({
                            scrollLeft: nowScroll + bloqueAyuda + 'px'
                        }, 150, function () {

                        });
                    } else {

                        listaMostrar.show('slow', function () {
                            menu.animate({
                                scrollLeft: bloqueAyuda + 'px'
                            }, 150, function () {

                            });
                        });
                    }

                }
            });
        });
    </script>
@endpush