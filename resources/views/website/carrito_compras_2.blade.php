@extends('website.layout')

@section('title', 'Carrito de Compras')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/index">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['Shopping cart'] }}</li>
            </ol>
        </nav>
    </div>

    <section class="pt-5 pb-5" v-if="iCargando === 1" v-cloak>
        <div class="container-xl">
            <div class="row">
                <div class="col-12 text-center">
                    <img src="/img/spinner.svg">
                </div>
            </div>
        </div>
    </section>

    <section class="pt-5 pb-5" v-if="lstCarritoCompras.length === 0 && iCargando === 0" v-cloak>
        <div class="container-xl py-5 my-5">
            <div class="row justify-content-center">
                <div class="col-5 col-sm-2 col-md-1">
                    <h1 class="text-center h4 text-ecovalle-2">
                        <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="shopping-cart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-shopping-cart fa-w-18 fa-2x">
                            <path fill="currentColor"
                                  d="M551.991 64H129.28l-8.329-44.423C118.822 8.226 108.911 0 97.362 0H12C5.373 0 0 5.373 0 12v8c0 6.627 5.373 12 12 12h78.72l69.927 372.946C150.305 416.314 144 431.42 144 448c0 35.346 28.654 64 64 64s64-28.654 64-64a63.681 63.681 0 0 0-8.583-32h145.167a63.681 63.681 0 0 0-8.583 32c0 35.346 28.654 64 64 64 35.346 0 64-28.654 64-64 0-17.993-7.435-34.24-19.388-45.868C506.022 391.891 496.76 384 485.328 384H189.28l-12-64h331.381c11.368 0 21.177-7.976 23.496-19.105l43.331-208C578.592 77.991 567.215 64 551.991 64zM240 448c0 17.645-14.355 32-32 32s-32-14.355-32-32 14.355-32 32-32 32 14.355 32 32zm224 32c-17.645 0-32-14.355-32-32s14.355-32 32-32 32 14.355 32 32-14.355 32-32 32zm38.156-192H171.28l-36-192h406.876l-40 192z"
                                  class=""></path>
                        </svg>
                    </h1>
                </div>
                <div class="col-12">
                    <h2 class="h5 text-center">Su carrito de compras est&aacute; vac&iacute;o</h2>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-5" v-if="lstCarritoCompras.length > 0 && iCargando === 0" v-cloak>
        <div class="container-xl">
            <div class="row pb-5">
                <div class="col-lg-4">
                    <button href="#" class="btn btn-block btn-ecovalle-2 active font-weight-bold mb-3">
                        1. {{ $lstLocales['Shopping cart'] }}
                    </button>
                </div>
                <div class="col-lg-4">
                    <button :disabled="!fVentaValida" v-on:click="ajaxLocalizarFacturacion" class="btn btn-block btn-outline-ecovalle font-weight-bold mb-3">
                        2. {{ $lstTraduccionesCarritoCompras['billing_and_delivery'] }}
                    </button>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-block btn-outline-ecovalle font-weight-bold mb-3" disabled>
                        3. {{ $lstTraduccionesCarritoCompras['payment'] }}
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h1 class="h5 mb-3">
                        <span class="font-weight-bold text-ecovalle-2">{{ $lstLocales['Shopping cart'] }}</span>
                        <button class="btn btn-sm btn-ecovalle float-right" v-on:click="ajaxActualizarCarritoCompras()" title="{{ $lstTraduccionesCarritoCompras['update_cart'] }}">
                            <i class="fas fa-sync" :class="{ 'fa-spin': iCargando === 1 }" :disabled="iCargando === 1"></i>
                        </button>
                    </h1>
                    <div class="row">
                        <div class="col-12 mb-3" v-for="(detalle, i) in lstCarritoCompras">
                            <div class="p-4 bg-white">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row justify-content-center">
                                            <div class="col-5 col-md-2">
                                                <img class="img-fluid mb-3 mb-md-0" v-if="detalle.producto.imagenes.length > 0" :src="detalle.producto.imagenes[0].ruta">
                                            </div>
                                            <div class="col-12 col-md-6 text-center text-md-left">
                                                <p class="font-weight-bold mb-1 mb-md-3">@{{ locale === 'es' ? detalle.producto.nombre_es : detalle.producto.nombre_en }}</p>
                                                <a href="#" :title="locale === 'es' ? 'Eliminar' : 'Delete'" class="small d-block mb-2 mb-md-2 text-danger" v-on:click.prevent="ajaxEliminarDelCarrito(detalle.producto, i)">@{{ locale === 'es' ? 'Eliminar' : 'Delete'}}</a>
                                                <h4 class="small text-amarillo-ecovalle font-weight-bold d-inline mr-2" v-if="detalle.producto.oferta_vigente && detalle.producto.promocion_vigente == null">
                                                    S/ @{{ (Math.round((detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)) * 10) / 10).toFixed(2) }}
                                                </h4>
                                                <h4 class="small text-amarillo-ecovalle font-weight-bold d-inline" v-if="detalle.producto.oferta_vigente == null && detalle.producto.promocion_vigente == null">
                                                    S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                                </h4>
                                                <h4 class="small text-muted font-weight-bold d-inline" v-if="detalle.producto.oferta_vigente && detalle.producto.promocion_vigente == null" style="text-decoration:line-through;">
                                                    S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                                </h4>
                                                <h4 class="small text-amarillo-ecovalle font-weight-bold d-inline mr-2" v-if="detalle.producto.oferta_vigente == null && detalle.producto.promocion_vigente ">
                                                    <p class="d-inline" v-if="detalle.producto.cantidad >= detalle.producto.promocion_vigente.min && detalle.producto.cantidad <= detalle.producto.promocion_vigente.max">
                                                     S/ @{{ (Math.round((detalle.producto.promocion_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.promocion_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.promocion_vigente.monto)) * 10) / 10).toFixed(2) }}
                                                    </p>
                                                 </h4>
                                                <h4 class="small text-muted font-weight-bold d-inline"  v-if="detalle.producto.oferta_vigente == null && detalle.producto.promocion_vigente" style="text-decoration:line-through;">
                                                    <p class="d-inline" v-if="detalle.producto.cantidad >= detalle.producto.promocion_vigente.min && detalle.producto.cantidad <= detalle.producto.promocion_vigente.max">
                                                        S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                                    </p>
                                                </h4>

                                                <h4 class="small text-amarillo-ecovalle font-weight-bold"  v-if="detalle.producto.oferta_vigente == null && detalle.producto.promocion_vigente">
                                                    <p class="d-inline" v-if="detalle.producto.cantidad >= detalle.producto.promocion_vigente.min && detalle.producto.cantidad <= detalle.producto.promocion_vigente.max">
                                                    </p>
                                                    <p class="d-inline" v-else>
                                                        S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                                    </p>
                                                </h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="row d-block d-md-flex">
                                                    <div class="col-12 col-md-12 float-right">
                                                        <div class="row" v-if="detalle.producto.promocion_vigente">
                                                            <div class="text-right p-1" :class="detalle.cantidad >= detalle.producto.promocion_vigente.min && detalle.cantidad <= detalle.producto.promocion_vigente.max ? 'col-6' : 'col-12'">
                                                                <p class="font-weight-bold mb-1 mt-3 mt-md-0">
                                                                    S/ @{{  (Math.round((detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                                                        (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) :
                                                                        (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))) * 10)/10).toFixed(2) }}</p>
                                                            </div>
                                                            <div class="col-6 text-right p-1" v-if="detalle.cantidad >= detalle.producto.promocion_vigente.min && detalle.cantidad <= detalle.producto.promocion_vigente.max">
                                                                <p class="font-weight-bold mb-1 mt-3 mt-md-0 text-danger">
                                                                    - S/ @{{ (Math.round((detalle.cantidad * (detalle.cantidad >= detalle.producto.promocion_vigente.min && detalle.cantidad <= detalle.producto.promocion_vigente.max ? (detalle.producto.promocion_vigente.porcentaje ? ((detalle.producto.precio_actual.monto * detalle.producto.promocion_vigente.porcentaje) / 100) : (detalle.producto.promocion_vigente.monto)) : 0.00)) * 10) / 10 ).toFixed(2) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="row" v-else>
                                                            <div class="col-12">
                                                                <p class="text-right font-weight-bold mb-1 mt-3 mt-md-0">
                                                                    S/ @{{ (Math.round((detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                                                        (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) :
                                                                        (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))) * 10)/10).toFixed(2) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-7 col-md-12">
                                                        <div class="input-group mt-1 float-right" style="max-width: 160px">
                                                            <span class="input-group-prepend">
                                                                <button type="button" class="btn btn-ecovalle" v-on:click="ajaxDisminuirCantidadProductoCarrito(detalle.producto, i)">
                                                                    <i class="fas" :class="{ 'fa-minus': detalle.producto.cantidad > 1, 'fa-trash-alt': detalle.producto.cantidad === 1 }"></i>
                                                                </button>
                                                            </span>
                                                            <input type="text" class="form-control text-center" :value="detalle.cantidad" v-on:keyup="changeCantidad(detalle.producto,i)" :placeholder="detalle.cantidad" :id="'cantidad'+i" onkeypress="return isNumber(event)">
                                                            <span class="input-group-append">
                                                                <button type="button" class="btn btn-ecovalle" :disabled="detalle.cantidad >= detalle.producto.stock_actual" v-on:click="ajaxAumentarCantidadProductoCarrito(detalle.producto, i)">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row bg-white">
                        <div class="col-12 text-center text-uppercase">
                            <h1 class="h5 mb-1 mt-4 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesCarritoCompras['order_summary'] }}</h1>
                            <div class="hr-compra"></div>
                        </div>
                        <div class="col-12">
                            <div class="row pr-4 pl-4 mt-2 align-items-end">
                                <div class="col-md-9 p-1">
                                    <label style="font-size: 12px;"><b>Aplicar el c&oacute;digo promocional</b></label>
                                    <input type="text" style="font-size: 12px;" class="form-control d-inline mb-2" placeholder="Un código promocional por pedido">
                                </div>
                                <div class="col-md-3 p-1">
                                    <button type="button" style="font-size: 12px;" class="btn btn-outline-dark btn-block d-inline mb-2">Aplicar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="pr-4 pl-4">
                                <p class="mb-0 text-muted">@{{ lstCarritoCompras.length }} detalles</p>
                                <p class="mb-3 text-muted">@{{ iArticulos }} art&iacute;culos</p>
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2">Sub total <span class="float-right">S/ @{{ (fSubtotal + fDescuento).toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold">Ahorraste <span class="float-right">S/ @{{ fDescuento.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2 h5">TOTAL <span class="float-right">S/ @{{ fSubtotal.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <div class="mb-0 mt-3 w-100">
                                    <p v-if="!fVentaValida" class="text-ecovalle-compra d-inline">¡No olvides!</p> <p v-if="!fVentaValida" class="text-muted-compra d-inline">Tu compra m&iacute;nima es desde S/. 50.00</p>
                                </div>
                                <button class="btn btn-block btn-amarillo-compra mt-3" :disabled="!fVentaValida" v-on:click="ajaxLocalizarFacturacion">Procesar compra</button>
                                <a href="/tienda" class="btn btn-block btn-ecovalle-compra mt-3">Seguir comprando</a>
                                <div class="mt-2 py-3 py-lg-0">
                                    <div class="row">
                                        <div class="col-md-6 text-right p-1">
                                            <img src="/img/pago_seguro.svg" class="img-atributo-ecovalle">
                                        </div>
                                        <div class="col-md-6 text-left p-1">
                                            <p class="m-0 font-weight-bold text-muted text-uppercase" style="font-size: 12px">Compra 100%</p>
                                            <p class="font-weight-bolder text-ecovalle-compra-3 m-0 text-uppercase h4">SEGURO</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-amarillo text-center p-2 mt-3">
                                    <p>
                                        <b>¿Desea al por mayor?, ¡¡Cont&aacute;ctanos!!</b>
                                    </p>
                                    <a href="/contactanos" class="btn btn-sm btn-ecovalle">Contactar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/carritoCompras2.js?n=1"></script>
@endsection
