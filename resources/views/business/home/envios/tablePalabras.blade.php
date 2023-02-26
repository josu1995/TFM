
        <div class="business-table-row row-xs no-pd-h mg-t-6">
            <div class="table-responsive table-flow" id='tableResponsiveCheckOut' style="width: 100%;padding: 0px;padding-top: 14px;">
                <table class="table table-striped business-table"  style="font-size: 14px;">
                    <thead>
                    <tr>
                        <th><input class="header-checkbox" type="checkbox" autocomplete="off" ></th>
                        <th>Vocabulario</th>
                        <th style="white-space: nowrap;">Idioma</th>
                        <th>Traduccion</th>
                        <th>Familia</th>
                    
                    </tr>
                    </thead>
                    <tbody>
                    @if($recursos)
                        @foreach($recursos as $recurso)
                        <tr>
                            <td id="{{ $recurso->id }}">
                                <input class="table-checkbox field" data-edit-name="regla_id" id="regla_id" type="checkbox" value="{{ $recurso->id }}" autocomplete="off" >
                            </td>
                            <td class="editable">
                                <span class="field" data-edit-name="regla_prioridad">
                                    <span class="value">{{$recurso->vocabulario->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>
                
                            <td class="editable">
                                <span class="field" data-edit-name="regla_nombre">
                                    <span class="value">{{$recurso->idioma->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>

                            <td class="editable" style="white-space: nowrap;">
                                <span class="field" data-edit-name="regla_nombre">
                                    <span class="value">{{$recurso->texto}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>

                            <td class="editable">
                                <span class="field" data-edit-name="activa">
                                    <span class="value">{{$recurso->vocabulario->familia->nombre}}</span>
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
                {{ $recursos->links() }}
            </div>
            
        </div>
    </div>   
</div>
