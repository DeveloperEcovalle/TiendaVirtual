@extends('website.layout')

@section('title', 'Facturación y Envío')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Shopping cart'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstTraduccionesFacturacionEnvio['Billing and shipping'] }}</li>
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

    <section class="pt-4 pb-5" v-if="lstCarritoCompras.length > 0 && iCargando === 0" v-cloak>
        <div class="container-xl">
            <div class="row pb-5">
                <div class="col-lg-4">
                    <a href="/carrito-compras" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3">
                        1. {{ $lstLocales['Shopping cart'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="#" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3">
                        2. {{ $lstTraduccionesFacturacionEnvio['billing_and_delivery'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-block btn-outline-ecovalle font-weight-bold mb-3" disabled>
                        3. {{ $lstTraduccionesFacturacionEnvio['payment'] }}
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <div class="bg-amarillo-2">
                                <div class="form-group row p-4 align-items-end">
                                    <div class="col-lg-4 col-12" > 
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Envío a nivel nacional">
                                                <input type="radio" name="tipo_compra" value="E" v-model="sLocation">
                                                &nbsp;<b>Envío a nivel nacional</b>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Recojo en tienda">
                                                <input type="radio" name="tipo_compra" value="R" v-model="sLocation">
                                                &nbsp;<b>Recojo en tienda</b>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Delivery Trujillo">
                                                <input type="radio" name="tipo_compra" value="D" v-model="sLocation">
                                                &nbsp;<b>Delivery Trujillo</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-right" v-if="sLocation == 'E'">
                            <a href="#" class="btn btn btn-sm btn-info float-right mb-2" data-toggle="modal" data-target="#modalTarifasEnvio">Ver tarifas de env&iacute;o nacional</a>
                        </div>
                        <div class="col-12 text-right" v-if="sLocation == 'D'">
                            <a href="#" class="btn btn-sm btn-info float-right mb-2" data-toggle="modal" data-target="#modalTarifasDelivery"><b>Ver tarifas delivery Trujillo</b></a>
                        </div>

                        <div class="col-12 mb-3" v-if="sLocation == 'E'">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de la direcci&oacute;n</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4">
                                        <span class="font-weight-bold">Datos de env&iacute;o</span>
                                    </p>
                                    <div v-if="!bDireccionEnvioValida || !bVerificaRuc || !bVerificaDni" class="col-8 col-lg-6 d-none" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div class="col-8 col-lg-6"></div>
                                    @if(session()->has('cliente'))
                                    <a class="col-4 col-lg-2 btn btn-primary float-right" href="#" data-toggle="modal" data-target="#modalEditarDireccionEnvio">Editar</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @else
                                    <a class="col-4 col-lg-2 btn btn-ecovalle-2 float-right" href="/registro">Registrarse</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @endif
                                </div>
                                @if(session()->has('cliente'))
                                <div v-if="iDireccionEnvioEstablecida === 0">
                                    <p>Direcci&oacute;n de env&iacute;o no establecida. Click en 'Editar' para actualizar esta informaci&oacute;n.</p>
                                </div>
                                @else
                                <div v-if="iDireccionEnvioEstablecida === 0">
                                    <p>Direcci&oacute;n de env&iacute;o no establecida. Click en 'Registrarse'.</p>
                                </div>
                                @endif
                                <div class="row" v-else>
                                    <div v-if="datosEnvio.sTipoDoc === 'DNI'" class="col-12">
                                        <p class="font-weight-bold mb-0"><b>Nombres y Apellidos: </b>@{{ datosEnvio.sNombres + ' ' + datosEnvio.sApellidos + ' ' }}</p>
                                    </div>
                                    <div v-else class="col-12">
                                        <p class="font-weight-bold mb-0"><b>Raz&oacute;n Social: </b>@{{ datosEnvio.sNombres}}</p>
                                    </div>
                                    <div class="col-12">
                                        <p class="mb-0"><b>Direcci&oacute;n: </b>@{{ datosEnvio.sDireccion + ' - ' + datosEnvio.sDistrito + ' / ' + datosEnvio.sProvincia + ' / ' + datosEnvio.sDepartamento }}</p>
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
                                        <p v-if="datosEnvio.sAgencia != ''"><b>Agencia: </b>@{{ datosEnvio.sAgencia }}</p>
                                        <p v-else class="text-danger"><b>Agencia: </b><i class="fa fa-exclamation-circle"></i> <b>Seleccionar agencia </b></p>
                                    </div>
                                    <div class="col-12">
                                        <div class="bg-ecovalle">
                                            <div class="form-group row p-4 align-items-end">
                                                <div v-for="(tipo, i) in lstTiposComprobante" class="col-lg-6 col-12 m-0" >
                                                     <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0">
                                                            <input type="radio" name="sTipoComprobante" :value="tipo.tipo_comprobante_sunat.descripcion" v-model="datosEnvio.sTipoComprobante" :disabled="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosEnvio.sTipoDoc">
                                                            &nbsp;<b>@{{ tipo.tipo_comprobante_sunat.descripcion }}</b> <p v-if="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosEnvio.sTipoDoc" class="text-danger d-inline" style="font-size: 10;"><b>(Necesita @{{ tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura }})</b></p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!bComprobanteEnvio" class="col-12" style="background:url('/img/flecha-ultra.gif') no-repeat center; background-size: contain; height: 80px;">

                                    </div>
                                </div>
                                <div class="text-center">
                                    @if(session()->has('cliente'))
                                    <div v-icheck class="m-2">
                                        <label class="m-0">
                                            <input type="checkbox" value="1" name="eatermcond[]" v-model="eatermcond">&nbsp;Aceptar <a href="/terminos-condiciones">t&eacute;rminos y condiciones.</a>
                                        </label>
                                    </div>
                                    <button class="btn btn-ecovalle" :disabled="!bDireccionEnvioValida || bVerificaRuc != 1 || bVerificaDni != 1 || !bComprobanteEnvio" v-if="eatermcond.length > 0" v-on:click="confirmarFacturacion('E')">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3" v-if="sLocation == 'R'">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de recojo en tienda</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4 m-0">
                                        <span class="font-weight-bold">Datos de recojo</span>
                                    </p>
                                    <div v-if="!bRecojoValida" class="col-8 col-lg-6 items-center d-none" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div class="col-8 col-lg-6"></div>
                                    @if(session()->has('cliente'))
                                    <a class="col-4 col-lg-2 btn btn-primary float-right" href="#" data-toggle="modal" data-target="#modalEditarRecojo">Editar</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @else
                                    <a class="col-4 col-lg-2 btn btn-ecovalle-2 float-right" href="/registro">Registrarse</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @endif
                                    <p class="col-12 text-justify">
                                        <span class="font-weight-bold text-ecovalle-2 h5">Direcci&oacute;n de recojo en Av. Prolongación Vallejo Urb. Galeno 1 Mz. I Lote 2, Trujillo - Trujillo (Ref. a media cuadra del Real Plaza)</span>
                                    </p>
                                </div>
                                @if(session()->has('cliente'))
                                <div v-if="iRecojoEstablecido === 0">
                                    <p>Datos de recojo no establecidos. Click en 'Editar' para actualizar esta informaci&oacute;n.</p>
                                </div>
                                @else
                                <div v-if="iRecojoEstablecido === 0">
                                    <p>Datos de recojo no establecidos. Click en 'Registrarse'.</p>
                                </div>
                                @endif
                                <div class="row" v-else>
                                    <div v-if="datosRecojo.rTipoDoc === 'DNI'" class="col-12">
                                        <p class="font-weight-bold mb-0"><b>Nombres y Apellidos: </b>@{{ datosRecojo.sNombres + ' ' + datosRecojo.sApellidos + ' ' }}</p>
                                    </div>
                                    <div v-else class="col-12">
                                        <p class="font-weight-bold mb-0"><b>Raz&oacute;n Social: </b>@{{ datosRecojo.sNombres}}</p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <p class="mb-0"><b>Tel&eacute;fono: </b>@{{ datosRecojo.sTelefono }}</p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <p class="mb-0" v-if="datosRecojo.rTipoDoc == 'DNI'"><b>DNI: </b>@{{ datosRecojo.sDocumento }}</p>
                                        <p class="mb-0" v-else><b>RUC: </b>@{{ datosRecojo.sDocumento }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p><b>Email: </b>@{{ datosRecojo.sEmail }}</p>
                                    </div>
                                    <div class="col-12">
                                        <div class="bg-ecovalle">
                                            <div class="form-group row p-4 align-items-end">
                                                <div v-for="(tipo, i) in lstTiposComprobante" class="col-lg-6 col-12 m-0" > <!--style="background:url('/img/delivery_aux.png') no-repeat right; background-size: contain;"-->
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0">
                                                            <input type="radio" name="sTipoComprobante" :value="tipo.tipo_comprobante_sunat.descripcion" v-model="datosRecojo.sTipoComprobante" :disabled="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosRecojo.rTipoDoc">
                                                            &nbsp;<b>@{{ tipo.tipo_comprobante_sunat.descripcion }}</b> <p v-if="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosRecojo.rTipoDoc" class="text-danger d-inline" style="font-size: 10;"><b>(Necesita @{{ tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura }})</b></p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!bComprobanteRecojo" class="col-12" style="background:url('/img/flecha-ultra.gif') no-repeat center; background-size: contain; height: 80px;">

                                    </div>
                                </div>
                                <div class="text-center">
                                    @if(session()->has('cliente'))
                                    <div v-icheck class="m-2">
                                        <label class="m-0">
                                            <input type="checkbox" value="1" name="ratermcond[]" v-model="ratermcond">&nbsp;Aceptar <a href="/terminos-condiciones">t&eacute;rminos y condiciones.</a>
                                        </label>
                                    </div>
                                    <button class="btn btn-ecovalle" :disabled="!bRecojoValida || !bComprobanteRecojo || bVerificaDniRecojo != 1 || bVerificaRucRecojo != 1" v-if="ratermcond.length > 0" v-on:click="confirmarFacturacion('R')">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3" v-if="sLocation == 'D'">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n delivery Trujillo</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4">
                                        <span class="font-weight-bold">Datos de delivery</span>
                                    </p>
                                    <div v-if="!bDeliveryValida" class="col-6 col-lg-6 items-center d-none" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div class="col-8 col-lg-6"></div>
                                    @if(session()->has('cliente'))
                                    <a class="col-6 col-lg-2 btn btn-primary float-right" href="#" data-toggle="modal" data-target="#modalEditarDelivery">Editar</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @else
                                    <a class="col-6 col-lg-2 btn btn-ecovalle-2 float-right" href="/registro">Registrarse</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @endif
                                </div>
                                @if(session()->has('cliente'))
                                <div v-if="iDeliveryEstablecido === 0">
                                    <p>Datos de delivery no establecidos. Click en 'Editar' para actualizar esta informaci&oacute;n.</p>
                                </div>
                                @else
                                <div v-if="iDeliveryEstablecido === 0">
                                    <p>Datos de delivery no establecidos. Click en 'Registrarse'.</p>
                                </div>
                                @endif
                                <div class="row" v-else>
                                    <div v-if="datosDelivery.dTipoDoc === 'DNI'" class="col-12">
                                        <p class="font-weight-bold mb-0 d-inline" :class="{'text-danger' : datosDelivery.sNombres == '' || datosDelivery.sApellidos == ''}">Nombres y Apellidos:</p> <p v-if="datosDelivery.sNombres != ''" class="font-weight-bold mb-0 d-inline">@{{ datosDelivery.sNombres }}</p><p v-else class="font-weight-bold mb-0 d-inline text-danger"><i class="fa fa-exclamation-circle"></i> Completar nombres</p> <p v-if="datosDelivery.sApellidos != ''" class="font-weight-bold mb-0 d-inline">@{{ datosDelivery.sApellidos + ' ' }}</p><p v-else class="font-weight-bold mb-0 d-inline text-danger"><i class="fa fa-exclamation-circle"></i> Completar apellidos</p>
                                    </div>
                                    <div v-else class="col-12">
                                        <p class="font-weight-bold mb-0 d-inline" :class="{'text-danger' : datosDelivery.sNombres == ''}"><b>Raz&oacute;n Social: </b></p><p v-if="datosDelivery.sNombres != ''" class="font-weight-bold mb-0 d-inline">@{{ datosDelivery.sNombres}}</p><p v-else class="font-weight-bold text mb-0 d-inline"><i class="fa fa-exclamation-circle"></i> Completar nombres</p> <p v-if="datosDelivery.sApellidos != ''" class="font-weight-bold mb-0 d-inline">@{{ datosDelivery.sApellidos + ' ' }}</p><p v-else class="font-weight-bold mb-0 d-inline text-danger"><i class="fa fa-exclamation-circle"></i> Completar apellidos</p>
                                    </div>
                                    <div class="col-12">
                                        <p v-if="datosDelivery.sDistrito != ''" class="mb-0"><b>Direcci&oacute;n:</b> @{{ datosDelivery.sDireccion }} - @{{ datosDelivery.sDepartamento }} / @{{ datosDelivery.sProvincia }} / @{{ datosDelivery.sDistrito }}</p>
                                        <p v-else class="mb-0"><b>Direcci&oacute;n:</b> @{{ datosDelivery.sDireccion }} - @{{ datosDelivery.sDepartamento }} / @{{ datosDelivery.sProvincia }} / <b class="text-danger"><i class="fa fa-exclamation-circle"></i> Seleccionar distrito</b></p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <p class="mb-0"><b>Tel&eacute;fono:</b> @{{ datosDelivery.sTelefono }}</p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <p class="mb-0" v-if="datosDelivery.dTipoDoc == 'DNI'"><b>DNI: </b>@{{ datosDelivery.sDocumento }}</p>
                                        <p class="mb-0" v-else><b>RUC: </b>@{{ datosDelivery.sDocumento }}</p>
                                    </div>
                                    <div class="col-12">
                                        <p><b>Email:</b> @{{ datosDelivery.sEmail }}</p>
                                    </div>
                                    <div class="col-12">
                                        <div class="bg-ecovalle">
                                            <div class="form-group row p-4 align-items-end">
                                                <div v-for="(tipo, i) in lstTiposComprobante" class="col-lg-6 col-12 m-0" > <!--style="background:url('/img/delivery_aux.png') no-repeat right; background-size: contain;"-->
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0">
                                                            <input type="radio" name="sTipoComprobante" :value="tipo.tipo_comprobante_sunat.descripcion" v-model="datosDelivery.sTipoComprobante" :disabled="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosDelivery.dTipoDoc">
                                                            &nbsp;<b>@{{ tipo.tipo_comprobante_sunat.descripcion }}</b> <p v-if="tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura != datosDelivery.dTipoDoc" class="text-danger d-inline" style="font-size: 10;"><b>(Necesita @{{ tipo.tipo_comprobante_sunat.tipos_documento[0].abreviatura }})</b></p>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="!bComprobanteDelivery" class="col-12" style="background:url('/img/flecha-ultra.gif') no-repeat center; background-size: contain; height: 80px;">

                                    </div>
                                </div>
                                <div class="text-center">
                                    @if(session()->has('cliente'))
                                    <div v-icheck class="m-2">
                                        <label class="m-0">
                                            <input type="checkbox" value="1" name="datermcond[]" v-model="datermcond">&nbsp;Aceptar <a href="/terminos-condiciones">t&eacute;rminos y condiciones.</a>
                                        </label>
                                    </div>
                                    <button class="btn btn-ecovalle" :disabled="!bDeliveryValida || !bComprobanteDelivery || bVerificaDniDelivery != 1 || bVerificaRucDelivery != 1" v-if="datermcond.length > 0" v-on:click="confirmarFacturacion('D')">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row bg-white">
                        <div class="col-12">
                            <div class="px-3 pt-3 text-center">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesFacturacionEnvio['order_summary'] }}</h1>
                                <div class="hr-compra"></div>
                            </div>
                        </div>
                        <div class="col-12" v-for="(detalle, i) in lstCarritoCompras">
                            <div class="p-3">
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
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesFacturacionEnvio['price_summary'] }}</h1>
                                <div class="hr-compra"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3">
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

    <div class="modal fade" id="modalEditarDireccionEnvio" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-amarillo">
                    <h5 class="modal-title"><b>Editar direcci&oacute;n de env&iacute;o nivel nacional</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="frmDireccionEnvio" v-on:submit.prevent="confirmarDireccionEnvio">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo documento</label>
                                    <select name="tipo_documento" id="tipo_documento" class="form-control" :class="{'is-invalid' : datosEnvio.sTipoDoc == ''}" v-model="datosEnvio.sTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tipo de documento</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input v-if="datosEnvio.sTipoDoc === 'DNI'" type="text" name="documento" v-model="datosEnvio.sDocumento" class="form-control" :class="{'is-invalid' : datosEnvio.sDocumento == '' || bVerificaDniE != 1}"  maxlength="8" minlength="8" required>
                                        <input v-else type="text" name="documento" v-model="datosEnvio.sDocumento" class="form-control" :class="{'is-invalid' : datosEnvio.sDocumento == '' || bVerificaRuc != 1}"  maxlength="11" minlength="11" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApi()"><i class="fa fa-search"></i> </button></span>
                                    </div>
                                    <span :class="{'d-none' : datosEnvio.sDocumento != ''}">
                                        <strong class="small text-danger"><b>Completar documento</b></strong>
                                    </span>

                                    <span :class="{'d-none' : bVerificaDniE != 0}">
                                        <strong class="small text-danger"><b>Completar 8 caracteres</b></strong>
                                    </span>
                                    <span :class="{'d-none' : bVerificaRuc != 0}">
                                        <strong class="small text-danger"><b>Completar 11 caracteres</b></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <section class="pt-5 pb-5" v-if="iCargandoConsultaApi === 1" v-cloak>
                            <div class="container-xl">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="/img/spinner.svg">
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="row">
                            <div class="col-md-12" v-if="iCargandoConsultaApi === 0">
                                <div class="form-group">
                                    <label v-if="datosEnvio.sTipoDoc == 'DNI'">Nombres</label>
                                    <label v-else>Raz&oacute;n social</label>
                                    <input class="form-control" :class="{'is-invalid' : datosEnvio.sNombres == ''}" v-model="datosEnvio.sNombres" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar nombres</strong>
                                    </span>
                                </div>
                                <div class="form-group"  v-if="datosEnvio.sTipoDoc === 'DNI'">
                                    <label>Apellidos</label>
                                    <input class="form-control" :class="{'is-invalid' : datosEnvio.sApellidos == '' && datosEnvio.sTipoDoc == 'DNI'}" v-model="datosEnvio.sApellidos" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar apellidos</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Agencia</label>
                                    <select  class="form-control" :class="{'is-invalid' : datosEnvio.sAgencia == ''}" v-model="datosEnvio.sAgencia">
                                        <option value="">Seleccionar</option>
                                        <option v-for="agencia in lstAgencias" :value="agencia.nombre">@{{agencia.nombre}}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar agencia</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tel&eacute;fono o celular</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosEnvio.sTelefono == ''}" v-model="datosEnvio.sTelefono" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tel&eacute;fono</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" :class="{'is-invalid' : datosEnvio.sEmail == ''}" v-model="datosEnvio.sEmail" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar correo electr&oacute;nico</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Direcci&oacute;n</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosEnvio.sDireccion == ''}" v-model="datosEnvio.sDireccion" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar direcci&oacute;n</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <select class="form-control" :class="{'is-invalid' : datosEnvio.sDepartamento == ''}" v-model="datosEnvio.sDepartamento">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar departamento</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Provincia</label>
                                    <select class="form-control" :class="{'is-invalid' : datosEnvio.sProvincia == ''}" v-model="datosEnvio.sProvincia">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar provincia</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Distrito</label>
                                    <select class="form-control" :class="{'is-invalid' : datosEnvio.sDistrito == ''}" v-model="datosEnvio.sDistrito">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar distrito</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12" style="border: solid 1px #EE9722;border-radius:5px;">
                                <label><b>Datos de quien recoge el pedido</b></label>
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label>Documento (DNI)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" :class="{'is-invalid' : datosEnvio.sRecoge.sDocumento == ''}" v-model="datosEnvio.sRecoge.sDocumento" autocomplete="off">
                                                <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApiRecoge()"><i class="fa fa-search"></i> </button></span>
                                            </div>
                                            <span class="invalid-feedback">
                                                <strong class="text-danger">Campo obligatorio</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label>Tel&eacute;fono</label>
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosEnvio.sRecoge.sTelefono == ''}" v-model="datosEnvio.sRecoge.sTelefono" autocomplete="off">
                                            <span class="invalid-feedback">
                                                <strong class="text-danger">Campo obligatorio</strong>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12" v-if="iCargandoConsultaApiRecoge === 1">
                                        <section class="pt-5 pb-5" v-cloak>
                                            <div class="container-xl">
                                                <div class="row">
                                                    <div class="col-12 text-center">
                                                        <img src="/img/spinner.svg">
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                    <div class="col-12" v-else>
                                        <div class="form-group">
                                            <label>Nombres y Apellidos</label>
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosEnvio.sRecoge.sRazonSocial == ''}" v-model="datosEnvio.sRecoge.sRazonSocial" autocomplete="off">
                                            <span class="invalid-feedback">
                                                <strong class="text-danger">Campo obligatorio</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmDireccionEnvio" class="btn btn-ecovalle" :disabled="!bDireccionEnvioValida || bVerificaRuc != 1 || bVerificaDni != 1">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarRecojo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-amarillo">
                    <h5 class="modal-title"><b>Editar datos de recojo</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="frmRecojo" v-on:submit.prevent="confirmarRecojo">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo documento</label>
                                    <select name="rtipo_documento" id="rtipo_documento" class="form-control" :class="{'is-invalid' : datosRecojo.rTipoDoc == ''}" v-model="datosRecojo.rTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tipo de documento</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input v-if="datosRecojo.rTipoDoc === 'DNI'" type="text" name="documento" v-model="datosRecojo.sDocumento" class="form-control" :class="{'is-invalid' : datosRecojo.sDocumento == '' || bVerificaDniR != 1}"  maxlength="8" minlength="8" required>
                                        <input v-else type="text" name="documento" v-model="datosRecojo.sDocumento" class="form-control" :class="{'is-invalid' : datosRecojo.sDocumento == '' || bVerificaRucRecojo != 1}"  maxlength="11" minlength="11" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApir()"><i class="fa fa-search"></i> </button></span>
                                    </div>
                                    <span :class="{'d-none' : datosRecojo.sDocumento != ''}">
                                        <strong class="small text-danger"><b>Completar documento</b></strong>
                                    </span>

                                    <span :class="{'d-none' : bVerificaDniR != 0}">
                                        <strong class="small text-danger"><b>Completar 8 caracteres</b></strong>
                                    </span>
                                    <span :class="{'d-none' : bVerificaRucRecojo != 0}">
                                        <strong class="small text-danger"><b>Completar 11 caracteres</b></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <section class="pt-5 pb-5" v-if="iCargandoConsultaApir === 1" v-cloak>
                            <div class="container-xl">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="/img/spinner.svg">
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="row">
                            <div class="col-md-12" v-if="iCargandoConsultaApir === 0">
                                <div class="form-group">
                                    <label v-if="datosRecojo.rTipoDoc == 'DNI'">Nombres</label>
                                    <label v-else>Raz&oacute;n social</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sNombres == ''}" v-model="datosRecojo.sNombres" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar nombres</strong>
                                    </span>
                                </div>
                                <div class="form-group" v-if="datosRecojo.rTipoDoc == 'DNI'">
                                    <label>Apellidos</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sApellidos == '' && datosRecojo.rTipoDoc == 'DNI'}" v-model="datosRecojo.sApellidos" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar apellidos</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" :class="{'is-invalid' : datosRecojo.sEmail == ''}" v-model="datosRecojo.sEmail" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar correo electr&oacute;nico</strong>
                                    </span>
                                </div>
                                <div class="form-group" >
                                    <label>Teléfono</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sTelefono == ''}" v-model="datosRecojo.sTelefono" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tel&eacute;fono</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmRecojo" class="btn btn-ecovalle" :disabled="!bRecojoValida || bVerificaDniRecojo != 1 || bVerificaRucRecojo != 1">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarDelivery" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-amarillo">
                    <h5 class="modal-title"><b>Editar datos de delivery Trujillo</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <form id="frmDelivery" v-on:submit.prevent="confirmarDelivery">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo documento</label>
                                    <select name="dtipo_documento" id="dtipo_documento" :class="{'is-invalid' : datosDelivery.dTipoDoc == ''}" class="form-control" v-model="datosDelivery.dTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tipo de documento</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input v-if="datosDelivery.dTipoDoc === 'DNI'" type="text" name="documento" v-model="datosDelivery.sDocumento" class="form-control" :class="{'is-invalid' : datosDelivery.sDocumento == '' || bVerificaDni != 1}"  maxlength="8" minlength="8" required>
                                        <input v-else type="text" name="documento" v-model="datosDelivery.sDocumento" class="form-control" :class="{'is-invalid' : datosDelivery.sDocumento == '' || bVerificaRucDelivery != 1}"  maxlength="11" minlength="11" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApid()"><i class="fa fa-search"></i> </button></span>
                                    </div>
                                    <span :class="{'d-none' : datosDelivery.sDocumento != ''}">
                                        <strong class="small text-danger"><b>Completar documento</b></strong>
                                    </span>

                                    <span :class="{'d-none' : bVerificaDniD != 0}">
                                        <strong class="small text-danger"><b>Completar 8 caracteres</b></strong>
                                    </span>
                                    <span :class="{'d-none' : bVerificaRucDelivery != 0}">
                                        <strong class="small text-danger"><b>Completar 11 caracteres</b></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <section class="pt-5 pb-5" v-if="iCargandoConsultaApid === 1" v-cloak>
                            <div class="container-xl">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img src="/img/spinner.svg">
                                    </div>
                                </div>
                            </div>
                        </section>
                        <div class="row" v-if="iCargandoConsultaApid === 0">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label v-if="datosDelivery.dTipoDoc == 'DNI'">Nombres</label>
                                    <label v-else>Raz&oacute;n social</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sNombres == ''}" v-model="datosDelivery.sNombres" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar nombres</strong>
                                    </span>
                                </div>
                                <div class="form-group" v-if="datosDelivery.dTipoDoc == 'DNI'">
                                    <label>Apellidos</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sApellidos == ''}" v-model="datosDelivery.sApellidos" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar apellidos</strong>
                                    </span>
                                </div>
                                <div class="form-group" >
                                    <label>Tel&eacute;fono</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sTelefono == ''}" v-model="datosDelivery.sTelefono" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tel&eacute;fono</strong>
                                    </span>
                                </div>
                                <div class="form-group" >
                                    <label>Direci&oacute;n</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sDireccion == ''}" v-model="datosDelivery.sDireccion" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar direcci&oacute;n</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sDepartamento == ''}" v-model="datosDelivery.sDepartamento" readonly autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar departamento</strong>
                                    </span>
                                </div>
                                <div class="form-group" >
                                    <label>Provincia</label>
                                    <input class="form-control" :class="{'is-invalid' : datosDelivery.sProvincia == ''}" v-model="datosDelivery.sProvincia" readonly autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar provincia</strong>
                                    </span>
                                </div>
                                <div class="form-group" >
                                    <label>Distrito</label>
                                    <select name="ddistrito" id="ddistrito" :class="{'is-invalid' : datosDelivery.sDistrito == ''}" class="form-control" v-model="datosDelivery.sDistrito" required>
                                        <option value="">Seleccionar</option>
                                        <option v-for="distrito in lstDistritosD" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar distrito</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" :class="{'is-invalid' : datosDelivery.sEmail == ''}" v-model="datosDelivery.sEmail" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar correo electr&oacute;nico</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmDelivery" class="btn btn-ecovalle" :disabled="!bDeliveryValida || bVerificaDniDelivery != 1 || bVerificaRucDelivery != 1">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTarifasEnvio" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-amarillo">
                    <h5 class="modal-title"><b>Tarifas de env&iacute;o nivel nacional</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" v-cloak>
                    <div class="form-group">
                        <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por departamento, provincia ó distrito">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tblBlogs">
                            <thead>
                                <tr>
                                    <th class="bg-ecovalle-2">Departamento</th>
                                    <th class="bg-ecovalle-2">Provincia</th>
                                    <th class="bg-ecovalle-2">Distrito</th>
                                    <th class="bg-ecovalle-2">Tarifa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr v-for="ubigeo  of lstPreciosEnvioNacionalFiltrado" v-cloak>
                                    <td>@{{ ubigeo.departamento }}</td>
                                    <td>@{{ ubigeo.provincia }}</td>
                                    <td>@{{ ubigeo.distrito }}</td>
                                    <td>S/. @{{ ubigeo.tarifa.toFixed(2) }}</td>
                                </tr>
                                <tr v-if="lstPreciosEnvioNacional.length === 0" v-cloak>
                                    <td colspan="4" class="text-center">No hay datos para mostrar</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTarifasDelivery" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-amarillo">
                    <h5 class="modal-title"><b>Tarifas delivery Trujillo</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" v-cloak>
                    <div class="form-group">
                        <input type="text" v-model="sBuscard" class="form-control" placeholder="Buscar por distrito">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tblBlogs">
                            <thead>
                                <tr>
                                    <th class="bg-ecovalle-2">Departamento</th>
                                    <th class="bg-ecovalle-2">Provincia</th>
                                    <th class="bg-ecovalle-2">Distrito</th>
                                    <th class="bg-ecovalle-2">Tarifa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr v-for="ubigeo  of lstPreciosDeliveryFiltrado" v-cloak>
                                    <td>@{{ ubigeo.departamento }}</td>
                                    <td>@{{ ubigeo.provincia }}</td>
                                    <td>@{{ ubigeo.distrito }}</td>
                                    <td>S/. @{{ ubigeo.tarifa.toFixed(2) }}</td>
                                </tr>
                                <tr v-if="lstPreciosDelivery.length === 0" v-cloak>
                                    <td colspan="4" class="text-center">No hay datos para mostrar</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/website/facturacionEnvio.js?n=1"></script>
@endsection
