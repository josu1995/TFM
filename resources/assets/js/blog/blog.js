'use strict';

$(function() {

    function initPopovers() {
        $('.shareBlog').popover({
            // selector: '[rel=popover]',
            trigger: 'click focus',
            content: function () {
                return $(this).siblings('.share-popover-content').html();
            },
            placement: "bottom",
            html: true
        });
    }


    $(window).load(function() {
        $('.blogPrincipal').show();
    });

    // Buscador
    $('.input-ayuda').on('keyup', function() {
        var ruta = $('.ruta_buscar').text();
        var datos = { 'texto': $(this).val()};

        if($(this).val().length > 3 ) {
            $.get(ruta, datos)
                .done(function(response) {
                    $('#resultados-buscador').html(response).fadeIn();
                })
                .fail(function() {
                    console.log( "error" );
                });
        } else {
            $('#resultados-buscador').fadeOut();
        }

    });

    $('.verMas').on('click', function() {
        $(this).hide();
        $('.spinnerMas').show();
        var ruta = $('.ruta_mas').text();
        var datos = { 'count': $('.lastPost').children().length};
        $.get(ruta, datos)
            .done(function(response) {
                if(response.includes('No quedan más entradas.')) {
                    $('.lastPost').append($.parseHTML(response));
                    $('.verMas').remove();
                    $('.spinnerMas').remove();
                } else {
                    var parsed = $.parseHTML(response);
                    $(response).find('img').load(function() {
                        $('.lastPost').children().each(function() {
                            $(this).removeClass('new');
                        });
                        $('.spinnerMas').hide();
                        $('.verMas').show();
                    });
                    $('.lastPost').append(parsed);

                    initPopovers();

                }
            })
            .fail(function() {
                console.log( "error" );
            });
    });

    function showPost(elem) {

        loadSecondary(elem);

        $('.blogContainer').attr('style', 'height:'+$('.blogContainer').height()+'px');
        $('.blogPrincipal').hide('slide', {direction: 'left'}, 1000, function() {
            $('.blogContainer').animate({height:$('.blogSecondary').height()+60},400, function() {
                $('.blogSecondary').show('slide', {direction: 'right'}, 1000, function() {
                    $('.blogContainer').attr('style', 'height:auto');
                });
            });
        });

        $('.blogBack').on('click', function() {
            $('.blogContainer').attr('style', 'height:'+$('.blogContainer').height()+'px');
            $('.blogSecondary').hide('slide', {direction: 'right'}, 1000, function() {
                $('.blogContainer').animate({height:$('.blogPrincipal').height()+60},400, function() {
                    $('.blogPrincipal').show('slide', {direction: 'left'}, 1000, function() {
                        $('.blogContainer').attr('style', 'height:auto');
                    });
                });
            });
        });
    }

    function loadSecondary(elem) {
        $('.postImage').attr('src', elem.find('img.img-responsive').attr('src'));

        $('.postTitle').text(elem.find('.blogTitle').text());
        $('.postAutor').text(elem.find('.blogAutor').text());

        //Content
        var content = elem.find('.blogContent').html().replace(new RegExp('&lt;(/)?b\\s*&gt;', 'gi'), '<$1b>');
        var decoded = $("<div/>").html(content).text();
        $('.postContent').empty().append($.parseHTML(decoded));
    }

    initPopovers();

});