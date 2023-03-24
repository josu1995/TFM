<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            @if(\Auth::user()->rol == 1)
                <li>
                <a href="{{ route('business_envios_pendientes_pago') }}">
                    <span>Gestión de palabras</span>
                        <span class="pull-right-container">
                        
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_usuarios') }}">
                        <span>Gestión de usuarios</span>
                        <span class="pull-right-container">
                            
                        </span>
                    </a>    
                </li>
                <li>
                    <a href="{{ route('admin_redaccion') }}">
                        <span>Corrección de redacciones</span>
                        <span class="pull-right-container">
                            
                        </span>
                    </a>    
                </li>
            @else
                <li>
                    <a href="{{ route('usuario_get_estudios') }}">
                        <span>{!! trans('usuario.estudios') !!}</span>
                        <span class="pull-right-container">
                            
                        </span>
                    </a>    
                </li>
                <li>
                    <a href="{{ route('usuario_get_redaccion') }}">
                        <span>{!! trans('usuario.redaccion') !!}</span>
                        <span class="pull-right-container">  
                        </span>
                    </a>    
                </li>
            @endif
     
            
            
                
           
            
            <li class="separator"></li>

                
            <li id="datos-usuario"><a href="{{ route('usuario_get_datos') }}"><i class="fas fa-user"></i> {!! trans('usuario.datos') !!}</a></li>
                
            
            <li id="soporte" class="treeview">
                <a href="{{ route('business_ayuda') }}">
                    <i class="material-icons">live_help</i>&nbsp<span class="tree-title">{!! trans('usuario.ayuda') !!}</span>
                    <span class="pull-right-container addMargin">
                       
                    </span>
                </a>
                
            </li>
            <li>
                <a href="{{ route('business_logout') }}">
                    <i class="fas fa-sign-out-alt fa-rotate-180"></i> <span>{!! trans('usuario.cerrar') !!}</span>
                    <span class="pull-right-container">
                       
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