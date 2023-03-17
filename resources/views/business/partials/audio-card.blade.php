

<div class="GlobalStoreCard" id="checkOutCard" style="width: 100%;min-height: 7rem">
    <div onclick="checkAnswer('{{$recurso->id}}','{{$correcto->id}}');" class="row" style="cursor: pointer;">
        <form action="{{ route('usuario_comprobar') }}" method="get">
            <div class="col-md-12" style="position:absolute;top:21%;text-align:center;">
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
                        $('.card0').removeClass('animate__fadeInTopLeft');
                        $('.card0').addClass('animate__fadeOutTopLeft');

                        $('.card1').removeClass('animate__fadeInTopRight');
                        $('.card1').addClass('animate__fadeOutTopRight');

                        $('.card2').removeClass('animate__fadeInBottomLeft');
                        $('.card2').addClass('animate__fadeOutBottomLeft');

                        $('.card3').removeClass('animate__fadeInBottomRight');
                        $('.card3').addClass('animate__fadeOutBottomRight');

                        $('.title').removeClass('animate__heartBeat');
                        $('.title').addClass('animate__backOutUp');
                        
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }, 1000);
                    
                }else{
                    $('#recurso-'+recurso).css('color','red');
                    $('#recurso-'+correcto).css('color','#62ec09');
                    setTimeout(() => {
                        $('.card0').removeClass('animate__fadeInTopLeft');
                        $('.card0').addClass('animate__fadeOutTopLeft');
                        
                        $('.card1').removeClass('animate__fadeInTopRight');
                        $('.card1').addClass('animate__fadeOutTopRight');

                        $('.card2').removeClass('animate__fadeInBottomLeft');
                        $('.card2').addClass('animate__fadeOutBottomLeft');

                        $('.card3').removeClass('animate__fadeInBottomRight');
                        $('.card3').addClass('animate__fadeOutBottomRight');

                        $('.title').removeClass('animate__heartBeat');
                        $('.title').addClass('animate__backOutUp');
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