<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Precio de Env&iacute;o</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(precioEnvio.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Departamento <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select class="form-control" name="departamento" v-model="precioEnvio.departamento" required="required">
                    <option v-for="ubigeo in lstDepartamentos" :value="ubigeo.departamento">@{{ ubigeo.departamento }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Provincia</label>
            <div class="col-md-9">
                <select class="form-control" name="provincia" v-model="precioEnvio.provincia" autocomplete="off">
                    <option v-for="ubigeo in lstProvincias" :value="ubigeo.provincia">@{{ ubigeo.provincia }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Distrito</label>
            <div class="col-md-9">
                <select class="form-control" name="distrito" v-model="precioEnvio.distrito" autocomplete="off">
                    <option v-for="ubigeo in lstDistritos" :value="ubigeo.distrito">@{{ ubigeo.distrito }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Precio</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="precio" v-model="precioEnvio.precio" autocomplete="off">
            </div>
        </div>
        <div class="form-group mb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" :disabled="iActualizando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" :disabled="iActualizando === 1" v-cloak>
                <span v-if="iActualizando === 0">Guardar cambios</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
