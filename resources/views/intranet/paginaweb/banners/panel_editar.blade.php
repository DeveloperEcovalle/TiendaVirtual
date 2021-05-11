<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Banner</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(banner.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <label class="font-weight-bold"><input type="checkbox" name="activo" v-bind:checked="banner.activo === 1" v-on:change="changeActivo"> Activo</label>
        </div>

        <div class="form-group">
            <label class="font-weight-bold">Orden <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="orden" v-model="banner.orden" autocomplete="off" required="required">
            <small>Ingrese el n&uacute;mero que indica en qu&eacute; orden aparecer&aacute; este banner.</small>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Imagen</label>
            <img v-bind:src="banner.ruta_imagen" class="img-fluid">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Enlace</label>
            <input type="text" class="form-control" name="enlace" v-model="banner.enlace" autocomplete="off">
            <small>Ingresa a qu&eacute; direcci&oacute;n quieres que te lleve este banner al hacer click en &eacute;l.</small>
        </div>
        <div class="form-group">
            <label class="font-weight-bold"><input type="checkbox" name="banner_en_medio_de_la_pagina" v-bind:checked="banner.medio === 1" v-on:change="changeMedio"> Banner en medio de la p&aacute;gina</label>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Descripci&oacute;n</label>
            <textarea class="form-control" name="descripcion" rows="4" v-model="banner.descripcion"></textarea>
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
