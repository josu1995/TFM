'use strict';

var checkedRows = [];
var mobile = screen.width < 768;

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
        showEnviarEtiqueta();
        disableTableButtons(false);
        addToChecked($(this).val());
    });

    $('.table-checkbox').on('ifUnchecked', function (event) {
        removeFromChecked($(this).val());
        if(!Object.keys(checkedRows).length) {
            disableTableButtons(true);
            hideEnviarEtiqueta();
        }
    });
}

function showEnviarEtiqueta() {
    $('.send-etiqueta-btn, .send-etiqueta-divider, .see-etiqueta-pdf').show();
}

function hideEnviarEtiqueta() {
    $('.send-etiqueta-btn, .send-etiqueta-divider, .see-etiqueta-pdf').hide();
}

function disableTableButtons(bool) {
    $('#btn-etiquetas').attr('disabled', bool);
}

function addToChecked(val) {
    var page = getCurrentPage();
    if(!checkedRows[page]) {
        checkedRows[page] = [];
    }
    if(checkedRows[page].indexOf(val) === -1) {
        checkedRows[page].push(val);
    }
}

function removeFromChecked(val) {
    var page = getCurrentPage();
    var index = checkedRows[page].indexOf(val);
    checkedRows[page].splice(index, 1);
    if(!checkedRows[page].length) {
        delete checkedRows[page];
    }
}

function getCurrentPage() {
    var elem = $('.pagination > li.active > span');
    if(!elem.length) {
        return "1";
    } else {
        return $('.pagination > li.active > span').text();
    }
}

function initPaginationListener() {
    $('.pagination li > a').click(function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var busquedaText = $('.buscar-input').val();
        if(busquedaText !== '') {
            url += '&t=' + busquedaText;
        }
        $('.business-table-row').load(url, function () {

            initCheckboxes();
            initPaginationListener();
            initPopovers();
            initSeguimientoLinks();
            initEditable();
            var newPage = getCurrentPage();
            if(checkedRows[newPage] && checkedRows[newPage].length) {
                if(checkedRows[newPage].length === 10) {
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
    if(!elem.length) {
        return "1";
    } else {
        return $('.pagination > li.active > span').text();
    }
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

function initSeguimientoLinks() {
    $('.seguimiento-link').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $('#data').load(url, function() {
            if(!mobile) {
                $('#data > #l-envio-popup').on('shown.bs.modal', function () {
                    customScroll('.estados-info');
                });
            } else {
                $('.close-icon').click(function() {
                    $('.tracking-modal').modal('hide');
                });
                $('#data > #l-envio-popup').on('shown.bs.modal', function () {
                    let sL = $('.estados-info').width();
                    $('.estados-info').animate({
                        scrollLeft: 1000
                    }, 2000);
                });
            }
            crearMapa();
            $('#data > .modal').modal();
        });
    });
}

function initEditable() {
    $('.editable').click(function() {
        var modalName = $(this).attr('data-modal-name');
        var id = $(this).parent().attr('data-envio-id');

        if(modalName === 'origen') {
            var origenModal = $('#l-envio-popup');
            $.get( showOrigenRoute.replace('envio_id', id), function( data ) {

                $('#nombre-store').html('<strong>' + data.nombre + '</strong>');
                if(data.calle) {
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
                if(data.horarios) {
                    $('.m-info-punto').show();
                    $('.horario-punto .mañana, .horario-punto .tarde').remove();
                    data.horarios.forEach(function (horario) {
                        if (!$('.horario-punto .' + horario.dia + ' .mañana').length) {
                            td = '<td class="mañana">';
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
        }


    });

    $('.expandable-motivo').click(function (e) {
        e.preventDefault();
        var route = $(this).attr('href');
        $.get(route, function(data) {
            $('#motivo-data').empty().append(data);
            $('#modal-detalles-motivo').modal();
        });
    });

}

$(function() {

    $('.export-pdf-btn, .export-xls-btn').click(function(e) {
        e.preventDefault();
        var url = $(this).children('a').attr('href');
        var data = [];
        checkedRows.forEach(function(arr) {
            arr.forEach(function(val) {
                data.push(val);
            });
        });
        if(data.length) {
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

    $('.see-etiqueta-pdf').click(function(e) {
        e.preventDefault();
        if(checkedRows.length > 0) {
            var url = $(this).children('a').attr('href');
            var data = [];
            checkedRows.forEach(function (arr) {
                arr.forEach(function (val) {
                    data.push($('#'+val).attr('data-envio-id'));
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

    $('.send-etiqueta-btn').click(function(e) {
        e.preventDefault();
        if(checkedRows.length > 0) {
            var url = $(this).children('a').attr('href');
            var data = [];
            checkedRows.forEach(function (arr) {
                arr.forEach(function (val) {
                    data.push($('#'+val).attr('data-envio-id'));
                });
            });
            if (data.length) {
                $.ajax({
                    url: url,
                    headers: {'X-CSRF-TOKEN': csrf},
                    type: 'POST',
                    data: {'data': data},
                    success: function (data) {
                        new PNotify({
                            title: 'Transporter',
                            text: 'Etiquetas enviadas correctamente',
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

    $('.buscar-input').keyup(function() {
        var text = $(this).val();
        var self = $(this);
        setTimeout(function() {
            if(text === self.val()) {
                $('.business-table-row').load(rutaSearchDevoluciones + '?t=' + encodeURIComponent(text), function () {
                    initCheckboxes();
                    initPaginationListener();
                    initPopovers();
                    initSeguimientoLinks();
                    initEditable();
                    checkedRows = [];
                });
            }
        }, 200);
    });

    initCheckboxes();
    initPaginationListener();
    initPopovers();
    initSeguimientoLinks();
    initEditable();
});