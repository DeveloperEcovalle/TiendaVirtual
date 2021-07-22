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
        <img src="/img/cargando-carrito.gif" alt="Shopping">
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

    <section class="pt-4 pb-2" v-if="lstCarritoCompras.length > 0 && iCargando === 0 && iPagado === 0" v-cloak>
        <div class="container-xl">
            <div class="row">
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
                        <div class="col-12 mb-3 mt-4" v-if="sNNacional === 0">
                            <div class="row">
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">SELECCIONAR M&Eacute;TODO DE PAGO</p>
                                    <div class="hr-line"></div>
                                </div>
                                <div class="col-12 mb-4 mt-4">
                                    <div class="row pl-3 pr-3">
                                        <div class="col-12 border-left border-right border-top p-0">
                                            <div class="row pt-1 pl-2 pr-2  align-items-center">
                                                <div class="col-10">
                                                    <div class="d-inline-block i-checks pt-1" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0 text-ecovalle font-weight-bold" title="Pago en linea">
                                                            <input type="radio" name="tipo_pago" value="Pago-en-linea">
                                                            Pago en linea
                                                        </label>
                                                    </div>
                                                    <img src="/img/culqi.png" class="float-right" alt="Culqi" style="width: 80px;height: 40px;">
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#pagoEnLinea" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_1')"><i class="fa" :class="sCollapse_1 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse border-top m-0 p-4 bg-ecovalle-6" id="pagoEnLinea">
                                                <div class="row">
                                                    <div class="col-12 col-md-8 border-right">
                                                        <p>El pago se realizar&aacute; utilizado los servicios de CULQI, realizando el d&eacute;bito desde su cuenta corriente o de ahorros de manera segura</p>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="w-100">
                                                            <img src="/img/visa.png" class="float-left" alt="VISA" style="width: 45%; height: 40px;">
                                                            <img src="/img/mastercard.svg" class="float-right" alt="MasterCard" style="width: 45%; height: 40px;">
                                                        </div>
                                                        <div class="w-100">
                                                            <img src="/img/american_express.svg" class="float-left" alt="AmericanEx" style="width: 45%; height: 60px;">
                                                            <img src="/img/dinners_club.svg" class="float-right" alt="DinnersClub" style="width: 45%; height: 60px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 border-left border-right p-0 border-top">
                                            <div class="row pt-1 pl-2 pr-2 align-items-center">
                                                <div class="col-10">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0 text-ecovalle font-weight-bold" title="Dep&oacute;sito bancario">
                                                            <input type="radio" name="tipo_pago" value="Deposito-bancario">
                                                            Dep&oacute;sito bancario
                                                        </label>
                                                    </div>
                                                    <img src="/img/bbva.jpg" class="float-right" alt="BBVA" style="width: 80px;height: 40px;">
                                                    <img src="/img/bcp.png" class="float-right" alt="BCP" style="width: 80px;height: 40px;">
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#depositoBancario" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_2')"><i class="fa" :class="sCollapse_2 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse p-4 m-0 w-100 border-top bg-ecovalle-6" id="depositoBancario">
                                                <div class="row">
                                                    <div class="col-12 col-md-8 border-right">
                                                        <p>Realiza el pago de tu pedido mediante un dep&oacute;sito bancario.</p>
                                                        <p class="mb-0">Dep&oacute;sitos y/o transferencias</p>
                                                        <p class="mb-0 text-ecovalle">BCP | Ahorros soles: 570-2048517-0-64</p>
                                                        <p class="text-ecovalle">BBVA | Ahorros soles: 0011-0246-01-00025289</p>
                                                        <p class="text-ecovalle">Titular: Agroensancha S.R.L</p>
                                                        <p><b class="text-danger">*</b> Los dep&oacute;sitos desde provincia por ventanilla o agentes, deber&aacute; adicionar al monto total la suma de S/ 9 de comisi&oacute;n que cobran respectivamente, de lo contrario el monto recibido ser&aacute; menor y por ende el pedido o podr&aacute; ser procesado. Para evitar la comisi&oacute;n, puede optar por pago desde banca m&oacute;vil, Yape o Plin.</p>
                                                        <p><b class="text-danger">*</b> Tiempo maximo para realizarel pago es de 5 horas, luego de ese lapso de no haber realizado el respectivo pago, su pedido ser&aacute; anulado autom&aacute;ticamente.</p>
                                                        <p><b class="text-danger">*</b> !No olvidar! Adjuntar el comprobante de pago (foto o captura) en esta misma plataforma.</p>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 border-left border-right p-0 border-top border-bottom">
                                            <div class="row pt-1 pl-2 pr-2">
                                                <div class="col-10">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0 text-ecovalle font-weight-bold" title="Billetera virtual">
                                                            <input type="radio" name="tipo_pago" value="Billetera-virtual">
                                                            Billetera virtual
                                                        </label>
                                                    </div>
                                                    <img src="/img/plin.png" class="float-right" alt="Plin" style="width: 35px;height: 35px;">
                                                    <img src="/img/yape.png" class="float-right" alt="Yape" style="width: 80px;height: 40px;">
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#billeteraVirtual" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_3')"><i class="fa" :class="sCollapse_3 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse p-4 m-0 w-100 border-top bg-ecovalle-6" id="billeteraVirtual">
                                                <div class="row">
                                                    <div class="col-12 col-md-8 border-right">
                                                        <p>Realiza el pago de tu pedido desde la comodidad de una billetera virtual, puedes elegir entre Yaple o Plin.</p>
                                                        <p class="mb-0 text-ecovalle">Paga con Yape/Plin:</p>
                                                        <p class="text-ecovalle">957 819 664</p>
                                                        <p class="text-ecovalle">Titular: Agroensancha S.R.L</p>
                                                        <p><b class="text-danger">*</b> !No olvidar! Adjuntar el comprobante de pago (foto o captura) en esta misma plataforma.</p>
                                                    </div>
                                                    <div class="col-12 col-md-4">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                              
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
                                    <button v-on:click.prevent="mostrarModalPago()" class="btn btn-amarillo-compra">Pagar S/ @{{ fTotal.toFixed(2) }} <span>PEN</span></button>
                                </div>
                                <div class="mt-2 alert alert-danger text-center p-2" v-if="sMensajeError != ''">
                                    @{{ sMensajeError }}
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
                                    <button v-on:click.prevent="mostrarModalPago()" class="btn btn-amarillo-compra">Pagar S/ @{{ fTotal.toFixed(2) }} <span>PEN</span></button>
                                </div>
                                <div class="mt-2 alert alert-danger text-center p-2" v-if="sMensajeError != ''">
                                    @{{ sMensajeError }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="px-3 pt-4 text-center">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesPagoEnvio['order_summary'] }}</h1>
                                <div class="hr-compra"></div>
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
                            <div class="px-3 pt-3 text-center text-uppercase">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesPagoEnvio['price_summary'] }}</h1>
                                <div class="hr-compra"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3">
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2">Sub total <span class="float-right">S/ @{{ (fSubtotal + fDescuento).toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold">Ahorraste <span class="float-right">S/ @{{ fDescuento.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold">Cargos de env&iacute;o <span class="float-right">S/ @{{ fDelivery.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2 h5">Total <span class="float-right">S/ @{{ fTotal.toFixed(2) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="pt-4 pb-2" v-if="iPagado === 1"> 
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
