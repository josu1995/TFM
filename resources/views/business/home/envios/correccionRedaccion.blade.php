@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
            Corrección de redacciones
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">Home</i></li>
            <li class="active">Admin</li>
            <li class="active">Corrección de redacciones</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="box-body" style="border-top: 1px solid #e5e5e5;">
    <div class="row">
        <div class="row-xs buscar-row" id="tableCheckOut" style="padding-top: 20px;">
                            <div class="btn-group pull-right">
                                
                                
                            </div>
                            <input id="buscarInputTalbeCheckOut" class="buscar-input pull-left" type="text" placeholder="Buscar">

        </div>
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
    </div>   
</div>
</section>


@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
    <script type="text/javascript" src="{{ mix('js/vendor/icheck.min.js') }}"></script>

    
<script>
    var checkedRows = [];
   

    function eliminarConfiguracion(){

    }

    //Buscar

    $('.buscar-input').keyup(function() {
        let text = $(this).val();
        filterListData( text);
    });

    function filterListData(text) {

        var rutaSearchRegla = '{!! route('admin_buscar_redaccion') !!}';
        $('.business-table-row').load(rutaSearchRegla + '?t=' + encodeURIComponent(text), function () {
            
            initCheckboxes();
            initPaginationListener();
            checkedRows = [];
            
        });
    }

    function initCheckboxes() {
        $('.table-checkbox').not('.switch').iCheck({
            checkboxClass: 'icheckbox_square-yellow',
            radioClass: 'iradio_square',
            increaseArea: '20%' // optional
        });

        $('.header-checkbox').not('.switch').iCheck({
            checkboxClass: 'icheckbox_square-yellow',
            radioClass: 'iradio_square',
            increaseArea: '20%' // optional
        });

        $('.header-checkbox').on('ifChecked', function (event) {
            $('.table-checkbox').not('.header-checkbox').iCheck('check');
            $('#eliminarRegla').prop('disabled',false);
           
        });

        $('.header-checkbox').on('ifUnchecked', function (event) {
            $('#eliminarRegla').prop('disabled',true);
            $('.table-checkbox').not('.header-checkbox').iCheck('uncheck');
            var pages = getPages();

            for(var i = 1; i<=pages;i++){
                checkedRows[i] = [];
            }
        });

        $('.table-checkbox').on('ifChecked', function (event) {
            $('#eliminarRegla').prop('disabled',false);
            // showEliminar();
            if ($('#filtro-activos').val() == 1){          
                showDesactivar();
            } else {
                showActivar();
            }
            addToChecked($(this).val());
        });

        $('.table-checkbox').on('ifUnchecked', function (event) {
            removeFromChecked($(this).val());
           
            if(!Object.keys(checkedRows).length) {
                
                $('#eliminarRegla').prop('disabled',true);
                // hideEliminar();
                hideActivar();
                hideDesactivar();
            }
        });

    
    }

    function showActivar() {
        $('.activar-productos, .activar-divider').show();
    }

    function showDesactivar() {
        $('.desactivar-productos, .desactivar-divider').show();
    }

    function hideActivar() {
        $('.activar-productos, .activar-divider').hide();
    }

    function hideDesactivar() {
        $('.desactivar-productos, .desactivar-divider').hide();
    }

    function initPaginationListener() {
        
        $('.pagination li > a').click(function (e) {

            e.preventDefault();
            var url = $(this).attr('href');

            var chk;
    
            if($('input.header-checkbox').is(':checked')){

                chk = true;
           
            }else{
                
                chk = false;
            }

            var busquedaText = $('.buscar-input').val();
            if(busquedaText !== '') {
                url += '&t=' + busquedaText;
            }
            $('.business-table-row').load(url, function () {
               
                
                initCheckboxes();
                initPaginationListener();
                var newPage = getCurrentPage();
                if(checkedRows[newPage] && checkedRows[newPage].length) {
                   
                    

                   
                    if(chk){
                        $('.header-checkbox').iCheck('check');
                    }
                    
                    if(checkedRows[newPage].length === 10) {
                    
                    } else {
                        checkedRows[newPage].forEach(function (val) {
                            $('input[value="' + val + '"]').iCheck('check');
                        });
                    }
                }
            
            });
        });
    }

    function getCurrentPage() {
        var elem = $('.pagination > li.active > span');
        if(!elem.length) {
            return "1";
        } else {
            return $('.pagination > li.active > span').text();
        }
    }

    function addToChecked(val) {
        var pages = getPages();
    
        for(var i = 1; i<=pages;i++){
        if (!checkedRows[i]) {
            checkedRows[i] = [];
        }
        }
        if ($('input.header-checkbox').is(':checked')) {
        for(var i = 1; i<=pages;i++){
            if (checkedRows[i].indexOf(val) === -1) {
            checkedRows[i].push(val);
            }
        }
        
        }else{

        var page = getCurrentPage();
        if (!checkedRows[page]) {
            checkedRows[page] = [];
        }
        if (checkedRows[page].indexOf(val) === -1) {
            checkedRows[page].push(val);
        }
        }
    
    }

    function removeFromChecked(val) {
        var pages = getPages(); 
        var cont = 0;
    


        var page = getCurrentPage();
        var index = checkedRows[page].indexOf(val);
        checkedRows[page].splice(index, 1);

        for(var i = 1;i<=pages;i++){
            cont = cont + checkedRows[i].length;
        }
    
        if(cont == 0){
            hideActivar();
            hideDesactivar();
        }

        if(!checkedRows[page].length) {
            delete checkedRows[page];
        }
    }

    function getPages(){
        var elem = $('.pagination > li > span');
        var elem = $('.pagination > li > span');
        if(elem.length == 0){
        return [1];
        }else{
        return elem.length;
        }
    }

    $(function() {

        filterListData('');
        
    });


</script>
@endpush