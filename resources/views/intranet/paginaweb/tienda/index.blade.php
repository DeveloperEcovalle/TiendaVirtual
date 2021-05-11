@extends('intranet.layout_sidebar')

@section('title', 'TIENDA')

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
                            <strong>Tienda</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-7">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Imagen de portada</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevaImagen + ')' }" v-if="nuevaImagenPortada"></div>
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + pagina.ruta_imagen_portada + ')' }" v-else></div>
                                <form role="form" class="mt-4" id="frmEditarImagenPortada" v-on:submit.prevent="ajaxActualizarImagenPortada" v-cloak>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-3">Cambiar imagen de portada:</label>
                                        <div class="col-md-9">
                                            <div class="custom-file">
                                                <input id="aImagen" type="file" class="custom-file-input" name="imagen_de_portada" v-on:change="cambiarImagen">
                                                <label for="aImagen" class="custom-file-label">@{{ sNombreNuevaImagen }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-3">Enlace imagen de portada:</label>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="enlace_de_imagen_de_portada" autocomplete="off" v-model="pagina.enlace_imagen_portada">
                                        </div>
                                    </div>
                                    <div class="form-group text-right mb-0">
                                        <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizandoImagenPortada === 1">
                                            <span v-if="iActualizandoImagenPortada === 0">Guardar</span>
                                            <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Contenido en espa&ntilde;ol</h5>
                            </div>
                            <div class="ibox-content">
                                <div id="sContenidoEspanol"></div>
                                <div class="form-group mb-0 mt-2 text-right">
                                    <button class="btn btn-primary" :disabled="iActualizandoContenidoEspanol === 1" v-on:click="ajaxActualizarContenidoEspanol">
                                        <span v-if="iActualizandoContenidoEspanol === 0">Guardar</span>
                                        <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Contenido en ingl&eacute;s</h5>
                            </div>
                            <div class="ibox-content">
                                <div id="sContenidoIngles"></div>
                                <div class="form-group mb-0 mt-2 text-right">
                                    <button class="btn btn-primary" :disabled="iActualizandoContenidoIngles === 1" v-on:click="ajaxActualizarContenidoIngles">
                                        <span v-if="iActualizandoContenidoIngles === 0">Guardar</span>
                                        <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                                    </button>
                                </div>
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
    <script src="/js/intranet/tienda.js?cvcn=14"></script>
@endsection
