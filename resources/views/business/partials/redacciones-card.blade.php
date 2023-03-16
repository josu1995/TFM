

<div class="GlobalStoreCard" id="checkOutCard" style="height: 48px;min-height: 0px;width: 100%;">

    
        <div class="row" style="height: 100%;">

            <div class="col-md-4" style="margin-top:7px;">
            
                <span style="color: #607d8b;font-weight: 700;font-size: 2em;">{{$redaccion->titulo}}</span>
               
            </div>
            <div class="col-md-4" style="margin-top:7px;">

                <span style="color: #607d8b;font-weight: 700;font-size: 2em;">{{$redaccion->idioma->nombre}}</span>
               
            </div>
            
            <div class="col-md-4" style="margin-top:4px;">
                @if($redaccion->corregido == 0)
                    <button  data-toggle="popover" class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: red;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                        <i style="font-weight: 700;margin-top: 5px;"class="material-icons">close</i>
                    
                    </button>
                @else
                    <form class="crear-redaccion" action="{{ route('usuario_get_correccion',$redaccion->id) }}" method="get">
                        <button class="btn rounded-btn-primary" style="color:white;position:absolute; left:calc(162px);background-color: #62ec09;border-radius: 100%;box-shadow: 0 0 7px 1px rgba(0,0,0,.2);height: 4rem;padding: 0;width: 4rem;">

                            <i style="font-weight: 700;margin-top: 5px;"class="material-icons">arrow_forward</i>

                        </button>
                    </form>
                @endif
            </div>
            
        </div>  
    
</div>

<script>
$(document).ready(function(){

    $('[data-toggle="popover"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Tu redacción aún no ha sido corregida.',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });
});
</script>
