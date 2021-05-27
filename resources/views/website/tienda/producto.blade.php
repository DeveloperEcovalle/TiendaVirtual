@extends('website.layout')

@section('title', 'Tienda')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/tienda">{{ $lstLocales['Store'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page" v-cloak>@{{ locale === 'en' ? producto.nombre_en : producto.nombre_es }}</li>
            </ol>
        </nav>
    </div>

    <section class="pt-md-3 pb-5">
        <div class="container-xl">
            <div class="row mb-5" v-if="iCargandoProducto === 1">
                <div class="col-12 text-center">
                    <img src="/img/spinner.svg">
                </div>
            </div>
            <div class="row mb-5" v-else v-cloak>
                <div class="col-12 col-md-1">
                    <div class="row mx-0">
                        <div class="col-2 col-md-12 px-1" v-for="(imagen, i) in producto.imagenes">
                            <a class="overflow-hidden" href="#" v-on:click.prevent="iImagenSeleccionada = i; sRutaImagenSeleccionada = imagen.ruta">
                                <img class="mb-0 mb-md-2 mr-2 mr-md-0 img-fluid img-thumbnail" :src="imagen.ruta"
                                     :class="{ 'border-success': iImagenSeleccionada === i, 'gray-scale': producto.stock_actual - producto.stock_separado === 0 }">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <span class="badge badge-danger badge-oferta position-absolute px-2 py-1" v-if="producto.oferta_vigente">
                        - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }} DSCTO.
                    </span>
                    <span class="badge badge-danger badge-promocion position-absolute px-2 py-1" v-if="producto.promocion_vigente">
                        +@{{ producto.promocion_vigente.min }}__@{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO.__-@{{ producto.promocion_vigente.max }}
                    </span>
                    <span class="badge badge-warning badge-nuevo position-absolute px-2 py-1 text-white"
                          v-if="(new Date().getTime() - new Date(producto.fecha_reg).getTime()) / (1000 * 3600 * 24) <= 30">
                        @{{ locale === 'es' ? 'NUEVO' : 'NEW' }}
                    </span>
                    <div class="modal-container" id="modal-container">
                        <div class="contenido">
                            <a href="#" onclick="fnExplota()" class="controlaButton">
                                <img class="img-fluid" :class="{ 'gray-scale': producto.stock_actual - producto.stock_separado === 0 }" :src="sRutaImagenSeleccionada">
                            </a>
                            <button type="button" class="btn-round position-absolute px-2 py-1 d-none" onclick="fnReduce()"><i class="fa fa-close"></i></button>
                        </div>
                    </div>
                    <div class="pt-3 pb-4 text-center">
                        <a v-for="documento in producto.documentos" class="btn btn-amarillo" :href="documento.ruta_archivo" target="_blank">
                            <i class="fas fa-download"></i> @{{ documento.nombre_descarga }}
                        </a>
                    </div>
                </div>
                <div class="col-md-7">
                    <h1 class="h2 text-ecovalle-2">@{{ locale === 'en' ? producto.nombre_en : producto.nombre_es }}</h1>
                    <div class="pb-3">
                        <div class="starrr" v-star-rating="{ readOnly: true, rating: producto.cantidad_calificaciones === 0 ? 0 : (producto.sumatoria_calificaciones / producto.cantidad_calificaciones) }"></div>
                        <p class="text-muted m-0 small">(@{{ producto.cantidad_calificaciones + (producto.cantidad_calificaciones === 1 ? ' calificación' : ' calificaciones') }})</p>
                    </div>
                    <h2 class="h3 text-amarillo-ecovalle font-weight-bold" v-if="producto.oferta_vigente">
                        S/ @{{ (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto)).toFixed(2) }}
                    </h2>
                    <h2 class="h3 text-amarillo-ecovalle font-weight-bold" v-else>
                        S/ @{{ producto.precio_actual.monto.toFixed(2) }}
                    </h2>
                    <p v-html="producto.beneficios_es" v-if="locale === 'es'"></p>
                    <p v-html="producto.beneficios_en" v-else></p>
                    <hr>
                    <div class="row justify-content-center justify-content-md-start pb-3">
                        <div class="col-6 col-sm-6 col-md-4 col-lg-4">
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
                                    <input type="text" class="form-control text-center" :value="producto.cantidad" v-on:keyup="changeCantidad(producto)" :placeholder="producto.cantidad" id="cantidad">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-ecovalle" :disabled="producto.cantidad >= producto.stock_actual" v-on:click="ajaxAumentarCantidadProductoCarrito(producto)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </span>
                                </div>
                                <button class="btn btn-sm btn-block btn-ecovalle py-2" v-on:click="ajaxAgregarAlCarrito(producto)" :disabled="iAgregandoAlCarrito === 1 && iProductoId === producto.id" v-else>
                                    <span class="small" v-if="iAgregandoAlCarrito === 1 && iProductoId === producto.id"><i class="fas fa-circle-notch fa-spin"></i></span>
                                    <span v-else><i class="fas fa-shopping-cart"></i>&nbsp;{{ $lstLocalesTiendaListaProductos['Add to cart'] }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3 col-md-4 col-lg-4">
                            <div class="badg  badge-danger badge-promocion-div text-center align-items-center" v-if="producto.promocion_vigente">
                                <b>+@{{ producto.promocion_vigente.min }}__ @{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO. __-@{{ producto.promocion_vigente.max }}</b>
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-4 col-lg-4">
                            <a href="/carrito-compras" class="d-inline-block mt-2"><i class="fas fa-shopping-cart"></i> Ver carrito de compras</a>
                        </div>
                    </div>
                    <div>
                        <p>{{ $lstLocalesTiendaListaProductos['Categories'] }}:&nbsp;
                            <span v-for="(categoria, i) in producto.categorias">
                                @{{ (locale === 'en' ? categoria.nombre_en : categoria.nombre_es) + (i === producto.categorias.length - 1 ? '' : ', ') }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#descripcion" role="tab" aria-controls="home" aria-selected="true">
                                {{ $lstLocalesTiendaListaProductos['description'] }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#modo-uso" role="tab" aria-controls="profile" aria-selected="false">
                                {{ $lstLocalesTiendaListaProductos['how_to_use'] }}
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show p-3 active" id="descripcion" role="tabpanel" aria-labelledby="home-tab"
                             v-html="locale === 'en' ? producto.descripcion_en : producto.descripcion_es">
                        </div>
                        <div class="tab-pane fade p-3" id="modo-uso" role="tabpanel" aria-labelledby="profile-tab"
                             v-html="locale === 'en' ? producto.modo_uso_en : producto.modo_uso_es">
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row mt-5" v-cloak>
                <div class="col-12" v-if="lstProductosRelacionados.length > 0">
                    <h2 class="h4 font-weight-bold text-center titulo-subrayado mb-4">{{ $lstLocalesTiendaListaProductos['Related Products'] }}</h2>
                </div>
                <div class="col-12 text-center" v-if="iCargandoProductosRelacionados === 1">
                    <img src="/img/spinner.svg">
                </div>
                <div class="col-12" v-else>
                    <div id="carouselProductos" class="carousel slide pb-5" data-ride="carousel" v-cloak>
                        <ol class="carousel-indicators" v-if="lstCarouselProductos.length > 1">
                            <li data-target="#carouselProductos" v-for="(lstProductos, i) in lstCarouselProductos" :data-slide-to="i" class="bg-ecovalle" :class="{ active: i === 0 }"></li>
                        </ol>
                        <div class="carousel-inner pb-2">
                            <div v-for="(lstProductos, i) in lstCarouselProductos" :class="{ active: i === 0 }" class="carousel-item">
                                <div class="row justify-content-center">
                                    <div class="col-11 col-md-3" v-for="(producto, i) in lstProductos">
                                        <div class="card my-2 shadow-lg">
                                            <div class="card-header p-0 bg-transparent" style="height: 180px">
                                                <span class="badge badge-success badge-oferta position-absolute px-2 py-1" v-if="producto.oferta_vigente">
                                                    - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }} DSCTO.
                                                </span>
                                                <span class="badge badge-danger badge-promocion position-absolute px-2 py-1" v-if="producto.promocion_vigente">
                                                    +@{{ producto.promocion_vigente.min }}__@{{ producto.promocion_vigente.porcentaje ? (producto.promocion_vigente.porcentaje + '%') : ('S/ ' + producto.promocion_vigente.monto) }} DSCTO.__-@{{ producto.promocion_vigente.max }}
                                                </span>
                                                <span class="badge badge-warning badge-nuevo position-absolute px-2 py-1 text-white"
                                                      v-if="(new Date().getTime() - new Date(producto.fecha_reg).getTime()) / (1000 * 3600 * 24) <= 30">
                                                    @{{ locale === 'es' ? 'NUEVO' : 'NEW' }}
                                                </span>
                                                <span class="badge badge-danger badge-ultimos position-absolute px-2 py-1 text-white"
                                                      v-if="producto.stock_actual - producto.stock_separado <= 10 && producto.stock_actual - producto.stock_separado > 1">
                                                    @{{ locale === 'es' ? `ÚLTIMOS ${producto.stock_actual - producto.stock_separado}` : `LAST ${producto.stock_actual - producto.stock_separado}` }}
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
                                                                <i class="fas" :class="{ 'fa-minus' : producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                                                            </button>
                                                        </span>
                                                        <input type="text" class="form-control text-center" :value="producto.cantidad" readonly>
                                                        <span class="input-group-append">
                                                            <button type="button" class="btn btn-ecovalle" :disabled="producto.cantidad >= producto.stock_actual" v-on:click="ajaxAumentarCantidadProductoCarrito(producto)">
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
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="/js/website/tiendaProducto.js?cvcn=14"></script>
@endsection
