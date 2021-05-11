<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Tipo de comprobante</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(tipoComprobante.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre" v-model="tipoComprobante.nombre" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Comprobante SUNAT asociado</label>
            <div class="col-md-9">
                <select class="form-control" name="comprobante_sunat_asociado" v-model="tipoComprobante.sunat_01_codigo">
                    <option value="">Ninguno</option>
                    <option v-for="tipoComprobanteSunat in lstTiposComprobanteSunat" v-bind:value="tipoComprobanteSunat.codigo" v-cloak>@{{ tipoComprobanteSunat.descripcion }}</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iActualizando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iActualizando === 1" v-cloak>
                <span v-if="iActualizando === 0">Guardar cambios</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
    <hr>
    <form role="form" id="frmAgregarSerie" v-on:submit.prevent="ajaxInsertarSerie(tipoComprobante.id)">
        <div class="form-group mb-0">
            <label class="font-weight-bold">Agregar nueva serie:</label>
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <input type="text" placeholder="Serie *" class="form-control" name="serie" required="required" autocomplete="off">
            </div>
            <div class="col-md-3">
                <input type="text" placeholder="Inicio *" class="form-control" name="correlativo_inicio" required="required" autocomplete="off">
            </div>
            <div class="col-md-3">
                <input type="text" placeholder="L&iacute;mite" class="form-control" name="correlativo_limite" autocomplete="off">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary" v-bind:disabled="iInsertandoSerie === 1">
                    <span v-if="iInsertandoSerie === 0">Agregar <i class="fas fa-arrow-down"></i></span>
                    <span v-if="iInsertandoSerie === 1"><i class="fas fa-circle-notch fa-spin"></i></span>
                </button>
            </div>
        </div>
    </form>
    <div class="d-block">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <td class="bg-primary text-center">Serie</td>
                    <td class="bg-primary text-center">Correlativo actual</td>
                    <td class="bg-primary text-center">Correlativo l&iacute;mite</td>
                    <td class="bg-primary text-center">Eliminar</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="serie in tipoComprobante.series" v-cloak>
                    <td class="text-center">@{{ serie.valor }}</td>
                    <td class="text-center">@{{ serie.correlativo_actual }}</td>
                    <td class="text-center">
                        <span v-if="serie.correlativo_limite">@{{ serie.correlativo_limite }}</span>
                        <i class="fas fa-infinity" v-else></i>
                    </td>
                    <td class="text-center">
                        <a href="#" class="text-danger" v-bind:disabled="iEliminandoSerie === 1" v-on:click.prevent="ajaxEliminarSerie(tipoComprobante.id, serie.id)">
                            <i v-if="iEliminandoSerie === 0" class="fas fa-trash-alt"></i>
                            <i v-if="iSerieIdEliminar === serie.id" class="fas fa-circle-notch fa-spin"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
