<div class="d-flex border-bottom white-bg">
    <div class="col-12 py-2 px-3 font-bold">
        <small>@{{ venta.tipo_comprobante }}</small><br>
        <small>PEDIDO COD: <b>@{{ venta.codigo }}</b></small>
    </div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Tipo</label>
        <div class="col-8">
            <p>@{{ venta.tipo_compra }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Emisi&oacute;n</label>
        <div class="col-8">
            <p>@{{ venta.emision }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Cliente</label>
        <div class="col-8">
            <p>@{{ venta.cliente }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Dcto. cliente</label>
        <div class="col-8">
            <p>@{{ venta.documento }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Telf / Cel</label>
        <div class="col-8">
            <p>@{{ venta.telefono }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-4">Correo</label>
        <div class="col-8">
            <p>@{{ venta.email }}</p>
        </div>
    </div>
    <div class="form-group m-0 row" v-if="venta.tipo_compra === 'ENVÍO NIVEL NACIONAL' || venta.tipo_compra === 'DELIVERY TRUJILLO'">
        <label class="font-weight-bold col-4">Direcci&oacute;n</label>
        <div class="col-8">
            <p>@{{ venta.direccion + ' - ' + venta.ubigeo.departamento + ' / ' + venta.ubigeo.provincia + ' / ' + venta.ubigeo.distrito }}</p>
        </div>
    </div>
    <div class="form-group m-0 row" v-if="venta.tipo_compra === 'ENVÍO NIVEL NACIONAL'">
        <label class="font-weight-bold col-4">¿Qui&eacute;n recoge?</label>
        <div class="col-8">
            <p>@{{ venta.recoge }}</p>
        </div>
    </div>
    <div class="form-group m-0 row" v-if="venta.tipo_compra === 'ENVÍO NIVEL NACIONAL'">
        <label class="font-weight-bold col-4">Dcto. ¿Qui&eacute;n recoge?</label>
        <div class="col-8">
            <p>@{{ venta.recoge_documento }}</p>
        </div>
    </div>
    <div class="form-group m-0 row" v-if="venta.tipo_compra === 'ENVÍO NIVEL NACIONAL'">
        <label class="font-weight-bold col-4">Tlf. ¿Qui&eacute;n recoge?</label>
        <div class="col-8">
            <p>@{{ venta.recoge_telefono }}</p>
        </div>
    </div>

    <table class="table table-bordered" style="font-size: 12px;">
        <thead>
        <tr>
            <th class="text-right">Cantidad</th>
            <th>Producto</th>
            <th class="text-right">Valor Unitario</th>
            <th class="text-right">Valor Venta</th>
            <th class="text-right">Promocion</th>
            <th class="text-right">Importe</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="detalle in venta.detalles">
            <td class="text-right">@{{ detalle.cantidad.toFixed(2) }}</td>
            <td>@{{ detalle.producto.nombre_es }}</td>
            <td class="text-right">@{{ detalle.precio_actual.toFixed(2) }}</td>
            <td class="text-right">@{{ detalle.precio_venta.toFixed(2) }}</td>
            <td class="text-right">@{{ detalle.promocion.toFixed(2) }}</td>
            <td class="text-right">@{{ ((detalle.precio_venta - detalle.promocion ) * detalle.cantidad).toFixed(2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="form-group m-0 row">
        <label class="font-weight-bold col-6">Sub Total</label>
        <div class="col-6">
            <p class="text-right">S/ @{{ (venta.subtotal + venta.descuento).toFixed(2) }}</p>
        </div>
    </div>

    <div class="form-group m-0 row">
        <label class="font-weight-bold col-6">Descuento</label>
        <div class="col-6">
            <p class="text-right">S/ @{{ venta.descuento.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-6">Delivery</label>
        <div class="col-6">
            <p class="text-right">S/ @{{ venta.delivery.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group m-0 row">
        <label class="font-weight-bold col-6">Importe total venta</label>
        <div class="col-6">
            <p class="text-right">S/ @{{ (venta.subtotal + venta.delivery).toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group row p-2 bg-muted mt-4 mb-2">
        <div class="col-12">
            <h4 class="mb-0">ESTADO DE PEDIDO</h4>
        </div>
    </div>
    <form class="mb-2" id="frmEstado">
        <div class="form-group row">
            <label class="col-md-3 py-md-2 font-weight-bold">Estado <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select name="estado" id="estado" class="form-control" v-model="estado" v-on:change="ajaxEditarEstado">
                    <option v-for="estado in lstEstados" :value="estado.id">@{{ estado.estado }}</option>
                </select>
            </div>
        </div>
    </form>
    <form class="mb-5" id="frmEstadoPago">
        <div class="form-group row">
            <label class="col-md-3 py-md-2 font-weight-bold">Pago <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select name="estado_pago" id="estado_pago" class="form-control" v-model="venta.estado_pago" disabled>
                    <option value="1">PAGADO</option>
                    <option value="0">POR PAGAR</option>
                </select>
            </div>
        </div>
    </form>
</div>
