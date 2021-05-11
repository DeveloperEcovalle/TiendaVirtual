<div class="d-flex border-bottom white-bg">
    <div class="col-12 py-2 px-3 font-bold">
        <small>@{{ venta.tipo_comprobante.nombre }}</small><br>
        @{{ venta.serie_comprobante + '-' + venta.nro_comprobante }}
    </div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Tipo</label>
        <div class="col-8">
            <p>@{{ venta.tipo_comprobante.nombre }}</p>
        </div>
    </div>
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Serie-N&uacute;mero</label>
        <div class="col-8">
            <p>@{{ venta.serie_comprobante + '-' + venta.nro_comprobante }}</p>
        </div>
    </div>
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Emisi&oacute;n</label>
        <div class="col-8">
            <p>@{{ venta.fecha_emision + ' ' + venta.hora_emision }}</p>
        </div>
    </div>
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Cliente</label>
        <div class="col-8">
            <p>@{{ venta.razon_social_cliente }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Dcto. cliente</label>
        <div class="col-8">
            <p>@{{ venta.nro_documento_cliente }}</p>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="text-right">Cantidad</th>
            <th>Producto</th>
            <th class="text-right">Precio Unitario Venta</th>
            <th class="text-right">Valor Unitario</th>
            <th class="text-right">Valor Venta</th>
            <th class="text-right">IGV</th>
            <th class="text-right">Importe</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="detalle in venta.detalles">
            <td class="text-right">@{{ detalle.cantidad.toFixed(2) }}</td>
            <td>@{{ detalle.descripcion }}</td>
            <td class="text-right">@{{ detalle.precio_venta_unitario_monto.toFixed(2) }}</td>
            <td class="text-right">@{{ detalle.valor_unitario.toFixed(2) }}</td>
            <td class="text-right">@{{ detalle.valor_venta.toFixed(2) }}</td>
            <td class="text-right">@{{ detalle.igv_monto_1.toFixed(2) }}</td>
            <td class="text-right">@{{ (detalle.precio_venta_unitario_monto * detalle.cantidad).toFixed(2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="form-group row">
        <label class="font-weight-bold col-4">Valor venta</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.total_valor_venta_neto.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Operaciones Gravadas</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.total_valor_venta_gravada_monto.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group mb-0 row">
        <label class="font-weight-bold col-4">Operaciones Exoneradas</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.total_valor_venta_exonerada_monto.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Operaciones Inafectas</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.total_valor_venta_inafecta_monto.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">IGV</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.sumatoria_igv_monto_1.toFixed(2) }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Importe total venta</label>
        <div class="col-8">
            <p class="text-right">S/ @{{ venta.importe_total_venta.toFixed(2) }}</p>
        </div>
    </div>
</div>
