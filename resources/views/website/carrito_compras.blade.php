@extends('website.layout')

@section('title', 'Carrito de Compras')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['Shopping cart'] }}</li>
            </ol>
        </nav>
    </div>

    <section class="pt-4 pb-5">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-3">
                    <button class="btn btn-block font-weight-bold mb-3 mb-md-0"
                            :class="{ 'btn-ecovalle': sEtapaCompra === 'carrito', 'btn-outline-ecovalle active': sEtapaCompra !== 'carrito' }">
                        1. {{ $lstLocales['Shopping cart'] }}
                    </button>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-block font-weight-bold mb-3 mb-md-0"
                            :class="{ 'btn-ecovalle': sEtapaCompra === 'facturacion', 'btn-outline-ecovalle': sEtapaCompra !== 'facturacion', 'active': sEtapaCompra === 'pago' }">
                        2. {{ $lstTraduccionesCarritoCompras['billing_and_delivery'] }}
                    </button>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-block font-weight-bold mb-3 mb-md-0"
                            :class="{ 'btn-ecovalle': sEtapaCompra === 'pago', 'btn-outline-ecovalle': sEtapaCompra !== 'pago' }">
                        3. {{ $lstTraduccionesCarritoCompras['payment'] }}
                    </button>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-block font-weight-bold mb-3 mb-md-0"
                            :class="{ 'btn-ecovalle': sEtapaCompra === 'resumen', 'btn-outline-ecovalle': sEtapaCompra !== 'resumen' }">
                        4. Resumen
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5 mb-5">
        <div class="container-xl">
            <div class="col-lg-12">
                <h1 class="h5 mb-3">
                    <span class="font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstLocales['Shopping cart'] }}</span>
                    <button class="btn btn-sm btn-ecovalle float-right" v-on:click="ajaxActualizarCarritoCompras()" title="{{ $lstTraduccionesCarritoCompras['update_cart'] }}">
                        <i class="fas fa-sync" :class="{ 'fa-spin': iCargando === 1 }" :disabled="iCargando === 1"></i>
                    </button>
                </h1>
                <div class="row" v-if="iCargando === 1">
                    <div class="col-12 text-center">
                        <img src="/img/spinner.svg">
                    </div>
                </div>
                <table class="table mb-5" v-else v-cloak>
                    <thead>
                    <tr>
                        <th class="bg-ecovalle-2" colspan="2">{{ $lstTraduccionesCarritoCompras['product'] }}</th>
                        <th class="bg-ecovalle-2 text-right">{{ $lstTraduccionesCarritoCompras['price'] }}</th>
                        <th class="bg-ecovalle-2 text-center">{{ $lstTraduccionesCarritoCompras['quantity'] }}</th>
                        <th class="bg-ecovalle-2 text-right">Subtotal</th>
                        <th class="bg-ecovalle-2 text-center">{{ $lstTraduccionesCarritoCompras['delete'] }}</th>
                    </tr>
                    </thead>
                    <tbody v-cloak>
                    <tr v-for="(detalle, i) in lstCarritoCompras">
                        <td class="text-center">
                            <img v-if="detalle.producto.imagenes.length > 0" :src="detalle.producto.imagenes[0].ruta" style="max-width: 60px">
                        </td>
                        <td>
                            <span class="font-weight-bold d-block mt-1">@{{ locale === 'es' ? detalle.producto.nombre_es : detalle.producto.nombre_en }}</span>
                            <span v-if="detalle.producto.oferta_vigente" class="badge badge-danger mt-1">
                                    S/ @{{ detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.oferta_vigente.porcentaje + '%') : ('- S/ ' + detalle.producto.oferta_vigente.monto) }}
                                </span>
                        </td>
                        <td>
                                <span class="mt-1 d-block text-right" :style="{ 'text-decoration-line': detalle.producto.oferta_vigente ? 'line-through' : 'none' }"
                                      :class="{ small: detalle.producto.oferta_vigente, 'text-danger': detalle.producto.oferta_vigente > 0 }">
                                    S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                </span>
                            <span class="mt-1 d-block text-right" v-if="detalle.producto.oferta_vigente">
                                    S/ @{{ (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)).toFixed(2) }}
                                </span>
                        </td>
                        <td>
                            <div class="input-group mx-auto mt-1" style="max-width: 160px">
                                    <span class="input-group-prepend">
                                        <button type="button" class="btn btn-ecovalle" :disabled="detalle.cantidad === 1" v-on:click="ajaxDisminuirCantidadProductoCarrito(detalle.producto, i)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </span>
                                <input type="text" class="form-control text-center" :value="detalle.cantidad">
                                <span class="input-group-append">
                                        <button type="button" class="btn btn-ecovalle" v-on:click="ajaxAumentarCantidadProductoCarrito(detalle.producto, i)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </span>
                            </div>
                        </td>
                        <td>
                                <span class="d-block mt-2 text-right">
                                    S/ @{{ (detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))).toFixed(2) }}
                                </span>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-danger d-block mt-2" v-on:click.prevent="ajaxEliminarDelCarrito(detalle.producto, i)"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <tr v-if="lstCarritoCompras.length === 0">
                        <td class="text-center" colspan="6">{{ $lstTraduccionesCarritoCompras['empty_cart'] }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <h1 class="h5 mb-3 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesCarritoCompras['order_summary'] }}</h1>
                <div class="row" v-if="iCargando === 1">
                    <div class="col-12 text-center">
                        <img src="/img/spinner.svg">
                    </div>
                </div>
                <table class="table mb-5" v-else v-cloak>
                    <tbody>
                    <tr>
                        <td>{{ $lstTraduccionesCarritoCompras['subtotal_product_amount'] }}</td>
                        <td class="text-right font-weight-bold h4">S/ @{{ fSubtotal.toFixed(2) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12 text-center">
                <button class="btn btn-lg btn-ecovalle" :disabled="lstCarritoCompras.length === 0" v-on:click="sEtapaCompra = 'facturacion'">
                    {{ $lstTraduccionesCarritoCompras['proceed_to_purchase'] }}
                </button>
            </div>
            {{--<div class="row" v-if="sEtapaCompra === 'carrito'" v-cloak>
                <div class="col-lg-12">
                    <h1 class="h5 mb-3">
                        <span class="font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstLocales['Shopping cart'] }}</span>
                        <button class="btn btn-sm btn-ecovalle float-right" v-on:click="ajaxActualizarCarritoCompras()" title="{{ $lstTraduccionesCarritoCompras['update_cart'] }}">
                            <i class="fas fa-sync" :class="{ 'fa-spin': iCargando === 1 }" :disabled="iCargando === 1"></i>
                        </button>
                    </h1>
                    <div class="row" v-if="iCargando === 1">
                        <div class="col-12 text-center">
                            <img src="/img/spinner.svg">
                        </div>
                    </div>
                    <table class="table mb-5" v-else v-cloak>
                        <thead>
                        <tr>
                            <th class="bg-ecovalle-2" colspan="2">{{ $lstTraduccionesCarritoCompras['product'] }}</th>
                            <th class="bg-ecovalle-2 text-right">{{ $lstTraduccionesCarritoCompras['price'] }}</th>
                            <th class="bg-ecovalle-2 text-center">{{ $lstTraduccionesCarritoCompras['quantity'] }}</th>
                            <th class="bg-ecovalle-2 text-right">Subtotal</th>
                            <th class="bg-ecovalle-2 text-center">{{ $lstTraduccionesCarritoCompras['delete'] }}</th>
                        </tr>
                        </thead>
                        <tbody v-cloak>
                        <tr v-for="(detalle, i) in lstCarritoCompras">
                            <td class="text-center">
                                <img v-if="detalle.producto.imagenes.length > 0" :src="detalle.producto.imagenes[0].ruta" style="max-width: 60px">
                            </td>
                            <td>
                                <span class="font-weight-bold d-block mt-1">@{{ locale === 'es' ? detalle.producto.nombre_es : detalle.producto.nombre_en }}</span>
                                <span v-if="detalle.producto.oferta_vigente" class="badge badge-danger mt-1">
                                    S/ @{{ detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.oferta_vigente.porcentaje + '%') : ('- S/ ' + detalle.producto.oferta_vigente.monto) }}
                                </span>
                            </td>
                            <td>
                                <span class="mt-1 d-block text-right" :style="{ 'text-decoration-line': detalle.producto.oferta_vigente ? 'line-through' : 'none' }"
                                      :class="{ small: detalle.producto.oferta_vigente, 'text-danger': detalle.producto.oferta_vigente > 0 }">
                                    S/ @{{ detalle.producto.precio_actual.monto.toFixed(2) }}
                                </span>
                                <span class="mt-1 d-block text-right" v-if="detalle.producto.oferta_vigente">
                                    S/ @{{ (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)).toFixed(2) }}
                                </span>
                            </td>
                            <td>
                                <div class="input-group mx-auto mt-1" style="max-width: 160px">
                                    <span class="input-group-prepend">
                                        <button type="button" class="btn btn-ecovalle" :disabled="detalle.cantidad === 1" v-on:click="ajaxDisminuirCantidadProductoCarrito(detalle.producto, i)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </span>
                                    <input type="text" class="form-control text-center" :value="detalle.cantidad">
                                    <span class="input-group-append">
                                        <button type="button" class="btn btn-ecovalle" v-on:click="ajaxAumentarCantidadProductoCarrito(detalle.producto, i)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="d-block mt-2 text-right">
                                    S/ @{{ (detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))).toFixed(2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="text-danger d-block mt-2" v-on:click.prevent="ajaxEliminarDelCarrito(detalle.producto, i)"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <tr v-if="lstCarritoCompras.length === 0">
                            <td class="text-center" colspan="6">{{ $lstTraduccionesCarritoCompras['empty_cart'] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <h1 class="h5 mb-3 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesCarritoCompras['order_summary'] }}</h1>
                    <div class="row" v-if="iCargando === 1">
                        <div class="col-12 text-center">
                            <img src="/img/spinner.svg">
                        </div>
                    </div>
                    <table class="table mb-5" v-else v-cloak>
                        <tbody>
                        <tr>
                            <td>{{ $lstTraduccionesCarritoCompras['subtotal_product_amount'] }}</td>
                            <td class="text-right font-weight-bold h4">S/ @{{ fSubtotal.toFixed(2) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 text-center">
                    <button class="btn btn-lg btn-ecovalle" :disabled="lstCarritoCompras.length === 0" v-on:click="sEtapaCompra = 'facturacion'">
                        {{ $lstTraduccionesCarritoCompras['proceed_to_purchase'] }}
                    </button>
                </div>
            </div>
            <div class="row" v-if="sEtapaCompra === 'facturacion'" v-cloak>
                <div class="col-12 text-center" v-if="iCargandoDatosFacturacion === 1 || iPagando === 1">
                    <img src="/img/spinner.svg">
                </div>
                <div class="col-12" v-else>
                    <div class="row justify-content-center">
                        <div class="col-md-7">
                            @if(!session()->has('cliente'))
                                <div class="row">
                                    <div class="col-12">
                                        <p>{!! $lstTraduccionesCarritoCompras['enter_your_data'] !!}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-12">
                                    <h1 class="h5 mb-3 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesCarritoCompras['billing'] }}</h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['receipt_type'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <select class="form-control" name="tipo_de_comprobante" v-model="formData.iTipoComprobanteId">
                                            <option v-for="tipoComprobante in lstTiposComprobante" :value="tipoComprobante.id">@{{ tipoComprobante.nombre }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['identity_card'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <select class="form-control" name="tipo_de_documento" :disabled="lstTiposDocumento.length <= 1" v-model="formData.iTipoDocumentoId">
                                            <option v-for="tipoDocumento in lstTiposDocumento" :value="tipoDocumento.codigo">@{{ tipoDocumento.abreviatura }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['document_number'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input class="form-control" name="numero_de_documento" v-model="formData.sNumeroDocumento">
                                    </div>
                                </div>
                                <div class="col-lg-6" v-if="formData.iTipoDocumentoId == 1">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['name'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nombres" v-model="formData.sNombres" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-6" v-if="formData.iTipoDocumentoId == 1">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['last_name'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="apellidos" v-model="formData.sApellidos" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-12" v-if="formData.iTipoDocumentoId == 6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['company_name'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="razon_social" v-model="formData.sRazonSocial" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <h1 class="h5 mt-4 mb-3 font-weight-bold text-ecovalle-2">{{ $lstTraduccionesCarritoCompras['delivery'] }}</h1>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Persona que recibe&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="persona_que_recibe" v-model="formData.sPersonaQueRecibe" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Departamento&nbsp;<span class="text-danger">*</span></label>
                                        <select class="form-control" name="departamento" v-model="formData.sDepartamento" required="required">
                                            <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Provincia&nbsp;<span class="text-danger">*</span></label>
                                        <select class="form-control" name="provincia" v-model="formData.sProvincia" autocomplete="off" required="required">
                                            <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Distrito&nbsp;<span class="text-danger">*</span></label>
                                        <select class="form-control" name="distrito" v-model="formData.sDistrito" autocomplete="off" required="required">
                                            <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12" v-if="!bDestinoEncontrado && formData.sDepartamento !== '' && formData.sProvincia !== '' && formData.sDistrito !== ''">
                                    <p class="text-danger font-weight-bold">Por ahora no disponemos de entregas para este destino.</p>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['delivery_address'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="direccion_de_envio" v-model="formData.sDireccion" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['phone'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="telefono" v-model="formData.sTelefono" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['email'] }}&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="email" v-model="formData.sCorreo" autocomplete="off" required="required">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h1 class="h5 my-3 font-weight-bold text-ecovalle-2 text-uppercase">{{ $lstTraduccionesCarritoCompras['additional_information'] }}</h1>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">{{ $lstTraduccionesCarritoCompras['notes_order'] }}</label>
                                        <textarea class="form-control" name="notas_para_el_pedido" v-model="formData.sNotas" autocomplete="off" rows="3" style="resize: vertical"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="col-lg-12">
                                <h1 class="h5 mb-3 text-ecovalle-2 text-uppercase font-weight-bold">{{ $lstTraduccionesCarritoCompras['your_order'] }}</h1>
                            </div>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th class="bg-ecovalle">{{ $lstTraduccionesCarritoCompras['product'] }}</th>
                                    <th class="bg-ecovalle text-right">Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="detalle in lstCarritoCompras">
                                    <td class="small">@{{ detalle.cantidad + ' x ' + (locale === 'es' ? detalle.producto.nombre_es : detalle.producto.nombre_en) }}</td>
                                    <td class="text-right small">
                                        S/ @{{ (detalle.cantidad * (detalle.producto.oferta_vigente === null ? detalle.producto.precio_actual.monto :
                                        (detalle.producto.oferta_vigente.porcentaje ? (detalle.producto.precio_actual.monto * (100 - detalle.producto.oferta_vigente.porcentaje) / 100) : (detalle.producto.precio_actual.monto - detalle.producto.oferta_vigente.monto)))).toFixed(2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="small">{{ $lstTraduccionesCarritoCompras['subtotal_product_amount'] }}</th>
                                    <th class="text-right small">S/ @{{ fSubtotal.toFixed(2) }}</th>
                                </tr>
                                <tr>
                                    <th class="small">{{ $lstTraduccionesCarritoCompras['delivery'] }}</th>
                                    <th class="text-right small">S/ @{{ fDelivery.toFixed(2) }}</th>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="bg-warning">
                                    <th class="h5 font-weight-bold">TOTAL</th>
                                    <th class="h5 text-right font-weight-bold">S/ @{{ fTotal.toFixed(2) }}</th>
                                </tr>
                                </tfoot>
                            </table>
                            <img src="/img/medios_pago.png" class="img-fluid">
                        </div>
                        <div class="col-12 col-md-7 mt-3 text-center">
                            <p class="mb-2">{!! $lstTraduccionesCarritoCompras['privacy_policy'] !!}</p>
                            <p class="mb-2">
                            <div v-icheck>
                                <label class="m-0">
                                    <input type="checkbox" v-model="formData.bTerminosCondiciones">&nbsp;{!! $lstTraduccionesCarritoCompras['terms_and_conditions'] !!}
                                </label>
                            </div>
                            </p>
                        </div>
                        <div class="col-12 col-md-7 text-center">
                            <p class="bg-danger text-white p-3" v-if="sMensajeError.length > 0">@{{ sMensajeError }}</p>
                            <button type="button" class="btn btn-ecovalle btn-xl" v-on:click="sEtapaCompra = 'pago'"
                                    :disabled="!bFormularioCorrecto || !bDestinoEncontrado || iPagando === 1">
                                <span>{{ $lstTraduccionesCarritoCompras['pay'] }} S/ @{{ fTotal.toFixed(2) }}</span>
                                <!--span v-else><i class="fas fa-circle-notch fa-spin"></i></span-->
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="sEtapaCompra === 'pago'" v-cloak>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <label><input type="radio" name="medio_de_pago" v-model="formData.sMedioPago" value="TARJETA"> TARJETA DE CR&Eacute;DITO O D&Eacute;BITO</label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label><input type="radio" name="medio_de_pago" v-model="formData.sMedioPago" value="YAPE_PLIN"> YAPE / PLIN</label>
                        </div>
                        <div class="col-md-4 text-center">
                            <label><input type="radio" name="medio_de_pago" v-model="formData.sMedioPago" value="TRANSFERENCIA"> TRANSFERENCIA BANCARIA</label>
                        </div>
                    </div>
                </div>
                <div class="col-12" v-if="formData.sMedioPago === 'TARJETA'">
                    <div class="p-5 text-center">
                        <button class="btn btn-ecovalle" v-on:click="mostrarModalPago()">
                            PAGAR CON TARJETA DE CR&Eacute;DITO O D&Eacute;BITO
                        </button>
                    </div>
                </div>
                <div class="col-12" v-if="formData.sMedioPago === 'YAPE_PLIN'">
                    <div class="p-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Apellidos y Nombres</label>
                            <input class="form-control" name="apellidos_y_nombres_yape_plin">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Constancia Yape / Plin</label>
                            <input type="file" class="form-control" name="constancia_yape_plin">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-ecovalle">Enviar constancia de Yape / Plin</button>
                        </div>
                    </div>
                </div>
                <div class="col-12" v-if="formData.sMedioPago === 'TRANSFERENCIA'">
                    <div class="p-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Apellidos y Nombres</label>
                            <input class="form-control" name="apellidos_y_nombres_yape_plin">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Constancia de transferencia</label>
                            <input type="file" class="form-control" name="constancia_transferencia_bancaria">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-ecovalle">Enviar constancia de transferencia</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" v-if="sEtapaCompra === 'resumen'" v-cloak>
                <div class="col-12 py-5">
                    <div class="row">
                        <div class="col-8">
                            <p class="py-5" :class="'text-' + respuestaPago.result" v-if="respuestaPago" v-html="respuestaPago.mensaje"></p>
                        </div>
                        <div class="col-4 text-center">
                            <h1 class="text-center" :class="'text-' + respuestaPago.result" v-if="respuestaPago">
                                <i class="fas fa-check-circle" v-if="respuestaPago.result === 'success'"></i>
                                <i class="fas fa-exclamation-circle" v-else></i>
                            </h1>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" v-if="respuestaPago.data">
                        <tbody>
                        <tr v-for="detalle in respuestaPago.data.lstSunatFacuraBoletaDetalles">
                            <td>@{{ detalle.cantidad }}</td>
                            <td>@{{ detalle.descripcion }}</td>
                            <td class="text-right">S/ @{{ (detalle.cantidad * detalle.precio_venta_unitario_monto).toFixed(2) }}</td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2" class="font-weight-bold">IMPORTE TOTAL DEL PEDIDO</td>
                            <td class="text-right font-weight-bold">S/ @{{ respuestaPago.data.sunatFacturaBoleta.importe_total_venta.toFixed(2) }}</td>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="text-center">
                        <a class="btn btn-ecovalle btn-lg mt-4" href="/tienda">{{ $lstTraduccionesCarritoCompras['finish'] }}</a>
                    </div>
                </div>
            </div>--}}
        </div>
    </section>
@endsection

@section('js')
    <script src="https://checkout.culqi.com/js/v3"></script>
    <script src="/js/website/carritoCompras.js?cvcn=14"></script>
@endsection
