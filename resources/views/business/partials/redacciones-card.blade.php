

<div class="GlobalStoreCard" id="checkOutCard" style="height: 48px;min-height: 0px;width: 100%;">

    
        <div class="row" style="height: 100%;">

            <div class="col-md-4" style="margin-top:7px;">
               {{$redaccion->titulo}}
            </div>
            <div class="col-md-4" style="margin-top:7px;">
               {{$redaccion->idioma_id}}
            </div>
            
            <div class="col-md-4" style="margin-top:4px;">
            @if($redaccion->corregido == 0)
                <button disabled class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: red;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                    <i style="font-weight: 700;margin-top: 5px;"class="material-icons">add</i>
                
                </button>
            @else
                <form class="crear-redaccion" action="{{ route('usuario_get_correccion',$redaccion->id) }}" method="get">
                    <button class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: #62ec09;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                        <i style="font-weight: 700;margin-top: 5px;"class="material-icons">add</i>

                    </button>
                </form>
            @endif
            </div>
            
        </div>  
    
</div>
