@extends('layouts.web')

@section('title', 'Perfil')

@section('content')
    <div class="row-fluid navegacion-home">
        <!-- Navegación -->
        @include('web.home.navegacion')

    </div>

    <div class="inner-perfil">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <ul class="links-perfil">
                        <li><a href="{{ route('perfil_usuario') }}" class="{{ url()->current() == route('perfil_usuario') ? 'perfil-activo' : '' }}">Datos de usuario</a></li>
                        <li><a href="{{ route('perfil_imagen') }}" class="{{ url()->current() == route('perfil_imagen') ? 'perfil-activo' : '' }}">Imagen de perfil</a></li>
                        <li><a href="{{ route('perfil_certificados') }}" class="{{ url()->current() == route('perfil_certificados') ? 'perfil-activo' : '' }}">Certificaciones</a></li>
                        <li><a href="{{ route('direccion_postal') }}" class="{{ url()->current() == route('direccion_postal') ? 'perfil-activo' : '' }}">Dirección postal</a></li>
                    </ul>
                </div>

                <div class="col-md-9">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Mensaje de éxito en actualización de perfil usuario --}}
                    @if(session('mensaje'))
                        <div class="alert alert-success">
                            <p>
                                {{ session('mensaje') }}
                            </p>
                        </div>
                    @endif

                    {{-- Mensajes de error --}}
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <p>
                                {{ session('error') }}
                            </p>
                        </div>
                    @endif

                    <div class="bloques-perfil">

                        @yield('perfil')

                    </div>

                </div>
            </div>
        </div>
    </div>



@endsection

@push('javascripts-footer')
<script type="text/javascript" src="{{asset('js/vendor/intltelinput/intlTelInput.min.js')}}"></script>
<script async defer type="text/javascript" src="{{asset('js/vendor/intltelinput/custom.js')}}"></script>

    <script defer>
        $(document).ready(function() {
            // --
            // Al pulsar enlaces del menu página "Perfil"
            // --

            var linksPerfil = $('.links-perfil li a');
//            var bloquesPerfil = $('.bloques-perfil li');

            linksPerfil.click(function () {
                var dataLink = $(this)
                        .attr('data-perfil');
//                bloquesPerfil.removeClass('perfil-activo');
                linksPerfil.removeClass('perfil-activo');
                $(this)
                        .addClass('perfil-activo');
//                bloquesPerfil.each(function () {
//                    var dataPerfil = $(this)
//                            .attr('data-perfil');
//                    if (dataLink == dataPerfil) {
//                        $(this)
//                                .toggleClass('perfil-activo');
//                    }
//                });
            });
        });
    </script>
@endpush