'use strict';

var checkedRows = [];

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

function initCheckboxes() {
    $('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_square-yellow',
        radioClass: 'iradio_square',
        increaseArea: '20%' // optional
    });

    $('.header-checkbox').on('ifChecked', function(event) {
        $('input[type="checkbox"]').not('.header-checkbox').iCheck('check');
    });

    $('.header-checkbox').on('ifUnchecked', function(event) {
        $('input[type="checkbox"]').not('.header-checkbox').iCheck('uncheck');
    });

    $('.table-checkbox').on('ifChecked', function (event) {
        addToChecked($(this).val());
    });

    $('.table-checkbox').on('ifUnchecked', function (event) {
        removeFromChecked($(this).val());
        if(!Object.keys(checkedRows).length) {
        }
    });
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

$(function() {

    $('.buscar-input').keyup(function() {
        var text = $(this).val();
        $('.business-table-row').load(rutaSearchFacturas + '?t=' + encodeURIComponent(text), function () {
            initCheckboxes();
            initPaginationListener();
            initPopovers();
            checkedRows = [];
        });
    });

    $('.factura-link').click(function(e) {
        e.preventDefault();
        window.open($(this).attr('href'));
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

    initPopovers();
    initCheckboxes();
    initPaginationListener();
});