'use strict';

const myLazyLoad = new LazyLoad();

// Hamburger

var navwrapper = document.getElementsByClassName('nav-wrapper')[0];
var close = document.getElementById("closebtn");
var menu = document.getElementById("mobileMenu");
var items = menu.children;

var wrapperMargin = parseFloat(window.getComputedStyle(navwrapper).marginLeft.split('px')[0]);
var initialPosition = '-' + (menu.clientWidth + wrapperMargin) + 'px';

var empresaDiv = $('#empresaForm');
var particularDiv = $('#particularForm');
var particularForm = $('.particularForm');
var empresaForm = $('.empresaForm');

close.addEventListener("click", function() {
    var menuIcon = close.children;
    for (var i = 0; i < menuIcon.length; i++) {
        menuIcon[i].classList.toggle("active");
    }
});

function navToggle() {
    //to close
    if (menu.style.right !== '' && menu.style.right !== initialPosition) {
        menu.style.right = initialPosition;
        for (var i = 0 ; i < items.length ; i++) {
            items[i].style.opacity = 0;
        }
    }
    //to open
    else if (menu.style.right === '' || menu.style.right === initialPosition) {
        menu.style.right = (navwrapper.clientWidth - 45 - menu.clientWidth) + 'px';
        for (var i = 0 ; i < items.length ; i++) {
            items[i].style.opacity = 1;
        }
    }
}



$(function() {

    var lastY;
    var mobile = screen.width < 768;

    $(document).on('touchstart', function(e) {
        lastY = e.originalEvent.changedTouches[0].pageY;
    });

    $('.modal').modal({
            dismissible: true, // Modal can be dismissed by clicking outside of the modal
            opacity: .5, // Opacity of modal background
            inDuration: 300, // Transition in duration
            outDuration: 200, // Transition out duration
            startingTop: '4%', // Starting top style attribute
            endingTop: '10%', // Ending top style attribute
            ready: function(modal, trigger) { // Callback for Modal open. Modal and trigger parameters available.
                if(mobile) {
                    $('body').css('overflow', 'hidden').css('position', 'fixed');
                }
                $('ul.tabs').tabs();
            },
            complete: function() {
                if(mobile) {
                    $('body').css('overflow', 'auto').css('position', 'inherit');
                }
            }
        }
    );
    $('select').material_select();

    $('input').on('focus', function() {
        $(this).removeClass('input-invalid').removeClass('select-invalid');
        $(this).siblings('label').removeClass('label-invalid').removeClass('label-invalid-with-text');
    });

    $('.btn-submit-particular').click(function() {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/api/tdriver/v1/registro/particular/validar',
            data: particularForm.serializeArray(),
            success: function (data) {
                // ok
                particularForm.submit();
            },
            error: function(data) {
                // error
                if (data.responseJSON.nombre) {
                    var nombreInput = $('#nombre');
                    nombreInput.addClass('input-invalid');
                    if(nombreInput.val() == '') {
                        nombreInput.siblings('label').addClass('label-invalid');
                    } else {
                        nombreInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="nombre"]').attr('data-error', data.responseJSON.nombre[0]);
                }
                if (data.responseJSON.email) {
                    var mailInput = $('#email');
                    mailInput.addClass('input-invalid');
                    if(mailInput.val() == '') {
                        mailInput.siblings('label').addClass('label-invalid');
                    } else {
                        mailInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="email"]').attr('data-error', data.responseJSON.email[0]);
                }
                if (data.responseJSON.password) {
                    var passwordInput = $('#password');
                    passwordInput.addClass('input-invalid');
                    if(passwordInput.val() == '') {
                        passwordInput.siblings('label').addClass('label-invalid');
                    } else {
                        passwordInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="password"]').attr('data-error', data.responseJSON.password[0]);
                }
                if (data.responseJSON.ciudad) {
                    var ciudadInput = $('#ciudad');
                    ciudadInput.addClass('input-invalid');
                    if(ciudadInput.val() == '') {
                        ciudadInput.siblings('label').addClass('label-invalid');
                    } else {
                        ciudadInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="ciudad"]').attr('data-error', data.responseJSON.ciudad[0]);
                }
            }
        });

    });

    $('.btn-submit-profesional').click(function() {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/api/tdriver/v1/registro/validar',
            data: empresaForm.serializeArray(),
            success: function (data) {
                // ok
                empresaForm.submit();
            },
            error: function(data) {
                // error
                if (data.responseJSON.nombre) {
                    var nombreInput = $('#nombreBusiness');
                    nombreInput.addClass('input-invalid');
                    if(nombreInput.val() == '') {
                        nombreInput.siblings('label').addClass('label-invalid');
                    } else {
                        nombreInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="nombreBusiness"]').attr('data-error', data.responseJSON.nombre[0]);
                }
                if (data.responseJSON.email) {
                    var mailInput = $('#emailBusiness');
                    mailInput.addClass('input-invalid');
                    if(mailInput.val() == '') {
                        mailInput.siblings('label').addClass('label-invalid');
                    } else {
                        mailInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="emailBusiness"]').attr('data-error', data.responseJSON.email[0]);
                }
                if (data.responseJSON.telefono) {
                    var telefonoInput = $('#telefono');
                    telefonoInput.addClass('input-invalid');
                    if(telefonoInput.val() == '') {
                        telefonoInput.siblings('label').addClass('label-invalid');
                    } else {
                        telefonoInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="telefono"]').attr('data-error', data.responseJSON.telefono[0]);
                }
                if (data.responseJSON.referencia) {
                    var referenciaSelect = $('.select-wrapper > input');
                    referenciaSelect.addClass('select-invalid');
                }
            }
        });
    });

    if($('input:radio:checked').attr('id') === 'empresa') {
        particularDiv.addClass('hide');
        empresaDiv.removeClass('hide');
    }


    $('input:radio').change(function() {
        if($(this).attr('id') === 'particular') {
            empresaDiv.addClass('hide');
            particularDiv.removeClass('hide');
        } else {
            particularDiv.addClass('hide');
            empresaDiv.removeClass('hide');
        }
    });

    var loginForm = $('.loginForm');
    loginForm.submit(loginSubmit);

    function loginSubmit() {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '/api/tdriver/v1/login/validar',
            data: loginForm.serializeArray(),
            success: function (data) {
                // ok
                loginForm.off('submit');
                loginForm.submit();
                loginForm.submit(loginSubmit);
            },
            error: function (data) {
                if (data.responseJSON) {
                    var emailInput = $('#loginEmail');
                    emailInput.addClass('input-invalid');
                    if(emailInput.val() == '') {
                        emailInput.siblings('label').addClass('label-invalid');
                    } else {
                        emailInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="loginEmail"]').attr('data-error', data.responseJSON);

                    var passwordInput = $('#loginPassword');
                    passwordInput.addClass('input-invalid');
                    if(passwordInput.val() == '') {
                        passwordInput.siblings('label').addClass('label-invalid');
                    } else {
                        passwordInput.siblings('label').addClass('label-invalid-with-text');
                    }
                    $('label[for="loginPassword"]').attr('data-error', data.responseJSON);
                }
            }
        });
        return false;
    }

});
