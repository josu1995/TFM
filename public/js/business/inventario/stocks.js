
const initPopovers = () => {
  function buildTooltip(ref, message) {
    $('[data-tooltip="' + ref + '"]').popover({
      trigger: 'hover',
      placement: 'bottom',
      html: true,
      content: message,
      container: 'body',
      template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    })
  }

  buildTooltip('low-level', 'Punto de aviso para aprovisionar (específico por almacén o total)')
  buildTooltip('incoming', 'Por devolución pendiente o transferencia en tránsito')
  buildTooltip('stored', 'Físicamente presente')
  buildTooltip('reserved', 'Por pedidos o transferencias por recoger')
  buildTooltip('available', 'Libre para vender')
 
 
}


const initEditables = () => {
  $('.box-body').on('click', '.business-table .editable', function (e) {
    e.preventDefault()

    const editModal = $('#EditStockModal')
    const id = $(this).parent().children().first().attr('id')
    const route = editModal.find('form').attr('action')

    editModal.find('form').attr('action', route.replace(/-?\d+/, id))

    $(this).parent().find('.field').each(function () {
      var text = $(this).find('.value').text().trim()
      var name = $(this).attr('data-edit-name')
      var input = editModal.find('input[name="' + name + '"]')

      if (!input.length) return

      input.val(text)
    });
    editModal.modal()
  });
}



var checkedRows = [];


var parseExcelAndPost = function (file) {
  var reader = new FileReader();

  reader.onload = function (e) {
    var data = e.target.result;
    var workbook = XLSX.read(data, {
      type: 'binary'
    });
    var filtroText = $( "#filtro-stores option:selected" ).text();
    var almacen =  filtroText.split(':');
    var calle = almacen[1].split('.');
    var calleFinal = calle[0].substring(0,20);

    if (!workbook.Sheets["Existencias"+calleFinal] || workbook.Sheets["Existencias"+calleFinal].A1.v !== "SKU / Referencia" || workbook.Sheets["Existencias"+calleFinal].B1.v !== "Item / Nombre Producto"
      || workbook.Sheets["Existencias"+calleFinal].C1.v !== "Tipo C. Barras" || workbook.Sheets["Existencias"+calleFinal].D1.v !== "Código de Barras" || workbook.Sheets["Existencias"+calleFinal].E1.v !== "Almacenado"
      || workbook.Sheets["Existencias"+calleFinal].F1.v !== "Almacenado real" || workbook.Sheets["Existencias"+calleFinal].G1.v !== "Diferencia") {
      new PNotify({
        title: 'Transporter',
        text: 'El excel introducido no es correcto. Prueba a descargar nuestra plantilla desde el menú de acciones.',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
      $('.import-from-excel-form > input[type="file"]').val('');
    } else {
      var almacen = $('#filtro-stores').val();
      var excelJson = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[workbook.SheetNames[0]], {
        header: ["referencia", "nombre", "tipo", "codigo", "almacenado", "real", "diferencia"],
        range: 1
      });
      
      $.ajax({
        url: rutaImportarExcel,
        headers: { 'X-CSRF-TOKEN': csrf },
        type: 'POST',
        data: { 'data': excelJson,'almacen': almacen},
        success: function (data) {
          $('.import-from-excel-form > input[type="file"]').val('');
          $('.import-omit-form input[name="rows[]"]').remove();
          let filter = $('#filtro-stores').val();
          filterListData(filter, '');

          new PNotify({
            title: 'Citystock',
            text: 'Existencias editadas correctamente.',
            addclass: 'transporter-alert',
            icon: 'icon-transporter',
            autoDisplay: true,
            hide: true,
            delay: 5000,
            closer: false,
          });
          
        },
        error: function (data) {
          console.log('ee');
          $('.import-from-excel-form > input[type="file"]').val('');
          $('.import-omit-form input[name="rows[]"]').remove();
          var json = data.responseJSON;
          var keys = Object.keys(json);

          $('#modal-error-importacion .alert > ul').empty();
          keys.forEach(function (val, i) {
            var row = parseInt(val.split('.')[1]) + 2;
            var plainRow = row - 2;
            $('.import-omit-form').append('<input type="hidden" name="rows[]" value="' + plainRow + '">');
            $('#modal-error-importacion .alert > ul').append('<li>Fila ' + row + ': ' + json[val] + '</li>');
          });
          $('#modal-error-importacion').modal();
        }
      });
    }
  };

  reader.onerror = function (ex) {
    
  };

  reader.readAsBinaryString(file);
};

function initPaginationListener() {
  $('.pagination li > a').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var filtro = $('#filtro-stores').val();
    var chk;
    
    if($('input.header-checkbox').is(':checked')){
      chk = true;
    }else{
      chk = false;
    }
    var busquedaText = $('.buscar-input').val();
   
   
    if (busquedaText !== '') {
      console.log('entras');
      url += '&t=' + busquedaText ;
    }
    url += '&f=' + filtro;
    $('.business-table-row').load(url, function () {

    
      initCheckboxes();
      initPaginationListener();
   
     
      var newPage = getCurrentPage();
      if (checkedRows[newPage] && checkedRows[newPage].length) {
        
        console.log(chk);
        if(chk){
          $('.header-checkbox').iCheck('check');
        }
        if (checkedRows[newPage].length === 10) {
          $('.header-checkbox').iCheck('check');
        } else {
          checkedRows[newPage].forEach(function (val) {
            $('input[value="' + val + '"]').iCheck('check');
          });
        }
      }
      $('[id="low-level"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Punto de aviso para aprovisionar (específico por almacén o total)',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
      });

      $('[id="incoming"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Por devolución pendiente o transferencia en tránsito',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
      });

      $('[id="stored"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Físicamente presente',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
      });

      $('[id="reserved"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Por pedidos o transferencias por recoger',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
      });

      $('[id="available"]').popover({
        trigger: 'hover',
        placement: 'bottom',
        html: true,
        content: 'Libre para vender',
        container: 'body',
        template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
      });

     
    });
   
  });

}

function getCurrentPage() {
  var elem = $('.pagination > li.active > span');
  if (!elem.length) {
    return "1";
  } else {
    return $('.pagination > li.active > span').text();
  }
}

// function showEliminar() {
//     $('.eliminar-productos, .eliminar-divider').show();
// }

// function hideEliminar() {
//     $('.eliminar-productos, .eliminar-divider').hide();
// }

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

function getPages(){
  var elem = $('.pagination > li > span');
  if(elem.length == 0){
    return [1];
  }else{
    return elem.length;
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
  var page = getCurrentPage();
  var pages = getPages(); 
  
  if ($('input.header-checkbox').is(':checked')) {

  }else{

  
    
  }
  
  var index = checkedRows[page].indexOf(val);
  checkedRows[page].splice(index, 1);

  var cont = 0;
  
  for(var i = 1;i<=pages;i++){
    cont = cont + checkedRows[i].length;
  }

  if(cont > 1){
    $('#editarNivelBajoSeparator').show();
    $('#editarNivelBajo').show();
  }else{
    $('#editarNivelBajoSeparator').hide();
    $('#editarNivelBajo').hide();
  }

  if (!checkedRows[page].length) {
    
    delete checkedRows[page];
    
  }
}

function omitirButton(){
  $('.business-table-row').load(omitirImport, function () {
    $('#modal-error-importacion').modal('hide');

    new PNotify({
      title: 'Citystock',
      text: 'Referencias actualizadas con éxito.',
      addclass: 'transporter-alert',
      icon: 'icon-transporter',
      autoDisplay: true,
      hide: true,
      delay: 5000,
      closer: false,
    });

    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initEditables();
    checkedRows = [];
    // hideEliminar();
    hideActivar();
    hideDesactivar();
  }
  );
}

function updateProduct(){
  console.log($('#low_level_input1').val());

  $('.business-table-row').load(actualizar + 
    '?product_id=' + $('#productIdHidden').val() + 
    '&store_id=' + $('#filtro-stores').val() + 
    '&quantity_input=' + $('#quantity_input').val() +  
    '&low_level_input=' + $('#low_level_input1').val(),
    
    function () {
    $('#EditStockModal').modal('hide');

    new PNotify({
      title: 'Citystock',
      text: 'Referencia actualizada con éxito.',
      addclass: 'transporter-alert',
      icon: 'icon-transporter',
      autoDisplay: true,
      hide: true,
      delay: 5000,
      closer: false,
    });

    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initEditables();
    checkedRows = [];
    // hideEliminar();
    hideActivar();
    hideDesactivar();
  }
);
}

function initCheckboxes() {
  $('input[type="checkbox"]').not('.switch').iCheck({
    checkboxClass: 'icheckbox_square-yellow',
    radioClass: 'iradio_square',
    increaseArea: '20%' // optional
  });

  $('.header-checkbox').on('ifChecked', function (event) {
    $('input[type="checkbox"]').not('.header-checkbox').iCheck('check');
    $('#editarNivelBajoSeparator').show();
    $('#editarNivelBajo').show();
  });

  $('.header-checkbox').on('ifUnchecked', function (event) {

    $('input[type="checkbox"]').not('.header-checkbox').iCheck('uncheck');

    var pages = getPages();

    for(var i = 1; i<=pages;i++){
      checkedRows[i] = [];
    }
    
    $('#editarNivelBajoSeparator').hide();
    $('#editarNivelBajo').hide();
  });

  $('.table-checkbox').on('ifChecked', function (event) {
  
   var pages = getPages();

    if ($('#filtro-activos').val() == 1) {
      showDesactivar();
    } else {
      showActivar();
    }
    addToChecked($(this).val());
    var cont = 0;
    for(var i = 1;i<=pages;i++){
      cont = cont + checkedRows[i].length;
    }
    if(cont > 1){
      $('#editarNivelBajoSeparator').show();
      $('#editarNivelBajo').show();
    }else{
      $('#editarNivelBajoSeparator').hide();
      $('#editarNivelBajo').hide();
    }
  });

  $('.table-checkbox').on('ifUnchecked', function (event) {
 
    removeFromChecked($(this).val());
   
      

     
    if (!Object.keys(checkedRows).length) {
      // hideEliminar();
      hideActivar();
      hideDesactivar();
    }
   
  });

  $('.switch').bootstrapSwitch();
}

function filterListData(filter, text) {
  
  
  $('.business-table-row').load(rutaSearchProductos + '?f=' + filter + '&t=' + encodeURIComponent(text), function () {
    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initEditables();
    checkedRows = [];
    // hideEliminar();
    hideActivar();
    hideDesactivar();
    var filtroText = $( "#filtro-stores option:selected" ).text();
    if(filtroText != 'Total'){
     
      if(filtroText.includes("Citystock")){
        $('#editarExcel').hide();
        $('#editarExcelSeparator').hide();
        $('#descargarExistencias').hide();
        $('#descargarExistenciasSeparator').hide();
      }else{
        $('#editarExcel').show();
        $('#editarExcelSeparator').show();
        $('#descargarExistencias').show();
        $('#descargarExistenciasSeparator').show();
      }

      $('#verMapa').hide();
      $('#verMapaSeparator').hide();
   

    }
  });
}



function initEditable() {


  
  $('.editable').click(function() {
    var visibleMarkers = [];
    var productsId = [];

    if($('input.header-checkbox').is(':checked')){
      $("input:checkbox:checked").each(function() {
       
        productsId.push($(this).val());
      });
    }else{
      var pages = getPages();
      var cont = 0;
      console.log(checkedRows);
      if(checkedRows.length > 0){
        for(var i = 1;i<=pages;i++){
          cont = cont + checkedRows[i].length;
          productsId = productsId.concat(checkedRows[i]);
        }
      }
     
    }
   

      
      console.log(productsId);
      var modalName = $(this).attr('data-modal-name');

      if(modalName === 'origen') {
          var origenModal = $('#l-envio-popup');
          $.get( showOrigenRoute, {'products':productsId}, function( data ) {

           
         
      
              //$('.punto-mapa-iframe').attr('src', 'https://maps.google.com/maps?q=' + data[0].latitud + ',' + data[0].longitud + '&hl=es;z=4&output=embed');
           
              //var img = '<img src="' + data.imagen + '" onerror="this.onerror=null;this.src=\'/img/home/store-no-img.png\';">';
              //$('.foto-punto img').replaceWith(img);
              const myLatLng = { lat: data[0].latitud, lng: data[0].longitud };
              
              if(data.length == 1){
                var map = new google.maps.Map(document.getElementById("map"), {
                  zoom: 16,
                  center: myLatLng,
                  mapTypeControl:false,
                  streetViewControl: false,
                });
              }else{
                var map = new google.maps.Map(document.getElementById("map"), {
                  zoom: 5,
                  center: { lat: 39.3705675, lng:-3.4201541 },
                  mapTypeControl:false,
                  streetViewControl: false,
                });
              }
             
              var sku = '';
             
              $('.ubicacion').empty();
              for(var i = 0;i<data.length;i++){
                if(productsId.length == 1){
                  if(sku == '' || sku != data[i].referencia){
                    
                    if(data[i].referencia != null){
                      $('.ubicacion').append(' <p class="nombre-punto texto-inverso"><span id="nombre-store"><strong>'+ data[i].nombre+'</strong></span> <span class="nombre-ciudad">('+data[i].referencia+')</span></p>');
                    }else{
                      $('.ubicacion').append(' <p class="nombre-punto texto-inverso"><span id="nombre-store"><strong>'+ data[i].nombre+'</strong></span></p>');
                    }
                  
                    sku = data[i].referencia;
                }
                }

                
                    var lanlog = { lat: data[i].latitud, lng: data[i].longitud };
                    if(data[i].drop == 0){

                      
                      if(data[i].cantidad>= 1 && data[i].cantidad < 100){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                            
                            icon: '/img/maps/stock-marker-1.png'
                        });
                      }else if( data[i].cantidad >=100 &&  data[i].cantidad < 10000){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                          
                            icon: '/img/maps/stock-marker-2.png'
                        });
                      }else if(data[i].cantidad >= 10000){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                          
                            icon: '/img/maps/stock-marker-3.png'
                        });
                      }
                    }else{
                      if(data[i].cantidad>= 1 && data[i].cantidad < 100){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                            
                            icon: '/img/maps/city-marker-1.png'
                        });
                      }else if( data[i].cantidad >=100 &&  data[i].cantidad < 10000){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                          
                            icon: '/img/maps/city-marker-2.png'
                        });
                      }else if(data[i].cantidad >= 10000){
                        var marker = new google.maps.Marker({
                          position: lanlog,
                          label:{
                            text:(data[i].cantidad).toString(),
                            color: 'white',
                            fontSize: '18px',
                            fontStyle: 'bold'
                          },
                          
                            icon: '/img/maps/city-marker-3.png'
                        });
                      }
                    }
                   
                    visibleMarkers.push(marker);
                    // To add the marker to the map, call setMap();
                    //marker.setMap(map);
              }
              var mcOptions = {gridSize: 30, maxZoom: 14, imagePath: '/img/maps/clustered/m', ignoreHidden: true};
              cluster =  new MarkerClusterer(map, visibleMarkers,mcOptions);
              var header = $('input.header-checkbox').is(':checked');

              if(productsId.length > 1 && !header){
                $('.ubicacion').append(' <p class="nombre-punto texto-inverso"><span id="nombre-store"><strong>'+productsId.length+' productos seleccionados</strong></span> </p>');
              }
              if( productsId.length == 0 || header){
                $('.ubicacion').append(' <p class="nombre-punto texto-inverso"><span id="nombre-store"><strong>Total existencias disponibles</strong></span> </p>');
              }
              if(data.length > 1){
                if (isMobileDevice1) {
                  $('.ubicacion').append('<p class="direccion-punto texto-inverso"><p id="direccion-store" style="color: white;margin-bottom:13px;"><i class="icon-ubicacion" style="margin-right: 8px;"></i>'+data.length+' ubicaciones</p></p>');
                }else{
                  $('.ubicacion').append('<p class="direccion-punto texto-inverso"><p id="direccion-store" style="color: white;"><i class="icon-ubicacion" style="margin-right: 8px;"></i>'+data.length+' ubicaciones</p></p>');
                }
              }else{
                if (isMobileDevice1) {
                  $('.ubicacion').append('<p class="direccion-punto texto-inverso"><p id="direccion-store" style="color: white;   margin-bottom:13px;"><i class="icon-ubicacion" style="margin-right: 8px;"></i>'+data[0].direccion+'</p></p>');
                }else{
                  $('.ubicacion').append('<p class="direccion-punto texto-inverso"><p id="direccion-store" style="color: white;"><i class="icon-ubicacion" style="margin-right: 8px;"></i>'+data[0].direccion+'</p></p>');
               
                }
              
              }
             
             

              
              origenModal.modal();
          });
      }
  });
}

$(function () {

  if (productoError) {
    if ($('#ruta_imagen_temp').val() != "") {
      $('#preview_imagen').attr('src', rutaBaseImagenTemp + $('#ruta_imagen_temp').val());
    }
    $('#modal-anadir-producto').modal();
  }

  if (productoEditError) {
    if ($('#ruta_imagen_temp_edit').val() != "") {
      // if ($('#ruta_imagen_temp_edit').val() != "" && $('#ruta_imagen_temp_edit').val() != rutaImagenPorDefecto){
      $('#preview_imagen_edit').attr('src', rutaBaseImagenTemp + $('#ruta_imagen_temp_edit').val());
      $('#image_changed_control_edit').val("1");
    } else if ($('#ruta_imagen_guardada').val() != "") {
      $('#preview_imagen_edit').attr('src', $('#ruta_imagen_guardada').val());
    }
    $('#modal-editar-producto').modal();
  }

  $('.editarNivelBajo').click(function(){
    $('#EditStockModal').modal();
    let filter = $( "#filtro-stores option:selected" ).text();
    $('#almacenModal').val(filter);
    $('#quantity_input').prop('disabled',true);
    $('.low_input').val(0);
    $('#storeIdHidden').val($( "#filtro-stores option:selected" ).val());
    $('#ocultarNivelBajo').css("display", "none");
    $('#existenciasTitle').text('Editar Nivel Bajo');
    
    


    var tamanio = $('#filtro-stores').children('option').length - 1;

    

    var total = 0;
    if ($('input.header-checkbox').is(':checked')) {
    
      $.ajax({
        url: obtenerProductos,
        headers: { 'X-CSRF-TOKEN': csrf },
        type: 'POST',
        data: { 'filter': filter },
        success: function (data) {
          $('#referenciaModal').val(data+' productos seleccionados');
          $('#nombreModal').val(data+' productos seleccionados');
          $('#productIdHidden').val('-1');
        }
      });
    
      $('#productIdHidden').val('-1');
    }else{

      var pages = getPages();
      var cont = 0;
      var group = [];
      for(var i = 1;i<=pages;i++){
        cont = cont + checkedRows[i].length;
        group = group.concat(checkedRows[i]);
      }

      total = cont;

    

      $('#productIdHidden').val(group);
    }

    $('#referenciaModal').val(total+' productos seleccionados');
    $('#nombreModal').val(total+' productos seleccionados');
   
  });




  $('.import-from-excel-btn').click(function () {
    $('.import-from-excel-form > input').click();
  });

  $('.import-from-excel-form > input').change(function () {

    parseExcelAndPost($('.import-from-excel-form > input[type="file"]').prop('files')[0]);
  });

  $('.export-pdf-btn, .export-xls-btn').click(function (e) {
    e.preventDefault();
    var url = $(this).children('a').attr('href');
    var data = [];
    var filter = $('#filtro-activos').val();

    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        data.push(val);
      });
    });
    if (data.length) {
      $.ajax({
        url: rutaSeleccionProductos,
        headers: { 'X-CSRF-TOKEN': csrf },
        type: 'POST',
        data: { 'data': data, 'filter': filter },
        success: function (data) {
          window.open(url);
        }
      });
    } else {
      // window.open(url);          
      $.ajax({
        url: rutaFiltroProductos,
        headers: { 'X-CSRF-TOKEN': csrf },
        type: 'POST',
        data: { 'filter': filter },
        success: function (data) {
          window.open(url);
        }
      });
    }

    // if(data.length) {
    //     $.ajax({
    //         url: rutaSeleccionProductos,
    //         headers: {'X-CSRF-TOKEN': csrf},
    //         type: 'POST',
    //         data: {'data': data},
    //         success: function (data) {
    //             window.open(url);
    //         }
    //     });
    // } else {
    //     window.open(url);
    // }
  });

  // $('.eliminar-productos').click(function(e) {
  //     e.preventDefault();
  //     $('.delete-products-form > div').empty();
  //     checkedRows.forEach(function(arr) {
  //         arr.forEach(function(val) {
  //             $('.delete-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
  //         });
  //     });
  //     $('.delete-products-form').submit();
  // });

  $('.activar-productos').click(function (e) {
    e.preventDefault();
    $('.activate-products-form > div').empty();
    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        $('.activate-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
      });
    });
    $('.activate-products-form').submit();
  });

  $('.desactivar-productos').click(function (e) {
    e.preventDefault();
    $('.deactivate-products-form > div').empty();
    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        $('.deactivate-products-form > div').append('<input type="hidden" name="ids[]" value="' + val + '">');
      });
    });
    $('.deactivate-products-form').submit();
  });


  $('.buscar-input').keyup(function () {
  
    let text = $(this).val();
    let filter = $('#filtro-stores').val();
   
    filterListData(filter, text);
   
  });


  $('#filtro-stores').change(function (e) {
    

    e.preventDefault();
    let filter = $(this).val();
    let text = $('.buscar-input').val();
    $('#editarNivelBajoSeparator').hide();
    $('#editarNivelBajo').hide();
    var filtroText = $( "#filtro-stores option:selected" ).text();

    if(filtroText == 'Total'){
      
  
      $('#editarExcel').hide();
      $('#verMapa').show();
      $('#editarExcelSeparator').hide();
      $('#verMapaSeparator').show();
      $('#descargarExistencias').hide();
      $('#descargarExistenciasSeparator').hide();
    }else{

     
      if(filtroText.includes("Citystock")){
        $('#editarExcel').hide();
        $('#editarExcelSeparator').hide();
        $('#descargarExistencias').hide();
        $('#descargarExistenciasSeparator').hide();
      }else{
        $('#editarExcel').show();
        $('#editarExcelSeparator').show();
        $('#descargarExistencias').show();
        $('#descargarExistenciasSeparator').show();
      }
     
      $('#verMapa').hide();
      $('#verMapaSeparator').hide();

    }

   
   
    filterListData(filter, text);
  });





  initCheckboxes();
  initPaginationListener();
  initPopovers();
  initEditables();
  hideActivar();
  hideDesactivar();
  initEditable();
});
