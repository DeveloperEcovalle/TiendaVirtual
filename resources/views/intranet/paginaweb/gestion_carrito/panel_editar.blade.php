<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Ubigeo</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <label class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="departamento" v-model="ubigeo.departamento" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Provincia <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="provincia" v-model="ubigeo.provincia" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Distrito <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="distrito" v-model="ubigeo.distrito" required="required" autocomplete="off">
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="font-weight-bold">Tarifa <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" name="tarifa" v-model="ubigeo.tarifa" required="required" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="font-weight-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" id="estado" class="form-control" v-model="ubigeo.estado">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="ANULADO">ANULADO</option>
                </select>
            </div>
        </div>
        <div class="form-group pb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iActualizando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iActualizando === 1" v-cloak>
                <span v-if="iActualizando === 0">Guardar cambios</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
