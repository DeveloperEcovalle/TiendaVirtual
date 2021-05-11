<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Precio de env&iacute;o</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Departamento <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select class="form-control" name="departamento" v-model="formData.sDepartamento" required="required">
                    <option v-for="ubigeo in lstDepartamentos" :value="ubigeo.departamento">@{{ ubigeo.departamento }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Provincia</label>
            <div class="col-md-9">
                <select class="form-control" name="provincia" v-model="formData.sProvincia" autocomplete="off">
                    <option v-for="ubigeo in lstProvincias" :value="ubigeo.provincia">@{{ ubigeo.provincia }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Distrito</label>
            <div class="col-md-9">
                <select class="form-control" name="distrito" v-model="formData.sDistrito" autocomplete="off">
                    <option v-for="ubigeo in lstDistritos" :value="ubigeo.distrito">@{{ ubigeo.distrito }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Precio</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="precio" autocomplete="off">
            </div>
        </div>
        <div class="form-group mb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
