<div class="modal fade" id="modalOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-ecovalle">
                <h5 class="modal-title"><b>PEDIDO COD: @{{ order.codigo }}</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-12">
                        <table class="w-100 table table-bordered table-hover" style="font-size: 12px;">
                            <thead class="bg-ecovalle-2">
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Descripci&oacute;n</th>
                                    <th>Valor Unitario</th>
                                    <th>Precio Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="detalle in order.detalles">
                                    <td>@{{ detalle.cantidad }} UND.</td>
                                    <td>@{{ detalle.producto.nombre_es }}</td>
                                    <td>S/. @{{ (detalle.precio_venta - detalle.promocion).toFixed(2) }}</td>
                                    <td>S/. @{{ ((detalle.precio_venta - detalle.promocion) * detalle.cantidad).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 col-lg-6"></div>
                    <div class="col-12 col-lg-6">
                        <table class="float-right">
                            <tbody>
                                <tr style="border-bottom: 1px solid #333333;">
                                    <td class="w-60" style="width: 60%;"><b>SubTotal</b></td>
                                    <td class="text-right">S/.</td>
                                    <td class="text-right"> @{{ (order.subtotal).toFixed(2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #333333;">
                                    <td class="w-60"><b>Descuento</b></td>
                                    <td class="text-right">S/.</td>
                                    <td class="text-right"> @{{ (order.descuento).toFixed(2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #333333;">
                                    <td class="w-60"><b>Delivery</b></td>
                                    <td class="text-right">S/.</td>
                                    <td class="text-right"> @{{ (order.delivery).toFixed(2) }}</td>
                                </tr>
                                <tr style="border-bottom: 1px solid #333333;">
                                    <td class="w-60"><b>Total</b></td>
                                    <td class="text-right">S/.</td>
                                    <td class="text-right"> @{{ (order.subtotal + order.delivery).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>