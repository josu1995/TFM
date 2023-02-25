@extends('layouts.blog')
@section('meta_description', isset($post) ? $post->summary : "Citystock blog")
@section('meta_keywords', isset($post) ? $post->keywords : "Citystock blog")
@section('twitter_summary', isset($post) ? $post->summary : "Citystock blog")
@section('url', isset($post) ? route('blog_get_index').'/'.$post->link : route('blog_get_index'))
@section('titulo', isset($post) ? $post->title : "Blog")
@section('resumen', isset($post) ? $post->summary : "Blog de Citystock")
@section('imagen', isset($post) ? $post->image : asset('img/identidad/citystock.png'))

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

            <div class="index blogSecondary">
                <div class="lastPostDetail row">
                    <div class="post-items card col-md-8 no-pd">
                        <div class="col-md-12 mg-t-15">
                            <h3 class="list-group-item-heading blogTitle postTitle">{{$post->title}}</h3>
                        </div>
                        <div class="media col-md-12 no-pd">
                            <figure>
                                <img class="media-object img-responsive postImage" width="100%"  src="{{$post->image}}" alt="{{$post->title}}">
                            </figure>
                        </div>
                        <div class="col-md-12 postFullContent">
                            <p class="list-group-item-text postContent">{!! $post->content !!}</p>
                            <small class="pull-right postAutor">{{$post->author}}</small><br>
                            <small class="pull-right postAutor">{{ $post->created_at->diffForHumans()}}</small><br>

                        </div>
                        <div class="col-md-12 stores-social blog-social-container">
                            <div class="blog-social">
                                <a class="facebook fb-share-button" data-href="{{route('blog_get_index').'/'.$post->link}}" href="https://www.facebook.com/sharer/sharer.php?u={{route('blog_get_index').'/'.$post->link}}&amp;src=sdkpreparse" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                <a class="twitter mg-l-20" href="https://twitter.com/intent/tweet?text={{$post->title}}&via=transporter_es&url={{route('blog_get_index').'/'.$post->link}}" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.linkedin.com/shareArticle?mini=false&url={{route('blog_get_index').'/'.$post->link}}&title={{$post->title}}&summary={{$post->summary}}&source={{$post->image}}" class="linkedin mg-l-20" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4 blog-adds">

                        <div class="card col-md-12 no-pd mg-l-20">
                            <div class="col-md-12 mg-t-15">
                                <h3 class="list-group-item-heading blogTitle postTitle">Realiza tus envíos por el precio más barato del mercado</h3>
                            </div>

                            <div class="media col-md-12 no-pd">
                                <figure>
                                    <img class="media-object img-responsive postImage" width="100%"  src="{{asset('img/blog/realizar-envios-baratos.jpg')}}" alt="transporter realizar envios baratos">
                                </figure>
                            </div>

                            <div class="media col-md-12 no-pd cardButton">
                                <a href="{{route('formulario_envio')}}" class="btn btn-corporativo">Enviar</a>
                            </div>
                        </div>

                        <div class="card col-md-12 no-pd mg-l-20">
                            <div class="col-md-12 mg-t-15">
                                <h3 class="list-group-item-heading blogTitle postTitle">Transporta paquetes y ahorra dinero</h3>
                            </div>

                            <div class="media col-md-12 no-pd">
                                <figure>
                                    <img class="media-object img-responsive postImage" width="100%"  src="{{asset('img/blog/transporter-ahorro-viajes.jpg')}}" alt="transporter ahorrar dinero en viajes">
                                </figure>
                            </div>

                            <div class="media col-md-12 no-pd cardButton">
                                <a href="{{route('drivers_buscar_viaje')}}" class="btn btn-corporativo">Transportar</a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Push de scripts --}}
@push('javascripts-footer')
<script async defer type="text/javascript" src="{{mix('js/blog/blog.js')}}"></script>
@endpush
