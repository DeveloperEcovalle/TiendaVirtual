@extends('intranet.layout')

@section('title', 'CONTROL DE STOCK')

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
                            <a>Gesti&oacute;n de inventario</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Control de stock</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar producto por nombre">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="bg-primary">Producto</th>
                                <th class="bg-primary text-right">Stock m&iacute;nimo</th>
                                <th class="bg-primary text-right">Stock actual</th>
                                <th class="bg-primary text-right">Pendiente de despacho</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="producto of lstProductosFiltrados" v-bind:class="{selected: iIdSeleccionado === producto.id}" v-on:click="panelEditar(producto.id)" style="cursor: pointer;" v-cloak>
                                <td>@{{ producto.nombre_es }}</td>
                                <td class="text-right">@{{ producto.stock_minimo }}</td>
                                <td class="text-right" :class="{ 'bg-danger': producto.stock_actual < producto.stock_minimo
                                , 'bg-warning': producto.stock_actual === producto.stock_minimo
                                , 'bg-success': producto.stock_actual > producto.stock_minimo }">
                                    @{{ producto.stock_actual }}
                                </td>
                                <td class="text-right">@{{ producto.stock_separado }}</td>
                            </tr>
                            <tr v-if="lstProductosFiltrados.length === 0" v-cloak>
                                <td colspan="4" class="text-center">No hay datos para mostrar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
    <script src="/js/intranet/controlStock.js?cvcn=14"></script>
@endsection
