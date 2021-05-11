@extends('intranet.layout')

@section('title', 'CLIENTES')

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
                            <a>Personas</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Proveedores</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary float-right mt-2" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Buscar por razón social o documentos de identidad" v-model="sBuscar">
                </div>
                <table class="table table-bordered table-hover" id="tblClientes">
                    <thead>
                        <tr>
                            <th class="bg-primary">Raz&oacute;n social</th>
                            <th class="bg-primary">Documentos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="proveedor of lstProveedoresFiltrados" v-bind:class="{selected: iIdSeleccionado === proveedor.id}" v-on:click="panelEditar(proveedor.id)" style="cursor: pointer;" v-cloak>
                            <td>@{{ proveedor.persona.nombres }}</td>
                            <td>
                                <p class="mb-0" v-for="documento in proveedor.persona.documentos">@{{ documento.tipo_documento.abreviatura + ' ' + documento.numero }}</p>
                            </td>
                        </tr>
                        <tr v-if="lstProveedoresFiltrados.length === 0" v-cloak>
                            <td colspan="2" class="text-center">No hay datos para mostrar</td>
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
    <script src="/js/intranet/proveedores.js?cvcn=14"></script>
@endsection
