<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Perfil</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(perfil.id)" v-bind:disabled="iEliminando === 1" v-cloak>
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
                <input type="text" class="form-control" name="nombre" v-model="perfil.nombre" required="required" autocomplete="off">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3" v-for="menu in lstMenus" v-cloak>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-primary p-2">@{{ menu.nombre }}</li>
                    <li class="list-group-item p-2" v-for="permiso in menu.permisos">
                        <label class="mb-0"><input type="checkbox" name="permisos[]" v-model="lstPermisosSeleccionados" v-bind:value="permiso.id"> @{{ permiso.nombre }}</label>
                    </li>
                </ul>
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
