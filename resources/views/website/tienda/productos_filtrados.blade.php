@extends('website.layout')

@section('title', 'Tienda')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/tienda">{{ $lstLocales['Store'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['Search Result'] }}</a></li>
            </ol>
        </nav>
    </div>

    <section class="pt-md-3 pb-5" v-cloak>
        <div class="container-xl">
            <div class="row py-4 py-lg-0">
                <div class="col-12" v-cloak>
                    <input type="hidden" value="{{$sBuscar}}" id="sBuscar" class="form-input-p">
                    <p><b>Encontramos @{{lstProductos.length}} productos para </b><em>{{$sBuscar}}</em></p>
                </div>
            </div>
            <div class="row py-4 py-lg-0" v-if="iCargandoProductos === 1">
                <div class="col-12 text-center">
                    <p><img src="/img/spinner.svg"></p>
                </div>
            </div>
            <div class="row" v-else>
                <div class="col-md-4" v-for="(producto, i) in lstProductos">
                    <div class="card my-2 shadow">
                        <div id="carouselImagenesProducto" class="carousel slide" data-ride="carousel" style="height: 180px">
                            <div class="carousel-inner">
                                <div class="position-absolute div-oferta" v-if="producto.oferta_vigente">
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
                                    <input type="text" class="form-control text-center" :value="producto.cantidad" readonly onkeypress="return isNumber(event)">
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
    </section>

    <section class="pt-md-3 pb-5" v-cloak>
        <div class="container-xl">
            <div class="row py-4 py-lg-0">
                <div class="col-12" v-cloak>
                    <input type="hidden" value="{{$sBuscar}}" id="sBuscar" class="form-input-p">
                    <p><b>Encontramos @{{lstBlogs.length}} blogs para </b><em>{{$sBuscar}}</em></p>
                </div>
            </div>
            <div class="row py-4 py-lg-0" v-if="iCargandoProductos === 1">
                <div class="col-12 text-center">
                    <p><img src="/img/spinner.svg"></p>
                </div>
            </div>
            <div class="row pb-4 justify-content-center" v-else>
                <div class="col-md-4 col-sm-10 col-11 pb-5" v-for="blog in lstBlogs">
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
        </div>
    </section>
@endsection

@section('js')
    <script type="text/javascript" src="/js/website/productosFiltrados.js?n=1"></script>
@endsection
