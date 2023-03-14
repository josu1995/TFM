

@php($resource = '')

@if($recurso == '?')
    @php($resource = 'ask2')
@elseif ($recurso == '!' )
    @php($resource = 'exc2')
@elseif($recurso == ',' )
    @php($resource = 'coma')
@elseif($recurso == '¿' )
    @php($resource = 'ask1')
@elseif($recurso == '¡' )
    @php($resource = 'exc1')
@else
    @php($resource = $recurso)
@endif
<div class="GlobalStoreCard col-md-2" id="{{$resource}}" style="padding-right: 0px;padding-left: 0px;cursor: pointer;height: 41px;width: 100%;min-height: 41px;" draggable="true" ondragstart="drag(event)">
 
    <form action="{{ route('usuario_comprobar') }}" method="get">
        <div id="div-{{$resource}}" class="col-md-12" style="text-align:center;">
            <span id="recurso-{{$resource}}" style="color: #607d8b;font-weight: 700;font-size: 2em;">{{$recurso}}</span>
        </div>
    </form>
     
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
                    setTimeout(location.reload(), 3000);
                }else{
                    $('#recurso-'+recurso).css('color','red');
                    $('#recurso-'+correcto).css('color','#62ec09');
                    setTimeout(location.reload(), 3000);
                }
            },
            error: function (data) {
                
            }
        });
 }
</script>