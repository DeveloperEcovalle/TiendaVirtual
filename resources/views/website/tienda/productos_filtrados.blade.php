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

    <section class="pt-md-3 pb-5">
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
                                <span class="badge badge-success badge-oferta position-absolute px-2 py-1" v-if="producto.oferta_vigente">
                                    - @{{ producto.oferta_vigente.porcentaje ? (producto.oferta_vigente.porcentaje + '%') : ('S/ ' + producto.oferta_vigente.monto) }}
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
                                    <button type="button" class="btn btn-ecovalle" v-on:click="ajaxDisminuirCantidadProductoCarrito(producto)">
                                        <i class="fas" :class="{ 'fa-minus': producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                                    </button>
                                </span>
                                    <input type="text" class="form-control text-center" :value="producto.cantidad" readonly>
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
@endsection

@section('js')
    <script type="text/javascript" src="/js/website/productosFiltrados.js?cvcn=14"></script>
@endsection
