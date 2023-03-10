@extends('layouts.business')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="section-title">
            Gestión de palabras
        </h1>
        <ol class="breadcrumb">
            <li class="icon-crumb"><i class="material-icons">home</i></li>
            <li class="active">Admin</li>
            <li class="active">Gestión de palabras</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

    <div class="box-body" style="border-top: 1px solid #e5e5e5;">
    <div class="row">
        <div class="row-xs buscar-row" id="tableCheckOut" style="padding-top: 20px;">
                            <div class="btn-group pull-right">
                                <a class="btn btn-danger" style="height:34px;margin-left: 9px;border-radius: 3px;" href="{{ route('admin_plantilla') }}">Descargar plantilla</a>
                                <button type="button"class="btn btn-primary import-from-excel-btn" style="height:34px;margin-left: 9px;border-radius: 3px;margin-right: 9px;"  aria-haspopup="true" aria-expanded="false">
                                    Importar palabras 
                                </button>
                                <button type="button" id="eliminarRegla" onclick="eliminarReglas();" disabled class="btn btn-danger" style="height:34px;border-radius: 3px;" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                <button type="button" onclick="openCrearModal();"  class="btn btn-success" style="height:34px;margin-left: 9px;border-radius: 3px;"  aria-haspopup="true" aria-expanded="false">
                                <i style="line-height: 0.9;vertical-align: middle;" class="material-icons">add_circle_outline</i> Crear palabra 
                                </button>
                                
                            </div>
                            <input id="buscarInputTalbeCheckOut" class="buscar-input pull-left" type="text" placeholder="Buscar">

        </div>
        <div class="business-table-row row-xs no-pd-h mg-t-6">
            <div class="table-responsive table-flow" id='tableResponsiveCheckOut' style="width: 100%;padding: 0px;padding-top: 14px;">
                <table class="table table-striped business-table"  style="font-size: 14px;">
                    <thead>
                    <tr>
                        <th><input class="header-checkbox" type="checkbox" autocomplete="off" ></th>
                        <th>Vocabulario</th>
                        <th style="white-space: nowrap;">Idioma</th>
                        <th>Traduccion</th>
                        <th>Familia</th>
                        <th>hide</th>
                    
                    </tr>
                    </thead>
                    <tbody>
                    @if($recursos)
                        @foreach($recursos as $recurso)
                        <tr>
                            <td id="{{ $recurso->id }}">
                                <input class="table-checkbox field" data-edit-name="regla_id" id="regla_id" type="checkbox" value="{{ $recurso->id }}" autocomplete="off" >
                                
                            </td>
                            <td class="editable">
                                <span class="field" data-edit-name="vocabulario">
                                    <span class="value">{{$recurso->vocabulario->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                               
                            </td>
                
                            <td class="editable">
                                <span class="field" data-edit-name="idioma">
                                    <span class="value">{{$recurso->idioma->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
                            </td>

                            <td style="white-space: nowrap;">{{$recurso->texto}}</td>

                            <td class="editable">
                                <span class="field" data-edit-name="activa">
                                    <span class="value">{{$recurso->vocabulario->familia->nombre}}</span>
                                    <i class="fas fa-pencil-alt"></i>
                                </span>
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
                {{ $recursos->links() }}
            </div>
            
        </div>
    </div>   
</div>

<form action="{{ route('business_configuracion_paquetes_importar_excel') }}" method="post" class="import-from-excel-form" enctype="multipart/form-data">
        <input type="file" name="excel">
        {{ csrf_field() }}
</form>
</section>

@include('business.partials.crearPalabra')
@include('business.partials.editarPalabra')

@endsection

@push('javascripts-head')

@endpush

@push('javascripts-footer')
    <script type="text/javascript" src="{{ mix('js/vendor/icheck.min.js') }}"></script>
    <script type="text/javascript" src="{{ mix('js/vendor/xlsx.min.js') }}"></script>

    
<script>
    var checkedRows = [];
   
    function openCrearModal(){
        
        $('#crearPalabra').modal();
    }

    function eliminarReglas(){
        
        if($('input.header-checkbox').is(':checked')){
            //Eliminamos todos
            checkedRows = -1;
        }

        var eliminar = '{!! route('admin_eliminar_palabra') !!}';
        var csrf = '{!! csrf_token() !!}';
        $.ajax({
            url: eliminar,
            headers: { 'X-CSRF-TOKEN': csrf },
            data: {'ids':checkedRows},
            type: 'POST',
            success: function (data) {
                location.reload();
            },
            error: function (data) {
                
            }
        });

        
       
    }

    $('.box-body').on('click', '.business-table .editable', function(e) {

        e.preventDefault();
       
        var id = $(this).parent().children().first().attr('id');
        var route = 'http://127.0.0.1:8000/admin/editarPalabra';

        var editModal = $('#editarPalabra');

        editModal.find('form').attr('action', route + '/' + id);
        
        var id = $(this).parent().children().first().attr('id');
        
        $(this).parent().find('.field').each(function() {
            var text = $(this).find('.value').text().trim();
            var name = $(this).attr('data-edit-name');


            if(name == 'idioma_id'){
                
                var select = editModal.find('select[name="idioma"]').val(text);
           
            }else{

                var input = editModal.find('input[name="' + name + '"]')
                input.val(text);

            }
           
            


        });

        editModal.modal();
    });



    //Buscar

    $('.buscar-input').keyup(function() {
        let text = $(this).val();
        filterListData( text);
    });

    function filterListData(text) {

        var rutaSearchRegla = '{!! route('admin_buscar') !!}';
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

    $('.import-from-excel-btn').click(function() {
        $('.import-from-excel-form > input').click();
    });

    $('.import-from-excel-form > input').change(function() {
        parseExcelAndPost($('.import-from-excel-form > input[type="file"]').prop('files')[0]);
    });



    var parseExcelAndPost = function(file) {
    var reader = new FileReader();

    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        if(!workbook.Sheets["Recursos"] || workbook.Sheets["Recursos"].A1.v !== "Tipo" || workbook.Sheets["Recursos"].B1.v !== "Texto" || workbook.Sheets["Recursos"].C1.v !== "Orden" || workbook.Sheets["Recursos"].D1.v !== "Vocabulario" || workbook.Sheets["Recursos"].E1.v !== "Familia"|| workbook.Sheets["Recursos"].F1.v !== "Idioma" || workbook.Sheets["Recursos"].G1.v !== "Dificultad") {
            new PNotify({
                title: 'IdioGrabber',
                text: 'El excel introducido no es correcto. Prueba a descargar nuestra plantilla desde el menú de acciones.',
                addclass: 'transporter-alert',
                autoDisplay: true,
                hide: true,
                delay: 5000,
                closer: false,
            });
            $('.import-from-excel-form > input[type="file"]').val('');
        } else {
            var excelJson = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[workbook.SheetNames[0]], {
                header: ["tipo", "texto", "ordem", "vocabulario","familia","idioma","dificultad"],
                range: 1
            });
			var importarRecurso = '{!! route('admin_importar_excel') !!}';
			var csrf = '{!! csrf_token() !!}';
            $.ajax({
                url: importarRecurso,
                headers: { 'X-CSRF-TOKEN': csrf },
                type: 'POST',
                data: {'data' : excelJson},
                success: function (data) {
                    $('.import-from-excel-form > input[type="file"]').val('');
                    location.reload();
                },
                error: function (data) {
   
                }
            });
        }
    };

    reader.onerror = function(ex) {
        console.log(ex);
    };

    reader.readAsBinaryString(file);
};


</script>
@endpush