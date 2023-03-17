@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
            Nueva redacción
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">Nueva redacción</li>
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
        <form class="save-redaccion" action="{{ route('admin_save_redaccion',$redaccion->id) }}" method="post">

            <div class="row">
                <div class="col-md-8">
                    <label>TITULO</label>
                    <input value="{{$redaccion->titulo}}" readonly name="titulo" type="text" id="titulo"  class="form-control autocomplete-input">
                </div>
                
                <div class="col-md-4">
                    <label>IDIOMA</label>
                    <input value="{{$redaccion->idioma->nombre}}" readonly name="titulo" type="text" id="titulo"  class="form-control autocomplete-input">
                </div>
            </div>

            <div class="form.group" style="margin-top: 4%;text-align: center;">
                <h2>Corregir redacción</h2>
                <textarea name="texto" id="editor" rows="10" cols="80">
                         {{$redaccion->texto}}  
                </textarea>
            </div>

            {{ csrf_field() }}

            <div class="col-md-12" style="margin-top:4px;text-align: center;margin-top: 3%;">
                <button class="btn rounded-btn-primary" style="color:white; left:calc(162px);background-color: #ee8026;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 50%;">

                    Corregir
                
                </button>
            </div>
        </form>

       
    </section>



@endsection


@push('javascripts-head')

@endpush

@push('javascripts-footer')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<script>

    window.addEventListener("load", function(event) {
        CKEDITOR.replace( 'editor' ); 
    });

</script>

@endpush