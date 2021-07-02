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
                            <strong>Recepci&oacute;n</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row justify-content-center">

                    <div class="col-12 col-md-6 float-md-right">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5 style="text-transform: uppercase;">Tel&eacute;fono y correo electr&oacute;nico (PEDIDOS)</h5>
                            </div>
                            <div class="ibox-content">
                                <form role="form" id="frmEditar" v-on:submit.prevent="ajaxActualizar">
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Correo electr&oacute;nico - 1</label>
                                        <div class="col-md-8">
                                            <input type="email" class="form-control" name="correo_pedidos" v-model="empresa.correo_pedidos" placeholder="Email" required="required" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Tel&eacute;fono - 1</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="telefono_pedidos" v-model="empresa.telefono_pedidos" placeholder="N&uacute;mero de celular" required="required" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Correo electr&oacute;nico - 2</label>
                                        <div class="col-md-8">
                                            <input type="email" class="form-control" name="correo_pedidos_1" v-model="empresa.correo_pedidos_1" placeholder="Email" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-4">Tel&eacute;fono - 2</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" name="telefono_pedidos_1" v-model="empresa.telefono_pedidos_1" placeholder="N&uacute;mero de celular" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 text-right">
                                        <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizando === 1" v-cloak>
                                            <span v-if="iActualizando === 0">Guardar cambios</span>
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
    <script src="/js/intranet/recepcion.js?cvcn=14"></script>
@endsection
