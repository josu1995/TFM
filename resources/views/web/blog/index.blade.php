@extends('layouts.blog')
@section('titulo', 'Blog | Citystock')
@section('meta_description', "En el blog de Citystock encontrarás información sobre logística, e- commerce, retail, movilidad sostenible y las últimas innovaciones tecnológicas en estos sectores.")
@section('meta_keywords', "Citystock blog")
@section('resumen', "En el blog de Citystock encontrarás información sobre logística, e- commerce, retail, movilidad sostenible y las últimas innovaciones tecnológicas en estos sectores.")
@section('url', route('blog_get_index'))
@section('imagen', asset('img/redes/citystock-redes.jpg'))

@inject('Carbon', 'Carbon\Carbon')

@section('content')

    <div class="blogBusquedaContainer">
        <div class="m-busqueda-ayuda barra-busqueda-blog fondo-corporativo">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 text-left"><p class="texto-inverso">Búsqueda en el <strong>blog</strong></p></div>
                    <div class="col-md-7 no-pd blogBuscarInput">
                        <div class="input-group margin-bottom-sm">
                            <span class="input-group-addon"><i class="fas fa-search fa-fw"></i></span>
                            <input class="input-ayuda input-blog" type="text" placeholder="Buscar">
                        </div>
                        <div class="hidden-ayuda" id="resultados-buscador">
                        </div>
                    </div>
                    <div class="col-md-2 text-right"><a href="#"></a></div>
                </div>
                <div class="row">
                </div>
                <p class="ruta_buscar oculto">{{route('blog_buscar')}}</p>
            </div>
        </div>

        <div class="blogContainer">

            <div class="index blogSecondary row">
                <div class="col-md-8 no-pd">
                     @if(count($posts)==0)
                               <div class="alert alert-warning" role="alert">Todavía no tenemos entradas en el blog. Pronto comenzaremos a publicar contenido.</div>
                     @else
                    <div class="lastPost col-md-12 no-pd">
                        @foreach($posts as $post)
                            <div class="post-items card col-md-12 no-pd">
                                <div class="media col-md-4 no-pd">
                                    <a href="{{route('blog_get_index').'/'.$post->link}}">
                                        <figure>
                                            <img class="media-object img-responsive" width="100%"  src="{{$post->image}}" alt="{{$post->title}}">
                                        </figure>
                                    </a>
                                </div>
                                <div class="col-md-8 pd-t-15 pd-b-15">

                                    <h3 class="list-group-item-heading blogTitle"><a class="blogIndexLink" href="{{route('blog_get_index').'/'.$post->link}}">{{$post->title}}</a></h3>

                                    <p class="list-group-item-text"> {{$post->summary}}</p>
                                    <br>
                                        <div class="row blogIndexFooter">
                                            <span class="pull-left pd-l-15"><small>Publicado por {{$post->author}}</small><br><small>{{ $post->created_at->diffForHumans()}}</small></span>
                                            <br class="pull-right">
                                            <a type="button" class="btn btn-link shareBlog pull-right no-pd" rel="popover"><i class="fas fa-share-alt" aria-hidden="true"></i> Compartir</a>
                                            <div class="share-popover-content">
                                                <a class="facebook fb-share-button" data-href="{{route('blog_get_index').'/'.$post->link}}" href="https://www.facebook.com/sharer/sharer.php?u={{route('blog_get_index').'/'.$post->link}}&amp;src=sdkpreparse" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                                <a class="twitter mg-l-20" href="https://twitter.com/intent/tweet?text={{$post->title}}&via=transporter_es&url={{route('blog_get_index').'/'.$post->link}}" target="_blank"><i class="fab fa-twitter"></i></a>
                                                <a href="https://www.linkedin.com/shareArticle?mini=false&url={{route('blog_get_index').'/'.$post->link}}&title={{$post->title}}&summary={{$post->summary}}&source={{$post->image}}" class="linkedin mg-l-20" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4 col-md-offset-4 text-center">
                            <button class="btn btn-default btn-lg verMas">Ver más</button>
                            <span class="spinnerMas"><i class="fas fa-sync-alt fa-spin fa-5x fa-fw"></i></span>
                            <p class="ruta_mas oculto">{{route('blog_cargar')}}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4 blog-adds">

                    <div class="card col-md-12 no-pd mg-l-20">
                        <div class="col-md-12 mg-t-15">
                            <h3 class="list-group-item-heading blogTitle postTitle"> ¿Tienes un eCommerce o vendes en Marketplaces?</h3>
                        </div>

                        <div class="media col-md-12 no-pd">
                            <figure>
                                <img class="media-object img-responsive postImage" width="100%"  src="{{asset('img/blog/transporter-business.jpg')}}" alt="transporter realizar envios baratos">
                            </figure>
                        </div>

                        <div class="media col-md-12 no-pd cardButton">
                            <a href="{{route('business_landing_index')}}" class="btn btn-corporativo">Citystock</a>
                        </div>
                    </div>

                    {{-- <div class="card col-md-12 no-pd mg-l-20">
                        <div class="col-md-12 mg-t-15">
                            <h3 class="list-group-item-heading blogTitle postTitle">Envía tus paquetes con un servicio premium a precios asequibles</h3>
                        </div>

                        <div class="media col-md-12 no-pd">
                            <figure>
                                <img class="media-object img-responsive postImage" width="100%"  src="{{asset('img/blog/realizar-envios-baratos.jpg')}}" alt="transporter ahorrar dinero en viajes">
                            </figure>
                        </div>

                        <div class="media col-md-12 no-pd cardButton">
                            <a href="{{route('formulario_envio')}}" class="btn btn-corporativo">Enviar paquete</a>
                        </div>
                    </div> --}}

                </div>
            </div>


        </div>
    </div>

@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
<script async defer type="text/javascript" src="{{mix('js/blog/blog.js')}}"></script>
@endpush
