@extends('intranet.layout_sidebar')

@section('title', 'QUIÉNES SOMOS')

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
                            <strong>Qui&eacute;nes somos</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-4">
                    <button class="btn btn-primary float-right mt-2" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <table class="table table-bordered table-hover" id="tblSecciones">
                    <thead>
                        <tr>
                            <th class="bg-primary text-center">Orden</th>
                            <th class="bg-primary">T&iacute;tulo y Contenido</th>
                            <th class="bg-primary text-center">Imagen</th>
                            <th class="bg-primary text-center">V&iacute;deo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="seccion of lstSecciones" v-bind:class="{selected: iIdSeleccionado === seccion.id}" v-on:click="panelEditar(seccion.id)" style="cursor: pointer;" v-cloak>
                            <td class="text-center">@{{ seccion.orden }}</td>
                            <td>
                                <p class="font-weight-bold mb-1">@{{ seccion.titulo_es }}</p>
                                <div v-html="seccion.contenido_es"></div>
                            </td>
                            <td class="w-25 text-center">
                                <i class="fas fa-eye-slash mt-3" v-if="seccion.ruta_imagen === null"></i>
                                <img class="w-50" v-bind:src="seccion.ruta_imagen" v-else>
                            </td>
                            <td class="w-25 text-center">
                                <i class="fas fa-video-slash mt-3" v-if="seccion.enlace_video === null"></i>
                                <iframe v-else width="100%" v-bind:height="seccion.enlace_video === null ? 0 : 180" v-bind:src="seccion.enlace_video" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </td>
                        </tr>
                        <tr v-if="lstSecciones.length === 0" v-cloak>
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
    <script src="/js/intranet/seccionesQuienesSomos.js?cvcn=14"></script>
@endsection
