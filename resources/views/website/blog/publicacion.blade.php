@extends('website.layout')

@section('title', 'Blog')

@section('content')
    <section>
        <img src="/img/blog.jpg" class="w-100">
    </section>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mt-4 mb-0 px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/blog">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page" v-cloak>@{{ publicacion.titulo }}</li>
            </ol>
        </nav>
    </div>

    <section class="py-4">
        <div class="container-xl pb-2 overflow-hidden">
            <div class="row" v-cloak>
                <div class="col-12 text-center" v-if="iCargandoPublicacion === 1">
                    <img src="/img/spinner.svg">
                </div>
                <div class="col-12" v-else>
                    <img :src="publicacion.ruta_imagen_principal" class="img-fluid mb-3">
                    <h1 class="h2 text-ecovalle-2 font-weight-bold text-uppercase my-3">@{{ publicacion.titulo }}</h1>
                    <div class="w-100" v-html="publicacion.contenido"></div>
                </div>
                <div class="col-12 py-3" v-cloak>
                    <a class="btn btn-facebook" :href="'https://www.facebook.com/sharer/sharer.php?u=https://ecovalle.pe/blog?v=publicacion&publicacion=' + publicacion.enlace + '&c=' + publicacion.id" target="_blank"><i class="fab fa-facebook-f"></i> Compartir</a>
                    <a class="btn btn-twitter" :href="'https://twitter.com/intent/tweet?url=https://ecovalle.pe/blog?v=publicacion&publicacion=' + publicacion.enlace + '&c=' + publicacion.id + '&text=' + publicacion.resumen" target="_blank"><i class="fab fa-twitter"></i> Twittear</a>
                </div>
            </div>
        </div>
    </section>

    <section v-if="iCargandoUltimasPublicaciones === 0" v-cloak>
        <div class="container-xl">
            <div class="row justify-content-center" v-if="lstUltimasPublicaciones.length > 0">
                <div class="col-10 col-lg-12 py-3">
                    <h1 class="h2 font-weight-bold mb-4 text-center titulo-subrayado">{{ $lstTraduccionesBlog['latest_content'] }}</h1>
                </div>
            </div>
            <div class="row pb-5 justify-content-center">
                <div class="col-lg-4 col-md-4 col-sm-5 col-11 pb-5" v-for="blog in lstUltimasPublicaciones">
                    <div class="card rounded-lg shadow-lg">
                        <div class="img-background-thumbnail rounded-top-lg" :style="{ 'background-image': 'url(' + blog.ruta_imagen_principal + ')' }"></div>
                        <div class="card-body rounded-bottom-lg text-center">
                            <h5 class="card-title">@{{ blog.titulo }}</h5>
                            <p class="card-text small">@{{ blog.resumen }}</p>
                            <a :href="'/blog?v=publicacion&publicacion=' + blog.enlace + '&c=' + blog.id" class="btn btn-ecovalle-2 btn-sm px-4 text-uppercase">{{ $lstLocales['view_more'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('js')
    <script type="text/javascript" src="/js/website/publicacion.js?cvcn=14"></script>
@endsection
