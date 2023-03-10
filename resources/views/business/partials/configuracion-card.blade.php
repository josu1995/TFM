

<div class="GlobalStoreCard" id="checkOutCard" style="height: 48px;min-height: 0px;width: 100%;">

    <div class="row" style="height: 100%;">

        <div class="col-md-4" style="margin-top:4px;">
            <span style="color: #607d8b;font-weight: 700;font-size: 2em;">{{$configuracion->idioma->nombre}}</span>
        </div>
        <div class="col-md-4" style="margin-top: 4px;">
            
            <span style="font-size: 2em;">{{$configuracion->dificultad->nombre}}</span>

        </div>
        <div id="divPrecioCheck" class="col-md-4" style="margin-top:7px;">
        @if($configuracion->idioma->nombre == 'Español')
            <button type="button" id="jugar"  class="btn btn-danger" style="height:34px;border-radius: 3px;width: 100%; background-image: url(/img/banderas/español.png);background-repeat: no-repeat; background-size: 332px 32px;font-weight: bold;color: #000903;" aria-haspopup="true" aria-expanded="false">
                    Jugar     
            </button>
        @elseif($configuracion->idioma->nombre == 'Francés')
            <button type="button" id="jugar"  class="btn btn-danger" style="height:34px;border-radius: 3px;width: 100%; background-image: url(/img/banderas/frances.png);background-repeat: no-repeat; background-size: 332px 32px;font-weight: bold;color: #000903;" aria-haspopup="true" aria-expanded="false">
                    Jugar     
            </button>
        @elseif($configuracion->idioma->nombre == 'Inglés')
            <button type="button" id="jugar"  class="btn btn-danger" style="height:34px;border-radius: 3px;width: 100%; background-image: url(/img/banderas/ingles.png);background-repeat: no-repeat; background-size: 332px 32px;font-weight: bold;color: #000903;" aria-haspopup="true" aria-expanded="false">
                    Jugar     
            </button>
        @endif
           
           
        </div>
    </div>  
</div>
