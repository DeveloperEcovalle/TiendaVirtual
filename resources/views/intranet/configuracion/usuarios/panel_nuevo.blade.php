<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Usuario</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Perfil <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select class="form-control" name="perfil">
                    <option v-for="perfil in lstPerfiles" v-bind:value="perfil.id" v-cloak>@{{ perfil.nombre }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombres <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombres" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Paterno <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_paterno" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Materno <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_materno" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Username <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="username" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Contrase&ntilde;a <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="password" class="form-control" name="contrasena" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Confirmar contrase&ntilde;a <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="password" class="form-control" name="confirmar_contrasena" required="required" autocomplete="off">
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
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
