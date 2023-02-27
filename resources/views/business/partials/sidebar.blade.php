<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">


            <li class="treeview" id="pendientes-pago"><a href="{{ route('business_envios_pendientes_pago') }}"><i class="far fa-circle"></i> Gestión de palabras</a></li>
            <li class="treeview" id="pendientes-pago"><a href="{{ route('admin_usuarios') }}"><i class="far fa-circle"></i> Gestión de usuarios</a></li>
                   
     
            
            
                
           
            
            <li class="separator"></li>
            <li id="cuenta" class="treeview">
                <a href="#">
                    <i class="fas fa-user"></i> <span>Cuenta</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li id="datos-usuario"><a href="{{ route('business_cuenta_datos_usuario') }}"><i class="far fa-circle"></i> Datos de usuario</a></li>
                    <li id="datos-pago"><a href="{{ route('business_cuenta_datos_pago') }}"><i class="far fa-circle"></i> Datos de pago</a></li>
                    <li id="datos-facturacion"><a href="{{ route('business_cuenta_datos_facturacion') }}"><i class="far fa-circle"></i> Datos de facturación</a></li>
                    <li id="facturas"><a href="{{ route('business_cuenta_facturas') }}"><i class="far fa-circle"></i> Facturas</a></li>
                    <li id="condiciones-servicio"><a href="{{ route('business_cuenta_condiciones_servicio') }}"><i class="far fa-circle"></i> Condiciones de servicio</a></li>
                </ul>
            </li>
            <li id="soporte" class="treeview">
                <a href="#">
                    <i class="material-icons">live_help</i>&nbsp<span class="tree-title">Soporte</span>
                    <span class="pull-right-container addMargin">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li id="soporte-ayuda"><a href="{{ route('muestra_inicio_ayuda') }}"><i class="far fa-circle"></i> Ayuda</a></li>
                    <li id="soporte-atencion-cliente"><a href="{{ route('business_ayuda') }}"><i class="far fa-circle"></i> Atención al cliente</a></li>
                </ul>
            </li>
            <li>
                <a href="{{ route('business_logout') }}">
                    <i class="fas fa-sign-out-alt fa-rotate-180"></i> <span>Cerrar sesión</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

@push('javascripts-footer')
    <script>

        function change(){
            
            @php(Session::put('simular', 'checkout'))
        }
        $(function() {

            $.get('{!! route('business_home_badges') !!}', function(data) {

                if(data.devoluciones) {
                    var badge = '<small class="label sidebar-badge devoluciones-badge">' + data.devoluciones + '</small>';
                    $('.devoluciones-badge-container').each(function () {
                        $(this).append(badge);
                    });
                }

            });

        });

        $('#inventario-existencias').click(function(){
            console.log('aaa');
            @if(Session::has('filtro'))
                console.log('1');
                @php(Session::forget('filtro'))
                
            @else
                console.log('2');
                @php(Session::put('filtro',null))
            @endif
        });


    </script>
@endpush