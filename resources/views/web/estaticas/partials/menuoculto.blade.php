<div class="sublista-temas">
    <ul data="{{ $categoriaHija->id }}" class="lista-temas">
        <a href="#" class="atras-ayuda" data="{{$categoriaHija->id}}">

            <span class="icon-flecha-l"></span> {{ $categoriaHija->nombre }}

        </a>

        {{-- Categorías de categoría --}}
        @foreach($categoriaHija->categoriasHija as $categoriaNieta)
            <li class="categoria">
                <a href="" data-siguiente="{{ $categoriaNieta->id }}" data-anterior="{{$categoriaHija->id}}" class="sub-categoria categoryLink">
                    <i class="{{ $categoriaNieta->icono }}"></i>
                    {{ $categoriaNieta->nombre }}
                    <span class="icon-flecha-r"></span>
                </a>
            </li>

        @endforeach
        {{-- Paginas de categoria --}}

        @foreach($categoriaHija->paginas as $pagina)
            <li>
                <a href="{{ route('muestra_pagina_'.$categoria->slug, ['slug2' => $categoriaHija->slug, 'slug3'=> $pagina->slug]) }}" class="categoryLink">
                    {{ $pagina->titulo }}
                </a>
            </li>
        @endforeach
    </ul>
</div>

@foreach($categoriaHija->categoriasHija as $categoriaNieta)
    @if (count($categoriaNieta->categoriasHija) || count($categoriaNieta->paginas))
        @include('web.estaticas.partials.menuoculto', ['categoriaHija' => $categoriaNieta])
    @endif
@endforeach
