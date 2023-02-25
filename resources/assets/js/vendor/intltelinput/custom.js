/*** VALIDAR EL NUMERO DE TELEFONO ***/
jQuery(document).ready(function($){

var telInput = $("#telefono"),
  errorMsg = $("#error-msg"),
  validMsg = $("#valid-msg");

// inicializar el plugin
telInput.intlTelInput({
  utilsScript: "/js/vendor/intltelinput/utils.js",
  initialCountry: "es",
  preferredCountries: [],
  onlyCountries: [ "es" ],
  nationalMode: true,
  separateDialCode: true,
  numberType: "MOBILE"
});

var reset = function() {
  telInput.removeClass("error");
  errorMsg.addClass("hide");
  validMsg.addClass("hide");
};

telInput.blur(function() {
  reset();
  if ($.trim(telInput.val())) {
    if (telInput.intlTelInput("isValidNumber")) {
      validMsg.removeClass("hide");
    } else {
      telInput.addClass("error");
      errorMsg.removeClass("hide");
    }
  }
});

telInput.on("keyup change", reset);

$('form[name="configuracion"]').submit(function() {
    //obtiene el numero completo
   var numero = $("#telefono").intlTelInput("getNumber");
   $("#hidden-phone").val(numero);
});


});
