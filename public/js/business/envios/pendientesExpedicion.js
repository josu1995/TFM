'use strict';

var checkedRows = [];

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
    showCancelar();
    disableTableButtons(false);
    addToChecked($(this).val());
  });

  $('.table-checkbox').on('ifUnchecked', function (event) {
    removeFromChecked($(this).val());
    if (!Object.keys(checkedRows).length) {
      disableTableButtons(true);
      hideCancelar();
    }
  });
}

function showCancelar() {
  $('.cancelar-envios, .cancelar-divider').show();
}

function hideCancelar() {
  $('.cancelar-envios, .cancelar-divider').hide();
}

function disableTableButtons(bool) {
  $('#btn-etiquetas').attr('disabled', bool);
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

function disablePaqueteFields(bool) {
  $('#modal-editar-paquete').find('input:not([type="hidden"])').each(function () {
    $(this).attr('disabled', bool);
  });
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
      initEtiquetasListener();
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

function getCurrentPage() {
  var elem = $('.pagination > li.active > span');
  if (!elem.length) {
    return "1";
  } else {
    return $('.pagination > li.active > span').text();
  }
}

function initEditable() {
  $('.editable').click(function () {
    var modalName = $(this).attr('data-modal-name');
    var id = $(this).parent().attr('id');

    if (modalName === 'origen') {
      var origenModal = $('#l-envio-popup');
      $.get(showOrigenRoute.replace('envio_id', id), function (data) {

        $('#nombre-store').html('<strong>' + data.nombre + '</strong>');
        if (data.calle) {
          $('.show-store-modal').removeClass('domicilio store').addClass('store');
          $('.icon-ubicacion').removeClass('hidden');
          $('#direccion-store').text(data.calle);
        } else {
          $('.show-store-modal').removeClass('store domicilio').addClass('domicilio');
          $('.icon-ubicacion').addClass('hidden');
          $('#direccion-store').text('');
        }
        $('.nombre-ciudad').html('(' + data.ciudad + ')');
        $('.punto-mapa-iframe').attr('src', 'https://maps.google.com/maps?q=' + data.latitud + ',' + data.longitud + '&hl=es;z=14&output=embed');
        var img = '<img src="' + data.imagen + '" onerror="this.onerror=null;this.src=\'/img/home/store-no-img.png\';">';
        $('.foto-punto img').replaceWith(img);

        var td = null;
        if (data.horarios) {
          origenModal.find('.horario-punto td').remove();
          $('.m-info-punto').show();
          data.horarios.forEach(function (horario) {
            if (!$('.horario-punto .' + horario.dia + ' .mañana').length) {
              switch (horario.dia) {
                case 1:
                  td = '<td><strong>Lunes</strong></td>';
                  break;
                case 2:
                  td = '<td><strong>Martes</strong></td>';
                  break;
                case 3:
                  td = '<td><strong>Miércoles</strong></td>';
                  break;
                case 4:
                  td = '<td><strong>Jueves</strong></td>';
                  break;
                case 5:
                  td = '<td><strong>Viernes</strong></td>';
                  break;
                case 6:
                  td = '<td><strong>Sábado</strong></td>';
                  break;
                case 7:
                  td = '<td><strong>Domingo</strong></td>';
                  break;
              }
              td += '<td class="mañana">';
              if (horario.cerrado) {
                td += '<small class="texto-recogida">Cerrado</small>';
              } else {
                td += '<small>' + horario.inicio.substring(0, 5) + ' - ' + horario.fin.substring(0, 5) + '</small>';
              }
              td += '</td>';
            } else {
              td = '<td class="tarde">';
              if (horario.cerrado) {
                td += '<small class="texto-recogida">Cerrado</small>';
              } else {
                td += '<small>' + horario.inicio.substring(0, 5) + ' - ' + horario.fin.substring(0, 5) + '</small>';
              }
              td += '</td>';
            }

            $('.horario-punto .' + horario.dia).append(td);
          });
        } else {
          $('.m-info-punto').hide();
        }
        origenModal.modal();
      });
    } else {
      var route = $('#modal-editar-' + modalName).find('form').attr('action');

      var editModal = $('#modal-editar-' + modalName);

      editModal.find('form').attr('action', route + '/' + id);

      route = editModal.find('form').attr('data-route').replace('paquete_id', id);
      editModal.find('form').attr('action', route);

      editModal.find('.alert-danger').remove();

      if ($(this).find('.paquete-nombre').text() === 'Personalizado') {
        editModal.find('select[name="embalaje_edit"] option[value="0"]').attr('selected', true);
        disablePaqueteFields(false);
      } else {
        var self = $(this);
        editModal.find('select[name="embalaje_edit"] option').filter(function () {
          return $(this).html() === self.find('.paquete-nombre').text();
        }).attr('selected', true);
        disablePaqueteFields(true);
      }
      editModal.find('input[name="largo"]').val($(this).find('.paquete-largo').text());
      editModal.find('input[name="alto"]').val($(this).find('.paquete-alto').text());
      editModal.find('input[name="ancho"]').val($(this).find('.paquete-ancho').text());

      editModal.modal();
    }

  });
}

function initEtiquetasListener() {
  $('.localizador-td').off('click');
  $('.localizador-td').click(function (e) {
    e.preventDefault();
    var self = $(this);
    if (checkedRows.length > 0 || self.hasClass('localizador-td')) {
      var url = $(this).children('a').attr('href');
      var data = [];
      checkedRows.forEach(function (arr) {
        arr.forEach(function (val) {
          data.push(val);
        });
      });
      var button = $(this);
      if (data.length) {
        $.ajax({
          url: rutaSeleccionEnvios,
          headers: {'X-CSRF-TOKEN': csrf},
          type: 'POST',
          data: {'data': data},
          success: function (data) {
            var v = window.open(url);
            if (self.hasClass('print-etiqueta-pdf')) {
              v.print();
            }
          }
        });
      } else {
        var v = window.open(url);
        if (self.hasClass('print-etiqueta-pdf')) {
          v.print();
        }
      }
    } else {
      new PNotify({
        title: 'Transporter',
        text: 'Primero debes seleccionar un envío',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
    }
  });
}

$(function () {

  var tipoRecogidaOrigen = $('#tipo_recogida');
  var cpOrigenAutocomplete = $('#cp-origen-autocomplete');
  var direccionOrigenStore = $('#direccion-origen-store');
  var direccionOrigenDomicilio = $('#direccion-origen-domicilio');

  $('#modal-editar-paquete').find('select[name="embalaje_edit"]').change(function () {
    if ($(this).find('option:selected').val() == 0) {
      disablePaqueteFields(false);
      $('#modal-editar-paquete').find('input[name="largo"]').val(0);
      $('#modal-editar-paquete').find('input[name="alto"]').val(0);
      $('#modal-editar-paquete').find('input[name="ancho"]').val(0);
    } else {
      disablePaqueteFields(true);
      $('#modal-editar-paquete').find('input[name="largo"]').val($(this).find('option:selected').attr('largo'));
      $('#modal-editar-paquete').find('input[name="alto"]').val($(this).find('option:selected').attr('alto'));
      $('#modal-editar-paquete').find('input[name="ancho"]').val($(this).find('option:selected').attr('ancho'));
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
                $('#tipo_recogida_origen').val('');
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
                  $('#tipo_recogida_origen').val('');
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

  $('.see-etiqueta-pdf, .export-etiqueta-pdf, .print-etiqueta-pdf').click(function (e) {
    e.preventDefault();
    var self = $(this);
    if (checkedRows.length > 0 || self.hasClass('localizador-td')) {
      var url = $(this).children('a').attr('href');
      var data = [];
      checkedRows.forEach(function (arr) {
        arr.forEach(function (val) {
          data.push(val);
        });
      });
      var button = $(this);
      if (data.length) {
        $.ajax({
          url: rutaSeleccionEnvios,
          headers: {'X-CSRF-TOKEN': csrf},
          type: 'POST',
          data: {'data': data},
          success: function (data) {
            var v = window.open(url);
            if (self.hasClass('print-etiqueta-pdf')) {
              v.print();
            }
          }
        });
      } else {
        var v = window.open(url);
        if (self.hasClass('print-etiqueta-pdf')) {
          v.print();
        }
      }
    } else {
      new PNotify({
        title: 'Transporter',
        text: 'Primero debes seleccionar un envío',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
    }
  });

  $('.export-pdf-btn, .export-xls-btn').click(function (e) {
    e.preventDefault();
    var url = $(this).children('a').attr('href');
    var data = [];
    checkedRows.forEach(function (arr) {
      arr.forEach(function (val) {
        data.push(val);
      });
    });
    if (data.length) {
      $.ajax({
        url: rutaSeleccionEnvios,
        headers: {'X-CSRF-TOKEN': csrf},
        type: 'POST',
        data: {'data': data},
        success: function (data) {
          window.open(url);
        }
      });
    } else {
      window.open(url);
    }
  });

  $('.cancelar-envios').click(function (e) {
    e.preventDefault();
    if (checkedRows.length > 0) {
      var url = $(this).children('a').attr('href');
      var data = [];
      checkedRows.forEach(function (arr) {
        arr.forEach(function (val) {
          data.push(val);
        });
      });
      var button = $(this);
      if (data.length) {
        $.ajax({
          url: rutaSeleccionEnvios,
          headers: {'X-CSRF-TOKEN': csrf},
          type: 'POST',
          data: {'data': data},
          success: function (data) {
            // window.location.href = button.find('a').attr('href');
            // window.open(url);
            $('#modal-eliminar').modal();
          }
        });
      } else {
        // window.open(url);
        // window.location.href = button.find('a').attr('href');
      }
    } else {
      new PNotify({
        title: 'Transporter',
        text: 'Primero debes seleccionar un envío',
        addclass: 'transporter-alert',
        icon: 'icon-transporter',
        autoDisplay: true,
        hide: true,
        delay: 5000,
        closer: false,
      });
    }
  });

  $('#btn-delete').click(function () {
    $('#delete-form').submit();
  });

  $('.buscar-input').keyup(function () {
    var text = $(this).val();
    var self = $(this);
    setTimeout(function () {
      if (text === self.val()) {
        $('.business-table-row').load(rutaSearchEnvios + '?t=' + encodeURIComponent(text), function () {
          initCheckboxes();
          initPaginationListener();
          initEtiquetasListener();
          initEditable();
          checkedRows = [];
        });
      }
    }, 200);
  });

  $('#modal-editar-paquete').submit(function () {
    $(this).find('input').attr('disabled', false);
    return true;
  });

  if (paqueteEditError) {
    $('#modal-editar-paquete').modal();
  }

  initCheckboxes();
  initPaginationListener();
  initEtiquetasListener();
  initEditable();
});