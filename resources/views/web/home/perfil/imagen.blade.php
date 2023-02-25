@extends('web.home.perfil')

@section('perfil')
    <div class="box-perfil">
      <h4>Cambio de imagen de perfil</h4>
      <div class="inner-box-perfil">
        {!! Form::open(['action'=>'App\Http\Controllers\Web\HomeController@postImagen', 'files'=>true, 'id' => 'imageForm']) !!}
        <div class="row">
          <div class="col-sm-4 prev-perfil">
            @if($usuario->imagen)
              <div class="prev-perfil" style="background-image: url({{ $usuario->imagen->path }}); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
            @else
              <img src="/img/commons/transporter-default-user.jpg" alt="usuario transporter">
            @endif
          </div>
          <div class="form-group cambiar-imagen col-sm-8 pd-t-25">
            {!! Form::label('imagen', 'Elige tu imagen') !!}
            {!! Form::file('imagen', array('accept'=>'image/*')) !!}
              <input id="base64image" type="hidden" name="base64" value="">
            <p>Seleccionar una foto frontal es muy importante para que los usuarios puedan conocerte mejor.</p>
{{--            {!! Form::submit('Guardar', array( 'class'=>'btn btn-app', 'id'=>'submit-imagen' )) !!}--}}
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>

    {{-- Modal de imagen --}}
    <div class="modal fade" id="modalImagen" role="dialog" aria-labelledby="modalImagen" tabindex="-1" data-backdrop="static" data-keyboard="false" style="border-radius: 6px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                            <img src="" id="imageCrop" style="opacity: 0;">
                </div>
                <div class="modal-footer">
                    <button id="imagenSave" type="button" class="btn btn-app" data-dismiss="modal" style="display: inline;">Guardar</button>
                    <button type="button" class="btn btn-link" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Spinner --}}
    <div class="global-spinner-container fade">
        <i class="global-spinner fa fa-sync-alt fa-spin fade"></i>
    </div>
@endsection


@push('javascripts-footer')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropper/3.1.2/cropper.min.css" media="screen" title="no title" charset="utf-8">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/cropper/3.1.2/cropper.min.js"></script>
<script type="text/javascript" src="{{ mix('js/web/imagen.js') }}"></script>

@endpush
