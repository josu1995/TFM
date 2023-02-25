@extends('layouts.web')
@section('title', 'Alertas')

@section('content')
<section id="wrapper_principal">
    <div class="row-fluid navegacion-home">
        <!-- Navegación -->
        @include('web.home.navegacion')
    </div>
    <div class="inner-perfil">
        <div class="container">
            <div>
                <div class="col-xs-12">
                    @if(count($alertasPuntuales) == 0 && count($alertasHabituales) == 0 && count($alertasAntiguas) == 0)
                        <div class="m-nomensajes col-md-12" style="display: table;">
                            <h3>No tienes alertas</h3>
                            <p class="texto-gris">En estos momentos no tienes ninguna alerta. Establece una para que te avisemos por email de los envíos disponibles.</p>
                            <p class="no-mg"><a href="#" data-toggle="modal" data-target="#modalNuevaAlerta" class="btn-app no-mg">Crea una alerta</a></p>
                        </div>
                    @else
                        <div class="row">
                            <h1 class="title-seccion">Lista de alertas</h1>

                            <!-- Tabs -->
                            <ul class="nav nav-pills tabs-perfil" role="tablist">
                                <li role="presentation" class="active"><a href="#puntuales" aria-controls="confirmados" role="tab" data-toggle="pill">Puntuales</a></li>
                                <li role="presentation"><a href="#habituales" aria-controls="pendientes" role="tab" data-toggle="pill">Habituales</a></li>
                                <li role="presentation"><a href="#antiguas" aria-controls="historial" role="tab" data-toggle="pill">Historial</a></li>
                            </ul>
                        </div>

                        <div class="row">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="puntuales" style="margin-bottom: 15px;">
                                @if(count($alertasPuntuales) == 0)
                                    <div class="m-nomensajes col-md-12" style="display: table;">
                                        <h3>No tienes alertas puntuales</h3>
                                        <p class="texto-gris">En estos momentos no tienes ninguna alerta puntual. Establece una para que te avisemos por email de los envíos disponibles.</p>
                                        <p class="no-mg"><a href="#" data-toggle="modal" data-target="#modalNuevaAlerta" class="btn-app no-mg">Crea una alerta</a></p>
                                    </div>
                                @else
                                    <div class="row-xs">
                                    <div class="table-responsive table-flow">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <td><i class="icon-fecha"></i> Fecha</td>
                                                <td><i class="icon-punto"></i> Origen</td>
                                                <td><i class="icon-punto"></i> Destino</td>
                                                <td><i class="icon-paquete"></i> Acciones</td>
                                            </tr>
                                            </thead>

                                            @foreach($alertasPuntuales as $alerta)

                                                <tr class="alerta puntual" id="{{$alerta->id}}">
                                                    <td class="fecha">{{date('d/m/Y', strtotime($alerta->fecha))}}</td>
                                                    <td class="origen" id="{{$alerta->origen->id}}">{{$alerta->origen->nombre}}</td>
                                                    <td class="destino" id="{{$alerta->destino->id}}">{{$alerta->destino->nombre}}</td>
                                                    <td>
                                                        <ul class="list-unstyled list-inline no-mg">
                                                            <li>
                                                                <button type="button" name="button" class="btn btn-info btn-xs btn-block btn-editar-alerta" data-toggle="tooltip" data-placement="top" title="Editar">
                                                                    <i class="fas fa-pencil-alt " aria-hidden="true"></i>
                                                                </button>
                                                            </li>
                                                            <li>
                                                                {{ Form::open(['method' => 'DELETE', 'route' => ['alerta_delete', $alerta->id]]) }}
                                                                <button type="submit" name="name" value="eliminar" class="btn btn-block btn-xs btn-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                                {{ Form::close() }}
                                                            </li>
                                                        </ul>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </table>
                                    </div>
                                    </div>
                                @endif
                            </div>

                            <div role="tabpanel" class="tab-pane fade" id="habituales" style="margin-bottom: 15px;">
                                @if(count($alertasHabituales) == 0)
                                    <div class="m-nomensajes col-md-12" style="display: table;">
                                        <h3>No tienes alertas habituales</h3>
                                        <p class="texto-gris">En estos momentos no tienes ninguna alerta habitual. Establece una para que te avisemos por email de los envíos disponibles.</p>
                                        <p class="no-mg"><a href="#" data-toggle="modal" data-target="#modalNuevaAlerta" class="btn-app no-mg">Crea una alerta</a></p>
                                    </div>
                                @else
                                    <div class="row-xs">
                                    <div class="table-responsive table-flow">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <td><i class="icon-punto"></i> Origen</td>
                                                <td><i class="icon-punto"></i> Destino</td>
                                                <td><i class="icon-fecha"></i> Días</td>
                                                <td><i class="icon-paquete"></i> Acciones</td>
                                            </tr>
                                            </thead>

                                            @foreach($alertasHabituales as $alerta)

                                                <tr class="alerta habitual" id="{{$alerta->id}}">

                                                    <td class="origen" id="{{$alerta->origen->id}}">{{$alerta->origen->nombre}}</td>
                                                    <td class="destino" id="{{$alerta->destino->id}}">{{$alerta->destino->nombre}}</td>
                                                    <td class="dias">
                                                        @foreach(explode(',',$alerta->dias) as $dia)
                                                            @if($dia == 1)
                                                                <button class="btn btn-default">LU</button>
                                                            @elseif($dia == 2)
                                                                <button class="btn btn-default">MA</button>
                                                            @elseif($dia == 3)
                                                                <button class="btn btn-default">MI</button>
                                                            @elseif($dia == 4)
                                                                <button class="btn btn-default">JU</button>
                                                            @elseif($dia == 5)
                                                                <button class="btn btn-default">VI</button>
                                                            @elseif($dia == 6)
                                                                <button class="btn btn-default">SA</button>
                                                            @else
                                                                <button class="btn btn-default">DO</button>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <ul class="list-unstyled list-inline no-mg">
                                                            <li>
                                                                <button type="button" name="button" class="btn btn-info btn-xs btn-block btn-editar-alerta" data-toggle="tooltip" data-placement="top" title="Editar">
                                                                    <i class="fas fa-pencil-alt " aria-hidden="true"></i>
                                                                </button>
                                                            </li>
                                                            <li>
                                                                {{ Form::open(['method' => 'DELETE', 'route' => ['alerta_delete', $alerta->id]]) }}
                                                                <button type="submit" name="name" value="eliminar" class="btn btn-block btn-xs btn-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                                </button>
                                                                {{ Form::close() }}
                                                            </li>
                                                        </ul>
                                                    </td>

                                                </tr>

                                            @endforeach

                                        </table>
                                    </div>
                                        </div>

                                    @endif
                                </div>

                                <div role="tabpanel" class="tab-pane fade" id="antiguas" style="margin-bottom: 15px;">

                                    @if(count($alertasAntiguas) == 0)

                                        <div class="m-nomensajes col-md-12" style="display: table;">
                                            <h3>No tienes alertas antiguas</h3>
                                            <p class="texto-gris">En estos momentos no tienes ninguna alerta antigua. Establece una para que te avisemos por email de los envíos disponibles.</p>
                                            <p class="no-mg"><a href="#" data-toggle="modal" data-target="#modalNuevaAlerta" class="btn-app mo-mg">Crea una alerta</a></p>
                                        </div>

                                    @else
                                        <div class="row-xs">
                                        <div class="table-responsive table-flow">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <td><i class="icon-fecha"></i> Fecha</td>
                                                    <td><i class="icon-punto"></i> Origen</td>
                                                    <td><i class="icon-punto"></i> Destino</td>
                                                    <td><i class="icon-paquete"></i> Acciones</td>
                                                </tr>
                                                </thead>

                                                @foreach($alertasAntiguas as $alerta)

                                                    <tr>
                                                        <td>{{date('d/m/Y', strtotime($alerta->fecha))}}</td>
                                                        <td>{{$alerta->origen->nombre}}</td>
                                                        <td>{{$alerta->destino->nombre}}</td>
                                                        <td>
                                                            <ul class="list-unstyled list-inline no-mg">
                                                                <li>
                                                            {{ Form::open(['method' => 'DELETE', 'route' => ['alerta_delete', $alerta->id]]) }}
                                                            <button type="submit" name="name" value="eliminar" class="btn btn-block btn-xs btn-danger" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Eliminar">
                                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                                            </button>
                                                            {{ Form::close() }}
                                                                </li>
                                                            </ul>
                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </table>
                                        </div>
                                        </div>
                                    @endif

                                </div>

                            </div>
                            <button class="btn-app" data-toggle="modal" data-target="#modalNuevaAlerta"><i class="icon-anadir texto-inverso"></i> Añadir alerta</button><br>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{--Modal de tipo de alerta--}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalNuevaAlerta" aria-labelledby="modalNuevaAlerta" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Nueva alerta</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <ul class="nav nav-pills tabs-perfil" role="tablist" style="text-align: center;">
                            <li role="presentation" class="active" style="width: 50%;"><a href="#puntual" aria-controls="puntual" role="tab" data-toggle="pill" class="pill-group" style="border-radius: 4px 0 0 4px;">Viaje puntual</a></li>
                            <li role="presentation" class="no-mg" style="width: 50%;"><a href="#habitual" aria-controls="habitual" role="tab" data-toggle="pill" class="pill-group" style="border-radius: 0 4px 4px 0;">Viaje habitual</a></li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">

                    <div role="tabpanel" class="tab-pane fade in active" id="puntual" style="margin-bottom: 15px; text-align: center;">

                            <form class="formPuntual">
                                <div class="form-group">
                                    <div class="bloque-input no-mg">
                                        <span class="icon-punto texto-envio"></span>
                                        <input type="text" id="autocompleteOrigenPuntual" class="form-control destinoInput" placeholder="Ciudad de origen">
                                        <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                        <input type="hidden" name="origenPuntual" id="origenPuntual">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="bloque-input no-mg">
                                        <span class="icon-punto texto-recogida"></span>
                                        <input type="text" id="autocompleteDestinoPuntual" class="form-control destinoInput" placeholder="Ciudad de destino">
                                        <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                        <input type="hidden" name="destinoPuntual" id="destinoPuntual">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="bloque-input no-mg">
                                        <span class="fas fa-calendar-alt"></span>
                                        <input type="text" name="fecha" id="fecha" class="form-control destinoInput" placeholder="Fecha del viaje">
                                        <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                    </div>
                                </div>
                                {!! csrf_field() !!}

                                <button type="button" id="submitPuntual" class="btn-app" style="display: inline-block;">Crear alerta</button>
                            </form>

                    </div>

                    <div role="tabpanel" class="tab-pane fade in" id="habitual" style="margin-bottom: 15px; text-align: center;">

                        <form class="formHabitual">
                            <div class="form-group">
                                <div class="bloque-input no-mg">
                                    <span class="icon-punto texto-envio"></span>
                                    <input type="text" id="autocompleteOrigenHabitual" class="form-control destinoInput" placeholder="Ciudad de origen">
                                    <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                    <input type="hidden" name="origenHabitual" id="origenHabitual">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="bloque-input no-mg">
                                    <span class="icon-punto texto-recogida"></span>
                                    <input type="text" id="autocompleteDestinoHabitual" class="form-control destinoInput" placeholder="Ciudad de destino">
                                    <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                    <input type="hidden" name="destinoHabitual" id="destinoHabitual">
                                </div>
                            </div>

                            <div id="dias" class="form-group">
                                <label class="control-label">¿Qué días viajas?</label><br>
                                <div class="btn-group" data-toggle="buttons">
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="lunes" value="1">LU
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="martes" value="2">MA
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="miercoles" value="3">MI
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="jueves" value="4">JU
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="viernes" value="5">VI
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="sabado" value="6">SA
                                    </label>
                                    <label class="btn btn-default">
                                        <input type="checkbox" name="dias[]" id="domingo" value="7">DO
                                    </label>
                                </div>
                                <div class="feedback-alertas"></div>
                            </div>
                            {!! csrf_field() !!}

                            <button type="button" id="submitHabitual" class="btn-app" style="display: inline-block;">Crear alerta</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


{{--Modal de editar alerta puntual--}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalEditarAlertaPuntual" aria-labelledby="modalEditarAlertaPuntual" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Editar alerta</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                        <form class="formPuntualEditar text-center">
                            <div class="form-group">
                                <div class="bloque-input no-mg">
                                    <span class="icon-punto texto-envio"></span>
                                    <input type="text" id="autocompleteOrigenPuntualEditar" class="form-control destinoInput" placeholder="Ciudad de origen">
                                    <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                    <input type="hidden" name="origenPuntual" id="origenPuntualEditar">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="bloque-input no-mg">
                                    <span class="icon-punto texto-recogida"></span>
                                    <input type="text" id="autocompleteDestinoPuntualEditar" class="form-control destinoInput" placeholder="Ciudad de destino">
                                    <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                    <input type="hidden" name="destinoPuntual" id="destinoPuntualEditar">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="bloque-input no-mg">
                                    <span class="fas fa-calendar-alt"></span>
                                    <input type="text" name="fecha" id="fechaEditar" class="form-control destinoInput" placeholder="Fecha del viaje">
                                    <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                                </div>
                            </div>
                            <input type="hidden" name="alertaId" id="alertaIdPuntualEditar">
                            {!! csrf_field() !!}

                            <button type="button" id="submitPuntualEditar" class="btn-app" style="display: inline-block;">Guardar alerta</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


{{--Modal de editar alerta puntual--}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalEditarAlertaHabitual" aria-labelledby="modalEditarAlertaHabitual" aria-hidden="true">
    <div class="modal-dialog small-modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Editar alerta</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form class="formHabitualEditar text-center">
                    <div class="form-group">
                        <div class="bloque-input no-mg">
                            <span class="icon-punto texto-envio"></span>
                            <input type="text" id="autocompleteOrigenHabitualEditar" class="form-control destinoInput" placeholder="Ciudad de origen">
                            <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                            <input type="hidden" name="origenHabitual" id="origenHabitualEditar">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="bloque-input no-mg">
                            <span class="icon-punto texto-recogida"></span>
                            <input type="text" id="autocompleteDestinoHabitualEditar" class="form-control destinoInput" placeholder="Ciudad de destino">
                            <span class="glyphicon glyphicon-remove form-control-feedback feedback-alertas" aria-hidden="true"></span>
                            <input type="hidden" name="destinoHabitual" id="destinoHabitualEditar">
                        </div>
                    </div>

                    <div id="diasEditar" class="form-group">
                        <label class="control-label">¿Qué días viajas?</label><br>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="LU" value="1">LU
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="MA" value="2">MA
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="MI" value="3">MI
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="JU" value="4">JU
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="VI" value="5">VI
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="SA" value="6">SA
                            </label>
                            <label class="btn btn-default">
                                <input type="checkbox" name="dias[]" id="DO" value="7">DO
                            </label>
                        </div>
                        <div class="feedback-alertas"></div>
                    </div>
                    <input type="hidden" name="alertaId" id="alertaIdHabitualEditar">
                    {!! csrf_field() !!}

                    <button type="button" id="submitHabitualEditar" class="btn-app" style="display: inline-block;">Crear alerta</button>
                </form>

            </div>

        </div>
    </div>
</div>



@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')

    <script type="text/javascript" src="{{mix('js/web/alerta.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={!! env("MAPS_KEY") !!}&libraries=places&callback=initAutocompletes" async defer></script>

@endpush
