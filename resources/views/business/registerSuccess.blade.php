<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ Lang::get('business.meta.title') }}</title>

    <meta name="description" content="{{ Lang::get('business.meta.description') }}">

    <link rel="shortcut icon" type="image/ico" href="{{asset('img/identidad/favicon.ico')}}" />

    <style>
      
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('css/citystock.css') }}">



</head>

<body class="flex" style="flex-direction:column">

    <div class="business-register-header text-center">
        <img src="{{asset('img/business/citystock-white-logo.png') }}" alt="Transporter Business Logo" height="70px" />
    </div>

    <div class="container register-success-container">
        <div>
            <div class="text-center">
                <i class="fa fa-check round-icon"></i>
            </div>
            <div class="mg-t-40">
                <h2 class="title text-title text-center">¡Gracias! Por registrarte.</h2>
                <br>
                <p class="subtitle text-subtitle text-center">Ya puedes acceder a la plataforma, y comenzar tus estudios.</p>
            </div>

            <div class="inicio-container mg-t-40 text-center">
                <a href="{{ route('business_landing_index') }}" class="btn btn-corporativo square-button btn-corporativo"><i
                        class="fas fa-home"></i> Volver al inicio</a>
            </div>
        </div>

    </div>

</body>

</html>