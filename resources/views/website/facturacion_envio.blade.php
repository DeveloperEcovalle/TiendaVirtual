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
            <div class="row">
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
            
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-1">
                            <div class="row justify-content-between">
                                <div class="col-lg-4 pr-md-4 mb-1 col-12"> 
                                    <div class="pt-4 pl-4 pr-4 pb-2 bg-amarillo-2 text-center panel-tipo-envio">
                                        <img src="/img/carrito-01.png" class="img-facturacion-ecovalle"><br>
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Envío a nivel nacional">
                                                <input type="radio" id="tipo_compra_E" name="tipo_compra" value="E" v-model="sLocation">
                                                &nbsp;<b>Envío a nivel nacional</b>&nbsp;<p class="small">(Punto de partida Trujillo)</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 pr-md-3 pl-md-3  mb-1 col-12">
                                    <div class="pt-4 pl-4 pr-4 pb-2 bg-amarillo-2 text-center panel-tipo-envio">
                                        <img src="/img/carrito-02.png" class="img-atributo-ecovalle mb-2"><br>
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Recojo en tienda">
                                                <input type="radio" name="tipo_compra" id="tipo_compra_R" value="R" v-model="sLocation">
                                                &nbsp;<b>Recojo en tienda Trujillo</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 pl-md-4 mb-1 col-12">
                                    <div class="pt-4 pl-4 pr-4 pb-2 bg-amarillo-2 text-center panel-tipo-envio">
                                        <img src="/img/carrito-03.png" class="img-facturacion-ecovalle mb-2"><br>
                                        <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                            <label class="m-0" title="Delivery Trujillo">
                                                <input type="radio" name="tipo_compra" id="tipo_compra_D" value="D" v-model="sLocation">
                                                &nbsp;<b>Delivery Trujillo</b>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(session()->has('cliente'))
                        <div class="col-12 mb-3 mt-4" v-if="sLocation == 'E'">
                            <div class="row">
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold">SELECCIONAR MÉTODO DE ENVÍO</p>
                                </div>
                                <div class="col-12 mb-4">
                                    <div class="row pl-3 pr-3">
                                        <div class="col-12 border-left border-right border-top p-0">
                                            <div class="row pt-1 pl-2 pr-2">
                                                <div class="col-10">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0 text-ecovalle font-weight-bold" title="Recojo en agencia">
                                                            <input type="radio" name="tipo_recojo" value="RA" v-model="sTipoEnvio" v-on:change="fAgencia('')">
                                                            Recojo en agencia - Pago contra entrega
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_1')"><i class="fa" :class="sCollapse_1 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse border-top m-0 p-4 bg-ecovalle-6" id="collapseExample">
                                                <p>EcoValle, asume los gastos de envío hasta la agencia de transporte, en adelante el cliente es responsable de cancelar el monto de envío que cobra la agencia seleccionada.</p>
                                            </div>
                                        </div>
                                        <div class="col-12 border-left border-right p-0 border-top border-bottom" v-if="!bOlva">
                                            <div class="row pt-1 pl-2 pr-2">
                                                <div class="col-10">
                                                    <button class="d-inline" class="btn" disabled style="border-radius: 50%; width: 22px; height: 22px; border: 0.5px solid rgb(156, 156, 156);"></button>
                                                    <label class="d-inline m-0 text-ecovalle font-weight-bold" title="Envío por Olva Currier a domicilio">
                                                        Envío por @{{ lstAgencias.length > 0 ? lstAgencias[0].nombre : 'NO EXISTE OLVA CURIER' }} a domicilio - Pago según localidad
                                                    </label>
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#collapseExample1" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_2')"><i class="fa" :class="sCollapse_2 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse p-4 m-0 w-100 border-top bg-ecovalle-6" id="collapseExample1">
                                                <p>EcoValle, asume los gastos de envío hasta la agencia de transporte, en adelante el cliente es responsable de cancelar el monto de envío que cobra la agencia seleccionada.</p>
                                                <p class="text-danger"><i class="fa fa-exclamation-circle"></i> Olva Currier no esta disponible para este destino.</p>
                                            </div>
                                        </div>
                                        <div class="col-12 border-left border-right p-0 border-top border-bottom" v-if="bOlva">
                                            <div class="row pt-1 pl-2 pr-2">
                                                <div class="col-10">
                                                    <div class="d-inline-block i-checks" v-icheck="{ type: 'radio' }">
                                                        <label class="m-0 text-ecovalle font-weight-bold" title="Envío por Olva Currier a domicilio">
                                                            <input type="radio" name="tipo_recojo" value="EOC" v-model="sTipoEnvio" v-on:change="fAgencia(lstAgencias[0].nombre)">
                                                            Envío por @{{ lstAgencias.length > 0 ? lstAgencias[0].nombre : 'NO EXISTE OLVA CURIER' }} a domicilio - Pago según localidad
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-right">
                                                    <button class="btn btn-collapse" type="button" data-toggle="collapse" data-target="#collapseExample1" aria-expanded="false" aria-controls="collapseExample" v-on:click="fCollapse('collapse_2')"><i class="fa" :class="sCollapse_2 === 0 ? 'fa-chevron-down' : 'fa-chevron-up'"></i></button>
                                                </div>
                                            </div>
                                            <div class="collapse p-4 m-0 w-100 border-top bg-ecovalle-6" id="collapseExample1">
                                                <p>EcoValle, asume los gastos de envío hasta la agencia de transporte, en adelante el cliente es responsable de cancelar el monto de envío que cobra la agencia seleccionada.</p>
                                            </div>
                                        </div>
                                    </div>                              
                                </div>
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">DATOS DE FACTURACIÓN Y ENVÍO</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres y Apellidos</label>
                                                <p class="mt-1" v-if="datosEnvio.sNombres != '' && datosEnvio.sApellidos">@{{ datosEnvio.sNombres + ' ' + datosEnvio.sApellidos + ' ' }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Correo</label>
                                                <p class="mt-1" v-if="datosEnvio.sEmail != ''">@{{ datosEnvio.sEmail }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar email</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Dirección</label>
                                                <p class="mt-1" v-if=" datosEnvio.sDireccion != '' &&  datosEnvio.sDistrito != '' &&  datosEnvio.sProvincia != '' &&  datosEnvio.sDepartamento != ''">@{{ datosEnvio.sDireccion + ' - ' + datosEnvio.sDistrito + ' / ' + datosEnvio.sProvincia + ' / ' + datosEnvio.sDepartamento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar dirección</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Emitir</label>
                                                <p class="mt-1" v-if="bComprobanteEncontrado">@{{ bComprobanteEncontrado }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Seleccionar tipo de comprobante</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0 text-uppercase">@{{ datosEnvio.sTipoDoc }}</label>
                                                <p class="mt-1" v-if="datosEnvio.sDocumento != ''">@{{ datosEnvio.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosEnvio.sTelefono != ''">@{{ datosEnvio.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Agencia</label>
                                                <p class="mt-1" v-if="datosEnvio.sAgencia != ''">@{{ datosEnvio.sAgencia }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Seleccionar agencia</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">QUIÉN RECOGE</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres</label>
                                                <p class="mt-1" v-if ="datosEnvio.sRecoge.sRazonSocial != ''">@{{ datosEnvio.sRecoge.sRazonSocial }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres y apellidos</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosEnvio.sRecoge.sTelefono != ''">@{{ datosEnvio.sRecoge.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">DNI</label>
                                                <p class="mt-1"v-if="datosEnvio.sRecoge.sDocumento != ''">@{{ datosEnvio.sRecoge.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn btn-amarillo-compra btn-sm d-inline" data-toggle="modal" data-target="#modalEditarDatosEnvio">EDITAR</button>
                                            <button type="button" class="btn btn-ecovalle-compra btn-sm d-inline" data-toggle="modal" data-target="#modalTarifasEnvio">VER TARIFAS</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3 mt-4" v-if="sLocation == 'R'">
                            <div class="row">
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">PUNTO DE RECOJO EN TIENDA</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Tienda</label>
                                                <p class="mt-1" >Ecovalle Market Saludable</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Direcci&oacute;n</label>
                                                <p class="mt-1" >Av. Prolongaci&oacute;n C&eacute;sar Vallejo Mz. I. Lt. 2 Urb. Galeno 1, Trujillo Ref.: a 1/2 cuadra del Real Plaza</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Tel&eacute;fono</label>
                                                <p class="mt-1" >960 062 164</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Horario de atenci&oacute;n</label>
                                                <p class="mt-1" >Lunes a Domingo de 10:00 am a 9:00 pm</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">DATOS DE FACTURACI&Oacute;N</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres y Apellidos</label>
                                                <p class="mt-1" v-if="datosRecojo.sNombres != '' && datosRecojo.sApellidos">@{{ datosRecojo.sNombres + ' ' + datosRecojo.sApellidos + ' ' }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Correo</label>
                                                <p class="mt-1" v-if="datosRecojo.sEmail != ''">@{{ datosRecojo.sEmail }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar email</p>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosRecojo.sTelefono != ''">@{{ datosRecojo.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Emitir</label>
                                                <p class="mt-1" v-if="bComprobanteEncontrado">@{{ bComprobanteEncontrado }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Seleccionar tipo de comprobante</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0 text-uppercase">@{{ datosRecojo.rTipoDoc }}</label>
                                                <p class="mt-1" v-if="datosRecojo.sDocumento != ''">@{{ datosRecojo.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">QUIÉN RECOGE</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres</label>
                                                <p class="mt-1" v-if ="datosRecojo.sRecoge.sRazonSocial != ''">@{{ datosRecojo.sRecoge.sRazonSocial }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres y apellidos</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosRecojo.sRecoge.sTelefono != ''">@{{ datosRecojo.sRecoge.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">DNI</label>
                                                <p class="mt-1"v-if="datosRecojo.sRecoge.sDocumento != ''">@{{ datosRecojo.sRecoge.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn btn-amarillo-compra btn-sm d-inline" data-toggle="modal" data-target="#modalEditarRecojo">EDITAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3 mt-4" v-if="sLocation == 'D'">
                            <div class="row">
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">DATOS DE FACTURACI&Oacute;N</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres y Apellidos</label>
                                                <p class="mt-1" v-if="datosDelivery.sNombres != '' && datosDelivery.sApellidos">@{{ datosDelivery.sNombres + ' ' + datosDelivery.sApellidos + ' ' }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Correo</label>
                                                <p class="mt-1" v-if="datosDelivery.sEmail != ''">@{{ datosDelivery.sEmail }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar email</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Dirección</label>
                                                <p class="mt-1" v-if="datosDelivery.sDireccion != ''">@{{ datosDelivery.sDireccion }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar direcci&oacute;n</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Emitir</label>
                                                <p class="mt-1" v-if="bComprobanteEncontrado">@{{ bComprobanteEncontrado }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Seleccionar tipo de comprobante</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0 text-uppercase">@{{ datosDelivery.dTipoDoc }}</label>
                                                <p class="mt-1" v-if="datosDelivery.sDocumento != ''">@{{ datosDelivery.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosDelivery.sTelefono != ''">@{{ datosDelivery.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="h5 text-ecovalle-2 font-weight-bold mb-1">QUIÉN RECIBE</p>
                                    <div class="hr-line"></div>
                                    <div class="row mt-3">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Nombres</label>
                                                <p class="mt-1" v-if ="datosDelivery.sRecoge.sRazonSocial != ''">@{{ datosDelivery.sRecoge.sRazonSocial }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar nombres y apellidos</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">Teléfono</label>
                                                <p class="mt-1" v-if="datosDelivery.sRecoge.sTelefono != ''">@{{ datosDelivery.sRecoge.sTelefono }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar teléfono</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="text-ecovalle font-weight-bold mb-0">DNI</label>
                                                <p class="mt-1"v-if="datosDelivery.sRecoge.sDocumento != ''">@{{ datosDelivery.sRecoge.sDocumento }}</p>
                                                <p class="text-danger mt-1" v-else><i class="fa fa-exclamation-circle"></i> Completar documento</p>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn btn-amarillo-compra btn-sm d-inline" data-toggle="modal" data-target="#modalEditarDelivery">EDITAR</button>
                                            <button type="button" class="btn btn-ecovalle-compra btn-sm d-inline" data-toggle="modal" data-target="#modalTarifasDelivery">VER TARIFAS</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-3 mt-2">
                            <div class="row">
                                <div class="col-12">
                                    <div v-icheck class="m-2">
                                        <label class="m-0">
                                            <input type="checkbox" value="1" name="termCond[]" v-model="termCond">&nbsp;He leído y acepto los <a href="/terminos-condiciones" target="_blank">t&eacute;rminos y condiciones.</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-12 mb-3 mt-4">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <p>Direcci&oacute;n de env&iacute;o no establecida. Click en 'Registrarse'.</p>
                                </div>
                                <div class="col-12 col-md-4">
                                    <a class="btn btn-block btn-ecovalle-compra" href="/registro">Registrarse</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row bg-white">
                        <div class="col-12">
                            <div class="px-2 text-center">
                                <h1 class="h6 mb-0 mt-2 mt-md-0 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesFacturacionEnvio['order_summary'] }}</h1>
                                <div class="hr-compra"></div>
                            </div>
                        </div>
                        <div class="col-12" v-for="(detalle, i) in lstCarritoCompras">
                            <div class="p-2">
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
                            <div class="px-2 pt-2 text-center text-uppercase">
                                <h1 class="h6 mb-0 mt-2 mt-md-0 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesFacturacionEnvio['price_summary'] }}</h1>
                                <div class="hr-compra"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2">
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2">Sub total <span class="float-right">S/ @{{ (fSubtotal + fDescuento).toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold">Ahorraste <span class="float-right">S/ @{{ fDescuento.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold">Cargos de env&iacute;o <span class="float-right">S/ @{{ fDelivery.toFixed(2) }}</span></p>
                                <div class="hr-compra"></div>
                                <p class="mb-0 font-weight-bold text-ecovalle-compra-2 h5">Total <span class="float-right">S/ @{{ fTotal.toFixed(2) }}</span></p>
                            </div>
                        </div>
                        @if(session()->has('cliente'))
                        <div class="col-12" v-if="sLocation == 'E'">
                            <div class="p-2">
                                <button class="btn btn-block btn-ecovalle-compra" :disabled="!bDireccionEnvioValida || bVerificaRuc != 1 || bVerificaDni != 1" v-if="termCond.length > 0" v-on:click="confirmarFacturacion('E')">Continuar</button>
                            </div>
                        </div>
                        <div class="col-12" v-if="sLocation == 'R'">
                            <div class="p-2">
                                <button class="btn btn-block btn-ecovalle-compra" :disabled="!bRecojoValida || !bComprobanteRecojo || bVerificaDniRecojo != 1 || bVerificaRucRecojo != 1" v-if="termCond.length > 0" v-on:click="confirmarFacturacion('R')">Continuar</button>
                            </div>
                        </div>
                        <div class="col-12" v-if="sLocation == 'D'">
                            <div class="p-2">
                                <button class="btn btn-block btn-ecovalle-compra" :disabled="!bDeliveryValida || !bComprobanteDelivery || bVerificaDniDelivery != 1 || bVerificaRucDelivery != 1" v-if="termCond.length > 0" v-on:click="confirmarFacturacion('D')">Continuar</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalEditarDatosEnvio" tabindex="-1">
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
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Agencia</label>
                                    <select  class="form-control" :class="{'is-invalid' : datosEnvio.sAgencia == ''}" v-model="datosEnvio.sAgencia">
                                        <option value="">Seleccionar</option>
                                        <option v-for="agencia in bAgencias" :value="agencia.nombre">@{{ agencia.nombre }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar agencia</strong>
                                    </span>
                                    <span v-if="!bDestinoEncontrado" style="font-size: 12px;">
                                        <strong class="text-danger">Completar destino de envío</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Tipo Comprobante</label>
                                    <select  class="form-control" :class="{'is-invalid' : datosEnvio.sTipoComprobante == ''}" v-model="datosEnvio.sTipoComprobante" id="deTipoDoc" v-on:change="fControlTipoDoc('E')">
                                        <option value="">Seleccionar</option>
                                        <option v-for="(tipo, i) in lstTiposComprobante" :value="tipo.id">@{{ tipo.tipo_comprobante_sunat.descripcion }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar tipo comprobante</strong>
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
                                        <option value="">Seleccionar</option>
                                        <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar departamento</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Provincia</label>
                                    <select class="form-control" :class="{'is-invalid' : datosEnvio.sProvincia == ''}" v-model="datosEnvio.sProvincia">
                                        <option value="">Seleccionar</option>
                                        <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar provincia</strong>
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Distrito</label>
                                    <select class="form-control" :class="{'is-invalid' : datosEnvio.sDistrito == ''}" v-model="datosEnvio.sDistrito">
                                        <option value="">Seleccionar</option>
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
                        <div class="row" v-if="iCargandoConsultaApir === 0">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label v-if="datosRecojo.rTipoDoc == 'DNI'">Nombres</label>
                                    <label v-else>Raz&oacute;n social</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sNombres == ''}" v-model="datosRecojo.sNombres" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar nombres</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group" v-if="datosRecojo.rTipoDoc == 'DNI'">
                                    <label>Apellidos</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sApellidos == '' && datosRecojo.rTipoDoc == 'DNI'}" v-model="datosRecojo.sApellidos" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar apellidos</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Correo electr&oacute;nico</label>
                                    <input type="email" class="form-control" :class="{'is-invalid' : datosRecojo.sEmail == ''}" v-model="datosRecojo.sEmail" autocomplete="off" required>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar correo electr&oacute;nico</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group" >
                                    <label>Teléfono</label>
                                    <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sTelefono == ''}" v-model="datosRecojo.sTelefono" autocomplete="off">
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Completar tel&eacute;fono</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tipo Comprobante</label>
                                    <select  class="form-control" :class="{'is-invalid' : datosRecojo.sTipoComprobante == ''}" v-model="datosRecojo.sTipoComprobante" id="deTipoDoc" v-on:change="fControlTipoDoc('R')">
                                        <option value="">Seleccionar</option>
                                        <option v-for="(tipo, i) in lstTiposComprobante" :value="tipo.id">@{{ tipo.tipo_comprobante_sunat.descripcion }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar tipo comprobante</strong>
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
                                                <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sRecoge.sDocumento == ''}" v-model="datosRecojo.sRecoge.sDocumento" autocomplete="off">
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
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sRecoge.sTelefono == ''}" v-model="datosRecojo.sRecoge.sTelefono" autocomplete="off">
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
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosRecojo.sRecoge.sRazonSocial == ''}" v-model="datosRecojo.sRecoge.sRazonSocial" autocomplete="off">
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
                            <div class="col-12 col-md-6">
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
                            <div class="col-12 col-md-6">
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Tipo Comprobante</label>
                                    <select  class="form-control" :class="{'is-invalid' : datosDelivery.sTipoComprobante == ''}" v-model="datosDelivery.sTipoComprobante" id="deTipoDoc" v-on:change="fControlTipoDoc('D')">
                                        <option value="">Seleccionar</option>
                                        <option v-for="(tipo, i) in lstTiposComprobante" :value="tipo.id">@{{ tipo.tipo_comprobante_sunat.descripcion }}</option>
                                    </select>
                                    <span class="invalid-feedback">
                                        <strong class="text-danger">Seleccionar tipo comprobante</strong>
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
                            <div class="col-12" style="border: solid 1px #EE9722;border-radius:5px;">
                                <label><b>Datos de quien recibe el pedido</b></label>
                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label>Documento (DNI)</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" :class="{'is-invalid' : datosDelivery.sRecoge.sDocumento == ''}" v-model="datosDelivery.sRecoge.sDocumento" autocomplete="off">
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
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosDelivery.sRecoge.sTelefono == ''}" v-model="datosDelivery.sRecoge.sTelefono" autocomplete="off">
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
                                            <input type="text" class="form-control" :class="{'is-invalid' : datosDelivery.sRecoge.sRazonSocial == ''}" v-model="datosDelivery.sRecoge.sRazonSocial" autocomplete="off">
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
                    <div class="form-group d-none">
                        <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por departamento, provincia ó distrito">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tblBlogs">
                            <thead>
                                <tr>
                                    <th class="bg-ecovalle-2">Agencia</th>
                                    <th class="bg-ecovalle-2">Descripcion</th>
                                    <th class="bg-ecovalle-2">Direcci&oacute;n</th>
                                    <th class="bg-ecovalle-2">Tarifa</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr v-for="agencia  of bAgencias" v-cloak>
                                    <td>@{{ agencia.nombre }}</td>
                                    <td>@{{ agencia.descripcion }}</td>
                                    <td>@{{ agencia.pivot.direccion }}</td>
                                    <td>S/. @{{ agencia.pivot.tarifa.toFixed(2) }}</td>
                                </tr>
                                <tr v-if="bAgencias.length === 0" v-cloak>
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
