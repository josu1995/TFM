@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
            Redacciones
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">Redacciones</li>
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
        @foreach($redacciones as $redaccion)
            <div class="row" style="margin-top: 18px;">  
                <div id="cards" class="row StoreGrid col-lg-12" style="display:block;padding-right:0;">
                    @component('business.partials.redacciones-card', [
                        'redaccion' => $redaccion
                    ]) @endcomponent
                </div>
            </div>
        @endforeach

        <div class="row" style="margin-top: 18px;">  
            <div id="cards" class="row StoreGrid col-lg-12" style="display:block;padding-right:0;">
                @component('business.partials.new-redaccion-card', [
                        'idiomas' => $idiomas
                ]) @endcomponent     
            </div>
        </div>
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')



@endpush