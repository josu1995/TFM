<div class="modal fade" tabindex="-1" role="dialog" id="editarConfiguracion" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title col-md-6">Editar configuracion</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin_editar_configuracion','') }}" method="post">
                <div class="modal-body">

                    @if ($errorMessage)
                        <div class="alert alert-danger">
                            <span>{{$errorMessage}}</span>
                        </div>
                    @endif

                    <div class="form-group no-mg-768">
                        <div class="row no-mg-768">
                            <div class="col-xs-12 col-sm-12">
                                <label>NOMBRE</label>
                                <input type="text" class="form-control" name="nombre" value="{{ old('nombre') ? old('nombre') : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group no-mg-768">
                        <div class="row no-mg-768">
                            <div class="col-xs-12 col-sm-12">
                                <label>EMAIL</label>
                                <input type="text" class="form-control" name="email" value="{{ old('email') ? old('email') : '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <label>IDIOMA</label>
                                <select class="form-control" id="idioma" name="idioma">
                            
                                    @foreach($idiomas as $idioma)

                                        <option value="{{$idioma->id}}">{{$idioma->nombre}}</option>

                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <label>DIFICULTAD</label>
                                <select class="form-control" id="dificultad" name="dificultad">
                            
                                    @foreach($dificultades as $dificultad)

                                        <option value="{{$dificultad->id}}">{{$dificultad->nombre}}</option>

                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                {{ csrf_field() }}
               
                <div class="modal-footer">
                    <a type="button" class="btn btn-link" data-dismiss="modal">Cerrar</a>
                    <button type="submit" class="btn btn-corporativo business-btn m-w-200">Editar</button>
                </div>
            </form>
        </div>
    </div>
</div>