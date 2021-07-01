<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Nuevo Ubigeo</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
            <select class="form-control" name="departamento" v-model="departamento" required>
                <option value="">Seleccionar departamento</option>
                <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
            </select>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Provincia <span class="text-danger">*</span></label>
            <select class="form-control" name="provincia" required>
                <option>Seleccionar provincia</option>
                <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
            </select>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Distrito <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="distrito" required="required" autocomplete="off">
        </div>
        <div class="form-group row">
            <div class="col-md-6">
                <label class="font-weight-bold">Tarifa <span class="text-danger">*</span></label>
                <input type="number" step="0.1" class="form-control" name="tarifa" required="required" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="font-weight-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" id="estado" class="form-control">
                    <option value="ACTIVO" selected>ACTIVO</option>
                    <option value="ANULADO">ANULADO</option>
                </select>
            </div>
        </div>
        <div class="form-group pb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar cambios</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
