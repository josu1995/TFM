

<div class="GlobalStoreCard" id="checkOutCard" style="height: 48px;min-height: 0px;width: 100%;">

    <form class="crear-configuracion" action="{{ route('usuario_get_nueva_redaccion') }}" method="get">
        <div class="row" style="height: 100%;">
            <div class="col-md-12" style="margin-top:4px;text-align:center;">
                <button class="btn rounded-btn-primary" style="color:white; left:calc(162px);background-color: #ee8026;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;" @click="openModalButton();">

                    <i style="font-weight: 700;margin-top: 5px;"class="material-icons">add</i>
                
                </button>
            </div>
            
        </div>  
    </form>
</div>
