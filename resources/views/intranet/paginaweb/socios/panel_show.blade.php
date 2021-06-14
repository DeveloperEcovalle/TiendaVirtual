<div class="modal fade" id="modalBeneficio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-ecovalle">
                <h4 class="modal-title"><b>BENEFICIO</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="editarBeneficio"   v-on:submit.prevent="ajaxActualizar()">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" v-model="beneficio.nombre" required="required" autocomplete="off">
                            </div>
                            <div class="form-group align-items-center">
                                <label class="font-weight-bold">Imagen principal <span class="text-danger">*</span></label>
                                <div class="justify-content-center w-100">
                                    <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2" style="height: 250px;width: 80%;max-width: 90%;">
                                    <img v-else v-bind:src="beneficio.ruta_imagen" class="img-fluid mb-2"  style="height: 250px;">
                                </div>
                                <div class="custom-file">
                                    <input id="aImagen" accept=".jpeg,.png,.svg" type="file" class="custom-file-input" name="imagen" v-on:change="cambiarImagenBeneficio($event)">
                                    <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Ruta de enlace</label>
                                <input type="text" name="ruta_enlace" :value="beneficio.ruta_enlace" class="form-control">
                             </div>
                             <div class="form-group">
                                <label class="font-weight-bold">Descripci&oacute;n <span class="text-danger">*</span></label>
                                <textarea name="descripcion" class="form-control" id="descripcion" rows="2" required>@{{ beneficio.descripcion }}</textarea>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary ml-2" form="editarBeneficio" type="submit" v-bind:disabled="iActualizando === 1" v-cloak>
                    <span v-if="iActualizando === 0">Guardar cambios</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                </button>
                <button class="btn btn-danger ml-2" type="button" v-bind:disabled="iEliminando === 1" v-on:click="ajaxEliminar()" v-cloak>
                    <span v-if="iEliminando === 0">Eliminar</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>