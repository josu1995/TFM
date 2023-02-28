<header class="main-header">
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        @svg('img/identidad/logotipo.svg')
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" id="menuPrincipal" data-toggle="push-menu" role="button">
            @include('partials.hamburger', ['class' => 'pull-right', 'function' => ''])
        </a>
    </nav>
    <!-- Logo -->
    
</header>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

  var rutaMenu = '{!! route('business_configuracion_changeMenu') !!}';

  let details = navigator.userAgent;
  let regexp = /android|iphone|kindle|ipad/i;
  let isMobileDevice = regexp.test(details);
  @if(Session::has('menu'))
        @if(Session::get('menu') == 1)
           console.log('Menu abierto');
            $('body').removeClass('sidebar-collapse');
            
        @else
            console.log('Menu cerrado');
            $('body').addClass('sidebar-collapse');
            
        @endif
    @else
    
    @endif



    $('#menuPrincipal').click(function(){
        if (isMobileDevice) {
            $('body').removeClass('sidebar-collapse');
        }else{
  
            $.ajax({
            url: rutaMenu,
            headers: { 'X-CSRF-TOKEN': csrf },
            type: 'POST',
            success: function (data) {
                console.log(data);
            }
        });
        }
      
       
    });

    
</script>