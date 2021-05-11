@extends('intranet.layout')

@section('title', 'FACTURACIÓN ELECTRÓNICA')

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
                            <a>Configuraci&oacute;n</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Facturaci&oacute;n Electr&oacute;nica</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row justify-content-center">

                    <div class="col-12 col-md-6 float-md-right">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Usuario y Clave SOL</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarUsuarioClaveSOL" v-on:submit.prevent="ajaxActualizarUsuarioClaveSOL">
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Usuario SOL</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="usuario_sol" v-model="empresa.usuario_sol" placeholder="Usuario SOL" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Clave SOL</label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" name="clave_sol" v-model="empresa.clave_sol" placeholder="Clave SOL" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 text-right">
                                        <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizandoUsuarioClaveSOL === 1" v-cloak>
                                            <span v-if="iActualizandoUsuarioClaveSOL === 0">Guardar cambios</span>
                                            <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Certificado Digital</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditarCertificadoDigital" v-on:submit.prevent="ajaxActualizarCertificadoDigital">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Nuevo certificado:</label>
                                        <div class="col-md-8">
                                            <div class="custom-file" v-cloak>
                                                <input id="aCertificado" type="file" class="custom-file-input" name="certificado_digital" accept=".pfx" v-on:change="cambiarCertificado" v-bind:required="empresa.ruta_certificado_digital === null">
                                                <label for="aCertificado" class="custom-file-label">@{{ sNombreNuevoCertificado }}</label>
                                            </div>
                                            <small v-cloak v-if="empresa.ruta_certificado_digital" class="text-danger">El sistema ya tiene registrado un certificado digital, si desea reemplazarlo, adjunte un nuevo archivo.</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Contrase&ntilde;a del certificado digital:</label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" v-model="empresa.contrasena_certificado_digital" name="contrasena_del_certificado_digital" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label">Vigencia:</label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" v-model="empresa.fecha_inicio_certificado_digital" name="fecha_de_inicio" placeholder="yyyy-mm-dd" required="required">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" v-model="empresa.fecha_limite_certificado_digital" name="fecha_limite" placeholder="yyyy-mm-dd" required="required">
                                        </div>
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizandoCertificadoDigital === 1" v-cloak>
                                            <span v-if="iActualizandoCertificadoDigital === 0">Guardar cambios</span>
                                            <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                        </button>
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
    <script src="/js/intranet/facturacionElectronica.js?cvcn=14"></script>
@endsection
