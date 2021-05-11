<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Categor&iacute;a de producto</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label>Nombre en espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_es" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label>Nombre en ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label>Imagen <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)" required="required">
                <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
            </div>
        </div>
        <div class="form-group">
            <label>Imagen de selecci&oacute;n <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img v-if="imagenSeleccion" v-bind:src="sContenidoArchivoSeleccion" class="img-fluid mb-2">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagenSeleccion" type="file" class="custom-file-input" name="imagen_de_seleccion" v-on:change="cambiarImagenSeleccion($event)" required="required">
                <label for="aImagenSeleccion" class="custom-file-label">@{{ sNombreArchivoSeleccion }}</label>
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
