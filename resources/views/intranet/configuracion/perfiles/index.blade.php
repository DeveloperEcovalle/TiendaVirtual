@extends('intranet.layout')

@section('title', 'PERFILES DE USUARIO')

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
                            <a>Configuraci&oacute;n</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Perfiles de Usuario</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary float-right mt-2" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <table class="table table-bordered table-hover" id="tblPerfiles">
                    <thead>
                        <tr>
                            <th class="bg-primary text-center">ID</th>
                            <th class="bg-primary">Nombre</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="perfil of lstPerfiles" v-bind:class="{selected: iIdSeleccionado === perfil.id}" v-on:click="panelEditar(perfil.id)" style="cursor: pointer;" v-cloak>
                            <td class="text-center">@{{ perfil.id_interno }}</td>
                            <td>@{{ perfil.nombre }}</td>
                        </tr>
                        <tr v-if="lstPerfiles.length === 0" v-cloak>
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
    <script src="/js/intranet/perfiles.js?cvcn=14"></script>
@endsection
