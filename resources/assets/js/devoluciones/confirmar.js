'use strict';

$(function() {

    var mobile = screen.width < 768;

    $("[data-toggle=popover]").each(function() {
        $(this).popover({
            trigger: 'manual',
            placement: mobile ? 'top' : 'right',
            html: true,
            content: function() {
                return $('.horario-table').html();
            },
            container: 'body'
        });
    });

    if(mobile) {
        $('body').on('touchstart ', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    } else {
        $('body').on('mousedown', function (e) {
            if ($('.popover').length) {
                $('a[aria-describedby^="popover"]').popover('toggle');
            } else if ($(e.target).hasClass('ver-mas-link')) {
                $(e.target).popover('show');
            }
        });
    }

});