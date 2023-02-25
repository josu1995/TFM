/**
 * Created by urtzi on 03/11/2016.
 */
$(document).ready(function() {
    $('.cobertura-icon').on('click', function () {
        if ($(this).hasClass('fa-plus-square')) {
            $(this).removeClass('fa-plus-square');
            $(this).addClass('fa-minus-square');
        } else {
            $(this).removeClass('fa-minus-square');
            $(this).addClass('fa-plus-square');
        }
    });

    var metodoCobro = $('.metodo-cobro');
    // if(metodoCobro.length > 0){
    //     $('.metodo-cobro[data-value=2]').addClass('metodo-activo');
    // }
    metodoCobro.click(function(event) {
        var metodoSelected = $(this)
            .attr('data-value');
    //     metodoCobro.each(function(index, el) {
    //         $(this)
    //             .removeClass('metodo-activo');
    //     });
        $('.metodo_cobro_select').val(metodoSelected);
    //     $(this)
    //         .toggleClass('metodo-activo');
    });

    // $('.retrasarPagoForm').submit(function(e) {
    //     e.preventDefault();
    //     var form = $(this);
    //     if($('.codigoAplicado')) {
    //         $('#modalRetrasarPago').modal('show');
    //         return false;
    //     } else {
    //         form.submit();
    //     }
    // });
});