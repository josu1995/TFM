<div class="business-table-row row-xs no-pd-h mg-t-6">
            <div class="table-responsive table-flow" id='tableResponsiveUsuarios' style="width: 100%;padding: 0px;padding-top: 14px;">
                <table class="table table-striped business-table"  style="font-size: 14px;">
                    <thead>
                    <tr>
                        <th><input class="header-checkbox" type="checkbox" autocomplete="off" ></th>
                        <th>Titulo</th>
                        <th>Idioma</th>
                        <th>Dificultad</th>
                    
                    </tr>
                    </thead>
                    <tbody>
                    @if($redacciones)
                        @foreach($redacciones as $redaccion)
                            
                                <tr>
                                    <td id="{{ $redaccion->id }}">
                                        <input class="table-checkbox field" data-edit-name="redaccion_id" id="redaccion_id" type="checkbox" value="{{ $redaccion->id }}" autocomplete="off" >
                                    </td>
                                    <td>
                                        <span class="field" data-edit-name="nombre">
                                            <span class="value"></span>
                                           {{$redaccion->titulo}}
                                        </span>
                                    </td>
                        
                                    <td>
                                        <span class="field" data-edit-name="email">
                                            <span class="value"></span>
                                            {{$redaccion->idioma->nombre}}
                                        </span>
                                    </td>


                                    <td class="editable">
                                        <span class="field" data-edit-name="dificultad">
                                            <span class="value">botton</span>
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                    </td>

                                    
                                    
                                </tr>
                            
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
                {{ $redaccion->links() }}
            </div>
            
        </div>