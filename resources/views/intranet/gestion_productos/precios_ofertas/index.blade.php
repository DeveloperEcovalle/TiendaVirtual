@extends('intranet.layout')

@section('title', 'PRECIOS Y OFERTAS')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-6 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-12 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>Gesti&oacute;n de productos</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Precios y Ofertas</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar producto por nombre">
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="bg-primary">Producto</th>
                            <th class="bg-primary text-right">Precio actual</th>
                            <th class="bg-primary text-right">Oferta vigente</th>
                            <th class="bg-primary text-right">Promoci&oacute;n vigente</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="producto of lstProductosFiltrados" v-bind:class="{selected: iIdSeleccionado === producto.id}" v-on:click="panelEditar(producto.id)" style="cursor: pointer;" v-cloak>
                            <td>@{{ producto.nombre_es }}</td>
                            <td class="text-right">
                                <span v-if="producto.precio_actual">S/ @{{ producto.precio_actual.monto.toFixed(2) }}</span>
                                <span v-else>-</span>
                            </td>
                            <td class="text-right">
                                <p class="m-0" v-if="producto.oferta_vigente">
                                    <span v-if="producto.oferta_vigente.monto">S/ @{{ producto.oferta_vigente.monto.toFixed(2) }}</span>
                                    <span v-if="producto.oferta_vigente.porcentaje">@{{ producto.oferta_vigente.porcentaje.toFixed(2) }}%</span>
                                </p>
                                <p class="m-0" v-else>-</p>
                            </td>
                            <td class="text-right">
                                <p class="m-0" v-if="producto.promocion_vigente">
                                    <span v-if="producto.promocion_vigente.monto">S/ @{{ producto.promocion_vigente.monto.toFixed(2) }}</span>
                                    <span v-if="producto.promocion_vigente.porcentaje">@{{ producto.promocion_vigente.porcentaje.toFixed(2) }}%</span>
                                </p>
                                <p class="m-0" v-else>-</p>
                            </td>
                        </tr>
                        <tr v-if="lstProductosFiltrados.length === 0" v-cloak>
                            <td colspan="4" class="text-center">No hay datos para mostrar</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="col-12 col-md-6 p-0" id="panel">
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
    <script src="/js/intranet/preciosOfertas.js?cvcn=14"></script>
@endsection
