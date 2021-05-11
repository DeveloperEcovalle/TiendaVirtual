<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Producto</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2 font-weight-bold">Imagen <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4 col-6">
                        <img v-if="imagen" :src="sContenidoArchivo" class="img-fluid mb-2">
                    </div>
                </div>
                <div class="custom-file">
                    <input id="aImagen" type="file" class="custom-file-input" name="imagen" accept=".jpg,.jpeg,.png,.svg" v-on:change="cambiarImagen($event)" required="required">
                    <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2 font-weight-bold">Nombre espa&ntilde;ol <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_es" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2 font-weight-bold">Nombre ingl&eacute;s <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre_en" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-12 py-md-2 m-0 font-weight-bold">Descripci&oacute;n en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div class="col-12">
                <textarea class="form-control" name="descripcion_es" required="required" rows="4" style="resize: vertical"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-12 py-md-2 m-0 font-weight-bold">Descripci&oacute;n en ingl&eacute;s <span class="text-danger">*</span></label>
            <div class="col-12">
                <textarea class="form-control" name="descripcion_en" required="required" rows="4" style="resize: vertical"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-12 pt-2 m-0 font-weight-bold">Documentos <span class="text-danger" v-if="lstPdfs.length > 0">*</span></label>
            <div class="col-12" v-if="lstPdfs.length === 0">
                <p class="py-2 text-muted m-0">Sin documentos adjuntos</p>
            </div>
            <div class="col-md-12" v-for="(pdf, i) in lstPdfs">
                <div class="row pt-1">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nombres_documentos[]" autocomplete="off" placeholder="Nombre de descarga del archivo" required="required">
                    </div>
                    <div class="col-md-5 overflow-hidden">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="documentos[]" accept=".pdf" :required="i < lstPdfs.length" v-on:change="changeDocumento($event, i)" required="required">
                            <label class="custom-file-label">@{{ pdf.name.split('\\').pop() }}</label>
                        </div>
                    </div>
                    <a href="#" class="text-danger float-right p-2" v-on:click.prevent="eliminarDocumento(i)"><i class="fas fa-trash-alt"></i></a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="hr-line-dashed w-100 my-2"></div>
            </div>
            <div class="col-12">
                <a href="#" v-on:click.prevent="agregarDocumento"><i class="fas fa-plus"></i> Agregar nuevo documento</a>
            </div>
        </div>
        <div class="form-group row justify-content-end">
            <label class="col-md-12 pt-md-2 font-weight-bold">Categor&iacute;as <span class="text-danger">*</span></label>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="(categoria, i) in lstCategorias" v-cloak>
                        <label class="d-block">
                            <input type="checkbox" name="categorias[]" :value="categoria.id">&nbsp;@{{ categoria.nombre_es }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-12 pt-md-2 font-weight-bold">L&iacute;neas <span class="text-danger">*</span></label>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="(linea, i) in lstLineas" v-cloak>
                        <label class="d-block">
                            <input type="checkbox" name="lineas[]" :value="linea.id">&nbsp;@{{ linea.nombre_es }}
                        </label>
                    </div>
                </div>
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
