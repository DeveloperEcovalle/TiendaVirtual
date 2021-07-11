@extends('website.layout')

@section('title', 'Nosotros | Líneas de Productos')

@section('content')
    <section v-if="iCargando === 0">
        <a :href="pagina.enlace_imagen_portada" v-if="pagina.enlace_imagen_portada">
            <img :src="pagina.ruta_imagen_portada" class="w-100">
        </a>
        <img :src="pagina.ruta_imagen_portada" class="w-100" v-else>
    </section>

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

    <div class="container-xl" v-if="iCargando === 0" v-cloak>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/nosotros">{{ $lstLocales['About Us'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="container-xl py-5 text-center" v-if="iCargando === 1">
        <img class="my-5" src="/img/spinner.svg">
    </div>

    <section class="pt-4 pb-5" v-if="iCargando === 0" v-cloak>
        <div class="container-xl">
            <div class="row pb-5">
                <div class="col-lg-3">
                    <form v-on:submit.prevent>
                        <div class="row p-0">
                            <div class="col-12">
                                <div class="row mx-0 shadow mb-2">
                                    <div class="col-12 bg-amarillo text-white text-uppercase rounded-top py-2">
                                        {{ $lstLocales['Product lines'] }}
                                    </div>
                                    <div class="col-12 py-2 rounded-bottom text-center border-bottom" v-if="iCargandoLineasProductos === 1"><img src="/img/spinner.svg"></div>
                                    <div class="col-12 py-2 rounded-bottom" v-else>
                                        <div class="row">
                                            <div class="col-12 col-sm-4 col-lg-12 px-3" v-cloak>
                                                <div class="w-100 py-2">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0">
                                                            <input type="radio" name="iLineaProducto" value="0" v-model="iLineaProductoId">
                                                            &nbsp;@{{ locale === 'es' ? 'Todas las líneas' : 'All the lines' }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="m-0">
                                            </div>
                                            <div class="col-12 col-sm-4 col-lg-12 px-3" v-for="(linea, i) in lstLineasProductos" v-cloak>
                                                <div class="w-100 py-2">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0">
                                                            <input type="radio" name="iLineaProducto" :value="linea.id" v-model="iLineaProductoId">
                                                            &nbsp;@{{ locale === 'es' ? linea.nombre_espanol : linea.nombre_ingles }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <hr class="m-0" v-if="i < lstLineasProductos.length - 1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 py-2 text-center border-bottom" v-if="lstLineasProductos.length === 0 && iCargandoLineasProductos === 0" v-cloak>
                                        <span class="small">No hay líneas de producto registradas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="py-5 py-lg-0">
                        <a :href="pagina.enlace_baner_publicitario" v-if="pagina.enlace_baner_publicitario">
                            <img class="img-fluid" :src="pagina.ruta_baner_publicitario">
                        </a>
                        <img  v-else class="img-fluid" :src="pagina.ruta_baner_publicitario">
                    </div>
                </div>
                <div class="col-lg-9" v-if="iLineaProductoId == 0 && iCargandoLineasProductos == 0">
                    <a :href="'/nosotros/lineas-productos?linea=' + lstLineasProductosConImagen[0].id">
                        <div class="shadow-lg img-background-thumbnail" style="height: 100% !important;">
                            <img class="img-fluid" style="height: 100% !important;" :src="lstLineasProductosConImagen[0].ruta_imagen" alt="BannerLinea">
                        </div>
                    </a>
                </div>
                <div class="col-lg-9 pb-5" v-if="lineaSeleccionada !== null && iLineaProductoId != 0">
                    <div v-html="locale === 'es' ? lineaSeleccionada.contenido_espanol : lineaSeleccionada.contenido_ingles"></div>
                </div>
            </div>
            
            <div class="row justify-content-center pt-5 pt-md-0 pb-5" v-if="iLineaProductoId == 0">
                <div class="col-12">
                    <div id="carouselLineas" class="carousel slide pb-5" data-ride="carousel" v-cloak>
                        <ol class="carousel-indicators" v-if="lstCarouselLineasProductosConImagen.length > 1">
                            <li data-target="#carouselLineas" v-for="(lstLineas, i) in lstCarouselLineasProductosConImagen" :data-slide-to="i" class="bg-ecovalle" :class="{ active: i === 0 }"></li>
                        </ol>
                        <div class="carousel-inner pb-2">
                            <div v-for="(lstLineas, i) in lstCarouselLineasProductosConImagen" :class="{ active: i === 0 }" class="carousel-item">
                                <div class="row justify-content-center">
                                    <div class="col-md-4 pb-5" v-for="linea in lstLineas">
                                        <a :href="'/nosotros/lineas-productos?linea=' + linea.id">
                                            <div class="w-100 shadow-lg img-background-thumbnail-lg">
                                                <img class="img-fluid-linea" style="height: 100% !important;" :src="linea.ruta_imagen" alt="Bnner">
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev px-3 w-auto" href="#carouselLineas" role="button" data-slide="prev" v-if="lstCarouselLineasProductosConImagen.length > 1">
                            <span class="fas fa-chevron-left fa-2x text-ecovalle" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next px-3 w-auto" href="#carouselLineas" role="button" data-slide="next" id="next-carousel" v-if="lstCarouselLineasProductosConImagen.length > 1">
                            <span class="fas fa-chevron-right fa-2x text-ecovalle" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center pb-5" v-else>
                <div class="col-12 text-center" v-if="iCargandoProductosRelacionados === 1">
                    <img class="my-5" src="/img/spinner.svg">
                </div>
                <div class="col-12" v-if="iCargandoProductosRelacionados === 0 && lstCarouselProductos.length > 0">
                    <h2 class="titulo-subrayado text-center mb-4">{{ $lstLocales['related_products'] }}</h2>
                    <div id="carouselProductos" class="carousel slide pb-5" data-ride="carousel" v-if="iCargando === 0" v-cloak>
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
                                                    <div class="text-center">
                                                        @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }} Dscto.
                                                    </div>
                                                </div>
                                                <div class="div-promocion position-absolute" v-if="producto.promocion_vigente">
                                                    <div class="text-center">
                                                        @{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} Dscto.
                                                    </div>
                                                </div>
                                                <span class="badge badge-warning badge-nuevo position-absolute px-2 py-1 text-white"
                                                      v-if="(new Date().getTime() - new Date(producto.fecha_reg).getTime()) / (1000 * 3600 * 24) <= 90">
                                                    @{{ locale === 'es' ? 'NUEVO' : 'NEW' }}
                                                </span>
                                                <span class="badge badge-danger badge-ultimos position-absolute px-2 py-1 text-white"
                                                      v-if="producto.stock_actual - producto.stock_separado <= 10 && producto.stock_actual - producto.stock_separado > 1">
                                                    @{{ locale === 'es' ? `ULTIMOS ${producto.stock_actual - producto.stock_separado}` : `LAST ${producto.stock_actual - producto.stock_separado}` }}
                                                </span>
                                                <span class="badge badge-danger badge-ultimos position-absolute px-2 py-1 text-white"
                                                      v-if="producto.stock_actual - producto.stock_separado === 1">
                                                    @{{ locale === 'es' ? `ULTIMO` : `LAST ONE` }}
                                                </span>
                                                <div class="overflow-hidden">
                                                    <a class="img-producto" :href="'/tienda/producto/' + producto.id" v-if="producto.imagenes.length > 0">
                                                        <div class="img-background-thumbnail producto" :style="{ 'background-image': 'url(' + producto.imagenes[0].ruta + ')' }"></div>
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
                                                            <span class="text-amarillo-ecovalle font-weight-bold d-inline mr-2" v-if="producto.oferta_vigente && producto.promocion_vigente == null">
                                                                S/ @{{ (Math.round((producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto)) * 10) / 10).toFixed(2) }}
                                                            </span>
                                                            <span class="text-amarillo-ecovalle font-weight-bold d-inline" v-if="producto.oferta_vigente == null && producto.promocion_vigente == null">
                                                                S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                            </span>
                                                            <span class="text-muted font-weight-bold d-inline" v-if="producto.oferta_vigente && producto.promocion_vigente == null" style="text-decoration:line-through;">
                                                                S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                            </span>
                                                            <span class="text-amarillo-ecovalle font-weight-bold d-inline mr-2" v-if="producto.oferta_vigente == null && producto.promocion_vigente ">
                                                                <span class="d-inline" v-if="producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max">
                                                                 S/ @{{ (Math.round((producto.promocion_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.promocion_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.promocion_vigente.monto)) * 10) / 10).toFixed(2) }}
                                                                </span>
                                                            </span>
                                                            <span class="text-muted font-weight-bold d-inline"  v-if="producto.oferta_vigente == null && producto.promocion_vigente" style="text-decoration:line-through;">
                                                                <span class="d-inline" v-if="producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max">
                                                                    S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                                </span>
                                                            </span>
                                            
                                                            <span class="text-amarillo-ecovalle font-weight-bold"  v-if="producto.oferta_vigente == null && producto.promocion_vigente">
                                                                <span class="d-inline" v-if="producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max">
                                                                </span>
                                                                <span class="d-inline" v-else>
                                                                    S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                                </span>
                                                            </span>
                                                            <!--<span class="text-amarillo-ecovalle font-weight-bold" v-if="producto.oferta_vigente">
                                                                S/ @{{ (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto)).toFixed(2) }}
                                                            </span>
                                                            <span class="text-amarillo-ecovalle font-weight-bold" v-else>
                                                                S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                                                            </span>-->
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
                                                        <input type="text" class="form-control text-center" :value="producto.cantidad" disabled>
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
                        <a class="carousel-control-prev px-3 w-auto" href="#carouselProductos" role="button" data-slide="prev" v-if="lstCarouselProductos.length > 1">
                            <span class="fas fa-chevron-left fa-2x text-ecovalle" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next px-3 w-auto" href="#carouselProductos" role="button" data-slide="next" v-if="lstCarouselProductos.length > 1">
                            <span class="fas fa-chevron-right fa-2x text-ecovalle" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/lineasProductos.js?cvcn=14"></script>
@endsection
