<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Agencia</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(agencia.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre" v-model="agencia.nombre" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Descripci&oacute;n</label>
            <div class="col-md-9">
                <textarea name="descripcion" rows="2" class="form-control" v-model="agencia.descripcion"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Estado <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select name="estado" class="form-control" v-model="agencia.estado">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="ANULADO">ANULADO</option>
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
</div>
