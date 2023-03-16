@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
            Correción
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">Correción</li>
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
        <div class="row">
            <div class="col-md-8">
                <h1>{{$correccion->titulo}}</h1>
            </div>
            <div class="col-md-4">
                <h3>{{$correccion->idioma->nombre}}</h3>
            </div>
        </div>

        <div class="form.group" style="margin-top:4%;text-align: center;">
            <h4>Redacción</h4>
            <textarea name="texto" id="texto" rows="10" cols="80" readonly>
                {{$correccion->texto}}   
            </textarea>
        </div>

        <div class="form.group" style="margin-top:4%;text-align: center;">
            <h4>Corrección</h4>
            <textarea name="texto" id="correccion" rows="10" cols="80" readonly>
                {{$correccion->coreccion}}  
            </textarea>
        </div>

        
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<script>

    window.addEventListener("load", function(event) {
        CKEDITOR.replace( 'texto' ); 
    });

    window.addEventListener("load", function(event) {
        CKEDITOR.replace( 'correccion' ); 
    });

</script>


@endpush