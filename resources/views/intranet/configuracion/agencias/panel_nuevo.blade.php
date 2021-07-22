<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nueva Agencia</div>
</div>
<div class="d-flex p-4 bg-white border-top" id="layoutRight">
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombre <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombre" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Descripci&oacute;n</label>
            <div class="col-md-9">
                <textarea name="descripcion" rows="2" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group row d-none">
            <label class="col-md-3 py-md-2">Direcci&oacute;n</label>
            <div class="col-md-9">
                <textarea name="direccion" rows="2" class="form-control"></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Estado <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <select name="estado" class="form-control" required>
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="ANULADO">ANULADO</option>
                </select>
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
