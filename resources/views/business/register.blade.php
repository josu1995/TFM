<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title></title>

    <meta name="description" content="{{ Lang::get('business.meta.description') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="">
    <meta name="twitter:creator" content="">
    <meta name="twitter:title" content="{{ Lang::get('business.meta.title') }}">
    <meta name="twitter:description" content="{{ Lang::get('business.meta.description') }}">
 

    <meta property="og:url" content="{{ route('business_landing_index') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ Lang::get('business.meta.title') }}" />
    <meta property="og:description" content="{{ Lang::get('business.meta.description') }}" />


    <link rel="shortcut icon" type="image/ico" href="{{asset('img/identidad/favicon.ico')}}" />

 
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ mix('css/citystock.css') }}">

   


</head>

<body>

    <div class="business-register-header text-center">
        <img src="{{asset('img/business/citystock-white-logo.png') }}" alt="Transporter Business Logo" height="70px" />
    </div>

    <div class="container pd-t-40">

       

        <h1 class="text-center">Registro de usuario</h1>

        <form action="{{ route('business_post_register_informacion') }}" method="POST" class="business-register-form pd-t-40">
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
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <label for="nombre">* NOMBRE</label>
                        <input type="text" id="nombre" class="form-control" name="nombre" value="{{ old('nombre') }}">
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <label for="apellido">* APELLIDO</label>
                        <input type="text" id="apellido" class="form-control" name="apellido"
                            value="{{ old('apellido') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <label for="email">* CORREO ELECTRÓNICO</label>
                        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <label for="pass">* CONTRASEÑA</label>
                        <input type="password" id="nuevo" class="form-control" name="nuevo" value="{{ old('pass') }}">
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <label for="pass1">* REPITA SU CONTRASEÑA</label>
                        <input type="password" id="nuevo_confirmation" class="form-control" name="nuevo_confirmation"
                            value="{{ old('pass1') }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-sm-6 col-xs-12">
                        <label for="pass">* IDIOMA</label>
                        <select class="form-control" id="idioma" name="idioma">
                            
                            @foreach($idiomas as $idioma)

                                <option value="{{$idioma->id}}">{{$idioma->nombre}}</option>

                            @endforeach

                        </select>
                    </div>
                    <div class="col-sm-6 col-xs-12">
                        <label for="pass1">* DIFICULTAD</label>
                            <select class="form-control" id="dificultad" name="dificultad">

                                @foreach($dificultades as $dificultad)

                                    <option value="{{$dificultad->id}}">{{$dificultad->nombre}}</option>

                                @endforeach

                            </select>
                    </div>
                </div>
            </div>

            <div class="form-group text-center mg-t-50">
                
                {!! csrf_field() !!}
                <button type="submit" class="btn btn-corporativo square-button">Registrarse</button>
            </div>
        </form>

    </div>

</body>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" type="text/javascript"></script>

</html>