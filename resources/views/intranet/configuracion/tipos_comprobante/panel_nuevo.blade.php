<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Tipo de comprobante</div>
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
            <label class="col-md-3 py-md-2">Comprobante SUNAT asociado</label>
            <div class="col-md-9">
                <select class="form-control" name="comprobante_sunat_asociado" v-model="sComprobanteSunatAsociado">
                    <option value="">Ninguno</option>
                    <option v-for="tipoComprobanteSunat in lstTiposComprobanteSunat" v-bind:value="tipoComprobanteSunat.codigo" v-cloak>@{{ tipoComprobanteSunat.descripcion }}</option>
                </select>
            </div>
        </div>
        <hr>
        <h4>Datos de la primera Serie</h4>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Serie <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="serie" v-bind:placeholder="sSerieEjemplo" required="required" autocomplete="off" maxlength="4">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Correlativo inicio <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="correlativo_inicio" required="required" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Correlativo l&iacute;mite</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="correlativo_limite" autocomplete="off">
                <small>Dejar vac&iacute;o si la serie no tendr&aacute; l&iacute;mite (infinito)</small>
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
