<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Certificaci&oacute;n</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(certificacion.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Orden <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="orden" v-model="certificacion.orden" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre en Espa&ntilde;ol <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_es" v-model="certificacion.nombre_es" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre en Ingl&eacute;s <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_en" v-model="certificacion.nombre_en" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Imagen <span class="text-danger">*</span></label>
            <div class="col-md-9 text-center">
                <img v-if="imagen" v-bind:src="sContenidoArchivo" class="w-50 mb-2">
                <img v-else v-bind:src="certificacion.ruta_imagen" class="w-50 mb-2">
                <div class="custom-file">
                    <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)">
                    <label for="aImagen" class="custom-file-label text-left">@{{ sNombreArchivo }}</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Descripci&oacute;n en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <div id="sDescripcionES"></div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Descripci&oacute;n en ingl&eacute;s <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <div id="sDescripcionEN"></div>
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
