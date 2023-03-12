@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
        
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">Juego</li>
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
        @if($traduccion)
            @if($traduccion->tipo_recurso == 'Palabra')
                <h1>¿Cómo se dice {{$traduccion->vocabulario->nombre}} ?</h1>
                @foreach($recursos as $r)
                    <div id="cards" class="row StoreGrid col-lg-4" style="display:block;padding-right:0;margin-top: 10%;height:300px">
                        
                        @component('business.partials.juego-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion 
                        ]) @endcomponent  

                    </div>
                    
                @endforeach
            @elseif($traduccion->tipo_recurso == 'Frase')
                <h1>{{$traduccion->vocabulario->nombre}}</h1>
            @else
                <h1> {{$traduccion->vocabulario->nombre}} </h1>
            @endif
        @else
            <h1>No hay mas para estudiar hoy :( </h1>
        @endif
        
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')



@endpush