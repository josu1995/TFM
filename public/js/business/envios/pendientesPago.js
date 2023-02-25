'use strict';

var checkedRows = [];
var masProductosComponent = '<div class="row form-group producto added">' +
  '<div class="col-lg-6 col-xs-12 form-group-md">' +
  '<label>SKU O NOMBRE PRODUCTO</label>' +
  '<input type="text" class="form-control autocomplete-input" name="nombre_producto_edit[]" value="" required>' +
  '</div>' +
  '<div class="col-lg-3 col-xs-6">' +
  '<label>Nº</label>' +
  '<input type="number" min="1" class="form-control cantidad-input" name="num_productos_edit[]" value="" required>' +
  '</div>' +
  '<div class="col-lg-3 col-xs-6">' +
  '<label class="text-nowrap">PESO (kg)</label>' +
  '<input type="text" class="form-control peso-input" name="peso_producto_edit[]" placeholder="0.0" value="" required>' +
  '</div>' +
  '</div>';
var menosProductosLink;

function initCheckboxes() {
  $('input[type="checkbox"]').iCheck({
    checkboxClass: 'icheckbox_square-yellow',
    radioClass: 'iradio_square',
    increaseArea: '20%' // optional
  });

  $('.header-checkbox').on('ifChecked', function (event) {
    $('input[type="checkbox"]').not('.header-checkbox').iCheck('check');
  });

  $('.header-checkbox').on('ifUnchecked', function (event) {
    $('input[type="checkbox"]').not('.header-checkbox').iCheck('uncheck');
  });

  $('.table-checkbox').on('ifChecked', function (event) {
    disableTableButtons(false);
    addToChecked($(this).val());
  });

  $('.table-checkbox').on('ifUnchecked', function (event) {
    removeFromChecked($(this).val());
    if (!Object.keys(checkedRows).length) {
      disableTableButtons(true);
    }
  });
}

function disableTableButtons(bool) {
  $('#btn-eliminar-envios, #btn-pagar').attr('disabled', bool);
}

function addToChecked(val) {
  var page = getCurrentPage();
  if (!checkedRows[page]) {
    checkedRows[page] = [];
  }
  if (checkedRows[page].indexOf(val) === -1) {
    checkedRows[page].push(val);
  }
}

function removeFromChecked(val) {
  var page = getCurrentPage();
  var index = checkedRows[page].indexOf(val);
  checkedRows[page].splice(index, 1);
  if (!checkedRows[page].length) {
    delete checkedRows[page];
  }
}

function getCurrentPage() {
  var elem = $('.pagination > li.active > span');
  if (!elem.length) {
    return "1";
  } else {
    return $('.pagination > li.active > span').text();
  }
}

function highlightValue(val, text) {
  if (val && val.toLowerCase().includes(text.toLowerCase())) {
    var pos = val.toLowerCase().search(text.toLowerCase());
    var newVal = '';
    if (pos !== 0) {
      newVal += val.substr(0, pos);
    }
    newVal += '<span class="highlight">' + val.substr(pos, text.length) + '</span>';
    if (pos + text.length !== val.length) {
      newVal += val.substr(pos + text.length);
    }
    return newVal;
  }
  return val;
}

function initCpAutocompleteListener(component) {
  var businessAutocomplete = '';
  var originalInput = component.parent().find('.original-input');
  component.keyup(function (e) {
    if (e.keyCode === 8) {
      originalInput.val('');
    }
    var text = $(this).val();
    var self = $(this).parent();
    setTimeout(function () {

      if (text === component.val() && component.is(':focus')) {
        var pais = '';
        if (component.is($('#cp-destino-autocomplete')) && $('input[name="pais_destino_id"]').val() !== '') {
          pais = '&p=' + $('input[name="pais_destino_id"]').val();
        } else if (component.is($('#cp-origen-autocomplete'))) {
          pais = '&p=64';
        }

        $.ajax({
          type: "get",
          url: "/codigos-postales/search?t=" + text + pais,
          success: function (data) {
            if (data.length && component.val() !== '') {
              var autocomplete = '<div class="business-autocomplete">';
              data.forEach(function (val) {
                var cp = highlightValue(val.codigo_postal, text);
                var ciudad = highlightValue(val.ciudad, text);

                autocomplete += '<div class="autocomplete-item">' +
                  '<p id="' + val.id + '">' + cp + ' - ' + ciudad + '</p>' +
                  '</div>';
              });
              autocomplete += '</div>';
              if (businessAutocomplete.length) {
                businessAutocomplete.remove();
              }
              self.append(autocomplete);
              businessAutocomplete = component.parent().find('.business-autocomplete');

              businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                var id = $(this).children('p').attr('id');
                var text = $(this).text();
                businessAutocomplete.parent().children('input').val(text);
                originalInput.val(id);
                if (component.is($('#cp-destino-autocomplete'))) {
                  $('#tipo_entrega_destino, input[name="direccion_destino_edit"], #direccion-destino-store').val('');
                } else {
                  $('#tipo_recogida, input[name="direccion_edit"], #direccion-origen-store').val('');
                }
              });
            } else if (businessAutocomplete.length || component.val() === '') {
              businessAutocomplete.remove()
            }
          }
        });
      }

    }, 200);
  });

  component.blur(function () {
    if (businessAutocomplete.length) {
      businessAutocomplete.hide();
    }
    if (originalInput.val() === '') {
      component.val('');
    }
  });

  component.focus(function () {
    if (businessAutocomplete.length) {
      if (component.val() === '') {
        businessAutocomplete.remove();
      } else {
        businessAutocomplete.show();
      }
    }
  });
}

function initPaisAutocompleteListener(component) {
  var businessAutocomplete = '';
  var originalInput = component.parent().find('.original-input');
  component.keyup(function (e) {
    if (e.keyCode === 8) {
      originalInput.val('');
    }
    var text = $(this).val();
    var self = $(this).parent();
    setTimeout(function () {

      if (text === component.val() && component.is(':focus')) {

        $.ajax({
          type: "get",
          url: "/paises/search?t=" + text,
          success: function (data) {
            if (data.length && component.val() !== '') {
              var autocomplete = '<div class="business-autocomplete">';
              data.forEach(function (val) {
                var pais = highlightValue(val.nombre, text);

                autocomplete += '<div class="autocomplete-item">' +
                  '<p id="' + val.id + '">' + pais + '</p>' +
                  '</div>';
              });
              autocomplete += '</div>';
              if (businessAutocomplete.length) {
                businessAutocomplete.remove();
              }
              self.append(autocomplete);
              businessAutocomplete = component.parent().find('.business-autocomplete');

              businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                var id = $(this).children('p').attr('id');
                var text = $(this).text();
                businessAutocomplete.parent().children('input').val(text);
                originalInput.val(id);
                if (id == 172 || id == 185 || id == 16) {
                  $('#tipo_entrega_destino > option[value="1"]').attr('disabled', false);
                  $('#tipo_entrega_destino > option[value="2"]').attr('disabled', true);
                } else if (id == 140) {
                  $('#tipo_entrega_destino > option[value="1"]').attr('disabled', true);
                  $('#tipo_entrega_destino > option[value="2"]').attr('disabled', false);
                } else {
                  $('#tipo_entrega_destino > option[value="1"]').attr('disabled', false);
                  $('#tipo_entrega_destino > option[value="2"]').attr('disabled', false);
                }
                $('#cp-destino-autocomplete').val('');
                $('#tipo_entrega_destino').val(0);
                $('input[name="direccion_destino_edit"]').val('');
                $('input[name="store_destino_edit"]').val('');
              });
            } else if (businessAutocomplete.length || component.val() === '') {
              businessAutocomplete.remove()
            }
          }
        });
      }

    }, 200);
  });

  component.blur(function () {
    if (businessAutocomplete.length) {
      businessAutocomplete.hide();
    }
    if (originalInput.val() === '') {
      component.val('');
    }
  });

  component.focus(function () {
    if (businessAutocomplete.length) {
      if (component.val() === '') {
        businessAutocomplete.remove();
      } else {
        businessAutocomplete.show();
      }
    }
  });
}

function initProductosAutocompleteListener(component) {
  var businessAutocomplete = '';
  var originalInput = component.parent().find('.original-input');
  component.keyup(function (e) {
    if (e.keyCode === 8) {
      originalInput.val('');
    }
    var text = $(this).val();
    var self = $(this).parent();
    setTimeout(function () {

      if (text === component.val() && component.is(':focus')) {

        $.ajax({
          type: "get",
          url: "/configuracion/productos/search-data?t=" + text,
          success: function (data) {
            if (data.length && component.val() !== '') {
              var autocomplete = '<div class="business-autocomplete">';
              data.forEach(function (val) {
                var referencia = highlightValue(val.referencia, text);
                var nombre = highlightValue(val.nombre, text);
                if (referencia) {
                  autocomplete += '<div class="autocomplete-item">' +
                    '<p id="' + val.id + '" peso="' + val.peso + '">' + referencia + ' - ' + nombre + '</p>' +
                    '</div>';
                } else {
                  autocomplete += '<div class="autocomplete-item">' +
                    '<p id="' + val.id + '" peso="' + val.peso + '">' + nombre + '</p>' +
                    '</div>';
                }
              });
              autocomplete += '</div>';
              if (businessAutocomplete.length) {
                businessAutocomplete.remove();
              }
              self.append(autocomplete);
              businessAutocomplete = $('.business-autocomplete');

              businessAutocomplete.children('.autocomplete-item').mousedown(function () {
                var id = $(this).children('p').attr('id');
                var peso = $(this).children('p').attr('peso');
                var text = $(this).text();
                $(this).parent().parent().children('input').val(text);
                $(this).parent().parent().parent().find('.peso-input').val(peso);
                $(this).parent().parent().parent().find('.cantidad-input').val(1);
                originalInput.val(id);
              });
            } else if (businessAutocomplete.length && component.val() === '') {
              businessAutocomplete.remove();
            }
          }
        });
      }

    }, 200);
  });

  component.blur(function () {
    if (businessAutocomplete.length) {
      businessAutocomplete.hide();
    }
    if (originalInput.val() === '') {
      component.val('');
    }
  });

  component.focus(function () {
    if (businessAutocomplete.length) {
      if (component.val() === '') {
        businessAutocomplete.remove();
      } else {
        businessAutocomplete.show();
      }
    }
  });
}

function seleccionarStore(puntoId, tipoId) {
  if ($('#data').hasClass('origen')) {
    var inputs = '<input type="hidden" class="punto-origen-id-input" name=punto_origen_id value="' + puntoId + '">' +
      '<input type="hidden" class="punto-origen-tipo-input" name=punto_origen_tipo value="' + tipoId + '">';
    $('#origen-form').append(inputs);
    $('#direccion-origen-store > input').val($('.store-active').find('.nombre').text().trim() + ', ' + $('.store-active').find('.direccion').text().trim()).attr('disabled', 'disabled');

    $('.ciudad-origen-input, #tipo_recogida').attr('disabled', true);
    $('#cambiar-origen-link').removeClass('hidden');

    $('#modal-stores-search').modal('hide');
  } else {
    var destinoInputs = '<input type="hidden" class="punto-destino-id-input" name=punto_destino_id value="' + puntoId + '">' +
      '<input type="hidden" class="punto-destino-tipo-input" name=punto_destino_tipo value="' + tipoId + '">';
    $('#destinatario-form').append(destinoInputs);
    $('#direccion-destino-store > input[name="store_destino_edit"]').val($('.store-active').find('.nombre').text().trim() + ', ' + $('.store-active').find('.direccion').text().trim()).attr('disabled', 'disabled');
    // if(!$('#direccion-destino-domicilio').hasClass('hidden')) {
    //     $('#direccion-destino-domicilio').addClass('hidden');
    // }

    // $('#direccion-destino-store').removeClass('hidden');


    $('.ciudad-destino-input, #tipo_entrega_destino, #pais-autocomplete').attr('disabled', true);
    $('#cambiar-destino-link').removeClass('hidden');

    $('#modal-stores-search').modal('hide');
  }
}

function initPaginationListener() {
  $('.pagination li > a').click(function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var busquedaText = $('.buscar-input').val();
    if (busquedaText !== '') {
      url += '&t=' + busquedaText;
    }
    $('.business-table-row').load(url, function () {

      initCheckboxes();
      initPaginationListener();
      initEditable();
      var newPage = getCurrentPage();
      if (checkedRows[newPage] && checkedRows[newPage].length) {
        if (checkedRows[newPage].length === 10) {
          $('.header-checkbox').iCheck('check');
        } else {
          checkedRows[newPage].forEach(function (val) {
            $('input[value="' + val + '"]').iCheck('check');
          });
        }
      }
    });
  });
}

function disablePaqueteFields(bool) {
  $('#modal-editar-paquete').find('input:not([type="hidden"])').each(function () {
    $(this).attr('disabled', bool);
  });
}

function initPopovers() {
  $('.popover-precio').popover({
    trigger: 'hover',
    placement: 'top',
    html: true,
    content: 'IVA no incl.',
    container: 'body',
    template: '<div class="popover business-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
  });
}

function initEditable() {
  $('.editable').click(function () {
    var modalName = $(this).attr('data-modal-name');
    var id = $(this).parent().attr('id');
    var route = $('#modal-editar-' + modalName).find('form').attr('action');

    var editModal = $('#modal-editar-' + modalName);

    editModal.find('form').attr('action', route + '/' + id);

    editModal.find('.alert-danger').remove();

    switch (modalName) {
      case 'origen':
        route = editModal.find('form').attr('data-route').replace('origen_id', id);
        editModal.find('form').attr('action', route);
        editModal.find('#codigo-postal-origen-input').val($(this).find('.cp-origen').attr('data-id'));
        editModal.find('#cp-origen-autocomplete').val($(this).find('.cp-origen').text() + ' - ' + $(this).find('.ciudad-origen').text());
        editModal.find('#tipo_recogida').val($(this).find('.tipo-recogida-origen').text());
        editModal.find('input[name="tipo_recogida_id"]').val($(this).find('.tipo-recogida-origen').text());
        if ($(this).find('.direccion-origen').length) {
          editModal.find('input[name="direccion_edit"]').val($(this).find('.direccion-origen').text());
          editModal.find('#tipo_recogida').attr('disabled', false);
          editModal.find('#cp-origen-autocomplete').attr('disabled', false);
          if (!$('#cambiar-origen-link').hasClass('hidden')) {
            $('#cambiar-origen-link').addClass('hidden');
          }
          if (!editModal.find('#direccion-origen-store').hasClass('hidden')) {
            editModal.find('#direccion-origen-store').addClass('hidden');
          }
          editModal.find('#direccion-origen-domicilio').removeClass('hidden');
        } else {
          editModal.find('input[name="store_edit"]').val($(this).find('.store-origen').text()).attr('disabled', true);
          editModal.find('input[name="store_origen_id"]').val($(this).find('.store-origen').attr('data-id'));
          editModal.find('#tipo_recogida').attr('disabled', true);
          editModal.find('#cp-origen-autocomplete').attr('disabled', true);
          $('#cambiar-origen-link').removeClass('hidden');
          if (!editModal.find('#direccion-origen-domicilio').hasClass('hidden')) {
            editModal.find('#direccion-origen-domicilio').addClass('hidden');
          }
          editModal.find('#direccion-origen-store').removeClass('hidden');
        }
        break;
      case 'pedido':
        route = editModal.find('form').attr('data-route').replace('pedido_id', id);
        editModal.find('form').attr('action', route);
        editModal.find('.producto.added').remove();
        editModal.find('input[name="referencia_edit"]').val($(this).find('.pedido-id:first').text());
        $(this).find('.producto-cell').each(function (index) {
          if (index !== 0) {
            menosProductosLink.removeClass('hidden');
            $('#mas-productos-link').parent().before(masProductosComponent);
          } else {
            menosProductosLink.addClass('hidden');
          }
          editModal.find('input[name="nombre_producto_edit[]"]:last').val($(this).find('.producto-nombre').text());
          editModal.find('input[name="peso_producto_edit[]"]:last').val($(this).find('.peso-hidden').text());
          var cantidad = $(this).find('.producto-num').text();
          editModal.find('input[name="num_productos_edit[]"]:last').val(cantidad !== '' ? cantidad : '1');
          initProductosAutocompleteListener(editModal.find('input[name="num_productos_edit[]"]:last'));
        });

        break;
      case 'paquete':
        route = editModal.find('form').attr('data-route').replace('paquete_id', id);
        editModal.find('form').attr('action', route);
        var self = $(this);
        if ($(this).find('.paquete-nombre').text() === 'Personalizado') {
          editModal.find('select[name="embalaje_edit"] option[value="0"]').attr('selected', true);
          disablePaqueteFields(false);
        } else {
          editModal.find('select[name="embalaje_edit"] option').filter(function () {
            return $(this).html() === self.find('.paquete-nombre').text();
          }).attr('selected', true);
          disablePaqueteFields(true);
        }
        editModal.find('input[name="largo"]').val($(this).find('.paquete-largo').text());
        editModal.find('input[name="alto"]').val($(this).find('.paquete-alto').text());
        editModal.find('input[name="ancho"]').val($(this).find('.paquete-ancho').text());
        break;
      case 'destinatario':
        route = editModal.find('form').attr('data-route').replace('destinatario_id', id);
        editModal.find('form').attr('action', route);
        editModal.find('input[name="nombre_edit"]').val($(this).find('.destinatario-nombre').text());
        editModal.find('input[name="apellidos_edit"]').val($(this).find('.destinatario-apellido').text());
        editModal.find('input[name="email_edit"]').val($(this).find('.destinatario-email').text());
        editModal.find('input[name="telefono_edit"]').val($(this).find('.destinatario-telefono').text());
        editModal.find('select[name="prefijo"]').val($(this).find('.destinatario-telefono').attr('data-prefijo'));

        editModal.find('input[name="pais_destino_id"]').val($(this).find('.pais-destino').attr('data-pais-id'));
        editModal.find('input[name="pais_destino_edit"]').val($(this).find('.pais-destino').attr('data-pais-nombre'));
        editModal.find('input[name="cp_destino_id"]').val($(this).find('.cp-destino').attr('data-id'));
        editModal.find('input[name="codigo_postal_destino_edit"]').val($(this).find('.cp-destino').text() + ' - ' + $(this).find('.ciudad-destino').text());

        editModal.find('#tipo_entrega_destino').val($(this).find('.tipo-entrega-destino').text());
        editModal.find('input[name="tipo_entrega_destino_id"]').val($(this).find('.tipo-entrega-destino').text());

        $('.punto-destino-id-input, .punto-destino-tipo-input').remove();
        if ($(this).find('.direccion-destino').length) {
          editModal.find('input[name="direccion_destino_edit"]').val($(this).find('.direccion-destino').text());
          editModal.find('#cp-destino-autocomplete, #pais-autocomplete, #tipo_entrega_destino').attr('disabled', false);
          if (!$('#cambiar-destino-link').hasClass('hidden')) {
            $('#cambiar-destino-link').addClass('hidden');
          }
          if (!editModal.find('#direccion-destino-store').hasClass('hidden')) {
            editModal.find('#direccion-destino-store').addClass('hidden');
          }
          editModal.find('#direccion-destino-domicilio').removeClass('hidden');
        } else {
          editModal.find('input[name="store_destino_edit"]').val($(this).find('.store-destino').text()).attr('disabled', true);
          editModal.find('input[name="punto_destino_id"], input[name="punto_destino_tipo"]').remove();
          var idInput = '<input type="hidden" class="punto-destino-id-input" name="punto_destino_id" value="' + $(this).find('.store-destino').attr('data-id') + '">';
          var tipoInput = '<input type="hidden" class="punto-destino-id-input" name="punto_destino_tipo" value="' + $(this).find('.store-destino').attr('data-id').substring(0, 1) + '">';
          editModal.find('#destinatario-form').append(idInput);
          editModal.find('#destinatario-form').append(tipoInput);
          editModal.find('#tipo_entrega_destino, #cp-destino-autocomplete, #pais-autocomplete').attr('disabled', true);
          $('#cambiar-destino-link').removeClass('hidden');
          if (!editModal.find('#direccion-destino-domicilio').hasClass('hidden')) {
            editModal.find('#direccion-destino-domicilio').addClass('hidden');
          }
          editModal.find('#direccion-destino-store').removeClass('hidden');
        }

        break;
    }

    // $(this).parent().find('.field').each(function() {
    //
    //     var text = $(this).find('.value').text().trim();
    //     var name = $(this).attr('data-edit-name');
    //     editModal.find('input[name="' + name + '"]').val(text);
    // });
    editModal.modal();
  });
}

$(function () {

  menosProductosLink = $('#menos-productos-link');
  var tipoRecogidaOrigen = $('#tipo_recogida');
  var cpOrigenAutocomplete = $('#cp-origen-autocomplete');
  var direccionOrigenStore = $('#direccion-origen-store');
  var direccionOrigenDomicilio = $('#direccion-origen-domicilio');

  var tipoEntregaDestino = $('#tipo_entrega_destino');
  var cpDestinoAutocomplete = $('#cp-destino-autocomplete');
  var direccionDestinoStore = $('#direccion-destino-store');
  var direccionDestinoDomicilio = $('#direccion-destino-domicilio');

  if (origenEditError) {
    $('#modal-editar-origen').modal();
  }

  if (pedidoEditError) {
    $('#modal-editar-pedido').modal();
  }

  if (paqueteEditError) {
    $('#modal-editar-paquete').modal();
  }

  if (destinatarioEditError) {
    $('#modal-editar-destinatario').modal();
  }

  $('#cambiar-origen-link').click(function () {
    $('#modal-editar-origen input:not([type="hidden"])').each(function () {
      $(this).attr('disabled', false).val('');
    });
    $('#modal-editar-origen select option:selected').attr('selected', false);
    $('#modal-editar-origen select').attr('disabled', false);
    $(this).addClass('hidden');
  });

  $('#cambiar-destino-link').click(function () {
    $('#modal-editar-destinatario input:disabled').each(function () {
      $(this).attr('disabled', false).val('');
    });
    $('#modal-editar-destinatario select option:selected').attr('selected', false);
    $('#modal-editar-destinatario select').attr('disabled', false);
    $(this).addClass('hidden');
  });

  $('#modal-editar-paquete').find('select[name="embalaje_edit"]').change(function () {
    if ($(this).find('option:selected').val() == 0 || $(this).find('option:selected').val() == -1) {
      disablePaqueteFields(false);
      $('#modal-editar-paquete').find('input[name="largo"]').val('');
      $('#modal-editar-paquete').find('input[name="alto"]').val('');
      $('#modal-editar-paquete').find('input[name="ancho"]').val('');
    } else {
      disablePaqueteFields(true);
      $('#modal-editar-paquete').find('input[name="largo"]').val($(this).find('option:selected').attr('largo'));
      $('#modal-editar-paquete').find('input[name="alto"]').val($(this).find('option:selected').attr('alto'));
      $('#modal-editar-paquete').find('input[name="ancho"]').val($(this).find('option:selected').attr('ancho'));
    }
  });

  $('#modal-editar-paquete').submit(function () {
      $(this).find('input').attr('disabled', false);
      return true;
  });

  $('#mas-productos-link').click(function () {
    menosProductosLink.removeClass('hidden');
    $(this).parent().before(masProductosComponent);
    initProductosAutocompleteListener($('#modal-editar-pedido').find('.producto:last input[name="nombre_producto_edit[]"]'));
  });

  menosProductosLink.click(function () {
    $('.producto:last').remove();
    if ($('.producto').length === 0) {
      menosProductosLink.addClass('hidden');
    }
  });

  tipoRecogidaOrigen.change(function () {
    $('.punto-origen-id-input, .punto-origen-tipo-input').remove();
    $('#direccion-origen-store > input').val('').attr('disabled', false);
    var val = $(this).val();
    $('input[name="tipo_recogida_id"]').val(val);
    if (val == 2 && cpOrigenAutocomplete.val() === '') {
      $(this).val('');
      new PNotify({
        title: 'Transporter',
        text: 'Primero debes seleccionar un código postal o ciudad.',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
      cpOrigenAutocomplete.focus();
    } else {
      if (val == 1 && !direccionOrigenStore.hasClass('hidden')) {
        direccionOrigenStore.addClass('hidden');
        direccionOrigenDomicilio.removeClass('hidden');
      } else if (val == 2 && !direccionOrigenDomicilio.hasClass('hidden')) {
        direccionOrigenDomicilio.addClass('hidden');
        direccionOrigenStore.removeClass('hidden');
      }
    }

    if (val == 2) {
      $('.ciudad-input').val($('.ciudad-origen-input').val());
      $.ajax({
        type: "post",
        url: '/configuracion/punto-recogida/selection',
        data: {
          cp_id: $('#codigo-postal-origen-input').val(),
          tipo_recogida: tipoRecogidaOrigen.val(),
          codigo_postal: $('.ciudad-origen-input').val(),
          _token: $('input[name="_token"]').val()
        },
        success: function (data) {
          $('#data').empty();
          $('#data').removeClass('origen').removeClass('destino').addClass('origen');
          if (typeof mapa !== 'undefined' && tipoRecogidaOrigen.val() == 2) {
            $('#data').append(data);
            $('#modal-stores-search').on('show.bs.modal', function () {
              $('#modal-editar-origen').modal('hide');
            });
            $('#modal-stores-search').on('hide.bs.modal', function () {
              if (direccionOrigenStore.val() === '') {
                tipoRecogidaOrigen.val('');
              }
            });
            $('#modal-stores-search').on('hidden.bs.modal', function () {
              $('#modal-editar-origen').modal();
            });

          } else {
            $.when(
              $.getScript(storesSearchJs),
              $.getScript(customScrollJs),
              $.Deferred(function (deferred) {
                $(deferred.resolve);
              })
            ).done(function () {

              $('#data').append(data);
              $('#modal-stores-search').on('show.bs.modal', function () {
                $('#modal-editar-origen').modal('hide');
              });
              $('#modal-stores-search').on('hide.bs.modal', function () {
                if (direccionOrigenStore.find('input').val() === '') {
                  tipoRecogidaOrigen.val('');
                }
              });

              $('#modal-stores-search').on('hidden.bs.modal', function () {
                $('#modal-editar-origen').modal();
              });

            });
          }

          $('.punto-origen-id-input, .punto-origen-tipo-input').remove();

        }
      });
    }
  });

  tipoEntregaDestino.change(function () {
    $('.punto-destino-id-input, .punto-destino-tipo-input').remove();
    $('#direccion-destino-store > input').val('').attr('disabled', false);
    var val = $(this).val();
    $('input[name="tipo_entrega_destino_id"]').val(val);
    if (val == 2 && cpDestinoAutocomplete.val() === '') {
      $(this).val('');
      new PNotify({
        title: 'Transporter',
        text: 'Primero debes seleccionar un código postal o ciudad.',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
      cpDestinoAutocomplete.focus();
    } else {
      if (val == 1 && !direccionDestinoStore.hasClass('hidden')) {
        direccionDestinoStore.addClass('hidden');
        direccionDestinoDomicilio.removeClass('hidden');
      } else if (val == 2 && !direccionDestinoDomicilio.hasClass('hidden')) {
        direccionDestinoDomicilio.addClass('hidden');
        direccionDestinoStore.removeClass('hidden');
      }
    }

    if (val == 2) {
      $('.ciudad-input').val($('.ciudad-destino-input').val());
      $.ajax({
        type: "post",
        url: '/configuracion/punto-recogida/selection',
        data: {
          cp_id: $('#codigo-postal-destino-input').val(),
          tipo_recogida: tipoEntregaDestino.val(),
          codigo_postal: $('.ciudad-destino-input').val(),
          _token: $('input[name="_token"]').val()
        },
        success: function (data) {
          $('#data').empty();
          $('#data').removeClass('origen').removeClass('destino').addClass('destino');
          if (typeof mapa !== 'undefined' && tipoRecogidaOrigen.val() == 2) {
            $('#data').append(data);
            $('#modal-stores-search').on('show.bs.modal', function () {
              $('#modal-editar-destinatario').modal('hide');
            });
            $('#modal-stores-search').on('hide.bs.modal', function () {
              if (direccionDestinoStore.val() === '') {
                $('#tipo_entrega_destino').val('');
              }
            });
            $('#modal-stores-search').on('hidden.bs.modal', function () {
              $('#modal-editar-destinatario').modal();
            });

          } else {
            $.when(
              $.getScript(storesSearchJs),
              $.getScript(customScrollJs),
              $.Deferred(function (deferred) {
                $(deferred.resolve);
              })
            ).done(function () {

              $('#data').append(data);
              $('#modal-stores-search').on('show.bs.modal', function () {
                $('#modal-editar-destinatario').modal('hide');
              });
              $('#modal-stores-search').on('hide.bs.modal', function () {
                if (direccionDestinoStore.find('input').val() === '') {
                  $('#modal-editar-destinatario').find('#tipo_entrega_destino').val('');
                }
              });

              $('#modal-stores-search').on('hidden.bs.modal', function () {
                $('#modal-editar-destinatario').modal();
              });

            });
          }

          $('.punto-destino-id-input, .punto-destino-tipo-input').remove();

        }
      });
    }
  });

  initCheckboxes();
  initPaginationListener();
  initCpAutocompleteListener($('#cp-origen-autocomplete'));
  initPaisAutocompleteListener($('#pais-autocomplete'));
  initCpAutocompleteListener($('#cp-destino-autocomplete'));
  $('#producto-autocomplete,.producto .autocomplete-input').each(function () {
    initProductosAutocompleteListener($(this));
  });
  initPopovers();
  initEditable();


  $('#btn-eliminar-envios').click(function () {
    $('#modal-eliminar').modal();
  });

  $('#btn-delete').click(function (e) {
    // e.preventDefault();
    $('#delete-form .ids').empty();
    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        $('#delete-form .ids').append('<input type="hidden" name="ids[]" value="' + val + '">');
      });
    });
    $('#delete-form').submit();
  });

  $('#btn-pagar').click(function () {

    $('#pagar-form > input.id').remove();

    var ids = [];
    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        var input = '<input type="hidden" class="id" name="ids[]" value="' + val + '">';
        $('#pagar-form').append(input);
        ids.push(val);
      });
    });

    $('#pagar-form').submit();
  });

  $('.buscar-input').keyup(function () {
    var text = $(this).val();
    var self = $(this);
    setTimeout(function () {
      if (text === self.val()) {
        $('.business-table-row').load(rutaSearchEnvios + '?t=' + encodeURIComponent(text), function () {
          initCheckboxes();
          initPaginationListener();
          initPopovers();
          initEditable();
          checkedRows = [];
        });
      }
    }, 200);
  });
});