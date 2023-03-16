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
                {{$correccion->titulo}}
            </div>
            <div class="col-md-4">
                {{$correccion->idioma->nombre}}
            </div>
        </div>

        <div class="form.group">
            <label>Redaccion</label>
            <textarea name="texto" id="editor" rows="10" cols="80" readonly>
                {{$correccion->texto}}   
            </textarea>
        </div>

        <div class="form.group">
            <label>Corrección</label>
            <textarea name="texto" id="editor" rows="10" cols="80" readonly>
                {{$correccion->correccion}}  
            </textarea>
        </div>

        
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>


@endpush