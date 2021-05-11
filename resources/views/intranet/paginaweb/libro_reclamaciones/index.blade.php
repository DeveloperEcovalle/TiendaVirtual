@extends('intranet.layout_sidebar')

@section('title', 'LIBRO RECLAMACIONES')

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
                            <strong>Libro de Reclamaciones</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Im&aacute;gen de portada</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevaImagen + ')' }" v-if="nuevaImagenLibro" v-cloak></div>
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + empresa.ruta_imagen_libro + ')' }" v-else v-cloak></div>
                                <form role="form" class="mt-4" id="frmEditarImagenLibro" v-on:submit.prevent="ajaxActualizarImagenLibro" v-cloak>
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-3">Cambiar portada:</label>
                                        <div class="col-md-6">
                                            <div class="custom-file">
                                                <input id="aImagen" type="file" class="custom-file-input" name="imagen_de_portada" v-on:change="cambiarImagen" required="required">
                                                <label for="aImagen" class="custom-file-label">@{{ sNombreNuevaImagen }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoImagenLibro === 1">
                                                <span v-if="iActualizandoImagenLibro === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Datos de la empresa</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" class="mt-4" id="frmEditarRuc" v-on:submit.prevent="ajaxActualizarRuc" v-cloak>
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-3">Ruc</label>
                                        <div class="form-group col-md-6 mb-1">
                                            <input id="ruc_empresa" minlength="11" v-model="empresa.ruc_empresa" maxlength="11" type="text" class="form-control m-0" name="ruc_empresa" required="required">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoRuc === 1">
                                                <span v-if="iActualizandoRuc === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="hr-line-dashed"></div>
                                <form role="form" class="mt-4" id="frmEditarRazon" v-on:submit.prevent="ajaxActualizarRazon" v-cloak>
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-12">Raz&oacute;n social</label>
                                        <div class="form-group col-md-9 mb-1">
                                            <textarea rows="2" class="form-control m-0" v-model="empresa.razon_social" name="razon_social" id="razon_social" required="required"></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoRazon === 1">
                                                <span v-if="iActualizandoRazon === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Mensaje de Libro de reclamaciones</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarMensaje" v-on:submit.prevent="ajaxActualizarMensaje" v-cloak>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Mensaje Español</label>
                                            <textarea rows="4" class="form-control m-0" v-model="empresa.mensaje_libro_reclamaciones_es" name="mensaje_libro_reclamaciones_es" id="mensaje_libro_reclamaciones_es" required="required"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="col-form-label">Mensaje Inglés</label>
                                            <textarea rows="4" class="form-control m-0" v-model="empresa.mensaje_libro_reclamaciones_en" name="mensaje_libro_reclamaciones_en" id="mensaje_libro_reclamaciones_en" required="required"></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoMensaje === 1">
                                                <span v-if="iActualizandoMensaje === 0">Guardar</span>
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
    </div>
@endsection

@section('js')
<script src="/js/intranet/libroReclamaciones.js?cvcn=14"></script>
@endsection
