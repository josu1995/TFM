'use strict';

var checkedRows = [];
var charging = false;
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
        disableTableButtons(false);
        addToChecked($(this).val());
    });

    $('.table-checkbox').on('ifUnchecked', function (event) {
        removeFromChecked($(this).val());
        if(!Object.keys(checkedRows).length) {
            disableTableButtons(true);
        }
    });
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

function disablePaqueteFields(bool) {
    $('#modal-editar-paquete').find('input:not([type="hidden"])').each(function() {
        $(this).attr('disabled', bool);
    });
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
            initSeguimientoLinks();
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

function initSeguimientoLinks() {
    $('.seguimiento-link').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if(!charging) {
            charging = true;
            $('#data').load(url, function () {

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
                $('#l-envio-popup').modal();
                charging = false;
            });
        }
    });
}

$(function() {

    var tipoRecogidaOrigen = $('#tipo_recogida');
    var cpOrigenAutocomplete = $('#cp-origen-autocomplete');
    var direccionOrigenStore = $('#direccion-origen-store');
    var direccionOrigenDomicilio = $('#direccion-origen-domicilio');

    $('#modal-editar-paquete').find('select[name="embalaje_edit"]').change(function() {
        if($(this).find('option:selected').val() == 0) {
            disablePaqueteFields(false);
            $('#modal-editar-paquete').find('input[name="largo_edit"]').val(0);
            $('#modal-editar-paquete').find('input[name="alto_edit"]').val(0);
            $('#modal-editar-paquete').find('input[name="ancho_edit"]').val(0);
        } else {
            disablePaqueteFields(true);
            $('#modal-editar-paquete').find('input[name="largo_edit"]').val($(this).find('option:selected').attr('largo'));
            $('#modal-editar-paquete').find('input[name="alto_edit"]').val($(this).find('option:selected').attr('alto'));
            $('#modal-editar-paquete').find('input[name="ancho_edit"]').val($(this).find('option:selected').attr('ancho'));
        }
    });

    tipoRecogidaOrigen.change(function() {
        $('.punto-origen-id-input, .punto-origen-tipo-input').remove();
        $('#direccion-origen-store > input').val('').attr('disabled', false);
        var val = $(this).val();
        $('input[name="tipo_recogida_id"]').val(val);
        if(val == 2 && cpOrigenAutocomplete.val() === '') {
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

        if(val == 2) {
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
                success: function(data) {
                    $('#data').empty();
                    $('#data').removeClass('origen').removeClass('destino').addClass('origen');
                    if(typeof mapa !== 'undefined' && tipoRecogidaOrigen.val() == 2) {
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

    $('.cancelar-envios').click(function(e) {
        e.preventDefault();
        if(checkedRows.length > 0) {
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

    $('.see-etiqueta-pdf').click(function(e) {
        e.preventDefault();
        if(checkedRows.length > 0) {
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
                        window.open(url);
                    }
                });
            } else {
                window.open(url);
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

    $('#btn-delete').click(function() {
        $('#delete-form').submit();
    });

    $('.buscar-input').keyup(function() {
        var text = $(this).val();
        var self = $(this);
        setTimeout(function() {
            if(text === self.val()) {
                $('.business-table-row').load(rutaSearchEnvios + '?t=' + encodeURIComponent(text), function () {
                    initCheckboxes();
                    initPaginationListener();
                    initSeguimientoLinks();
                    checkedRows = [];
                });
            }
        }, 200);
    });

    initCheckboxes();
    initPaginationListener();
    initSeguimientoLinks();
});