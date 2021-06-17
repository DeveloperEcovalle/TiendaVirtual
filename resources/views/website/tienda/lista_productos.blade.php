@extends('website.layout')

@section('title', 'Tienda')

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

    <div class="container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-2 px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['Store'] }}</li>
            </ol>
        </nav>
    </div>

    <section class="pb-5">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-3">
                    <form v-on:submit.prevent>
                        <div class="row">
                            <div class="col-12">
                                <div class="row mx-0 border rounded shadow">
                                    <div class="col-12 bg-amarillo text-white text-uppercase py-2">
                                        {{ $lstTraduccionesTiendaListaProductos['Categories'] }}
                                    </div>
                                    <div class="col-12 py-2 text-center border-bottom" v-if="iCargandoCategorias === 1"><img src="/img/spinner.svg"></div>
                                    <div class="col-12 py-2" v-else>
                                        <div class="row">
                                            <div class="col-12 col-sm-4 col-lg-12 px-3" v-for="(categoria, i) in lstCategorias" v-cloak>
                                                <div class="w-100 py-2">
                                                    <div class="d-inline-block" v-icheck>
                                                        <label class="m-0">
                                                            <input type="checkbox" name="lstCategoriasSeleccionadas[]" :value="categoria.id" v-model="lstCategoriasSeleccionadas">
                                                            &nbsp;@{{ locale === 'es' ? categoria.nombre_es : categoria.nombre_en }}
                                                        </label>
                                                    </div>
                                                    <span class="badge badge-success float-right mt-1">@{{ categoria.cantidad_productos }}</span>
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
                        </div>
                    </form>
                    <div class="py-5 py-lg-0">
                        <a :href="pagina.enlace_baner_publicitario" v-if="pagina.enlace_baner_publicitario">
                            <img class="img-fluid" :src="pagina.ruta_baner_publicitario">
                        </a>
                        <img  v-else class="img-fluid" :src="pagina.ruta_baner_publicitario">
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="row py-4 py-lg-0" v-if="iCargandoProductos === 1">
                        <div class="col-12 text-center">
                            <p><img src="/img/spinner.svg"></p>
                        </div>
                    </div>
                    <div class="row py-4 py-lg-0" v-else>
                        <div class="col-12" v-if="lstCategoriasSeleccionadas.length === 0">
                            <div class="row mx-0 pb-4">
                                <div class="col-12 py-5" style="background-image: url('/img/buscar_producto.jpg'); background-position: center; background-size: cover">
                                    <div class="row">
                                        <div class="col-12 col-md-4"></div>
                                        <div class="col-12 col-md-7">
                                            <h2 class="text-center text-white text-uppercase font-weight-bold">
                                                {{ $lstTraduccionesTiendaListaProductos['find_your_favorite_product_here'] }}
                                            </h2>
                                            <input class="form-control form-control-lg" placeholder="{{ $lstTraduccionesTiendaListaProductos['search_product'] }}" autocomplete="off" v-model="sBuscar"
                                                   v-autocomplete="{ url: '/tienda/ajax/buscarProducto', select: onSelectAutocompleteProducto, renderItem: renderProducto}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 pb-4">
                                    <a href="/tienda?pagina=0&orden=popular&categorias=1">
                                        <img class="img-fluid" src="/img/sistema_digestivo.jpg">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12" v-else>
                            <div class="row">
                                <div class="col-md-8 align-items-center d-flex">
                                    <p class="m-0" v-if="lstProductos.length > 0">{{ $lstTraduccionesTiendaListaProductos['showing'] }} @{{ iIndiceInicioMuestra + '-' + iIndiceFinMuestra }} {{ $lstTraduccionesTiendaListaProductos['of'] }} @{{ iTotalProductos }} resultados</p>
                                    <p class="m-0" v-else>{{ $lstTraduccionesTiendaListaProductos['no_results'] }}</p>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" v-model="sOrden">
                                        <option value="popular">{{ $lstTraduccionesTiendaListaProductos['most_popular'] }}</option>
                                        <option value="precio_asc">{{ $lstTraduccionesTiendaListaProductos['cheaper_first'] }}</option>
                                        <option value="precio_desc">{{ $lstTraduccionesTiendaListaProductos['more_expensive_first'] }}</option>
                                    </select>
                                </div>
                                <div class="col-12 py-3 text-center" v-if="lstProductos.length === 0">
                                    <p>{{ $lstTraduccionesTiendaListaProductos['no_products_to_show'] }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4" v-for="(producto, i) in lstProductos">
                                    <div class="card my-2 shadow">
                                        <div id="carouselImagenesProducto" class="carousel slide" data-ride="carousel" style="height: 180px">
                                            <div class="carousel-inner">
                                                <div class="position-absolute div-oferta" v-if="producto.oferta_vigente">
                                                    <div class="justify-content-between">
                                                        - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }} DSCTO.
                                                    </div>
                                                </div>
                                                <span class="badge badge-success badge-oferta position-absolute px-2 py-1 d-none" v-if="producto.oferta_vigente">
                                                    - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }}
                                                </span>
                                                <div class="div-promocion position-absolute" v-if="producto.promocion_vigente">
                                                    <div class="justify-content-between">
                                                        +@{{ producto.promocion_vigente.min }} hasta -@{{ producto.promocion_vigente.max }}
                                                        @{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO.
                                                    </div>
                                                </div>
                                                <span class="badge badge-danger badge-promocion position-absolute px-2 py-1 d-none" v-if="producto.promocion_vigente">
                                                    +@{{ producto.promocion_vigente.min }}__@{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO.__-@{{ producto.promocion_vigente.max }}
                                                </span>
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
                                                <div v-for="(imagen, i) in producto.imagenes" :class="{ active: i === 0 }" class="carousel-item">
                                                    <a class="img-producto" :href="'/tienda/producto/' + producto.id">
                                                        <div class="img-background-thumbnail producto" :class="{ 'gray-scale': producto.stock_actual - producto.stock_separado === 0 }"
                                                             :style="{ 'background-image': 'url(' + imagen.ruta + ')' }"></div>
                                                    </a>
                                                </div>
                                            </div>
                                            <a class="carousel-control-prev" href="#carouselImagenesProducto" role="button" data-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#carouselImagenesProducto" role="button" data-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="sr-only">Next</span>
                                            </a>
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
                                                    <button type="button" class="btn btn-ecovalle" v-on:click="ajaxDisminuirCantidadProductoCarritoA(producto, i)">
                                                        <i class="fas" :class="{ 'fa-minus': producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                                                    </button>
                                                </span>
                                                    <input type="text" class="form-control text-center" :value="producto.cantidad" :placeholder="producto.cantidad" v-on:keyup="changeCantidad(producto,i)" :id="'cantidad'+i" onkeypress="return isNumber(event)">
                                                <span class="input-group-append">
                                                    <button type="button" class="btn btn-ecovalle" :disabled="producto.cantidad >= producto.stock_actual" v-on:click="ajaxAumentarCantidadProductoCarritoA(producto, i)">
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
                            <div class="row">
                                <div class="col-12 pt-5 pb-3">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-center" v-cloak>
                                            <li class="page-item">
                                                <a class="page-link" href="#" v-on:click.prevent="navegarAnterior" aria-label="Previous" :disabled="iPaginaSeleccionada === 0">
                                                    <span aria-hidden="true">&laquo;</span>
                                                    <span class="sr-only">Previous</span>
                                                </a>
                                            </li>
                                            <li class="page-item" v-for="iPagina in lstPaginas" :class="{ active: iPagina === iPaginaSeleccionada }" :disabled="iPagina === -1">
                                                <a class="page-link" href="#" v-on:click.prevent="iPaginaSeleccionada = iPagina" v-if="iPagina !== -1">@{{ iPagina + 1 }}</a>
                                                <a class="page-link" href="#" v-on:click.prevent v-else>...</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="#" v-on:click.prevent="navegarSiguiente" aria-label="Next" :disabled="iPaginaSeleccionada + 1 === iTotalPaginas">
                                                    <span aria-hidden="true">&raquo;</span>
                                                    <span class="sr-only">Next</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" v-if="iCargandoProductos === 0 && lstCategoriasSeleccionadas.length === 0">
                <div class="col-md-6 py-4">
                    <a href="/tienda?pagina=0&orden=popular&categorias=13">
                        <img class="img-fluid" src="/img/belleza.jpg">
                    </a>
                </div>
                <div class="col-md-6 py-4">
                    <a href="/tienda?pagina=0&orden=popular&categorias=11">
                        <img class="img-fluid" src="/img/control_peso.jpg">
                    </a>
                </div>
                <div class="col-12 py-4">
                    <h2 class="font-weight-bold titulo-subrayado text-center mb-3">{{ $lstLocales['new_revenues'] }}</h2>
                    <div id="carouselProductos" class="carousel slide pb-5" data-ride="carousel">
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
                                                        <span class="small" v-if="iAgregandoAlCarrito === 1 && iProductoId === producto.id"><i class="fas fa-circle-notch fa-spin"></i></span>
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
                <div class="col-12 pb-4">
                    <img class="img-fluid" src="/img/delivery_ecovalle.jpg">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="/js/website/tiendaListaProductos.js?n=1"></script>
@endsection
