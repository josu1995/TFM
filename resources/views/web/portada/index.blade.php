@extends('layouts.web')
@section('title', ' Envío de paquetes económico, envía tu paquete al mejor precio')
@section('meta_description', 'Transporter, plataforma tecnológica de envío y transporte de paquetería. Envíos nacionales e internacionales. Enviar paquetes baratos nunca ha sido tan fácil con nuestro servicio premium a precios económicos.')

@inject('Carbon', 'Carbon\Carbon')

@section('content')
    <!--  Hero Section  -->
	@include('web.partials.cabecera-enviar-home')
	<!--  End Hero Section  -->

    <!--  Featured On Section  -->
    <section class="home-separator home-search-bar">
    	<div class="location-search">
			<span>
				<img class="lazy" data-src="{{ asset('img/home/map.png') }}" height="75px" alt="Envíos geolocalizados">
				<label>Sigue tu envío:</label>
			</span>
			<span class="square-input button-right">
				<input class="form-control tracking-input" placeholder="Introduce tu localizador"/>
				<button class="btn btn-corporativo btn-tracking"><i class="fas fa-chevron-right"></i></button>
			</span>
		</div>
		{{--<div class="stores-search">--}}
			{{--<span>--}}
				{{--<img src="{{ asset('img/home/store.png') }}" height="75px" alt="Encuentra tu Transporter Store">--}}
				{{--<label>Encuentra tu Store<br>más cercano:</label>--}}
			{{--</span>--}}
			{{--<span class="square-input button-right">--}}
				{{--<input class="form-control stores-search-input" placeholder="Introduce tu ciudad o CP"/>--}}
				{{--<button class="btn btn-corporativo btn-stores-search"><i class="fas fa-chevron-right"></i></button>--}}
			{{--</span>--}}
		{{--</div>--}}
    </section>
    <!--  End Featured On Section  -->

	<!--  App Features Section  -->
	<section class="app_features" id="app_features">
		<div class="container">
			<div class="row text-center">
				<div class="col-sm-4 col-md-4 details wow fadeInDown" data-wow-delay="0s">
					<span class="icono-caracteristica"><i class="fas fa-euro-sign"></i></span>
					<h2 class="text-uppercase">Económico</h2>
					<p>Ahorra en tus envíos con un servicio premium a precios asequibles.<br></p>
				</div>
				<div class="col-sm-4 col-md-4 details wow fadeInDown" data-wow-delay=".1s">
					<span class="icono-caracteristica"><i class="fas fa-map-marker-alt"></i></span>
					<h2 class="text-uppercase">Localizable</h2>
					<p>Nuestra tecnología permite ver dónde está tu envío en todo momento. SMS/Email de recepción y entrega al destinatario.<br></p>
				</div>
				<div class="col-sm-4 col-md-4 details wow fadeInDown" data-wow-delay=".1s">
					<span class="icono-caracteristica"><i class="fas fa-thumbs-up"></i></span>
					<h2 class="text-uppercase">Sencillo</h2>
					<p>Gestión 100% online del envío. No necesitas tener en casa ni embalaje ni cinta adhesiva.<br></p>
				</div>
			</div>
		</div>
	</section>
	<!--  And App Features Section  -->



    {{-- Weecomments --}}
	<section class="home-separator">
		<h3>Nuestros usuarios opinan:</h3>
	</section>
    <section class="testimonials animated wow fadeIn" id="testimonials" data-wow-duration="2s">
    	<div class="container">

            <div class="opiniones">

				<ul class="no-pd">

					@foreach($opiniones as $key => $opinion)
						<li class="opinion text-center">
							<div class="rating">
								<span class="valoracion">{{ $opinion->rating }}/10</span>
								<br>
								@if($opinion->rating/2 >= 1)
									<span class="ministar ministar-full"></span>
								@elseif($opinion->rating/2 > 0 && $opinion->rating/2 < 1)
									<span class="ministar ministar-half"></span>
								@else
									<span class="ministar ministar-none"></span>
								@endif
								@if($opinion->rating/2 >= 2)
									<span class="ministar ministar-full"></span>
								@elseif($opinion->rating/2 > 1 && $opinion->rating/2 < 2)
									<span class="ministar ministar-half"></span>
								@else
									<span class="ministar ministar-none"></span>
								@endif
								@if($opinion->rating/2 >= 3)
									<span class="ministar ministar-full"></span>
								@elseif($opinion->rating/2 > 2 && $opinion->rating/2 < 3)
									<span class="ministar ministar-half"></span>
								@else
									<span class="ministar ministar-none"></span>
								@endif
								@if($opinion->rating/2 >= 4)
									<span class="ministar ministar-full"></span>
								@elseif($opinion->rating/2 > 3 && $opinion->rating/2 < 4)
									<span class="ministar ministar-half"></span>
								@else
									<span class="ministar ministar-none"></span>
								@endif
								@if($opinion->rating/2 >= 5)
									<span class="ministar ministar-full"></span>
								@elseif($opinion->rating/2 > 4 && $opinion->rating/2 < 5)
									<span class="ministar ministar-half"></span>
								@else
									<span class="ministar ministar-none"></span>
								@endif
							</div>
							<p>
								@if(!is_null($opinion->usuario) && !is_null($opinion->usuario->imagen))
									<img class="opinion-avatar lazy" data-src="{{ $opinion->usuario->imagen->path }}" width="100px" height="100px" alt="Opinión de {{ $opinion->usuario->configuracion->nombre }} en Transporter">
								@elseif(!is_null($opinion->usuario))
									<img class="opinion-avatar lazy" data-src="{{ asset('img/commons/transporter-default-user.png') }}" width="100px" height="100px" alt="Opinión de {{ $opinion->usuario->configuracion->nombre }} en Transporter">
								@else
									<img class="opinion-avatar lazy" data-src="{{ asset('img/commons/transporter-default-user.png') }}" width="100px" height="100px" alt="Opinión de {{ $opinion->usuario_nombre }} en Transporter">
								@endif
							</p>
							<p class="opinion-text">{{ $opinion->opinion }}</p>
							<p class="opinion-nombre">{{ $opinion->usuario_nombre ?? $opinion->usuario->configuracion->nombre }}</p>
							<p class="opinion-fecha">{{ $opinion->created_at->formatLocalized('%d de %B, %Y') }}</p>
						</li>

					@endforeach

				</ul>

			</div>
        </div>
    </section>

    <section class="section-business">
		<div>
			<div class="business-image-container lazy" data-src="{{ asset('img/home/transporter-business.jpg') }}"></div>
		</div>
		<div>
			<div class="business-text-container">
				<div>
					<h4>Transporter Business</h4>
					<h1>¿Tienes una empresa?</h1>
					<h3>Gestiona y sigue en tiempo real los envíos de tu tienda online. Diferénciate de la competencia y asombra a tus clientes con un servicio único.</h3>
					<a href="{{ route('business_landing_index') }}" class="btn btn-corporativo">EMPEZAR<i class="fas fa-chevron-right"></i></a>
				</div>
			</div>
		</div>
    </section>

   	<section class="featured_on section-prensa">
		<div class="container">
			<div class="row">
				<a class="col-sm-3 abc" title="Transporter en ABC">
					<img class="lazy" data-src="{{asset('img/medios/logo-abc-transporter-250.png')}}" width="180px" alt="Transporter en ABC">
				</a>
				<a class="col-sm-3" title="Transporter en El País">
					<img class="lazy" data-src="{{asset('img/medios/logo-elpais-transporter.png')}}" width="250px" alt="Transporter en El País">
				</a>
				<a class="col-sm-3" title="Transporter en Inversión y finanzas">
					<img class="lazy" data-src="{{asset('img/medios/logo-inversionyfinanzas-transporter.png')}}" width="250px" alt="Transporter en Inversión y finanzas">
				</a>
				<a class="col-sm-3" title="Transporter en El Confidencial">
					<img class="lazy" data-src="{{asset('img/medios/logo-elconfidencial-transporter.png')}}" width="357px" alt="Transporter en El Confidencial">
				</a>
			</div>
		</div>
   	</section>
@endsection

@push('javascripts-footer')
	{{-- Scripts --}}
	<script type="text/javascript" src="{{asset('js/vendor/wow.js')}}"></script>
	<script src="{{ mix('js/dist/lazyload.min.js') }}"></script>

	<script type="text/javascript" async defer>

        const myLazyLoad = new LazyLoad({
            elements_selector: ".lazy"
        });

		$(function() {

		    $('.btn-tracking').click(function() {
		        const value = $('.tracking-input').val();
		        if(value !== '') {
                    window.location = '/tracking/' + value;
                }
			});

            $('.btn-stores-search').click(function() {
                const value = $('.stores-search-input').val();
                if(value !== '') {
                    window.location = '/busqueda-stores?city=' + value;
                }
            });



		});
        //wow.js on scroll animations initialization
        wow = new WOW({
            animateClass: 'animated',
            mobile: true,
            offset: 50
        });
        wow.init();

	</script>
@endpush