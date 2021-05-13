@extends('intranet.layout_sidebar')

@section('title', 'CARRITO')

@section('head')
    <link href="/css/website.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-8 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-8 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>P&aacute;gina web</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Gesti&oacute;n Carrito</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft" v-cloak>
                <div class="form-group">
                    <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por departamento, provincia ó distrito">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tblBlogs">
                        <thead>
                            <tr>
                                <th class="bg-primary">Departamento</th>
                                <th class="bg-primary">Provincia</th>
                                <th class="bg-primary">Distrito</th>
                                <th class="bg-primary">Tarifa</th>
                                <th class="bg-primary">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="ubigeo  of lstUbigeoFiltrados" v-bind:class="{selected: iIdSeleccionado === ubigeo.id}" v-on:click="panelEditar(ubigeo.id)" style="cursor: pointer;" v-cloak>
                                <td>@{{ ubigeo.departamento }}</td>
                                <td>@{{ ubigeo.provincia }}</td>
                                <td>@{{ ubigeo.distrito }}</td>
                                <td>@{{ ubigeo.tarifa ? 'S/. '+ubigeo.tarifa.toFixed(2) : '' }}</td>
                                <td>@{{ ubigeo.estado }}</td>
                            </tr>
                            <tr v-if="lstUbigeo.length === 0" v-cloak>
                                <td colspan="5" class="text-center">No hay datos para mostrar</td>
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
    <script src="/js/intranet/carrito.js?cvcn=14"></script>
@endsection
