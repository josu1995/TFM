@if (!is_null($paginas) && count($paginas))
    @foreach($paginas as $pagina)
        <div class="b--busquedas">
            <div class="col-md-12">
                @if (!is_null($pagina->categoria) && !is_null($pagina->categoria->categoriaPadre))

                    <a href="{{ route('muestra_pagina_ayuda' , ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                        <p class="texto-corporativo">
                            <span class="{{ $pagina->icono }} texto-corporativo"></span>
                            <strong>{{ $pagina->titulo }}</strong> en {{ $pagina->categoria->nombre }}
                        </p>
                        <div class="texto-oscuro">{{ strip_tags(str_limit($pagina->texto, 200)) }}</div>
                    </a>
                @elseif(!is_null($pagina->categoria))
                    <a href="{{ route('muestra_pagina_ayuda' , ['slug2' => $pagina->categoria->slug, 'slug3' => $pagina->slug]) }}">
                        <p class="texto-corporativo">
                            <span class="{{ $pagina->icono }} texto-corporativo"></span>
                            <strong>{{ $pagina->titulo }}</strong> en {{ $pagina->categoria->nombre }}
                        </p>
                        <div class="texto-oscuro">{{ strip_tags(str_limit($pagina->texto, 200)) }}</div>
                    </a>
                @endif
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
