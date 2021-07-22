<div class="d-flex border-bottom white-bg">
    <div class="col-8 p-3 font-bold">Editar Agencia</div>
    <div class="col-4 px-3 py-2">
        <button class="btn btn-danger btn-sm mt-1 float-right" v-on:click="ajaxEliminar(agencia.id)" v-bind:disabled="iEliminando === 1" v-cloak>
            <i class="fas fa-trash-alt" v-if="iEliminando === 0"></i>
            <i class="fas fa-circle-notch fa-spin" v-else></i>
        </button>
    </div>
</div>
<div class="p-2 bg-white border-top" id="layoutRight">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form role="form" v-on:submit.prevent="ajaxActualizar" class="w-100" id="frmEditar">
                    <div class="form-group row">
                        <label class="col-md-3 py-md-2">Nombre <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="nombre" v-model="agencia.nombre" required="required" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 py-md-2">Descripci&oacute;n</label>
                        <div class="col-md-9">
                            <textarea name="descripcion" rows="2" class="form-control" v-model="agencia.descripcion"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 py-md-2">Estado <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="estado" class="form-control" v-model="agencia.estado">
                                <option value="ACTIVO">ACTIVO</option>
                                <option value="ANULADO">ANULADO</option>
                            </select>
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
            
            <div class="col-12 border pt-2 mb-4 pb-2">
                <div class="row pb-2 pt-2 bg-muted mb-3">
                    <div class="col-12">
                        <h4 class="mb-0">DESTINOS</h4>
                    </div>
                </div>
                <form id="frmAgregarDestino" v-on:submit.prevent="ajaxAgregarDestino()" v-if="iCargando === 0">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Departamento <span class="text-danger">*</span></label>
                                <select name="departamento" class="form-control" v-model="sDepartamentoSeleccionado" required>
                                    <option value="">Seleccionar</option>
                                    <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Provincia <span class="text-danger">*</span></label>
                                <select name="provincia" class="form-control" v-model="sProvinciaSeleccionada" required>
                                    <option value="">Seleccionar</option>
                                    <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Distrito <span class="text-danger">*</span></label>
                                <select name="distrito" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                    <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Tarifa <span class="text-danger">*</span></label>
                                <input type="text" name="tarifa" class="form-control" placeholder="Ingrese tarifa">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label>Direcci&oacute;n</label>
                                <textarea name="direccion" rows="2" class="form-control" placeholder="Ingrese direcci&oacute;n"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary float-left" :disabled="iInsertandoDestino === 1">
                                    <i class="fas fa-arrow-down my-1" v-if="iInsertandoDestino === 0"></i>
                                    <i class="fas fa-circle-notch fa-spin my-1" v-else></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--<div class="form-group justify-content-between align-items-center">
                        <input type="text" class="form-control" style="width: calc(100% - 42px)" placeholder="Buscar ubigeo" v-model="sNuevoDestino"
                            v-autocomplete="{ url: '/intranet/app/configuracion/agencias/ajax/actualizar/autocompletarUbigeo', appendTo: '#frmAgregarDestino', select: onSelectAutocompleteDestino, change: onChangeAutocompleteDestino }">
                        <button type="submit" class="btn btn-primary" :disabled="iInsertandoDestino === 1">
                            <i class="fas fa-level-down-alt" v-if="iInsertandoDestino === 0"></i>
                            <i class="fas fa-circle-notch fa-spin" v-else></i>
                        </button>
                    </div>-->
                </form>
                <div class="container mt-2">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Departamento</th>
                                    <th>Provincia</th>
                                    <th>Distrito</th>
                                    <th>Direcci&oacute;n</th>
                                    <th>Tarifa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="destino in lstDestinos">
                                    <td>@{{ destino.departamento }}</td>
                                    <td>@{{ destino.provincia }}</td>
                                    <td>@{{ destino.distrito }}</td>
                                    <td>@{{ destino.direccion }}</td>
                                    <td>S/. @{{ destino.tarifaDestino.toFixed(2) }}</td>
                                </tr>
                                <tr v-if="lstDestinos.length === 0">
                                    <td colspan="4" class="text-center" v-if="iCargando === 0">No hay destinos registrados</td>
                                    <td colspan="4" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando destinos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
