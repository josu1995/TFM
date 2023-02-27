<div class="business-table-row row-xs no-pd-h mg-t-6">
            <div class="table-responsive table-flow" id='tableResponsiveUsuarios' style="width: 100%;padding: 0px;padding-top: 14px;">
                <table class="table table-striped business-table"  style="font-size: 14px;">
                    <thead>
                    <tr>
                        <th><input class="header-checkbox" type="checkbox" autocomplete="off" ></th>
                        <th>Nombre</th>
                        <th style="white-space: nowrap;">Email</th>
                        <th>Idioma</th>
                        <th>Dificultad</th>
                    
                    </tr>
                    </thead>
                    <tbody>
                    @if($usuarios)
                        @foreach($usuarios as $usuario)
                            @foreach($usuario->configuracion as $configuracion)
                                <tr>
                                    <td id="{{ $configuracion->id }}">
                                        <input class="table-checkbox field" data-edit-name="configuracion_id" id="configuracion_id" type="checkbox" value="{{ $configuracion->id }}" autocomplete="off" >
                                    
                                        <span  class="field" data-edit-name="idioma_id">
                                            <span style="display:none;" class="value">{{$configuracion->idioma->id}}</span>
                                        
                                        </span>
                                        <span  class="field" data-edit-name="dificultad_id">
                                            <span style="display:none;" class="value">{{$configuracion->dificultad->id}}</span>
                                            
                                        </span>
                                    </td>
                                    <td >
                                        <span class="field" data-edit-name="nombre">
                                            <span class="value">{{$usuario->nombre}} {{$usuario->apellido}}</span>
                                            
                                        </span>
                                    </td>
                        
                                    <td >
                                        <span class="field" data-edit-name="email">
                                            <span class="value">{{$usuario->email}}</span>
                                            
                                        </span>
                                    </td>

                                   

                                    

                                    <td class="editable">
                                        <span class="field" data-edit-name="idioma">
                                            <span class="value">{{$configuracion->idioma->nombre}}</span>
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </td>

                                    
                                    <td class="editable">
                                        <span class="field" data-edit-name="dificultad">
                                            <span class="value">{{$configuracion->dificultad->nombre}}</span>
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach


                    @else

                        <tr>
                            <td colspan="5">
                                <p class="text-center mg-t-10">
                                    Sin resultados.
                                </p>
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
                {{ $usuarios->links() }}
            </div>
            
        </div>