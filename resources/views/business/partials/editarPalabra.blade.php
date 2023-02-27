<div class="modal fade" tabindex="-1" role="dialog" id="editarPalabra" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title col-md-6">Editar palabra</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin_editar_palabra','') }}" method="post">
                <div class="modal-body">

                    @if ($errorMessage)
                        <div class="alert alert-danger">
                            <span>{{$errorMessage}}</span>
                        </div>
                    @endif

                    <div class="form-group no-mg-768">
                        <div class="row no-mg-768">
                            <div class="col-xs-12 col-sm-12">
                                <label>VOCABULARIO</label>
                                <input type="text" class="form-control" name="vocabulario" value="{{ old('vocabulario') ? old('vocabulario') : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group no-mg-768">
                        <div class="row no-mg-768">
                            <div class="col-xs-12 col-sm-12">
                                <label>FAMILIA</label>
                                <input type="text" class="form-control" name="familia" value="{{ old('familia') ? old('familia') : '' }}" required>
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
                    <div class="form-group no-mg-768">
                        <div class="row no-mg-768">
                            <div class="col-xs-12 col-sm-12">
                                <label>RECURSO</label>
                                <input type="text" class="form-control" name="recurso" value="{{ old('recurso') ? old('recurso') : '' }}" required>
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