<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Perfil</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre" required="required" autocomplete="off">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3" v-for="menu in lstMenus" v-cloak>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-primary p-2">@{{ menu.nombre }}</li>
                    <li class="list-group-item p-2" v-for="permiso in menu.permisos">
                        <label class="mb-0"><input type="checkbox" name="permisos[]" v-bind:value="permiso.id"> @{{ permiso.nombre }}</label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="form-group pb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
