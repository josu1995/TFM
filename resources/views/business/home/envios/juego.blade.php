@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
        
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">{!! trans('usuario.juego') !!}</li>
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
        @if(!is_null($traduccion))
            @if($traduccion->tipo_recurso == 'Palabra')
                
                @if(session()->get('locale') == 'es')
                    <h1>{!! trans('usuario.palabra', [ 'palabrota' => $traduccion->vocabulario->nombre]) !!}</h1>
                @else
                    @php($inglis = \App\Models\recurso::where('vocabulario_id','=',$traduccion->vocabulario->id)->where('idioma_id','=',2)->get()->first())
                        <h1>{!! trans('usuario.palabra', [ 'palabrota' => $inglis->texto]) !!}</h1>
                @endif
             
                
                @foreach($recursos as $r)
                    <div id="cards" class="row StoreGrid col-lg-4 animate__animated animate__rollIn" style="display:block;padding-right:0;margin-top: 10%;height:300px">
                        
                        @component('business.partials.juego-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion 
                        ]) @endcomponent  

                    </div>
                    
                @endforeach
            @elseif($traduccion->tipo_recurso == 'Frase')

                @if(session()->get('locale') == 'es')
                    <h1 id="traduccionFrase" class="animate__animated animate__bounceInDown">{{$traduccion->vocabulario->nombre}}</h1>
                @else
                    @php($inglis = \App\Models\recurso::where('vocabulario_id','=',$traduccion->vocabulario->id)->where('idioma_id','=',2)->get()->first())
                    <h1 id="traduccionFrase" class="animate__animated animate__bounceInDown">{{$inglis->texto}}</h1>
                @endif

               
                
                <div class="row">

                    <div class="col-md-11">
                        <div id ="solucion" class="row arrastrar" ondrop="drop(event)" ondragover="allowDrop(event)" style="height:50px;border-radius: 0.5rem;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);">  
                        </div>  
                    </div>

                    <div class="col-md-1" style="margin-top: 6px;">
                        <button onclick="enviarRespuesta('{{$traduccion->id}}');" class="btn rounded-btn-primary" style="color:white; left:calc(162px);background-color: #ee8026;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;" @click="openModalButton();">

                            <i style="font-weight: 700;margin-top: 5px;"class="material-icons">check</i>

                        </button>
                    </div>

                </div>
                @php($cont = 0)
                @php($margin = 5)
                @foreach($recursos as $r)
                    @if($cont == 0)
                        <div class="row arrastrar palabras" style="margin-top: {{$margin}}%; height:50px;" ondrop="drop(event)" ondragover="allowDrop(event)"> 
                    @endif
                    @php($resource = '')
                    @if($r == '?')
                        @php($resource = 'ask2')
                    @elseif ($r == '!' )
                        @php($resource = 'exc2')
                    @elseif($r == ',' )
                        @php($resource = 'coma')
                    @elseif($r == '¿' )
                        @php($resource = 'ask1')
                    @elseif($r == '¡' )
                        @php($resource = 'exc1')
                    @else
                        @php($resource = $r)
                    @endif
                    <div id="{{$resource}}" class="row StoreGrid col-lg-2 animate__animated animate__jackInTheBox animate__delay-1s" style="display:block;padding-right:0;height:0px" draggable="true" ondragstart="drag(event)">
                        
                        @component('business.partials.frases-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion ,
                            'resource' => $resource
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
                <div class="row animate__animated animate__heartBeat title">
                    <div class="col-md-3">
                        <h1>{!! trans('usuario.reproduce') !!}</h1>
                    </div>
                    <div class="col-md-9">
                        <audio controls style="margin-top: 2%;">
                            <source src="{{ asset('audios/'.$traduccion->vocabulario->nombre.'.mp4') }}" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                </div>
                @php($cont = 0)
                @foreach($recursos as $r)

                @if($cont == 0)
                    <div id="cards" class="row StoreGrid col-lg-6 animate__animated animate__fadeInTopLeft card0" style="display:block;padding-right:0;margin-top: 3%;">    
                @elseif($cont == 1)
                    <div id="cards" class="row StoreGrid col-lg-6 animate__animated animate__fadeInTopRight card1" style="display:block;padding-right:0;margin-top: 3%;">
                @elseif($cont == 2)
                    <div id="cards" class="row StoreGrid col-lg-6 animate__animated animate__fadeInBottomLeft card2" style="display:block;padding-right:0;margin-top: 3%;">
                @elseif($cont == 3)
                    <div id="cards" class="row StoreGrid col-lg-6 animate__animated animate__fadeInBottomRight card3" style="display:block;padding-right:0;margin-top: 3%;"> 
                @endif
                @php($cont++)
                 
                        @component('business.partials.audio-card', [
                            'recurso' => $r, 
                            'correcto' => $traduccion 
                        ]) @endcomponent  

                    </div>
                    
                @endforeach
            @endif
        @else
            <h1>{!! trans('usuario.nomas') !!} :( </h1>
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
        if(ev.target.classList.contains('arrastrar')){
            var numb = ev.target.childElementCount;
            if(ev.target.classList.contains('palabras')){
         
                if(numb <=4){
                    var data = ev.dataTransfer.getData("text");
                    $('#'+data).css('width','auto');
                    $('#'+data).css('marginTop','0px');
                    $('#div-'+data).removeClass('col-md-12');
                    $('#div-'+data).addClass('col-md-2');
                 
                    
                    $('#'+data).removeClass(' animate__jackInTheBox');
                    $('#'+data).removeClass('animate__heartBeat');
                    $('#'+data).removeClass('animate__tada');
                    $('#'+data).removeClass('animate__delay-1s');
                    

                    $('#'+data).addClass('animate__tada');
                    
                    ev.target.appendChild(document.getElementById(data));
                }else{
                    $(function() {
                        new PNotify({
                        title: 'IdioGrabber',
                        text: 'No puedes poner mas palabras en esa fila',
                        addclass: 'transporter-alert',
                        icon: '',
                        autoDisplay: true,
                        hide: true,
                        delay: 5000,
                        closer: false,
                        });
                    });
                }
                
            }else{
               
                var data = ev.dataTransfer.getData("text");
                $('#'+data).css('width','auto');
                $('#'+data).css('marginTop','5px');
                $('#div-'+data).removeClass('col-md-12');
                $('#div-'+data).addClass('col-md-2');

                
                $('#'+data).removeClass(' animate__jackInTheBox');
                $('#'+data).removeClass('animate__heartBeat');
                $('#'+data).removeClass('animate__tada');
                $('#'+data).removeClass('animate__delay-1s');

                $('#'+data).addClass('animate__heartBeat');
                
                
                ev.target.appendChild(document.getElementById(data));
            }
           

        }

    }

    function enviarRespuesta(correcto){
        
        var respuesta = '';
        document.getElementById("solucion").querySelectorAll('span').forEach(element => {
          
            respuesta = respuesta + ' ' +element.innerText;
        })

        //console.log(respuesta);

        var comprobar = '{!! route('usuario_comprobar') !!}';
        $.ajax({
            url: comprobar,
            data: {'recurso':respuesta,'correcto':correcto},
            type: 'GET',
            success: function (data) {
                
                if(data == 'true'){

                    document.getElementById("solucion").querySelectorAll('span').forEach(element => {
          
                       console.log(element);
                       element.style.color ="#62ec09";
                        setTimeout(() => {
                            $('#solucion').addClass('animate__animated')
                            $('#solucion').addClass('animate__hinge')
                            $('.rounded-btn-primary').addClass('animate__animated')
                            $('.rounded-btn-primary').addClass('animate__hinge')
                            $('#traduccionFrase').addClass('animate__hinge')

                            $('.StoreGrid').removeClass(' animate__jackInTheBox');
                            $('.StoreGrid').removeClass('animate__heartBeat');
                            $('.StoreGrid').removeClass('animate__tada');
                            $('.StoreGrid').removeClass('animate__delay-1s');
                            
                            $('.StoreGrid').addClass('animate__hinge')

                            setTimeout(() => {
                                location.reload();
                            }, 2500);

                        }, 2000);

                        
                      
                    })

                }else{
                    console.log(data);
                    $('#solucion').empty();
                    $('#solucion').append("<h2 style='margin-top: 7px;color: red;font-weight: bold;'>"+data+'</h2>')
                    
                    setTimeout(() => {
                            $('#solucion').addClass('animate__animated')
                            $('#solucion').addClass('animate__hinge')
                            $('.rounded-btn-primary').addClass('animate__animated')
                            $('.rounded-btn-primary').addClass('animate__hinge')
                            $('#traduccionFrase').addClass('animate__hinge')

                            $('.StoreGrid').removeClass(' animate__jackInTheBox');
                            $('.StoreGrid').removeClass('animate__heartBeat');
                            $('.StoreGrid').removeClass('animate__tada');
                            $('.StoreGrid').removeClass('animate__delay-1s');

                            $('.StoreGrid').addClass('animate__hinge')
                            
                            setTimeout(() => {
                                location.reload();
                            }, 2500);

                    }, 2000);

                }
            },
            error: function (data) {
                
            }
        });
    }

</script>


@endpush