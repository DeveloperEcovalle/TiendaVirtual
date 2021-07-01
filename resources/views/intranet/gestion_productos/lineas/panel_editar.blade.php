<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Categor&iacute;a</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(linea.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <label class="font-weight-bold">Nombre en espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_espanol" v-model="linea.nombre_espanol" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Nombre en ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_ingles" v-model="linea.nombre_ingles" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Cambiar Imagen <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-12" v-if="linea.ruta_imagen">
                    <button type="button" class="btn btn-sm btn-danger float-right" v-on:click="ajaxEliminarImagen()">
                        <span v-if="iEliminandoImagen == 0"><i class="fa fa-remove"></i></span>
                        <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                    </button>
                </div>
                <div class="col-md-4 col-6">
                    <img v-if="imagen" v-bind:src="sContenidoImagen" class="img-fluid">
                    <img v-else v-bind:src="linea.ruta_imagen" class="img-fluid">
                </div>
            </div>
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagen($event)">
                <label for="aImagen" class="custom-file-label">@{{ sNombreImagen }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Contenido en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div id="sContenidoEspanol"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Contenido en ingl&eacute;s <span class="text-danger">*</span></label>
            <div id="sContenidoIngles"></div>
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
