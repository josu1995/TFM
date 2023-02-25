@extends('layouts.web')

@section('title', 'Cuenta')

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
                        <li><a href="{{ route('resumen_pedidos') }}" class="{{ url()->current() == route('resumen_pedidos') ? 'perfil-activo' : '' }}">Pagos Efectuados</a></li>
                        <li><a href="{{ route('cuenta_metodo_pago') }}" class="{{ url()->current() == route('cuenta_metodo_pago') ? 'perfil-activo' : '' }}">Métodos de pago</a></li>
                        <li><a href="{{ route('cuenta_password') }}" class="{{ url()->current() == route('cuenta_password') ? 'perfil-activo' : '' }}">Cambio de Contraseña</a></li>

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

        linksPerfil.click(function () {
            var dataLink = $(this)
                .attr('data-perfil');
            linksPerfil.removeClass('perfil-activo');
            $(this)
                .addClass('perfil-activo');
        });
    });
</script>
@endpush