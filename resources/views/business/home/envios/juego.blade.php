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
                <div class="row" ondrop="drop(event)" ondragover="allowDrop(event)" >
                    <div class="col-md-12" style="height:50px;background-color: red;">
                    </div>
                </div>
               
                @php($cont = 0)
                @php($margin = 5)
                @foreach($recursos as $r)
                    @if($cont == 0)
                        <div class="row" style="margin-top: {{$margin}}%;" > 
                    @endif

                    <div id="cards" class="row StoreGrid col-lg-2" style="display:block;padding-right:0;height:0px" >
                        
                        @component('business.partials.frases-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion 
                        ]) @endcomponent  

                    </div>

                    @if($cont == 0)
                        @php($cont++)
                    @else
                        @php($cont++)
                            @if($cont == 5)
                                </div >
                                @php($cont = 0)
                                @php($margin = $margin + 2)
                            @endif
                    @endif
                    
                @endforeach
            @else
                <h1>Audio del recurso {{$traduccion->vocabulario->nombre}} </h1>
                @foreach($recursos as $r)
                    <div id="cards" class="row StoreGrid col-lg-6" style="display:block;padding-right:0;margin-top: 10%;height:300px">
                        
                        @component('business.partials.juego-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion 
                        ]) @endcomponent  

                    </div>
                    
                @endforeach
            @endif
        @else
            <h1>No hay mas para estudiar hoy :( </h1>
        @endif
        
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
<script>

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }

    function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
 
    $('#'+data).css('width','8%');
    $('#div-'+data).removeClass('col-md-12');
    $('#div-'+data).addClass('col-md-2');
    ev.target.appendChild(document.getElementById(data));
    }

</script>


@endpush