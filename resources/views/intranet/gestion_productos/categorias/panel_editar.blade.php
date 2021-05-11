<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Categor&iacute;a</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(categoria.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <label>Nombre en espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_es" v-model="categoria.nombre_es" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label>Nombre en ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en" v-model="categoria.nombre_en" autocomplete="off" required="required">
        </div>
        <div class="form-group">
            <label>Cambiar Imagen <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2">
                    <img v-else v-bind:src="categoria.ruta_imagen" class="img-fluid mb-2">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)">
                <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
            </div>
        </div>
        <div class="form-group">
            <label>Cambiar Imagen de selecci&oacute;n <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img v-if="imagenSeleccion" v-bind:src="sContenidoArchivoSeleccion" class="img-fluid mb-2">
                    <img v-else v-bind:src="categoria.ruta_imagen_hover" class="img-fluid mb-2">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagenSeleccion" type="file" class="custom-file-input" name="imagen_de_seleccion" v-on:change="cambiarImagenSeleccion($event)">
                <label for="aImagenSeleccion" class="custom-file-label">@{{ sNombreArchivoSeleccion }}</label>
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
