<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Banner</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label class="font-weight-bold">Imagen <span class="text-danger">*</span></label>
            <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2">
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" v-on:change="cambiarImagen($event)" required="required">
                <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Enlace</label>
            <input type="text" class="form-control" name="enlace" autocomplete="off">
            <small>Ingresa a qu&eacute; direcci&oacute;n quieres que te lleve este banner al hacer click en &eacute;l.</small>
        </div>
        <div class="form-group">
            <label class="font-weight-bold"><input type="checkbox" name="banner_en_medio_de_la_pagina"> Banner en medio de la p&aacute;gina</label>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Descripci&oacute;n</label>
            <textarea class="form-control" name="descripcion" rows="4"></textarea>
            <small>Esta descripci&oacute;n se mostrar&aacute; al usuario en la parte inferior de la imagen.</small>
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
