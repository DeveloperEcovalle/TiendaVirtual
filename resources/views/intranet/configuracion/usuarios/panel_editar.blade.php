<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Usuario</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(usuario.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Perfil <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select class="form-control" name="perfil" v-model="usuario.perfil_id" v-cloak>
                    <option v-for="perfil in lstPerfiles" v-bind:value="perfil.id">@{{ perfil.nombre }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombres <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombres" v-model="usuario.persona.nombres" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Paterno <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_paterno" v-model="usuario.persona.apellido_1" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Materno <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_materno" v-model="usuario.persona.apellido_2" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Username <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="username" v-model="usuario.username" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Contrase&ntilde;a <span class="text-danger">*</span></label>
            <div class="col-md-9 pt-2">
                <a href="#" data-toggle="modal" data-target="#modalCambiarContrasena">Cambiar contrase&ntilde;a</a>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Correo</label>
            <div class="col-md-9">
                <input type="email" class="form-control" name="correo" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Tel&eacute;fono</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="telefono" autocomplete="off">
            </div>
        </div>
        <div class="form-group mb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iActualizando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iActualizando === 1" v-cloak>
                <span v-if="iActualizando === 0">Guardar cambios</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCambiarContrasena" tabindex="-1" role="dialog" aria-labelledby="modalCambiarContrasenaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambiarContrasenaLabel">Cambiar contrase&ntilde;a</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmCambiarContrasena" v-on:submit.prevent="ajaxActualizarContrasena">
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Contrase&ntilde;a</label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" name="contrasena" required="required" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-md-4">Confirmar contrase&ntilde;a</label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" name="confirmar_contrasena" required="required" autocomplete="off">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-primary" form="frmCambiarContrasena" v-bind:disabled="iActualizandoContrasena === 1">
                    <span v-if="iActualizandoContrasena === 0">Guardar cambios</span>
                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                </button>
            </div>
        </div>
    </div>
</div>
