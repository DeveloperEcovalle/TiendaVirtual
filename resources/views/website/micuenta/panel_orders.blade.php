<div class="col-12 p-4" style="background-color: #6BBD99;">
    <div class="form-group">
        <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por c&oacute;digo">
    </div>
    <div class="table-responsive" style="height: 500px;overflow: auto;">
        <table class="table table-bordered table-hover" id="tblOrders" style="font-size: 12px;">
            <thead class="bg-ecovalle">
                <tr>
                    <th class="text-center">C&oacute;digo</th>
                    <th class="text-center">Tipo Compra</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Total</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr v-for="order of lstOrdersFiltrado" v-bind:class="{selected: iIdSeleccionado === order.id}" v-on:click="panelShow(order.id)" style="cursor: pointer;" v-cloak>
                    <th class="text-center w-10">@{{ order.codigo }}</th>
                    <td class="text-center">@{{ order.tipo_compra }}</td>
                    <td class="text-center">@{{ order.estado.estado }}</td>
                    <td class="text-center">S/. @{{ (order.subtotal + order.delivery).toFixed(2) }}</td>
                    <td class="text-center"><a :href="'/mi-cuenta/ajax/download/'+order.codigo" class="btn btn-sm btn-outline-info btn-block"><i class="fa fa-download"></i></a></td>
                </tr>
                <tr v-if="lstOrdersFiltrado.length === 0" v-cloak>
                    <td colspan="5" class="text-center" v-if="iCargandoOrders === 0">No hay pedidos para mostrar</td>
                    <td colspan="5" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando pedidos</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>