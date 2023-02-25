@extends('layouts.estaticas', [ 'fixed' => true ])

@section('title', (isset($link) ? $link->titulo : "Enviar paquete") . ' | Citystock' )

@section('content')

    {{-- @include('web.partials.cabecera-enviar', ['class' => 'footer']) --}}

    <div class="container footer-link-container">

        <h3><strong>{{ $link->titulo }}</strong></h3>

        <hr>

        <div class="row">

            <div class="col-sm-4">
                <img src="{{ $link->image }}" width="100%" class="img-principal" alt="{{ $link->titulo }}">
            </div>

            <div class="col-sm-8">
                {!! $link->texto !!}
            </div>

        </div>

    </div>

@endsection