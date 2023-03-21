@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
        {!! trans('usuario.estudios') !!}
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">{!! trans('usuario.estudios') !!}</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        @if($errors->hasBag('configuracion'))

        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->getBag('configuracion')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

        @endif
        @foreach($configuraciones as $configuracion)
            <div class="row" style="margin-top: 18px;">  
                <div id="cards" class="row StoreGrid col-lg-12" style="display:block;padding-right:0;">
                    @component('business.partials.configuracion-card', [
                        'configuracion' => $configuracion
                    ]) @endcomponent
                </div>
            </div>
        @endforeach

        <div class="row" style="margin-top: 18px;">  
            <div id="cards" class="row StoreGrid col-lg-12" style="display:block;padding-right:0;">
                @component('business.partials.new-configuracion-card', [
                        'idiomas' => $idiomas,
                        'dificultades' => $dificultades
                ]) @endcomponent     
            </div>
        </div>

    </section>

@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')



@endpush