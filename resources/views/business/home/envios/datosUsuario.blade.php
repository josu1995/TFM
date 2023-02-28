@extends('layouts.business')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="section-title">
        Datos de usuario
    </h1>
    <ol class="breadcrumb">
        <li class="icon-crumb"><i class="material-icons">home</i></li>
        <li class="active">Cuenta</li>
        <li class="active">Datos de usuario</li>
    </ol>
</section>

<!-- Main content -->
<section class="content datos-usuario-section">

    <div class="box">
        <div class="box-body no-pd">
            <div class="row">

                <div class="col-xs-12 col-sm-6 separator-right">
                    <p class="separator-bottom"><label class="form-title">PERFIL</label></p>
                    <form class="indent" action="{{ route('usuario_post_perfil') }}" method="POST">
                        @if ($errors->hasBag('datos'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->getBag('datos')->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>NOMBRE</label>
                            <input type="text" class="form-control" name="nombre" value="{{ old('nombre') ? old('nombre') : $usuario->nombre }}">
                        </div>
                        <div class="form-group">
                            <label>APELLIDO</label>
                            <input type="text" class="form-control" name="apellido" value="{{ old('apellido') ? old('apellido') : $usuario->apellidos }}">
                        </div>
                        <div class="form-group">
                            <label>CORREO ELECTRÓNICO</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') ? old('email') : $usuario->email }}">
                        </div>
                        
                        {{ csrf_field() }}
                   
                        <button type="submit" class="btn btn-corporativo pull-right mg-t-20 business-btn">Guardar</button>
                    </form>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <p class="separator-bottom"><label class="form-title">CONTRASEÑA</label></p>
                    <form class="indent" action="{{ route('usuario_post_contraseña') }}" method="POST">
                        @if ($errors->hasBag('password'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->getBag('password')->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>CONTRASEÑA ACTUAL</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>NUEVA CONTRASEÑA</label>
                            <input type="password" id="password" name="nuevo" class="form-control password">
                        </div>
                        <div class="form-group">
                            <label>CONFIRMAR NUEVA CONTRASEÑA</label>
                            <input type="password" name="nuevo_confirmation" class="form-control">
                        </div>
                        <div id="pwd-container">
                            <div class="col-md-12 no-pd">
                                <div class="pwstrength_viewport_progress"></div>
                            </div>
                        </div>
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-corporativo pull-right mg-t-20 business-btn">Cambiar contraseña</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</section>
@endsection

@push('javascripts-footer')
    <script type="text/javascript" src="{{ asset('js/dist/pwstrength.js') }}"></script>
    <script>
        document.getElementById('cuenta').classList.add('active');
        document.getElementById('datos-usuario').classList.add('active');

        @if($message)
            $(function() {
                new PNotify({
                    title: 'Transporter',
                    text: '{!! $message !!}',
                    addclass: 'transporter-alert',
                    icon: 'icon-transporter',
                    autoDisplay: true,
                    hide: true,
                    delay: 5000,
                    closer: false,
                });
            });
        @endif

    </script>
@endpush