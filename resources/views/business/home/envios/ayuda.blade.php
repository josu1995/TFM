@extends('layouts.business')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="section-title">
        Atención al cliente
    </h1>
    <ol class="breadcrumb">
        <li class="icon-crumb"><i class="material-icons">home</i></li>
        <li class="active">{!! trans('usuario.soporte') !!}</li>
        <li class="active">{!! trans('usuario.atencion') !!}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content ayuda-business">

    <div class="box">
        <div class="box-body no-pd">
            <div class="row">
                <div class="col-xs-12">
                    <p class="separator-bottom business-box-title"><label class="form-title">{!! trans('usuario.formas') !!}</label></p>
                    <p class="mg-t-40 text-center">{!! trans('usuario.duda') !!}</p>
                    <div class="row mg-t-60 mg-b-20 metodos-row">
                        <div class="col-xs-6 col-sm-3 col-sm-custom-offset-3 text-center separator-bottom mg-r-10 ayuda-item">
                            <i class="fab fa-whatsapp fa-fw mg-b-20"></i><br>
                            <label>WhatsApp</label>
                        </div>
                        <div class="col-xs-6 col-sm-3 text-center separator-bottom mg-l-10 ayuda-item pd-t-7">
                            <i class="fas fa-phone fa-fw"></i><br>
                            <label>{!! trans('usuario.telefono') !!}</label>
                        </div>
                    </div>
                    <p class="text-center">{!! trans('usuario.chat') !!}</p>
                    <p class="text-center mg-t-40 ayuda-telefono">+34 123 45 67 89</p>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@push('javascripts-footer')
    <script type="text/javascript" src="{{ asset('js/dist/pwstrength.js') }}"></script>
    <script>
        document.getElementById('soporte').classList.add('active');
        document.getElementById('soporte-atencion-cliente').classList.add('active');
    </script>
@endpush