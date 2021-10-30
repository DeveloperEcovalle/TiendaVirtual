<div class="col-12 p-4" style="background-color: #6BBD99;">
    <div class="form-group">
        <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por c&oacute;digo">
    </div>
    <div class="table-responsive" style="height: 500px;overflow: auto;">
        <table class="table table-bordered table-hover" id="tblOrders" style="font-size: 14px;">
            <thead class="bg-ecovalle">
                <tr>
                    <th class="text-center">C&oacute;digo</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Pago</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Opciones</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr v-for="order of lstOrdersFiltrado" style="cursor: pointer;" v-cloak>
                    <th class="text-center w-10">@{{ order.codigo }}</th>
                    <td class="text-center">@{{ order.estado.estado }}</td>
                    <td class="text-center">
                        <span v-if="order.estado_pago == '1'" class="badge badge-success">
                            PAGADO
                        </span>
                        <span v-else class="badge badge-warning">
                            POR PAGAR
                        </span>
                    </td>
                    <td class="text-center">S/. @{{ (order.subtotal + order.delivery).toFixed(2) }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-info" v-on:click="panelShow(order.id)" title="Ver"><i class="fa fa-eye"></i></button>
                        <a :href="'/mi-cuenta/ajax/download/'+order.codigo" class="btn btn-sm btn-outline-primary" title="Descargar"><i class="fa fa-download"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-success" v-if="order.estado_pago == '0'" title="Pagar" v-on:click="mostrarModalPago(order.id)"><i class="fa fa-money"></i></button>
                    </td>
                </tr>
                <tr v-if="lstOrdersFiltrado.length === 0" v-cloak>
                    <td colspan="5" class="text-center" v-if="iCargandoOrders === 0">No hay pedidos para mostrar</td>
                    <td colspan="5" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando pedidos</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
