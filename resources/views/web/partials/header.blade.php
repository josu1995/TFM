<?php
$route = \Request::route() ? Request::route()->getName() : null;
?>

<div class="mobile-header-container">
    <div class="mobile-header-content main">
        <div class="link">
            <a href="#" data-toggle="modal" data-target="#modalLogin">
                Iniciar sesión <i class="fas fa-arrow-right pull-right"></i>
            </a>
        </div>
        <div class="bottom-button pd-20 text-center">
            <a href="{{ route('business_register') }}" class="btn btn-corporativo">Registrarse</a>
        </div>
    </div>
</div>

<header class="main">
    <div class="inner-header">
        <div class="h-100 relative overflow-hidden">
            <div class="business-header desktop-header h-100 bg-transparent">
                <a href="{{ route('business_landing_index') }}">
                    <img class="business-logo" src="{{ asset('img/business/citystock-white-logo.png') }}"/>
                </a>
                <div class="header-separator"></div>
                <div class="header-link">
                    <a href="#" data-toggle="modal" data-target="#modalLogin" class="link">Iniciar sesión</a>
                </div>
                <div class="header-link">
                    <a href="{{ route('business_register') }}" id="RegisterButton" class="btn btn-corporativo light">Registrarse&nbsp&nbsp<i class="fas fa-chevron-right"></i></a>
                </div>
            </div>

            <div class="mobile-header-container" style="top:64px">
                <div class="mobile-header business bg-transparent">
                    <span>
                        <a>
                            <img width="160" class="business-logo white" src="{{ asset('img/business/citystock-white-logo.png') }}"/>
                            <img width="160" class="business-logo color" src="{{ asset('img/business/citystock-white-color.png') }}"/>
                        </a>
                    </span>
                    <span class="header-separator"></span>
                    <span>
                        @include('partials.hamburger', ['class' => 'pull-right', 'function' => ''])
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>

@include('business.partials.login-popup')

@push('javascripts-footer')
    <script>

        const close = document.getElementById("closebtn");

        $(function() {
            let closing = false;
            let anim = false;
            const header = $('header');
            const baseHeight = header.height();
            const headerContent = $('.mobile-header-content');
            const burger = $('.hamburger');
            headerContent.css('top', '-' + headerContent.height() + 'px');
            burger.click(function() {
                if(!$('#menu_mobile').length) {
                    if (!anim && !$('.line1.active').length) {
                        anim = true;
                        header.removeClass('fixed');
                        headerContent.removeClass('fixed');
                        headerContent.css({'top': '-' + headerContent.height() + 'px'});
                        setTimeout(function () {
                            header.removeClass('active').addClass('reverse');
                            $('body').css('overflow', 'auto');
                            $('html').css('overflow', 'inherit');
                            headerContent.css({'visibility': 'hidden'});
                            anim = false;
                        }, 400);
                    } else if (!anim) {
                        anim = true;
                        header.removeClass('reverse').addClass('active');
                        setTimeout(function () {
                            headerContent.css({'visibility': 'visible', 'top': baseHeight + 'px'});
                        }, 400);
                        setTimeout(function () {
                            $('html, body').css('overflow', 'hidden');
                            header.addClass('fixed');
                            headerContent.addClass('fixed');
                            anim = false;
                        }, 750);
                    }
                }
            });

            function openMobileMenu() {
                $('#menu_mobile')
                    .show()
                    .animate({
                        opacity: 1
                    }, 200, function() {
                        $('#menu_body')
                            .animate({
                                left: 0 + '%'
                            }, 250)
                            .addClass('visible');
                        menuVisible = true;
                        $('body').css('overflow', 'hidden');
                    }).css('position', 'fixed');
            }


        //Show & Hide menu on mobile
        var menuVisible = false;

        $('#menu_body')
                .click(function(event) {
                    event.stopPropagation();
                });
        $(document)
                .click(function() {
                    if (menuVisible == true && !closing) {
                        closing = true;
                        closeBurger();
                        $('#menu_body')
                                .animate({
                                    left: -100 + '%'
                                }, 300, function() {
                                    $('#menu_mobile')
                                            .animate({
                                                opacity: 0
                                            }, 500, function() {
                                                $(this)
                                                        .hide().css('position', 'absolute');
                                                menuVisible = false;
                                                $('body').css('overflow', 'auto');
                                                closing = false;
                                            });
                                });
                    }
                });

        var touching = false;
        var originX;
        var originY;

        $('#menu_body').on('touchstart', function(e) {
            touching = true;
            originX = e.originalEvent.changedTouches[0].pageX;
            originY = e.originalEvent.changedTouches[0].pageY;
            lastY = e.originalEvent.changedTouches[0].pageY;
        });

        var lastY;
        $('#menu_mobile').on('touchmove', function(e) {
            var currentY = e.originalEvent.changedTouches[0].pageY;
            if(!$('.scroll-menu').find($(e.target)).length || ($('.scroll-menu').scrollTop() >= $('.scroll-menu')[0].scrollHeight - $('.scroll-menu').height() && currentY < lastY) || ($('.scroll-menu').scrollTop() == 0 && currentY > lastY)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        $('#menu_body').on('touchend', function(e) {
            if(touching && originX - e.originalEvent.changedTouches[0].pageX >= 50 && !closing) {
                closing = true;
                closeBurger();
                $('#menu_body')
                        .animate({
                            left: -100 + '%'
                        }, 300, function() {
                            $('#menu_mobile')
                                    .animate({
                                        opacity: 0
                                    }, 500, function() {
                                        $(this)
                                                .hide().css('position', 'absolute');
                                        menuVisible = false;
                                        touching = false;
                                        $('body').css('overflow', 'auto');
                                        closing = false;
                                    });
                        });
            }

        });
            close.addEventListener("click", function() {
                if((!$('#menu_mobile').length && !anim) || $('#menu_mobile').length) {
                    closeBurger();
                    if ($('#menu_mobile').length && !menuVisible) {
                        openMobileMenu();
                    }
                }
            });

            function closeBurger() {
                var menuIcon = close.children;
                for (var i = 0; i < menuIcon.length; i++) {
                    menuIcon[i].classList.toggle("active");
                }
            }
        });

        $( function( ) {
            $( '#login-form' ).on( 'submit', function( event ) {
                event.preventDefault( );
                $( '#login-error' ).text( '' );
                $( '.mensaje-error-login' ).html( '' );

                $.ajax( {
                    type : 'POST',
                    url  : "{{ route('business_validar_login') }}",
                    data : {
                        'email'    : $( '#login-email' ).val( ),
                        'password' : $( '#login-password' ).val( ),
                        '_token'   : '{{ csrf_token( ) }}'
                    },
                    success : function( data ) {
                        $( '#login-form' ).unbind( 'submit' ).submit( );
                    },
                    error : function( error ) {
                        $( '.login' ).addClass( 'has-error' ).addClass( 'has-feedback' );
                        $( '.login input' ).attr( 'placeholder', 'Credenciales incorrectas' );
                        if ( error.status === 403 )
                            $( '#login-error' ).text( error.responseJSON );
                    }
                } );
            } );
        } );
    </script>

@endpush