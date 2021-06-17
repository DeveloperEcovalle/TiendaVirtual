@extends('website.layout')

@section('title', 'Inicio')

@section('content')
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" v-if="iCargando === 0" v-cloak>
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" v-for="(banner, i) in lstBanners" :data-slide-to="i" :class="{ active: i === 0 }"></li>
        </ol>
        <div class="carousel-inner">
            <div v-for="(banner, i) in lstBanners" :class="{ active: i === 0 }" class="carousel-item">
                <a :href="banner.enlace" target="_blank" v-if="banner.enlace">
                    <img :src="banner.ruta_imagen" :alt="banner.descripcion" class="d-block w-100">
                </a>
                <img :src="banner.ruta_imagen" :alt="banner.descripcion" class="d-block w-100" v-else>
                <div class="carousel-caption d-none d-md-block" v-if="banner.descripcion">
                    <h5>@{{ banner.descripcion }}</h5>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <section class="bg-ecovalle-6">
        <div class="container-xl">
            <div class="row py-4 py-lg-5">
                <div class="col-lg-3 py-3 py-lg-0 text-center panel-atributo-ecovalle">
                    <img src="/img/delivery.svg" class="img-atributo-ecovalle">
                    <img src="/img/delivery_hover.svg" class="img-atributo-ecovalle hover">
                    <p class="font-weight-bolder text-uppercase mb-0 mt-2">Delivery</p>
                    <p class="mb-0">{{ $lstLocales['nationwide_shipments'] }}</p>
                </div>
                <div class="col-lg-3 py-3 py-lg-0 text-center panel-atributo-ecovalle">
                    <img src="/img/atencion_online.svg" class="img-atributo-ecovalle">
                    <img src="/img/atencion_online_hover.svg" class="img-atributo-ecovalle hover">
                    <p class="font-weight-bolder text-uppercase mb-0 mt-2">{{ $lstLocales['online_service'] }}</p>
                    <p class="mb-0">{{ $lstLocales['hours_from_monday_to_saturday'] }}</p>
                </div>
                <div class="col-lg-3 py-3 py-lg-0 text-center panel-atributo-ecovalle">
                    <img src="/img/pago_seguro.svg" class="img-atributo-ecovalle">
                    <img src="/img/pago_seguro_hover.svg" class="img-atributo-ecovalle hover">
                    <p class="font-weight-bolder text-uppercase mb-0 mt-2">{{ $lstLocales['secure_payment'] }}</p>
                    <p class="mb-0">{{ $lstLocales['guaranteed_payment_technology'] }}</p>
                </div>
                <div class="col-lg-3 py-3 py-lg-0 text-center panel-atributo-ecovalle">
                    <img src="/img/calidad_garantizada.svg" class="img-atributo-ecovalle">
                    <img src="/img/calidad_garantizada_hover.svg" class="img-atributo-ecovalle hover">
                    <p class="font-weight-bolder text-uppercase mb-0 mt-2">{{ $lstLocales['guaranteed_quality'] }}</p>
                    <p class="mb-0">{{ $lstLocales['thinking_about_the_welfare_of_all'] }}</p>
                </div>
            </div>
        </div>
    </section>

    <div class="text-center py-5 my-5" v-if="iCargando === 1">
        <img src="/img/spinner.svg">
    </div>

    <section v-if="iCargando === 0" v-cloak>
        <div class="container-xl pb-5" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>

    <section v-if="iCargando === 0 && lstCarouselProductos.length > 0" v-cloak>
        <div class="container-xl">
            <h2 class="h3 text-center font-weight-bold mb-4 titulo-subrayado">{{ $lstLocales['new_revenues'] }}</h2>
            <div id="carouselProductos" class="carousel slide pb-5" data-ride="carousel" v-if="iCargando === 0">
                <ol class="carousel-indicators" v-if="lstCarouselProductos.length > 1">
                    <li data-target="#carouselProductos" v-for="(lstProductos, i) in lstCarouselProductos" :data-slide-to="i" class="bg-ecovalle" :class="{ active: i === 0 }"></li>
                </ol>
                <div class="carousel-inner pb-2">
                    <div v-for="(lstProductos, i) in lstCarouselProductos" :class="{ active: i === 0 }" class="carousel-item">
                        <div class="row justify-content-center">
                            <div class="col-11 col-md-3" v-for="(producto, i) in lstProductos">
                                <div class="card my-2 shadow-lg">
                                    <div class="card-header p-0 bg-transparent" style="height: 180px">
                                        <div class="div-oferta position-absolute" v-if="producto.oferta_vigente">
                                            <div class="justify-content-between">
                                                - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }} DSCTO.
                                            </div>
                                        </div>
                                        <div class="div-promocion position-absolute" v-if="producto.promocion_vigente">
                                            <div class="justify-content-between">
                                                +@{{ producto.promocion_vigente.min }} hasta -@{{ producto.promocion_vigente.max }}
                                                @{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO.
                                            </div>
                                        </div>
                                        <span class="badge badge-warning badge-nuevo position-absolute px-2 py-1 text-white"
                                              v-if="(new Date().getTime() - new Date(producto.fecha_reg).getTime()) / (1000 * 3600 * 24) <= 90">
                                            @{{ locale === 'es' ? 'NUEVO' : 'NEW' }}
                                        </span>
                                        <span class="badge badge-danger badge-ultimos position-absolute px-2 py-1 text-white"
                                              v-if="producto.stock_actual - producto.stock_separado <= 10 && producto.stock_actual - producto.stock_separado > 1">
                                            @{{ locale === 'es' ? `ULTIMOS ${producto.stock_actual - producto.stock_separado}` : `LAST ${producto.stock_actual}` }}
                                        </span>
                                        <span class="badge badge-danger badge-ultimos position-absolute px-2 py-1 text-white"
                                              v-if="producto.stock_actual - producto.stock_separado === 1">
                                            @{{ locale === 'es' ? `ULTIMO` : `LAST ONE` }}
                                        </span>
                                        <div class="overflow-hidden">
                                            <a class="img-producto" :href="'/tienda/producto/' + producto.id" v-if="producto.imagenes.length > 0">
                                                <div class="img-background-thumbnail producto" :class="{ 'gray-scale': producto.stock_actual - producto.stock_separado === 0 }"
                                                     :style="{ 'background-image': 'url(' + producto.imagenes[0].ruta + ')' }"></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body bg-light p-3">
                                        <h6 class="card-title m-0">
                                            <a class="producto font-weight-bold" :href="'/tienda/producto/' + producto.id">@{{ locale === 'es' ? producto.nombre_es : producto.nombre_en }}</a>
                                        </h6>
                                        <div class="py-2">
                                            <div class="starrr" v-star-rating="{ readOnly: true, rating: producto.cantidad_calificaciones === 0 ? 0 : (producto.sumatoria_calificaciones / producto.cantidad_calificaciones) }"></div>
                                            <p class="text-muted m-0 small">(@{{ producto.cantidad_calificaciones + (producto.cantidad_calificaciones === 1 ? ' calificación' : ' calificaciones') }})</p>
                                        </div>
                                        <div class="pb-2">
                                            <div class="text-right">
                                                <p class="font-bold m-0 h4">
                                                    <span class="text-amarillo-ecovalle font-weight-bold" v-if="producto.oferta_vigente">
                                                        S/ @{{ (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto)).toFixed(2) }}
                                                    </span>
                                                    <span class="text-amarillo-ecovalle font-weight-bold" v-else>
                                                        S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div v-if="producto.stock_actual - producto.stock_separado === 0">
                                            <button class="btn btn-sm btn-block btn-danger font-weight-bold py-2" disabled="disabled">
                                                @{{ locale === 'es' ? 'AGOTADO' : 'SOLD OUT' }}
                                            </button>
                                        </div>
                                        <div v-else>
                                            <div class="input-group" v-if="producto.cantidad && producto.cantidad > 0">
                                                <span class="input-group-prepend">
                                                    <button type="button" class="btn btn-ecovalle" v-on:click="ajaxDisminuirCantidadProductoCarrito(producto)">
                                                        <i class="fas" :class="{ 'fa-minus': producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                                                    </button>
                                                </span>
                                                <input type="text" class="form-control text-center" :value="producto.cantidad">
                                                <span class="input-group-append">
                                                <button type="button" class="btn btn-ecovalle" v-on:click="ajaxAumentarCantidadProductoCarrito(producto)">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </span>
                                            </div>
                                            <button class="btn btn-sm btn-block btn-ecovalle py-2" v-on:click="ajaxAgregarAlCarrito(producto)" :disabled="iAgregandoAlCarrito === 1 && iProductoId === producto.id" v-else>
                                                <span v-if="iAgregandoAlCarrito === 1 && iProductoId === producto.id"><i class="fas fa-circle-notch fa-spin"></i></span>
                                                <span v-else><i class="fas fa-shopping-cart"></i>&nbsp;{{ $lstLocales['Add to cart'] }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev px-3 w-auto" href="#carouselProductos" role="button" data-slide="prev">
                    <span class="fas fa-chevron-left fa-2x text-ecovalle" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next px-3 w-auto" href="#carouselProductos" role="button" data-slide="next">
                    <span class="fas fa-chevron-right fa-2x text-ecovalle" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </section>

    <section v-if="iCargando === 0" v-cloak>
        <div class="container-xl py-5">
            <div class="row">
                <div class="col-md-6 bg-ecovalle-2">
                    <div class="p-3 p-md-5">
                        <div class="py-5">
                            <h2 class="h1 font-weight-bold text-white">{{ $lstTraduccionesInicio['become_an_ecovalle_partner'] }}</h2>
                            <p class="text-white text-justify">{{ $lstTraduccionesInicio['we_support_you'] }}</p>
                            <div class="text-center text-md-left">
                                <a href="/se-ecovalle" class="btn btn-outline-blanco btn-hover-ecovalle text-uppercase">{{ $lstTraduccionesInicio['join_now'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6" style="background: url('/img/inicio_se_socio.jpg') center center; background-size: cover">
                    <div class="py-5 my-5 d-md-none"><h1>&nbsp;</h1></div>
                </div>
            </div>
        </div>
    </section>

    <section v-if="iCargando === 0 && lstBlogs.length > 0" v-cloak>
        <div class="container-xl">
            <div class="row pt-5 justify-content-center">
                <div class="col-10 col-lg-12 py-3">
                    <h1 class="h2 font-weight-bold mb-4 text-center titulo-subrayado">{{ $lstTraduccionesInicio['latest_content'] }}</h1>
                </div>
            </div>
            <div class="row pb-4 justify-content-center">
                <div class="col-lg-4 col-md-4 col-sm-5 col-11 pb-5" v-for="blog in lstBlogs">
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

    @parent
@endsection

@section('js')
    <script src="/js/website/inicio.js?n=1"></script>
@endsection
