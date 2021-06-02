<div class="col-12 p-4" style="background-color: #6BBD99;">
    <form v-on:submit.prevent="actualizarAccount" class="w-100" id="frmAccount">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label>Nombres <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nombres" placeholder="Nombres" v-model="user.nombres" required>
                </div>
                <div class="form-group">
                    <label>Tipo Documento <span class="text-danger">*</span></label>
                    <select class="form-control" name="tipo_documento" v-model="user.tipo_documento" required>
                        <option value="DNI" selected>DNI</option>
                        <option value="RUC">RUC</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label>Apellidos <span class="text-danger">*</span></label>
                    <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" v-model="user.apellidos" required>
                </div>
                <div class="form-group">
                    <label>Documento <span class="text-danger">*</span></label>
                    <input type="text" name="documento" class="form-control" placeholder="Documento" :minlength="user.tipo_documento == 'DNI' ? 8 : 11" :maxlength="user.tipo_documento == 'DNI' ? 8 : 11" onkeypress="return isNumber(event)" v-model="user.documento" required>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Direcci&oacute;n de correo electr&oacute;nico <span class="text-danger">*</span></label>
                    <input type="email" name="correo" class="form-control" placeholder="Email" v-model="user.correo">
                </div>
            </div>
            <div class="col-12">
                <div class="password-update">
                    <label class="title-password">Cambio de contraseña</label>
                    <div class="row p-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Contraseña actual (d&eacute;jalo en blanco para no cambiarla)</label>
                                <input type="password" name="password_actual" id="password_actual" class="form-control" placeholder="Contraseña actual">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Nueva contraseña (d&eacute;jalo en blanco para no cambiarla)</label>
                                <input type="password" name="password_nueva" id="password_nueva" class="form-control" placeholder="Contraseña nueva">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Confirmar nueva contraseña (&eacute; en blanco para no cambiarla)</label>
                                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirmar contraseña">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mt-4 text-left">
                    <button class="btn btn-ecovalle" type="submit" :disabled="iActualizando === 1" v-cloak>
                        <span v-if="iActualizando === 0">Guardar cambios</span>
                        <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>