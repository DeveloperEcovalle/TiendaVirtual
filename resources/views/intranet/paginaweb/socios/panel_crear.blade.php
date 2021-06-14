<div class="modal fade" id="modalBeneficioCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-ecovalle">
                <h4 class="modal-title"><b>NUEVO BENEFICIO</b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="crearBeneficio"   v-on:submit.prevent="ajaxRegistrar()">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nombre" required="required" autocomplete="off">
                            </div>
                            <div class="form-group align-items-center">
                                <label class="font-weight-bold">Imagen principal <span class="text-danger">*</span></label>
                                <div class="justify-content-center w-100">
                                    <img v-if="imagen" v-bind:src="sContenidoArchivo" class="img-fluid mb-2" style="height: 250px;width: 80%;max-width: 90%;">
                                </div>
                                <div class="custom-file">
                                    <input id="aImagen" accept=".jpeg,.png,.svg" type="file" class="custom-file-input" required name="imagen" v-on:change="cambiarImagenBeneficioCrear($event)">
                                    <label for="aImagen" class="custom-file-label">@{{ sNombreArchivo }}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Ruta de enlace</label>
                                <input type="text" name="ruta_enlace" class="form-control">
                             </div>
                             <div class="form-group">
                                <label class="font-weight-bold">Descripci&oacute;n <span class="text-danger">*</span></label>
                                <textarea name="descripcion" class="form-control" id="descripcion" rows="2" required></textarea>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary ml-2" form="crearBeneficio" type="submit" v-bind:disabled="iRegistrando === 1" v-cloak>
                    <span v-if="iRegistrando === 0">Guardar</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cerrar</button>
            </div>
        </div>
    </div>
</div>