

<div class="GlobalStoreCard" id="checkOutCard" style="height: 48px;min-height: 0px;width: 100%;">

    <form class="crear-configuracion" action="{{ route('usuario_new_configuracion') }}" method="post">
        <div class="row" style="height: 100%;">

            <div class="col-md-4" style="margin-top:7px;">
                <select class="form-control" id="idioma" name="idioma">
                                
                    @foreach($idiomas as $idioma)
                
                        <option value="{{$idioma->id}}">{{session()->get('locale') == 'es' ? $idioma->nombre : $idioma->name}}</option>
                
                    @endforeach
                
                </select>
            </div>
            <div class="col-md-4" style="margin-top:7px;">
                <select class="form-control" id="dificultad" name="dificultad">

                    @foreach($dificultades as $dificultad)

                        <option value="{{$dificultad->id}}">{{session()->get('locale') == 'es' ? $dificultad->nombre : $dificultad->name}}</option>

                    @endforeach

                </select>
            </div>
            {{ csrf_field() }}
            <div class="col-md-4" style="margin-top:4px;">
                <button class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: #ee8026;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;" @click="openModalButton();">

                    <i style="font-weight: 700;margin-top: 5px;"class="material-icons">add</i>
                
                </button>
            </div>
            
        </div>  
    </form>
</div>
