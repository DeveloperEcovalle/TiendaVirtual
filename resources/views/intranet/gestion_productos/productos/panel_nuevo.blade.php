<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Producto</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label class="font-weight-bold">Nombre espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_espanol" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Nombre ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nombre_en_ingles" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Beneficios en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div id="sBeneficiosEspanol"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Beneficios en ingl&eacute;s <span class="text-danger">*</span></label>
            <div id="sBeneficiosIngles"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Descripci&oacute;n en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div id="sDescripcionEspanol"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Descripci&oacute;n en ingl&eacute;s <span class="text-danger">*</span></label>
            <div id="sDescripcionIngles"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Modo de uso en espa&ntilde;ol <span class="text-danger">*</span></label>
            <div id="sModoUsoEspanol"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Modo de uso en ingl&eacute;s <span class="text-danger">*</span></label>
            <div id="sModoUsoIngles"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Documentos <span class="text-danger" v-if="lstPdfs.length > 0">*</span></label>
            <p v-if="lstPdfs.length === 0" class="pt-2 text-muted m-0">Sin documentos adjuntos</p>
            <div class="row pt-1" v-for="(pdf, i) in lstPdfs">
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
            <div class="hr-line-dashed w-100 my-2"></div>
            <a href="#" v-on:click.prevent="agregarDocumento"><i class="fas fa-plus"></i> Agregar nuevo documento</a>
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
                            <input type="checkbox" name="lineas[]" :value="linea.id">&nbsp;@{{ linea.nombre_espanol }}
                        </label>
                    </div>
                </div>
            </div>
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
