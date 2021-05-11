@extends('intranet.layout')

@section('title', 'VENTAS')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-8 p-0">
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
                <table class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="bg-primary text-center">#</th>
                        <th class="bg-primary">Tipo</th>
                        <th class="bg-primary text-center">Serie</th>
                        <th class="bg-primary text-center">Número</th>
                        <th class="bg-primary">Cliente</th>
                        <th class="bg-primary text-right">Total</th>
                        <th class="bg-primary text-right">Valor venta</th>
                        <th class="bg-primary text-right">IGV</th>
                        <th class="bg-primary text-center">Enviado a SUNAT</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white">
                    <tr v-for="venta of lstVentas" v-bind:class="{selected: iIdSeleccionado === venta.id}" v-on:click="panelEditar(venta.id)" style="cursor: pointer;" v-cloak>
                        <td class="text-center">@{{ venta.id_interno }}</td>
                        <td>@{{ venta.tipo_comprobante.nombre }}</td>
                        <td class="text-center">@{{ venta.serie_comprobante }}</td>
                        <td class="text-center">@{{ venta.nro_comprobante }}</td>
                        <td>@{{ venta.razon_social_cliente }}</td>
                        <td class="text-right">@{{ venta.importe_total_venta.toFixed(2) }}</td>
                        <td class="text-right">@{{ venta.total_valor_venta_neto.toFixed(2) }}</td>
                        <td class="text-right">@{{ venta.sumatoria_igv_monto_1.toFixed(2) }}</td>
                        <td class="text-center">
                            <span class="text-navy font-weight-bold" v-if="venta.sunat_enviado">SI</span>
                            <span class="text-danger font-weight-bold" v-else>NO</span>
                        </td>
                    </tr>
                    <tr v-if="lstVentas.length === 0" v-cloak>
                        <td colspan="9" class="text-center">No hay datos para mostrar</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="col-12 col-md-4 p-0" id="panel">
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
