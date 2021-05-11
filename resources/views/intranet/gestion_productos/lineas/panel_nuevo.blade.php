<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva L&iacute;nea de producto</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label class="font-weight-bold">Nombre en espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_espanol" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Nombre en ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_ingles" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Imagen</label>
            <div class="row">
                <div class="col-md-4 col-6">
                    <img v-if="imagen" v-bind:src="sContenidoImagen" class="img-fluid">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)">
                <label for="aImagen" class="custom-file-label">@{{ sNombreImagen }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Contenido en espa&ntilde;ol</label>
            <div id="sContenidoEspanol"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Contenido en ingl&eacute;s</label>
            <div id="sContenidoIngles"></div>
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
