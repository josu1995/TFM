@if (!is_null($posts) && count($posts))
    @foreach($posts as $post)
        <div class="b--busquedas blog-busqueda-container">
            <div class="col-md-12">
                <a class="busquedaItem" href="{{route('blog_get_index').'/'.$post->link}}">
                    <p class="texto-corporativo">
                        <span class="{{ $post->image }} texto-corporativo"></span>
                        <strong>{{ $post->title }}</strong> de {{ $post->author }}
                    </p>
                    <div class="texto-oscuro">{{$post->summary}}</div>
                </a>
            </div>
        </div>
    @endforeach
@else
    <div class="text-center">
        <p>
            No se han encontrado resultados para los términos de búsqueda
        </p>
    </div>
@endif
