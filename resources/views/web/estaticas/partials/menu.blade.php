<ul class="lista-temas" data="{{$categoria->id}}" id="{{$categoria->id}}">
    @foreach ($categoriasHija as $categoriaHija)
        {{-- Categorias --}}
        <li class="categoria">
            <a href="" data-siguiente="{{ $categoriaHija->id }}" class="categoryLink">
                <i class="{{ $categoriaHija->icono }}"></i>
                {{ $categoriaHija->nombre }}
                <span class="icon-flecha-r"></span>
            </a>
        </li>
    @endforeach
    {{-- Páginas de categoría --}}
    @foreach ($categoria->paginas as $pagina)
        <li>
            <a href="{{ route('muestra_pagina_'.$categoria->slug, ['slug2' => $categoriaHija->slug, 'slug3'=> $pagina->slug]) }}" class="categoryLink">
                {{ $pagina->titulo }}
            </a>
        </li>
    @endforeach
</ul>


@foreach ($categoriasHija as $categoriaHija)
    @include('web.estaticas.partials.menuoculto', ['categoriaHija' => $categoriaHija])
@endforeach
