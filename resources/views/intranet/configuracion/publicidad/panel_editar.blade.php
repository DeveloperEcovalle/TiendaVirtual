<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Publicidad</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(publicidad.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <label class="font-weight-bold"><input type="checkbox" name="estado" v-bind:checked="publicidad.estado == 1" v-on:change="changeActivo"> Activo</label>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Imagen</label>
            <img v-bind:src="publicidad.ruta" class="img-fluid">
        </div>
        <div class="row">
            <div class="form-group col-12 col-lg-6">
                <label class="font-weight-bold">Fecha inicio</label>
                <input type="date" class="form-control" v-model="publicidad.f_inicio" name="f_inicio" required>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label class="font-weight-bold">Fecha fin</label>
                <input type="date" class="form-control" v-model="publicidad.f_fin" name="f_fin" required>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Enlace</label>
            <input type="text" class="form-control" name="enlace" v-model="publicidad.enlace" autocomplete="off">
            <small>Ingresa a qu&eacute; direcci&oacute;n quieres que te lleve este publicidad al hacer click en &eacute;l.</small>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Descripci&oacute;n</label>
            <textarea class="form-control" name="descripcion" rows="4" v-model="publicidad.descripcion"></textarea>
            <small>Esta descripci&oacute;n se mostrar&aacute; al usuario en la parte inferior de la imagen.</small>
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
