
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
                                
                                <span class="field" data-edit-name="idioma_id">
                                    <span style="display:none;" class="value">{{$recurso->idioma->id}}</span>
                                   
                                </span>

                                
                            </td>
                            <td class="editable">
                                <span class="field" data-edit-name="vocabulario">
                                    <span class="value">{{$recurso->vocabulario->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>
                
                            <td class="editable">
                                <span class="field" data-edit-name="idioma">
                                    <span class="value">{{$recurso->idioma->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>

                           

                            <td class="editable" style="white-space: nowrap;">
                                <span class="field" data-edit-name="recurso">
                                    <span class="value">{{$recurso->texto}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>

                            <td class="editable">
                                <span class="field" data-edit-name="familia">
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
