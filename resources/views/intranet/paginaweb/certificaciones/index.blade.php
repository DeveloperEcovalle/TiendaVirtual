@extends('intranet.layout_sidebar')

@section('title', 'CERTIFICACIONES')

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
                            <strong>Certificaciones</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary float-right mt-2" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <table class="table table-bordered table-hover" id="tblCertificaciones">
                    <thead>
                        <tr>
                            <th class="bg-primary text-center">Orden</th>
                            <th class="bg-primary">Nombre y Descripci&oacute;n</th>
                            <th class="bg-primary text-center">Imagen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="certificacion of lstCertificaciones" v-bind:class="{selected: iIdSeleccionado === certificacion.id}" v-on:click="panelEditar(certificacion.id)" style="cursor: pointer;" v-cloak>
                            <td class="text-center">@{{ certificacion.orden }}</td>
                            <td>
                                <p class="font-weight-bold mb-1">@{{ certificacion.nombre_es }}</p>
                                <div v-html="certificacion.descripcion_es"></div>
                            </td>
                            <td class="w-25 text-center">
                                <img class="w-50" v-bind:src="certificacion.ruta_imagen">
                            </td>
                        </tr>
                        <tr v-if="lstCertificaciones.length === 0" v-cloak>
                            <td colspan="3" class="text-center">No hay datos para mostrar</td>
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
    <script src="/js/intranet/certificaciones.js?cvcn=14"></script>
@endsection
