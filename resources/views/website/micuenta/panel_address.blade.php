<div class="col-12 p-4" style="background-color: #6BBD99;">
    <form v-on:submit.prevent="actualizarAddress" class="w-100" id="frmAddress" v-cloak>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label>Departamento <span class="text-danger">*</span></label>
                    <select class="form-control" name="departamento" v-model="user.departamento" required>
                        <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label>Provincia <span class="text-danger">*</span></label>
                    <select class="form-control" name="provincia" v-model="user.provincia" required>
                        <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label>Distrito <span class="text-danger">*</span></label>
                    <select class="form-control" name="distrito" v-model="user.distrito" required>
                        <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label>Direcci&oacute;n <span class="text-danger">*</span></label>
                    <input type="text" name="direccion" class="form-control" placeholder="Direcci&oacute;n" v-model="user.direccion">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-ecovalle" type="submit" :disabled="iActualizando === 1" v-cloak>
                        <span v-if="iActualizando === 0">Guardar cambios</span>
                        <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>