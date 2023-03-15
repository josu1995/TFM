

<div class="GlobalStoreCard" id="checkOutCard" style="height: 300px;width: 100%;">
    <div onclick="checkAnswer('{{$recurso->id}}','{{$correcto->id}}');" class="row" style="height: 100%;cursor: pointer;">
        <form action="{{ route('usuario_comprobar') }}" method="get">
            <div class="col-md-12" style="position:absolute;top:40%;text-align:center;">
                <span id="recurso-{{$recurso->id}}" style="color: #607d8b;font-weight: 700;font-size: 2em;">{{$recurso->texto}}</span>
            </div>
        </form>
    </div>  
</div>

<script>

 function checkAnswer(recurso,correcto){


    var comprobar = '{!! route('usuario_comprobar') !!}';
    $.ajax({
            url: comprobar,
            data: {'recurso':recurso,'correcto':correcto},
            type: 'GET',
            success: function (data) {
                //location.reload();
                console.log(data);
                if(data == 'true'){
                    $('#recurso-'+recurso).css('color','#62ec09');
                    setTimeout(() => {
                        $('.StoreGrid').removeClass('animate__rollIn');
                        $('.StoreGrid').addClass('animate__rollOut');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }, 1000);
                    
                }else{
                    $('#recurso-'+recurso).css('color','red');
                    $('#recurso-'+correcto).css('color','#62ec09');
                    setTimeout(() => {
                        $('.StoreGrid').removeClass('animate__rollIn');
                        $('.StoreGrid').addClass('animate__rollOut');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }, 2000);
                    
                }
            },
            error: function (data) {
                
            }
        });
 }
</script>