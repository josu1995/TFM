<?php
/**
 * Created by PhpStorm.
 * User: Mikel
 * Date: 08/08/2017
 * Time: 12:54
 */

if (!isset($errors)) {
    $errors = [];
};
?>

{{-- Modal de finalizacion --}}
<div class="modal modal-center-vertical fade" id="modal-finalizacion" role="dialog" aria-labelledby="modalPermission" tabindex="-1"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content pd-20">
            <p class="text-center mg-b-5">
                <i class="fas fa-check check-ok"></i>
                <br>
            </p>
            <p style="font-size: 20px;" class="mg-t-5 mg-b-20 text-center"><strong>Envío Pagado</strong></p>

            <p class="pd-l-30 pd-r-30" style="font-size: 1.1em;"> No olvides enseñar en el establecimiento <em>Transporter Store</em> el código
                <em>QR de reserva </em>que te hemos enviado por correo electrónico.
            </p>
            <p class="pd-l-30 pd-r-30" style="font-size: 1.1em;">
                Si no lo encuentras en tu bandeja de entrada,
                revisa la carpeta de promociones o de correo no deseado de tu proveedor de e-mail.
            </p>

            <button class="btn-app btn-app-center mg-b-15 mg-t-20" data-dismiss="modal">Aceptar</button>

        </div>
    </div>
</div>


@push('javascripts-footer')

<script type="text/javascript">

    $(function () {

        $('#modal-finalizacion').modal('show');

    });

</script>

@endpush