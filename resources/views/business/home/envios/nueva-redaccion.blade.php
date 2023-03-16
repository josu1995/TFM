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
        <form class="crear-redaccion" action="{{ route('usuario_new_redaccion') }}" method="post">

            <div class="row">
                <div class="col-md-8">
                    <label>TITULO</label>
                    <input name="titulo" type="text" id="titulo"  class="form-control autocomplete-input">
                </div>
                
                <div class="col-md-4">
                    <label>IDIOMA</label>
                    <select class="form-control" id="idioma" name="idioma">
                                
                        @foreach($idiomas as $idioma)
                    
                            <option value="{{$idioma->id}}">{{$idioma->nombre}}</option>
                    
                        @endforeach
                
                    </select>
                </div>
            </div>

            <div class="form.group">
                <label>Redaccion</label>
                <textarea name="texto" id="editor" rows="10" cols="80">
                           
                </textarea>
            </div>

            {{ csrf_field() }}

            <div class="col-md-4" style="margin-top:4px;">
                <button class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: #ee8026;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                    <i style="font-weight: 700;margin-top: 5px;"class="material-icons">add</i>
                
                </button>
            </div>
        </form>

       
    </section>



@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>


@endpush