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
                    <a href="/carrito-compras" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3 mb-md-0">
                        1. {{ $lstLocales['Shopping cart'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <a href="#" class="btn btn-block btn-ecovalle-2 font-weight-bold mb-3 mb-md-0">
                        2. {{ $lstTraduccionesFacturacionEnvio['billing_and_delivery'] }}
                    </a>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-block btn-outline-ecovalle font-weight-bold mb-3 mb-md-0" disabled>
                        3. {{ $lstTraduccionesFacturacionEnvio['payment'] }}
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <div style="background-color: #EE9722;color:#ffffff">
                                <div class="form-group row p-4 align-items-end">
                                    <div class="col-lg-4 col-12" > <!--style="background:url('/img/delivery_aux.png') no-repeat right; background-size: contain;"-->
                                        <div class="radio">
                                            <input type="radio" name="tipo_compra" id="sNNacional" v-on:click="sNNacionalFn()" checked>
                                            <label for="sNNacional" title="Envío a nivel nacional">
                                                <b>Envío a nivel nacional</b>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="radio">
                                            <input type="radio" name="tipo_compra" id="sRTienda" v-on:click="sRTiendaFn()">
                                            <label for="sRTienda" title="Recojo en tienda">
                                                <b>Recojo en tienda</b>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <div class="radio">
                                            <input type="radio" name="tipo_compra" id="sDelivery" v-on:click="sDeliveryFn()">
                                            <label for="sDelivery" title="Delivery Trujillo">
                                                <b>Delivery Trujillo</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-right" v-if="sNNacional === 0">
                            <a href="#" class="float-right" data-toggle="modal" data-target="#modalTarifasEnvio"><b>Ver tarifas de env&iacute;o nacional</b></a>
                        </div>
                        <div class="col-12 text-right" v-if="sDelivery === 0">
                            <a href="#" class="float-right" data-toggle="modal" data-target="#modalTarifasDelivery"><b>Ver tarifas delivery Trujillo</b></a>
                        </div>
                        <div class="col-12 mb-3" v-if="sNNacional === 0">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de la direcci&oacute;n</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4">
                                        <span class="font-weight-bold">Datos de env&iacute;o</span>
                                    </p>
                                    <div v-if="!bDireccionEnvioValida && (!bVerificaRuc || !bVerificaDni)" class="col-8 col-lg-6" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div v-else class="col-8 col-lg-6"></div>
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
                                        <p><b>Agencia: </b>@{{ datosEnvio.sAgencia }}</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    @if(session()->has('cliente'))
                                    <button class="btn btn-ecovalle" :disabled="!bDireccionEnvioValida && (!bVerificaRuc || !bVerificaDni) && iDireccionEnvioConfirmada === 1" v-on:click="confirmarFacturacion()" v-if="iDireccionEnvioConfirmada === 1">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3" v-if="sRTienda === 0">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n de recojo en tienda</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4">
                                        <span class="font-weight-bold">Datos de recojo</span>
                                    </p>
                                    <div v-if="!bRecojoValida" class="col-8 col-lg-6 items-center" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div v-else class="col-8 col-lg-6"></div>
                                    @if(session()->has('cliente'))
                                    <a class="col-4 col-lg-2 btn btn-primary float-right" href="#" data-toggle="modal" data-target="#modalEditarRecojo">Editar</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @else
                                    <a class="col-4 col-lg-2 btn btn-ecovalle-2 float-right" href="/registro">Registrarse</a> <!--v-if="iDireccionEnvioConfirmada === 0"-->
                                    @endif
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
                                    @if(session()->has('cliente'))
                                    <button class="btn btn-ecovalle" :disabled="!bRecojoValida && iRecojoConfirmado === 1" v-on:click="confirmarFacturacion()" v-if="iRecojoConfirmado === 1">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3" v-if="sDelivery === 0">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">Informaci&oacute;n delivery Trujillo</h1>
                                <div class="row">
                                    <p class="col-12 col-lg-4">
                                        <span class="font-weight-bold">Datos de delivery</span>
                                    </p>
                                    <div v-if="!bDeliveryValida" class="col-6 col-lg-6 items-center" style="background:url('/img/fle_r.gif') no-repeat right; background-size: contain;">
                                    </div>
                                    <div v-else class="col-8 col-lg-6"></div>
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
                                    @if(session()->has('cliente'))
                                    <button class="btn btn-ecovalle" :disabled="!bDeliveryValida && iDeliveryConfirmado === 1" v-on:click="confirmarFacturacion()" v-if="iDeliveryConfirmado === 1">Continuar</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3 d-none">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">1. Informaci&oacute;n de facturaci&oacute;n</h1>
                                <p class="font-weight-bold"></p>
                            </div>
                        </div>
                        <div class="col-12 mb-3 d-none">
                            <div class="p-4 bg-white">
                                <h1 class="h5 font-weight-bold text-ecovalle-2">2. Medio de pago</h1>
                                <p class="font-weight-bold">Seleccionar forma de pago</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="px-3 pt-3 bg-white">
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesFacturacionEnvio['order_summary'] }}</h1>
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
                                <h1 class="h6 mb-0 mt-4 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesFacturacionEnvio['price_summary'] }}</h1>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-white">
                                <p class="mb-0 text-muted">Subtotal <span class="float-right">S/ @{{ fSubtotal.toFixed(2) }}</span></p>
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
                                    <select name="tipo_documento" id="tipo_documento" class="form-control" v-on:change="cambiarTipoDoc()" v-model="datosEnvio.sTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input type="text" id="documento" name="documento" v-model="datosEnvio.sDocumento" class="form-control"  maxlength="8" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApi()"><i class="fa fa-search"></i> </button></span>
                                    </div>
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
                                <div class="form-group" v-if="datosEnvio.sTipoDoc === 'DNI'">
                                    <label>Nombres</label>
                                    <input class="form-control" v-model="datosEnvio.sNombres" autocomplete="off">
                                </div>
                                <div class="form-group"  v-if="datosEnvio.sTipoDoc === 'DNI'">
                                    <label>Apellidos</label>
                                    <input class="form-control" v-model="datosEnvio.sApellidos" autocomplete="off">
                                </div>
                                <div class="form-group"  v-if="datosEnvio.sTipoDoc === 'RUC'">
                                    <label>Razon Social</label>
                                    <input type="text" class="form-control" v-model="datosEnvio.sRazon" autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Agencia</label>
                                    <select  class="form-control" v-model="datosEnvio.sAgencia">
                                        <option value="">Seleccionar</option>
                                        <option v-for="agencia in lstAgencias" :value="agencia.nombre">@{{agencia.nombre}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tel&eacute;fono o celular</label>
                                    <input type="text" class="form-control" v-model="datosEnvio.sTelefono" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" v-model="datosEnvio.sEmail" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label>Direcci&oacute;n</label>
                                    <input type="text" class="form-control" v-model="datosEnvio.sDireccion" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <select class="form-control" v-model="datosEnvio.sDepartamento">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Provincia</label>
                                    <select class="form-control" v-model="datosEnvio.sProvincia">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Distrito</label>
                                    <select class="form-control" v-model="datosEnvio.sDistrito">
                                        <option value="" selected>Seleccionar</option>
                                        <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmDireccionEnvio" class="btn btn-ecovalle" :disabled="!bDireccionEnvioValida && (!bVerificaRuc || !bVerificaDni)">Guardar</button>
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
                                    <select name="rtipo_documento" id="rtipo_documento" class="form-control" v-on:change="rcambiarTipoDoc()" v-model="datosRecojo.rTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input type="text" id="rdocumento" name="rdocumento" v-model="datosRecojo.sDocumento" class="form-control"  maxlength="8" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApir()"><i class="fa fa-search"></i> </button></span>
                                    </div>
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
                                    <label>Nombres</label>
                                    <input type="text" class="form-control" v-model="datosRecojo.sNombres" autocomplete="off" required>
                                </div>
                                <div class="form-group" >
                                    <label>Apellidos</label>
                                    <input type="text" class="form-control" v-model="datosRecojo.sApellidos" autocomplete="off" required>
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" v-model="datosRecojo.sEmail" autocomplete="off" required>
                                </div>
                                <div class="form-group" >
                                    <label>Teléfono</label>
                                    <input type="text" class="form-control" v-model="datosRecojo.sTelefono" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmRecojo" class="btn btn-ecovalle" :disabled="!bRecojoValida">Guardar</button>
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
                                    <select name="dtipo_documento" id="dtipo_documento" class="form-control" v-on:change="dcambiarTipoDoc()" v-model="datosDelivery.dTipoDoc">
                                        <option value="">Seleccionar</option>
                                        <option value="DNI">DNI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Documento</label>
                                    <div class="input-group">
                                        <input type="text" id="ddocumento" name="ddocumento" v-model="datosDelivery.sDocumento" class="form-control"  maxlength="8" required>
                                        <span class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApid()"><i class="fa fa-search"></i> </button></span>
                                    </div>
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
                                    <label>Nombres</label>
                                    <input class="form-control" v-model="datosDelivery.sNombres" autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Apellidos</label>
                                    <input class="form-control" v-model="datosDelivery.sApellidos" autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Tel&eacute;fono</label>
                                    <input class="form-control" v-model="datosDelivery.sTelefono" autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Direci&oacute;n</label>
                                    <input class="form-control" v-model="datosDelivery.sDireccion" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departamento</label>
                                    <input class="form-control" v-model="datosDelivery.sDepartamento" readonly autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Provincia</label>
                                    <input class="form-control" v-model="datosDelivery.sProvincia" readonly autocomplete="off">
                                </div>
                                <div class="form-group" >
                                    <label>Distrito</label>
                                    <select name="ddistrito" id="ddistrito" class="form-control" v-model="datosDelivery.sDistrito">
                                        <option value="">Seleccionar</option>
                                        <option v-for="distrito in lstDistritosD" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" v-model="datosDelivery.sEmail" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="frmDelivery" class="btn btn-ecovalle" :disabled="!bDeliveryValida">Guardar</button>
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
    <script src="/js/website/facturacionEnvio.js?cvcn=14"></script>
@endsection
