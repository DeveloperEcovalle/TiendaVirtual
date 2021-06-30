@extends('intranet.layout')

@section('title', 'VENTAS')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-7 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-12 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>Gesti&oacute;n de ventas</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Ventas</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center bg-white" style="width: 50px"><i class="fas fa-calendar-alt fa-2x"></i></th>
                            <th class="bg-white">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" v-model="sPeriodo" v-on:change="ajaxListar()" v-cloak>
                                            <option>Diario</option>
                                            <option>Mensual</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" v-if="sPeriodo === 'Diario'">
                                        <select class="form-control" v-model="iDia" v-on:change="ajaxListar()" v-cloak>
                                            <option v-for="dia of lstDias">@{{ dia }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" v-model="iMes" v-on:change="ajaxListar()" v-cloak>
                                            <option value="0">ENE</option>
                                            <option value="1">FEB</option>
                                            <option value="2">MAR</option>
                                            <option value="3">ABR</option>
                                            <option value="4">MAY</option>
                                            <option value="5">JUN</option>
                                            <option value="6">JUL</option>
                                            <option value="7">AGO</option>
                                            <option value="8">SET</option>
                                            <option value="9">OCT</option>
                                            <option value="10">NOV</option>
                                            <option value="11">DIC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" v-model="iAnio" v-on:change="ajaxListar()" v-cloak>
                                            <option v-for="anio in lstAnios" :value="anio.value">@{{ anio.value }}</option>
                                        </select>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="bg-primary text-center">#</th>
                            <th class="bg-primary text-center">Tipo</th>
                            <th class="bg-primary text-center">C&oacute;digo</th>
                            <th class="bg-primary text-center">Documento</th>
                            <th class="bg-primary text-center">Cliente</th>
                            <th class="bg-primary text-right">Total</th>
                            <th class="bg-primary text-center">Estado</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="venta of lstVentas" v-bind:class="{selected: iIdSeleccionado === venta.id}" v-on:click="panelEditar(venta.id)" style="cursor: pointer;" v-cloak>
                            <td class="text-center">@{{ venta.id }}</td>
                            <td class="text-center">@{{ venta.tipo_compra }}</td>
                            <td class="text-center">@{{ venta.codigo }}</td>
                            <td class="text-center">@{{ venta.documento }}</td>
                            <td class="text-center">@{{ venta.cliente }}</td>
                            <td class="text-right">S/. @{{ (venta.subtotal + venta.delivery).toFixed(2) }}</td>
                            <td class="text-center" :class="{ 'bg-danger': venta.estado == 'POR ATENDER', 'bg-ecovalle': venta.estado == 'ENVIADO' || venta.estado == 'FACTURADO', 'bg-warning': venta.estado == 'RECHAZADO' || venta.estado == 'PEDIDO FALLIDO' }">@{{ venta.estado }}</td>
                        </tr>
                        <tr v-if="lstVentas.length === 0" v-cloak>
                            <td colspan="9" class="text-center">No hay datos para mostrar</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <div class="col-12 col-md-5 p-0" id="panel">
        </div>
    </div>
    <div class="row m-0 justify-content-center" v-else v-cloak>
        <div class="col-12 col-md-10 col-lg-8 pt-5">
            <h3 class="text-danger text-center font-bold">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                Ocurri&oacute; un error inesperado.&nbsp;
                Volver a cargar la p&aacute;gina deber&iacute;a solucionar el problema.<br>
                Si el error persiste, comun&iacute;quese con el administrador del sistema.
            </h3>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/intranet/ventas.js?cvcn=14"></script>
@endsection
