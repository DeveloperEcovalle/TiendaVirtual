<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Publicaci&oacute;n</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group">
            <label class="font-weight-bold">Categor&iacute;a <span class="text-danger">*</span></label>
            <a href="#" class="float-right" data-toggle="modal" data-target="#modalListaCategorias">Ver lista de categor&iacute;as</a>
            <select class="form-control d-inline-block" name="categoria" style="width: calc(100% - 40px)">
                <option v-for="categoria in lstCategorias" :value="categoria.id">@{{ categoria.nombre_espanol }}</option>
            </select>
            <button class="btn btn-primary float-right" type="button" data-toggle="modal" data-target="#modalNuevaCategoria"><i class="fas fa-plus"></i></button>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">T&iacute;tulo <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="titulo" required="required" autocomplete="off">
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Imagen principal <span class="text-danger">*</span></label>
            <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2">
            <div class="custom-file">
                <input id="aImagen" type="file" class="custom-file-input" accept=".jpeg,.png" name="imagen" v-on:change="cambiarImagen($event)" required="required">
                <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Resumen <span class="text-danger">*</span></label>
            <textarea class="form-control" name="resumen" v-model="sResumen" rows="5" required="required"></textarea>
            <small class="float-right" :class="{ 'text-danger': sResumen.length > 200 }">@{{ sResumen.length }}/200</small>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Contenido <span class="text-danger">*</span></label>
            <div id="sContenido"></div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">C&oacute;digo YouTube</label>
            <textarea name="cod_youtube" rows="4" class="form-control"></textarea>
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

<div class="modal fade" id="modalListaCategorias" tabindex="-1" role="dialog" aria-labelledby="modalListaCategoriasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalListaCategoriasLabel">Categor&iacute;as</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary float-right mb-4" data-toggle="modal" data-target="#modalNuevaCategoria">
                            <i class="fas fa-plus"></i> Nueva categor&iacute;a
                        </button>
                    </div>
                    <div class="col-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre en espa&ntilde;ol</th>
                                    <th>Nombre en ingl&eacute;s</th>
                                    <th>Banner</th>
                                    <th class="text-center">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="categoria in lstCategorias">
                                    <td>@{{ categoria.nombre_espanol }}</td>
                                    <td>@{{ categoria.nombre_ingles }}</td>
                                    <td><a :href="categoria.ruta_imagen" target="_blank">Ver imagen</a></td>
                                    <td class="text-center"><a href="#" v-on:click="ajaxEliminarCategoria(categoria.id)" class="text-danger"><i class="fas fa-trash-alt"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCategoria" tabindex="-1" role="dialog" aria-labelledby="modalNuevaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalNuevaCategoriaLabel">Nueva categor&iacute;a</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmNuevaCategoria" v-on:submit.prevent="ajaxInsertarCategoria()">
                    <div class="form-group">
                        <label>Nombre en espa&ntilde;ol</label>
                        <input type="text" class="form-control" name="nombre_espanol" placeholder="Ejemplo: Recetas" required="required">
                    </div>
                    <div class="form-group">
                        <label>Nombre en ingl&eacute;s</label>
                        <input type="text" class="form-control" name="nombre_ingles" placeholder="Ejemplo: Recipes" required="required">
                    </div>
                    <div class="form-group">
                        <label>Banner</label>
                        <input type="file" class="form-control" name="banner" required="required">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="frmNuevaCategoria">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
