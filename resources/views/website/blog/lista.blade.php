@extends('website.layout')

@section('title', 'Blog')

@section('content')
    <section>
        <img :src="sBanner" class="w-100">
    </section>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
    </div>

    <section class="py-5">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mx-0 border rounded shadow">
                                <div class="col-12 bg-amarillo text-white text-uppercase py-2">
                                    {{ $lstLocales['categories'] }}
                                </div>
                                <div class="col-12 py-2 text-center border-bottom" v-if="iCargandoCategorias === 1"><img src="/img/spinner.svg"></div>
                                <div class="col-12 py-2" v-else>
                                    <div class="row">
                                        <div class="col-12 col-sm-4 col-lg-12 px-3" v-cloak>
                                            <div class="w-100 py-2">
                                                <div class="d-inline-block" v-icheck="{ type: 'radio' }">
                                                    <label class="m-0">
                                                        <input type="radio" name="lstCategoriasSeleccionadas[]" value="0" v-model="iCategoriaSeleccionada">
                                                        &nbsp;@{{ locale === 'es' ? 'Todas las categorías' : 'All categories' }}
                                                    </label>
                                                </div>
                                                {{--<span class="badge badge-success float-right mt-1">@{{ categoria.productos.length }}</span>--}}
                                            </div>
                                            <hr class="m-0">
                                        </div>
                                        <div class="col-12 col-sm-4 col-lg-12 px-3" v-for="(categoria, i) in lstCategorias" v-cloak>
                                            <div class="w-100 py-2">
                                                <div class="d-inline-block" v-icheck="{ type: 'radio' }">
                                                    <label class="m-0">
                                                        <input type="radio" name="lstCategoriasSeleccionadas[]" :value="categoria.id" v-model="iCategoriaSeleccionada">
                                                        &nbsp;@{{ locale === 'es' ? categoria.nombre_espanol : categoria.nombre_ingles }}
                                                    </label>
                                                </div>
                                                {{--<span class="badge badge-success float-right mt-1">@{{ categoria.productos.length }}</span>--}}
                                            </div>
                                            <hr class="m-0" v-if="i < lstCategorias.length - 1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 py-2 text-center border-bottom" v-if="lstCategorias.length === 0 && iCargandoCategorias === 0" v-cloak>
                                    <span class="small">No hay categor&iacute;as para mostrar</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 py-5">
                            <div class="row mx-0 border rounded shadow">
                                <div class="col-12 bg-amarillo text-white text-uppercase py-2">
                                    {{ $lstTraduccionesBlog['last_posts'] }}
                                </div>
                                <div class="col-12 py-2 text-center border-bottom" v-if="iCargandoCategorias === 1"><img src="/img/spinner.svg"></div>
                                <div class="col-12 py-2" v-else>
                                    <div class="row">
                                        <div class="col-12 col-sm-4 col-lg-12 px-3" v-for="(blog, i) in lstUltimasPublicaciones" v-cloak>
                                            <a class="text-decoration-none" :href="'/blog?v=publicacion&publicacion=' + blog.enlace + '&c=' + blog.id">
                                                <div class="row py-3">
                                                    <div class="col-4">
                                                        <div class="h-100" style="background-size: cover; background-position: center"
                                                             :style="{ 'background-image': 'url(' + blog.ruta_imagen_principal + ')' }"></div>
                                                    </div>
                                                    <div class="col-8">
                                                        <h4 class="h6 font-weight-bold text-ecovalle-2">@{{ blog.titulo }}</h4>
                                                        <p class="m-0 text-dark">@{{ locale === 'es' ? blog.categoria.nombre_espanol : blog.categoria.nombre_ingles }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                            <hr class="m-0" v-if="i < lstUltimasPublicaciones.length - 1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 text-center" v-if="iCargandoPublicaciones === 1">
                    <img src="/img/spinner.svg">
                </div>
                <div class="col-md-8" v-else>
                    <div class="row pb-4 justify-content-center">
                        <div class="col-md-6 col-sm-10 col-11 pb-5" v-for="blog in lstPublicaciones">
                            <div class="card rounded-lg shadow-lg">
                                <a :href="'/blog?v=publicacion&publicacion=' + blog.enlace + '&c=' + blog.id">
                                    <div class="img-background-thumbnail rounded-top-lg" :style="{ 'background-image': 'url(' + blog.ruta_imagen_principal + ')' }"></div>
                                </a>
                                <div class="card-body rounded-bottom-lg text-center">
                                    <h5 class="card-title font-weight-bold"><a class="text-dark text-decoration-none" :href="'/blog?v=publicacion&publicacion=' + blog.enlace + '&c=' + blog.id">@{{ blog.titulo }}</a></h5>
                                    <p class="card-text small">@{{ blog.resumen }}</p>
                                    <a :href="'/blog?v=publicacion&publicacion=' + blog.enlace + '&c=' + blog.id" class="btn btn-ecovalle-2 btn-sm px-4 text-uppercase">{{ $lstLocales['view_more'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--<div class="row py-2" v-for="publicacion in lstPublicaciones">
                        <div class="col-md-4">
                            <a :href="'/blog?v=publicacion&publicacion=' + publicacion.enlace + '&c=' + publicacion.id">
                                <div class="masthead mb-3 mb-lg-0" :style="'background-image: url(' + publicacion.ruta_imagen_principal + '); min-height: 150px; height: 22vh'"></div>
                            </a>
                        </div>
                        <div class="col-md-8">
                            <h4><a class="nav-ecovalle-2" :href="'/blog?v=publicacion&publicacion=' + publicacion.enlace + '&c=' + publicacion.id">@{{ publicacion.titulo }}</a></h4>
                            <p class="small text-ecovalle-2">
                                <i class="fas fa-user mr-2"></i>@{{ publicacion.usuario.persona.nombres }}
                                <i class="fas fa-calendar-alt ml-5 mr-2"></i>@{{ publicacion.fecha_reg }}
                            </p>
                            <p class="text-justify">@{{ publicacion.resumen }}</p>
                            <a :href="'/blog?v=publicacion&publicacion=' + publicacion.enlace + '&c=' + publicacion.id" class="float-right nav-ecovalle">Seguir leyendo &raquo;</a>
                        </div>
                        <div class="col-12 col-md-12 pt-3">
                            <div class="border-top"></div>
                        </div>
                    </div>--}}

                    <div class="row" v-cloak>
                        <div class="col-12 text-center">
                            <nav class="d-inline-block">
                                <ul class="pagination" v-if="lstPaginas.length > 0">
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Previous" v-on:click.prevent="navegarAnterior" :disabled="iPaginaSeleccionada === 0">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item" v-for="iPagina in lstPaginas" :class="{ active: iPagina === iPaginaSeleccionada }" :disabled="iPagina === -1">
                                        <a class="page-link" href="#" v-on:click.prevent="iPaginaSeleccionada = iPagina" v-if="iPagina !== -1">@{{ iPagina + 1 }}</a>
                                        <a class="page-link" href="#" v-on:click.prevent v-else>...</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Next" v-on:click.prevent="navegarSiguiente" :disabled="iPaginaSeleccionada + 1 === iTotalPaginas">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/blog.js?cvcn=14"></script>
@endsection
