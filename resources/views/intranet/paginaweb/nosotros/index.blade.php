@extends('intranet.layout_sidebar')

@section('title', 'QUIÉNES SOMOS')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-12 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>P&aacute;gina web</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Nosotros</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Imagen de portada</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevaImagen + ')' }" v-if="nuevaImagenPortada"></div>
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + empresa.ruta_imagen_portada + ')' }" v-else></div>
                                <form role="form" class="mt-4" id="frmEditarImagenPortada" v-on:submit.prevent="ajaxActualizarImagenPortada" v-cloak>
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-3">Cambiar imagen de portada:</label>
                                        <div class="col-md-6">
                                            <div class="custom-file">
                                                <input id="aImagen" type="file" class="custom-file-input" name="imagen_de_portada" v-on:change="cambiarImagen" required="required">
                                                <label for="aImagen" class="custom-file-label">@{{ sNombreNuevaImagen }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoImagenPortada === 1">
                                                <span v-if="iActualizandoImagenPortada === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 p-0" id="panel">
        </div>
    </div>
    <div class="row m-0 justify-content-center" v-else>
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
    <script src="/js/intranet/nosotros.js?cvcn=14"></script>
@endsection
