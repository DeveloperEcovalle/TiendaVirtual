@extends('website.layout')

@section('title', 'Guia Compras')

@section('content')
    <div class="container-xl py-5 text-center" v-if="iCargando === 1">
        <img class="my-5" src="/img/spinner.svg">
    </div>

    <section v-if="iCargando === 0" class="h-35">
        <a :href="pagina.enlace_imagen_portada" v-if="pagina.enlace_imagen_portada">
            <img :src="pagina.ruta_imagen_portada" class="w-100 h-100">
        </a>
        <img :src="pagina.ruta_imagen_portada" class="w-100 h-100" v-else>
    </section>

    <div class="container-xl" v-if="iCargando === 0" v-cloak>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>
    
    <section class="py-4" v-if="iCargando === 0">
        <div class="pb-5" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>
@endsection

@section('js')
    <script src="/js/website/guia.js?cvcn=14"></script>
@endsection
