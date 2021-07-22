@extends('website.layout')

@section('title', 'Política de Privacidad')

@section('content')
    <div class="container-xl py-5 text-center" v-if="iCargandoPP === 1">
        <img class="my-5" src="/img/spinner.svg">
    </div>

    <section v-if="iCargandoPP === 0 && pagina.ruta_imagen_portada" class="h-35">
        <a :href="pagina.enlace_imagen_portada" v-if="pagina.enlace_imagen_portada">
            <img :src="pagina.ruta_imagen_portada" class="w-100 h-100">
        </a>
        <img :src="pagina.ruta_imagen_portada" class="w-100 h-100" v-else>
    </section>

    <div class="container-xl" v-if="iCargandoPP === 0" v-cloak>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>

    <section class="py-4" v-if="iCargandoPP === 0">
        <div class="container-xl pb-5" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>

    @parent
@endsection

@section('js')
    <script src="/js/website/politicaPrivacidad.js?cvcn=14"></script>
@endsection
