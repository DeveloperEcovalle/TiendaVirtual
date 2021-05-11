<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Producto</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(producto.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGaleria">
                <i class="fas fa-image"></i>&nbsp;Galer&iacute;a de im&aacute;genes
            </button>
            <button type="button" class="btn btn-primary ml-1" data-toggle="modal" data-target="#modalSubproductos">
                <i class="fas fa-boxes"></i>&nbsp;Lista de subproductos
            </button>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Nombre espa&ntilde;ol <span class="text-danger">*</span></label>
            <input type="text" class="form-control" v-model="producto.nombre_es" name="nombre_en_espanol" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Nombre ingl&eacute;s <span class="text-danger">*</span></label>
            <input type="text" class="form-control" v-model="producto.nombre_en" name="nombre_en_ingles" required="required" autocomplete="off">
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
        <div class="form-group row">
            <label class="col-md-12 pt-2 m-0 font-weight-bold">Documentos existentes <i class="fas fa-circle-notch fa-spin" v-if="iEliminandoDocumento === 1"></i></label>
            <div class="col-12 py-2" v-if="producto.documentos.length === 0">
                <p class="text-muted m-0">Sin documentos existentes</p>
            </div>
            <div class="col-md-12 py-2" v-for="(documento, i) in producto.documentos">
                <a :href="documento.ruta_archivo" target="_blank">@{{ documento.nombre_descarga }}</a>
                <a href="#" class="text-danger m-l-xl" v-on:click.prevent="ajaxEliminarDocumento(documento.id, i)" :disabled="iEliminandoDocumento === 1">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-12 pt-2 m-0 font-weight-bold">Nuevos documentos <span class="text-danger" v-if="lstPdfs.length > 0">*</span></label>
            <div class="col-12" v-if="lstPdfs.length === 0">
                <p class="pt-2 text-muted m-0">Sin documentos adjuntos</p>
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
        <div class="form-group row">
            <label class="col-md-12 pt-md-2 font-weight-bold">Categor&iacute;as <span class="text-danger">*</span></label>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4" v-for="(categoria, i) in lstCategorias" v-cloak>
                        <label class="d-block">
                            <input type="checkbox" name="categorias[]" v-model="lstCategoriasSeleccionadas" :value="categoria.id">&nbsp;@{{ categoria.nombre_es }}
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
                            <input type="checkbox" name="lineas[]" v-model="lstLineasSeleccionadas" :value="linea.id">&nbsp;@{{ linea.nombre_espanol }}
                        </label>
                    </div>
                </div>
            </div>
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

<div class="modal fade" id="modalGaleria" tabindex="-1" role="dialog" aria-labelledby="modalGaleriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalGaleriaLabel">Galer&iacute;a de im&aacute;genes @{{ producto.nombre_es }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <form action="#" class="dropzone">
                            <input type="hidden" name="producto_id" :value="producto.id">
                            <div class="fallback">
                                <input name="file" type="file" multiple/>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h4 class="mt-4">Im&aacute;genes agregadas</h4>
                    </div>
                    <div class="col-12 text-center" v-if="iCargandoImagenes === 1">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                    <div class="col-12" v-else>
                        <div class="row">
                            <div class="col-md-2 text-center" v-for="(imagen, i) in lstImagenes">
                                <img class="img-fluid" :src="imagen.ruta">
                                <a href="#" v-on:click.prevent="ajaxEliminarImagen(imagen.id, i)">Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSubproductos" tabindex="-1" role="dialog" aria-labelledby="modalSubproductosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalSubproductosLabel">Lista de subproductos de @{{ producto.nombre_es }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmAgregarSubproducto" v-on:submit.prevent="ajaxAgregarSubproducto()">
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <input type="text" class="form-control" style="width: calc(100% - 42px)" placeholder="Buscar producto" v-model="sNuevoSubproducto"
                               v-autocomplete="{ url: '/intranet/app/gestion-productos/productos/ajax/editar/autocompletarProductos', appendTo: '#frmAgregarSubproducto', select: onSelectAutocompleteProducto, change: onChangeAutocompleteProducto }">
                        <button type="submit" class="btn btn-primary" :disabled="iInsertandoSubproducto === 1">
                            <i class="fas fa-level-down-alt" v-if="iInsertandoSubproducto === 0"></i>
                            <i class="fas fa-circle-notch fa-spin" v-else></i>
                        </button>
                    </div>
                </form>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-right">Precio actual</th>
                            <th class="text-right">Oferta vigente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in producto.subproductos">
                            <td>@{{ p.nombre_es }}</td>
                            <td class="text-right">S/ @{{ p.precio_actual.monto.toFixed(2) }}</td>
                            <td class="text-right">
                                <p class="m-0" v-if="p.oferta_vigente">
                                    <span v-if="p.oferta_vigente.monto">S/&nbsp;</span>
                                    @{{ p.oferta_vigente.porcentaje ? p.oferta_vigente.porcentaje : p.oferta_vigente.monto }}
                                    <span v-if="p.oferta_vigente.porcentaje">%</span>
                                </p>
                                <p class="m-0" v-else>-</p>
                            </td>
                        </tr>
                        <tr v-if="producto.subproductos.length === 0">
                            <td class="text-center" colspan="3">Este producto no tiene subproductos</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
