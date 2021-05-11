<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">
        @{{ producto.nombre_es }}
        <small>Ajuste manual de stock</small>
    </div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <form v-on:submit.prevent="ajaxActualizarStock" id="frmActualizarStock">
        <div class="form-group">
            <label class="font-weight-bold">Disponibilidad</label>
            <p>@{{ producto.stock_actual }} UND</p>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Stock m&iacute;nimo <span class="text-danger">*</span></label>
            <input class="form-control" type="text" name="stock_minimo" v-model="producto.stock_minimo">
        </div>
        <hr class="my-4">
        <div class="form-group">
            <label class="font-weight-bold">Tipo de ajuste</label>
            <select class="form-control" name="tipo_de_ajuste" v-model="sTipoAjuste" required="required">
                <option value="">Ninguno</option>
                <option value="E">Ajuste de entrada</option>
                <option value="S">Ajuste de salida</option>
            </select>
        </div>
        <div class="form-group" v-if="sTipoAjuste !== ''">
            <label class="font-weight-bold">Cantidad <span class="text-danger">*</span></label>
            <input class="form-control" name="cantidad" type="text" required="required">
        </div>
        <div class="form-group pb-4 text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iActualizando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iActualizando === 1" v-cloak>
                <span v-if="iActualizando === 0">Guardar ajuste</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
