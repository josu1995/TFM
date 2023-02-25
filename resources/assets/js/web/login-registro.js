jQuery(document).ready(function($) {
	// Login
    $('#form_login').on('submit', function(event) {
        event.preventDefault();
        $('#login-error').text('');
        $('.mensaje-error-login').html('');

        $.ajax({
            type: "POST",
            url: "/api/tstore/v1/login/validar",
            data:  {
                'email': $('#login-email').val(),
                'password': $('#login-password').val(),
                '_token' : "{{ csrf_token() }}"
            },
            success: function(data) {
                $('#form_login').unbind('submit').submit();
            },
            error: function(error) {
                $('.login').addClass('has-error').addClass('has-feedback');
                $('.login input').attr('placeholder', 'Credenciales incorrectas');
                if(error.status === 403) {
                    $('#login-error').text(error.responseJSON);
                }
            }
        });
    });

    // Registro
    $('#form_registro').on('submit', function(event) {
        event.preventDefault();

        $('.mensaje-error').html('');

        $.ajax({
            type: "POST",
			url: "/api/tstore/v1/registro/validar",
            data:  {
                'nombre' : $('#registro_nombre').val(),
                'ciudad' : $('#registro_ciudad').val(),
                'email': $('#registro_email').val(),
                'password': $('#registro_password').val(),
                '_token' : "{{ csrf_token() }}"
            },
            success: function(data) {
                $('#form_registro').unbind('submit').submit();
            },
            error: function(error) {
                var errores = JSON.parse(error.responseText);
                $('.has-error').removeClass('has-error').removeClass('has-feedback');
                for (var error in errores) {
                    for (var j = 0; j < errores[error].length; j++) {
                        $('.'+error+'_error').addClass('has-error').addClass('has-feedback');
                        $('.'+error+'_error input').val('');
                        $('.'+error+'_error input').attr('placeholder', errores[error][j]);
                    }
                }
            }
        });
    });


	// --
	// Ver login y registro en paginas fijas
	// --
	var loginPagina = $('#modalloginPage');
	var registroPagina = $('#modalregisterPage');
	var olvidarPagina = $('#modalOlvidar');
    var recoverPagina = $('#modalrecoverPage');
    var emailPagina = $('#modalemailPage');

	if(loginPagina.length){
		loginPagina.modal({backdrop:'static'});
	}else if(registroPagina.length){
		registroPagina.modal({backdrop:'static'});
	}else if(recoverPagina.length){
        recoverPagina.modal({backdrop:'static'});
    }else if(emailPagina.length){
        emailPagina.modal({backdrop:'static'});
    } else if(recoverPagina.length) {
        recoverPagina.modal({backdrop:'static'});
    }

	olvidarPagina.on('hidden.bs.modal',function(){
		loginPagina.modal({backdrop:'static'});
	});

    recoverPagina.on('hidden.bs.modal',function(){
        window.location="/";
    });

    loginPagina.on('hidden.bs.modal',function(){
        window.location="/";
    });

    registroPagina.on('hidden.bs.modal',function(){
        window.location="/";
    });

});
