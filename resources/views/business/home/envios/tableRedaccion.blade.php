<div class="business-table-row row-xs no-pd-h mg-t-6">
            <div class="table-responsive table-flow" id='tableResponsiveUsuarios' style="width: 100%;padding: 0px;padding-top: 14px;">
                <table class="table table-striped business-table"  style="font-size: 14px;">
                    <thead>
                    <tr>
                        <th><input class="header-checkbox" type="checkbox" autocomplete="off" ></th>
                        <th>Titulo</th>
                        <th>Idioma</th>
                        <th>Corregir</th>
                    
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


                                    <td>
                                        <form class="get-redaccion" action="{{ route('admin_get_redaccion',$redaccion->id) }}" method="get">
                                            <button class="btn rounded-btn-primary" style="color:white; left:calc(162px);background-color: #ee8026;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                                            <i style="font-weight: 700;margin-top: 5px;"class="material-icons">manage_search</i>

                                            </button>
                                        </form>
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
                {{ $redacciones->links() }}
            </div>
            
        </div>