<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Certificaci&oacute;n</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre en Espa&ntilde;ol <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_es" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre en Ingl&eacute;s <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_en" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Imagen <span class="text-danger">*</span></label>
            <div class="col-md-9 text-center">
                <img v-if="imagen" v-bind:src="sContenidoArchivo" class="w-50 mb-2">
                <div class="custom-file">
                    <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)" required="required">
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
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
