@extends('website.layout')

@section('title', 'Pago y Envío')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Shopping cart'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pago y env&iacute;o</li>
            </ol>
        </nav>
    </div>

    <div class="modal-pago active" v-if="iPagando === 1">
        <img src="/img/carrito.gif" alt="Shopping">
        <b><span>Por favor, espere ...</span></b>
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

    <section class="pt-4 pb-5" v-if="lstCarritoCompras.length > 0 && iCargando === 0 && iPagado === 0" v-cloak>
        <div class="container-xl">
            <div class="row pb-5">
                <div class="col-lg-4">
                    <a href="/carrito-compras" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3">
                        1. {{ $lstLocales['Shopping cart'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="/facturacion-envio" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3">
                        2. {{ $lstTraduccionesPagoEnvio['billing_and_delivery'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="#" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3">
                        3. {{ $lstTraduccionesPagoEnvio['payment'] }}
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <div class="col-12 mb-3" v-if="sNNacional === 0">
                                <div class="p-4 bg-white">
                                    <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de env&iacute;o</h1>
                                    <div class="row">
                                        <div v-if="datosEnvio.sNombres != ''" class="col-12">
                                            <p class="font-weight-bold mb-0"><b>Nombres y Apellidos: </b>@{{ datosEnvio.sNombres + ' ' + datosEnvio.sApellidos + ' ' }}</p>
                                        </div>
                                        <div v-else class="col-12">
                                            <p class="font-weight-bold mb-0"><b>Raz&oacute;n Social: </b>@{{ datosEnvio.sRazon}}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-0"><b>Direcci&oacute;n: </b>@{{ datosEnvio.sDireccion + ', ' + datosEnvio.sDistrito + ', ' + datosEnvio.sProvincia + ', ' + datosEnvio.sDepartamento }}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0" v-if="datosEnvio.sTipoDoc == 'DNI'"><b>DNI: </b>@{{ datosEnvio.sDocumento }}</p>
                                            <p class="mb-0" v-if="datosEnvio.sTipoDoc == 'RUC'"><b>RUC: </b>@{{ datosEnvio.sDocumento }}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0"><b>Tel&eacute;fono: </b>@{{ datosEnvio.sTelefono }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-0"><b>Email: </b>@{{ datosEnvio.sEmail }}</p>
                                        </div>
                                        <div class="col-12">
                                            <div style="border: solid 1px #EE9722;border-radius:5px;padding: 4px;">
                                                <div class="row">
                                                    <div class="col-12 col-lg-8">
                                                        <p class="mb-0"><b>¿Quién recoge?: </b>@{{ datosEnvio.sRecoge.sRazonSocial }}</p>
                                                    </div>
                                                    <div class="col-12 col-lg-4">
                                                        <p class="mb-0"><b>DNI: </b>@{{ datosEnvio.sRecoge.sDocumento }}</p>
                                                    </div>
                                                    <div class="col-12">
                                                        <p class="mb-0"><b>Tel&eacute;fono: </b>@{{ datosEnvio.sRecoge.sTelefono }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <p><b>Agencia: </b>@{{ datosEnvio.sAgencia }}</p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button v-on:click.prevent="mostrarModalPago()" class="btn btn-amarillo">Pagar S/ @{{ fTotal.toFixed(2) }} <span>PEN</span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3" v-if="sRTienda === 0">
                                <div class="p-4 bg-white">
                                    <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de recojo en tienda</h1>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="font-weight-bold mb-0"><b>Nombres y Apellidos: </b>@{{ datosRecojo.sNombres + ' ' + datosRecojo.sApellidos}}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0"><b>Tel&eacute;fono: </b>@{{ datosRecojo.sTelefono }}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0"><b>DNI: </b>@{{ datosRecojo.sDocumento }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><b>Email: </b>@{{ datosRecojo.sEmail }}</p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button v-on:click.prevent="mostrarModalPago()" class="btn btn-amarillo">Pagar S/ @{{ fTotal.toFixed(2) }} <span>PEN</span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3" v-if="sDelivery === 0">
                                <div class="p-4 bg-white">
                                    <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n delivery Trujillo</h1>
                                    <div class="row">
                                        <div class="col-12">
                                            <p class="font-weight-bold mb-0"><b>Nombres y Apellidos: </b>@{{ datosDelivery.sNombres + ' ' + datosDelivery.sApellidos}}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-0"><b>Direcci&oacute;n:</b> @{{ datosDelivery.sDireccion }} - @{{ datosDelivery.sDepartamento }} / @{{ datosDelivery.sProvincia }} / @{{ datosDelivery.sDistrito }}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0"><b>Tel&eacute;fono:</b> @{{ datosDelivery.sTelefono }}</p>
                                        </div>
                                        <div class="col-12 col-lg-4">
                                            <p class="mb-0"><b>DNI: </b>@{{ datosDelivery.sDocumento }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p><b>Email:</b> @{{ datosDelivery.sEmail }}</p>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button v-on:click.prevent="mostrarModalPago()" class="btn btn-amarillo">Pagar S/ @{{ fTotal.toFixed(2) }} <span>PEN</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="px-3 pt-3 bg-white">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesPagoEnvio['order_summary'] }}</h1>
                            </div>
                        </div>
                        <div class="col-12" v-for="(detalle, i) in lstCarritoCompras">
                            <div class="p-3 bg-white">
                                <div class="row">
                                    <div class="col-3">
                                        <img class="img-fluid mb-3 mb-md-0" v-if="detalle.producto.imagenes.length > 0" :src="detalle.producto.imagenes[0].ruta">
                                    </div>
                                    <div class="col-5">
                                        <p class="font-weight-bold small mb-0">
                                            @{{ locale === 'es' ? detalle.producto.nombre_es : detalle.producto.nombre_en }}
                                        </p>
                                    </div>
                                    <div class="col-4">
                                        <p class="text-right font-weight-bold small mb-0">
                                            S/ @{{ (detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                            (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) :
                                            (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))).toFixed(2) }}</p>
                                        <p class="text-right font-weight-bold small mb-0">Cant: @{{ detalle.cantidad }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="px-3 pt-3 bg-white">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesPagoEnvio['price_summary'] }}</h1>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-white">
                                <p class="mb-0 text-muted">Subtotal <span class="float-right">S/ @{{ (fSubtotal + fDescuento).toFixed(2) }}</span></p>
                                <p class="mb-0 text-muted">Has ahorrado <span class="float-right">S/ @{{ fDescuento.toFixed(2) }}</span></p>
                                <p class="mb-3 text-muted">Cargos de env&iacute;o <span class="float-right">S/ @{{ fDelivery.toFixed(2) }}</span></p>
                                <p class="mb-0 font-weight-bold h5">Total <span class="float-right">S/ @{{ fTotal.toFixed(2) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-4 pb-5" v-if="iPagado === 1"> 
        <div class="container-xl">
            <div class="row">
                <div class="col-12 p-5 bg-white text-justify">
                    <p class="mb-0" style="font-size: 25px;">Su compra se ha realizado satisfactoriamente, se le enviará un correo electronico con el comprobante de pago que ha solicitado. ¡Muchas Gracias!</p>
                    <p class="font-weight-bold h5  text-ecovalle" style="font-size: 25px;">ECOVALLE</p>
                    <div class="text-center">
                        <a href="/tienda" class="btn btn-ecovalle">Continuar comprando</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://checkout.culqi.com/js/v3"></script>
    <script src="/js/website/pagoEnvio.js?n=1"></script>
@endsection
