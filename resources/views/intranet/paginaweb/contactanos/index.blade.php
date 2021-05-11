@extends('intranet.layout_sidebar')

@section('title', 'CONTÁCTANOS')

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
                            <strong>Contáctanos</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Imagen de portada</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevaImagen + ')' }" v-if="nuevaImagenContactanos" v-cloak></div>
                                <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + empresa.ruta_imagen_contactanos + ')' }" v-else v-cloak></div>
                                <form role="form" class="mt-4" id="frmEditarImagenContactanos" v-on:submit.prevent="ajaxActualizarImagenContactanos" v-cloak>
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-3">Cambiar imagen de portada:</label>
                                        <div class="col-md-6">
                                            <div class="custom-file">
                                                <input id="aImagen" type="file" class="custom-file-input" name="imagen_de_portada" v-on:change="cambiarImagen" required="required">
                                                <label for="aImagen" class="custom-file-label">@{{ sNombreNuevaImagen }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoImagenContactanos === 1">
                                                <span v-if="iActualizandoImagenContactanos === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Enlace del Mapa</h5>
                            </div>
                            <div class="ibox-content">
                                <div v-html="sEnlaceMapa"></div>
                                <form role="form" class="mt-4" id="frmEditarEnlaceMapa" v-on:submit.prevent="ajaxActualizarEnlaceMapa">
                                    <div class="form-group mb-0 row">
                                        <label class="col-form-label col-md-3">Cambiar enlace del mapa:</label>
                                        <div class="col-md-6">
                                            <input class="form-control" name="nuevo_enlace_del_mapa" v-model="sNuevoEnlaceMapa" autocomplete="off" required="required">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoEnlaceMapa === 1" v-cloak>
                                                <span v-if="iActualizandoEnlaceMapa === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 float-md-right">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Direcci&oacute;n de la empresa</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarDireccion" v-on:submit.prevent="ajaxActualizarDireccion">
                                    <div class="form-group mb-0 row">
                                        <div class="col-md-9">
                                            <input class="form-control" name="direccion_de_la_empresa" v-model="empresa.direccion" placeholder="Ingrese la direcci&oacute;n de la empresa">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoDireccion === 1" v-cloak>
                                                <span v-if="iActualizandoDireccion === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Redes Sociales</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarRedesSociales" v-on:submit.prevent="ajaxActualizarRedesSociales">
                                    <div class="form-group mb-0 row justify-content-end">
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-facebook-f"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_facebook" name="enlace_de_facebook" placeholder="Enlace de P&aacute;gina de Facebook" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-instagram"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_instagram" name="enlace_de_instagram" placeholder="Enlace de Perfil de Instagram" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-youtube"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_youtube" name="enlace_de_youtube" placeholder="Enlace de Canal de YouTube" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-linkedin-in"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_linkedin" name="enlace_de_linkedin" placeholder="Enlace de P&aacute;gina de LinkedIn" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-twitter"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_twitter" name="enlace_de_twitter" placeholder="Enlace de P&aacute;gina de Twitter" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group m-b">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-addon"><i class="fab fa-tiktok"></i></span>
                                                </div>
                                                <input type="text" v-model="empresa.enlace_tiktok" name="enlace_de_tiktok" placeholder="Enlace de P&aacute;gina de TikTok" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoRedesSociales === 1" v-cloak>
                                                <span v-if="iActualizandoRedesSociales === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Correo de recepci&oacute;n de mensajes</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarCorreo" v-on:submit.prevent="ajaxActualizarCorreoContactanos">
                                    <div class="form-group mb-0 row">
                                        <div class="col-md-9">
                                            <input class="form-control" name="correo_de_recepcion_para_contactanos" v-model="empresa.correo_contactanos" placeholder="Ingrese el correo donde se recibir&aacute;n los mensajes enviados desde Cont&aacute;ctanos">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iActualizandoCorreo === 1" v-cloak>
                                                <span v-if="iActualizandoCorreo === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Tel&eacute;fonos</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmInsertarTelefono" v-on:submit.prevent="ajaxInsertarTelefono">
                                    <div class="form-group mb-0 row">
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="col-md-3 pt-2">
                                                    <a href="#" class="mr-2" data-toggle="modal" data-target="#modalIcono">Cambiar &iacute;cono</a>
                                                    <i :class="sIcono"></i>
                                                    <input type="hidden" name="icono" v-model="sIcono">
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control" placeholder="Ingrese un teléfono" name="telefono" v-model="sTelefono" autocomplete="off" required="required">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="mt-2 mb-0"><input type="checkbox" name="whatsapp_de_la_empresa"> WhatsApp de la empresa</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-block btn-primary" v-bind:disabled="iInsertandoTelefono === 1" v-cloak>
                                                <span v-if="iInsertandoTelefono === 0">Guardar</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-bordered mt-2">
                                    <thead>
                                        <th class="bg-primary text-center">&Iacute;cono</th>
                                        <th class="bg-primary">N&uacute;mero</th>
                                        <th class="bg-primary text-center">WhatsApp de la empresa</th>
                                        <th class="bg-primary text-center">Eliminar</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="telefono of empresa.telefonos" v-if="empresa.telefonos && empresa.telefonos.length > 0" v-cloak>
                                            <td class="text-center"><i :class="telefono.icono"></i></td>
                                            <td>@{{ telefono.numero }}</td>
                                            <td class="text-center"><i class="fas fa-check text-navy" v-if="telefono.whatsapp === 1"></i></td>
                                            <td class="text-center"><a href="#" v-on:click="ajaxEliminarTelefono(telefono.id)"><i class="fas fa-trash-alt text-danger"></i></a></td>
                                        </tr>
                                        <tr v-if="empresa.telefonos.length === 0" v-cloak>
                                            <td colspan="4" class="text-center">
                                                No hay tel&eacute;fonos registrados.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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

    <div class="modal" id="modalIcono" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5>Cambiar &iacute;cono de nuevo tel&eacute;fono</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-phone')"><i class="fas fa-phone fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-phone-alt')"><i class="fas fa-phone-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-phone-square')"><i class="fas fa-phone-square fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-phone-square-alt')"><i class="fas fa-phone-square-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-address-book')"><i class="fas fa-address-book fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('far fa-address-book')"><i class="far fa-address-book fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-address-card')"><i class="fas fa-address-card fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('far fa-address-card')"><i class="far fa-address-card fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-comment')"><i class="fas fa-comment fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('far fa-comment')"><i class="far fa-comment fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-comment-alt')"><i class="fas fa-comment-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('far fa-comment-alt')"><i class="far fa-comment-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-comments')"><i class="fas fa-comments fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('far fa-comments')"><i class="far fa-comments fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-inbox')"><i class="fas fa-inbox fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-microphone')"><i class="fas fa-microphone fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-microphone-alt')"><i class="fas fa-microphone-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-mobile')"><i class="fas fa-mobile fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fas fa-mobile-alt')"><i class="fas fa-mobile-alt fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fab fa-whatsapp')"><i class="fab fa-whatsapp fa-3x"></i></a></div>
                        <div class="col-md-2 text-center mb-4"><a href="#" v-on:click.prevent="setsIcono('fab fa-whatsapp-square')"><i class="fab fa-whatsapp-square fa-3x"></i></a></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span>Click en un &iacute;cono para establecerlo</span>
                    <button class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/intranet/contactanos.js?cvcn=14"></script>
@endsection
