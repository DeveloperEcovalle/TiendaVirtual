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
                            <strong>Clientes</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary float-right mt-2 d-none" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Buscar por nombres, apellidos o documentos de identidad" v-model="sBuscar">
                </div>
                <table class="table table-bordered table-hover" id="tblClientes">
                    <thead>
                        <tr>
                            <th class="bg-primary">Nombres / Raz&oacute;n social</th>
                            <th class="bg-primary">Apellido Paterno</th>
                            <th class="bg-primary">Apellido Materno</th>
                            <th class="bg-primary">Documento</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="cliente of lstClientesFiltrados" v-bind:class="{selected: iIdSeleccionado === cliente.id}" v-on:click="panelEditar(cliente.id)" style="cursor: pointer;" v-cloak>
                            <td>@{{ cliente.persona.nombres }}</td>
                            <td>@{{ cliente.persona.apellido_1 }}</td>
                            <td>@{{ cliente.persona.apellido_2 }}</td>
                            <td>@{{ cliente.persona.tipo_documento }} @{{ cliente.persona.documento }}</td>
                        </tr>
                        <tr v-if="lstClientesFiltrados.length === 0" v-cloak>
                            <td colspan="4" class="text-center">No hay datos para mostrar</td>
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
    <script src="/js/intranet/clientes.js?cvcn=14"></script>
@endsection
