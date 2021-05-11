<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Nuevo Proveedor</div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <h4 class="mt-0">Documentos</h4>
    <form role="form" v-on:submit.prevent="agregarDocumento">
        <div class="form-group mb-1 row">
            <label class="col-md-3">
                <select class="form-control" v-model="sTipoDocumentoCodigo" v-cloak>
                    <option v-for="tipoDocumento in lstTiposDocumento" :value="tipoDocumento.codigo">@{{ tipoDocumento.abreviatura }}</option>
                </select>
            </label>
            <div class="col-md-9">
                <input type="text" class="form-control d-inline" v-model="sNumeroDocumento" placeholder="Número de documento" style="width: calc(100% - 50px)">
                <button type="submit" class="btn btn-primary float-right">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <p class="mb-0" v-if="iConsultandoDocumento === 1" v-cloak><i class="fas fa-circle-notch fa-spin"></i> Consultando n&uacute;mero de documento</p>
            </div>
        </div>
    </form>
    <table class="table table-bordered" v-cloak>
        <thead>
            <tr>
                <th>Tipo de documento</th>
                <th class="text-center">N&uacute;mero</th>
                <th class="text-center">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(documento, i) in lstDocumentos">
                <td>@{{ documento.tipo_documento.abreviatura }}</td>
                <td class="text-center">@{{ documento.numero }}</td>
                <td class="text-center">
                    <a href="#" v-on:click.prevent="eliminarDocumento(i)"><i class="text-danger fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <tr v-if="lstDocumentos.length === 0">
                <td colspan="3" class="text-center">No hay documentos agregados</td>
            </tr>
        </tbody>
    </table>
    <form role="form" v-on:submit.prevent="ajaxInsertar" class="w-100" id="frmNuevo">
        <h4 class="mt-4">Datos personales</h4>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Nombres / Raz&oacute;n social <span class="text-danger">*</span></label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="nombres" v-model="sNombres" required="required" autocomplete="off" v-on:keyup="restablecerHabido">
                <small class="font-weight-bold" v-if="sHabido">@{{ sHabido }}</small>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Paterno</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_paterno" v-model="sApellidoPaterno" autocomplete="off">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Apellido Materno</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="apellido_materno" v-model="sApellidoMaterno" autocomplete="off">
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
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Departamento</label>
            <div class="col-md-9">
                <select class="form-control" v-model="sDepartamentoSeleccionado" v-cloak>
                    <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Provincia</label>
            <div class="col-md-9">
                <select class="form-control" v-model="sProvinciaSeleccionada" v-cloak>
                    <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Distrito</label>
            <div class="col-md-9">
                <select class="form-control" name="distrito" v-cloak>
                    <option v-for="distrito in lstDistritos" :value="distrito.id">@{{ distrito.distrito }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-3 py-md-2">Direcci&oacute;n</label>
            <div class="col-md-9">
                <input type="text" class="form-control" name="direccion" v-model="sDireccion" autocomplete="off">
            </div>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-white" type="button" v-on:click="ajaxCancelar" v-bind:disabled="iInsertando === 1" v-cloak>Cancelar</button>
            <button class="btn btn-primary ml-2" type="submit" v-bind:disabled="iInsertando === 1" v-cloak>
                <span v-if="iInsertando === 0">Guardar</span>
                <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
            </button>
        </div>
    </form>
</div>
