<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">@{{ producto.nombre_es }}</div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <div class="row p-2 bg-muted mb-3">
        <h4 class="mb-0">PRECIOS</h4>
    </div>
    <form v-on:submit.prevent="ajaxInsertarPrecio()">
        <div class="form-group">
            <label class="mb-0 py-md-2 font-weight-bold">Registrar nuevo precio <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-8 col-md-10">
                    <input type="text" class="form-control" name="nuevo_precio" v-model="sNuevoPrecio" placeholder="Monto del precio" required="required" autocomplete="off">
                </div>
                <div class="col-4 col-md-2">
                    <button type="submit" class="btn btn-block btn-primary" :disabled="iInsertandoPrecio === 1">
                        <i class="fas fa-arrow-down my-1" v-if="iInsertandoPrecio === 0"></i>
                        <i class="fas fa-circle-notch fa-spin my-1" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <table class="table table-hover">
        <thead>
            <tr>
                <th colspan="3" class="pt-3">
                    <p class="m-0 d-inline-block">&Uacute;ltimos 5 precios registrados</p>
                    <a class="float-right" href="#" data-toggle="modal" data-target="#modalHistorialPrecios" v-on:click="ajaxListarPrecios()">Ver historial de precios</a>
                </th>
            </tr>
            <tr>
                <th>Precio</th>
                <th class="text-center">Registro</th>
                <th class="text-center">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="precio in lstUltimosPrecios">
                <td :class="{ 'text-success': precio.actual === 1, 'text-danger': precio.eliminado === 1 }">S/ @{{ precio.monto.toFixed(2) }}</td>
                <td :class="{ 'text-success': precio.actual === 1, 'text-danger': precio.eliminado === 1 }" class="text-center">@{{ precio.fecha_reg }}</td>
                <td class="text-center">
                    <a href="#" class="text-danger" v-if="precio.eliminado === 0" v-on:click="ajaxEliminarPrecio(precio.id)"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <tr v-if="lstUltimosPrecios.length === 0">
                <td colspan="3" class="text-center" v-if="iCargandoUltimosPrecios === 0">No hay precios registrados</td>
                <td colspan="3" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando precios</td>
            </tr>
        </tbody>
    </table>
    <div class="row p-2 bg-muted mt-4 mb-2">
        <h4 class="mb-0">OFERTAS / DESCUENTOS</h4>
    </div>
    <form v-on:submit.prevent="ajaxInsertarOferta()">
        <div class="form-group">
            <label class="font-weight-bold">Registrar nueva oferta / descuento <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-md-6">
                    <label class="pt-2 mr-3"><input type="radio" name="tipo_oferta" value="Monto" v-model="sTipoOferta" required="required">&nbsp;Monto</label>
                    <label class="pt-2"><input type="radio" name="tipo_oferta" value="Porcentaje" v-model="sTipoOferta">&nbsp;Porcentaje</label>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <div class="input-group-prepend" v-if="sTipoOferta === 'Monto'">
                            <span class="input-group-addon">S/</span>
                        </div>
                        <input type="text" class="form-control" name="nueva_oferta" v-model="sNuevaOferta" placeholder="Monto de la oferta / descuento" required="required" autocomplete="off">
                        <div class="input-group-append" v-if="sTipoOferta === 'Porcentaje'">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Vigencia de la oferta / descuento <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-8 col-md-10">
                    <div class="input-group input-daterange">
                        <input type="date" class="form-control text-center" v-model="sFechaInicio" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon pt-2 px-3">hasta</div>
                        <input type="date" class="form-control text-center" v-model="sFechaVencimiento" placeholder="yyyy-mm-dd">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <button type="submit" class="btn btn-block btn-primary" :disabled="iInsertandoOferta === 1">
                        <i class="fas fa-arrow-down my-1" v-if="iInsertandoOferta === 0"></i>
                        <i class="fas fa-circle-notch fa-spin my-1" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <table class="table table-hover">
        <thead>
            <tr>
                <th colspan="5" class="pt-3">
                    <p class="m-0 d-inline-block">&Uacute;ltimas 5 ofertas registradas</p>
                    <a class="float-right" href="#" data-toggle="modal" data-target="#modalHistorialOfertas" v-on:click="ajaxListarOfertas()">Ver historial de ofertas / descuentos</a>
                </th>
            </tr>
            <tr>
                <th>Oferta</th>
                <th class="text-center">Inicio</th>
                <th class="text-center">Fin</th>
                <th class="text-center">Registro</th>
                <th class="text-center">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="oferta in lstUltimasOfertas">
                <td :class="{ 'text-danger': oferta.eliminado === 1 }">
                    <span v-if="oferta.monto">S/ @{{ oferta.monto.toFixed(2) }}</span>
                    <span v-else>@{{ oferta.porcentaje.toFixed(2) }}%</span>
                </td>
                <td :class="{ 'text-danger': oferta.eliminado === 1 }" class="text-center">@{{ oferta.fecha_inicio }}</td>
                <td :class="{ 'text-danger': oferta.eliminado === 1 }" class="text-center">@{{ oferta.fecha_vencimiento }}</td>
                <td :class="{ 'text-danger': oferta.eliminado === 1 }" class="text-center">@{{ oferta.fecha_reg }}</td>
                <td class="text-center">
                    <a href="#" class="text-danger" v-if="oferta.eliminado === 0" v-on:click="ajaxEliminarOferta(oferta.id)"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <tr v-if="lstUltimasOfertas.length === 0">
                <td colspan="5" class="text-center" v-if="iCargandoUltimasOfertas === 0">No hay ofertas registradas</td>
                <td colspan="5" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando ofertas</td>
            </tr>
        </tbody>
    </table>
    <div class="row p-2 bg-muted mt-4 mb-2">
        <h4 class="mb-0">PROMOCIONES</h4>
    </div>
    <form v-on:submit.prevent="ajaxInsertarPromocion()">
        <div class="form-group">
            <label class="font-weight-bold">Registrar nueva promoci&oacute;n <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-md-6">
                    <label class="pt-2 mr-3"><input type="radio" name="tipo_promocion" value="Monto" v-model="sTipoPromocion" required="required">&nbsp;Monto</label>
                    <label class="pt-2"><input type="radio" name="tipo_promocion" value="Porcentaje" v-model="sTipoPromocion">&nbsp;Porcentaje</label>
                </div>
                <div class="col-md-6 mb-1">
                    <div class="input-group">
                        <div class="input-group-prepend" v-if="sTipoPromocion === 'Monto'">
                            <span class="input-group-addon">S/</span>
                        </div>
                        <input type="text" class="form-control" name="nueva_promocion" v-model="sNuevaPromocion" placeholder="Monto de la promocion" required="required" autocomplete="off">
                        <div class="input-group-append" v-if="sTipoPromocion === 'Porcentaje'">
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-1">
                    <label for="">M&iacute;nimo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="min_promocion" v-model="sMinPromocion" placeholder="Condición mínima de compra" required="required" autocomplete="off">
                </div>
                <div class="col-6 mb-1">
                    <label for="">M&aacute;ximo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="max_promocion" v-model="sMaxPromocion" placeholder="Condición máxima de compra" required="required" autocomplete="off">
                </div>
                <div class="col-12">
                    <label for="">Descripci&oacute;n <span class="text-danger">(lo que el usuario visualizará)*</span></label>
                    <textarea class="form-control" name="descripcion" id="descripcion" v-model="sDescripcionPromocion" rows="2">
                        Mayor a @{{sMinPromocion}} productos y menor a @{{sMaxPromocion}} llevatelos con @{{sNuevaPromocion}} de dscto.
                    </textarea>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Vigencia de la promocion <span class="text-danger">*</span></label>
            <div class="row">
                <div class="col-8 col-md-10">
                    <div class="input-group input-daterange">
                        <input type="date" class="form-control text-center" v-model="sFechaInicioP" placeholder="yyyy-mm-dd">
                        <div class="input-group-addon pt-2 px-3">hasta</div>
                        <input type="date" class="form-control text-center" v-model="sFechaVencimientoP" placeholder="yyyy-mm-dd">
                    </div>
                </div>
                <div class="col-4 col-md-2">
                    <button type="submit" class="btn btn-block btn-primary" :disabled="iInsertandoPromocion === 1">
                        <i class="fas fa-arrow-down my-1" v-if="iInsertandoPromocion === 0"></i>
                        <i class="fas fa-circle-notch fa-spin my-1" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
    <table class="table table-hover">
        <thead>
            <tr>
                <th colspan="5" class="pt-3">
                    <p class="m-0 d-inline-block">&Uacute;ltimas 5 promociones registradas</p>
                    <a class="float-right" href="#" data-toggle="modal" data-target="#modalHistorialPromociones" v-on:click="ajaxListarPromociones()">Ver historial de promociones</a>
                </th>
            </tr>
            <tr>
                <th>Promoci&oacute;n</th>
                <th class="text-center">Descripci&oacute;n</th>
                <th class="text-center">Inicio</th>
                <th class="text-center">Fin</th>
                <th class="text-center">Registro</th>
                <th class="text-center">Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="promocion in lstUltimasPromociones">
                <td :class="{ 'text-danger': promocion.eliminado === 1 }">
                    <span v-if="promocion.monto">S/ @{{ promocion.monto.toFixed(2) }}</span>
                    <span v-else>@{{ promocion.porcentaje.toFixed(2) }}%</span>
                </td>
                <td :class="{ 'text-danger': promocion.eliminado === 1 }" class="text-center">@{{ promocion.descripcion }}</td>
                <td :class="{ 'text-danger': promocion.eliminado === 1 }" class="text-center">@{{ promocion.fecha_inicio }}</td>
                <td :class="{ 'text-danger': promocion.eliminado === 1 }" class="text-center">@{{ promocion.fecha_vencimiento }}</td>
                <td :class="{ 'text-danger': promocion.eliminado === 1 }" class="text-center">@{{ promocion.fecha_reg }}</td>
                <td class="text-center">
                    <a href="#" class="text-danger" v-if="promocion.eliminado === 0" v-on:click="ajaxEliminarPromocion(promocion.id)"><i class="fas fa-trash-alt"></i></a>
                </td>
            </tr>
            <tr v-if="lstUltimasPromociones.length === 0">
                <td colspan="6" class="text-center" v-if="iCargandoUltimasPromociones === 0">No hay promociones registradas</td>
                <td colspan="6" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando promociones</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalHistorialPrecios" tabindex="-1" role="dialog" aria-labelledby="modalHistorialPreciosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialPreciosLabel">Historial de precios @{{ producto.nombre_es }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <select class="form-control" v-model="iMesPrecios">
                            <option value="0">ENE</option>
                            <option value="1">FEB</option>
                            <option value="2">MAR</option>
                            <option value="3">ABR</option>
                            <option value="4">MAY</option>
                            <option value="5">JUN</option>
                            <option value="6">JUL</option>
                            <option value="7">AGO</option>
                            <option value="8">SET</option>
                            <option value="9">OCT</option>
                            <option value="10">NOV</option>
                            <option value="11">DIC</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-control" v-model="iAnioPrecios">
                            <option v-for="anio in lstAniosPrecios" :value="anio.value">@{{ anio.value }}</option>
                        </select>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Precio</th>
                            <th class="text-center">Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="precio in lstPrecios">
                            <td>S/ @{{ precio.monto.toFixed(2) }}</td>
                            <td class="text-center">@{{ precio.fecha_reg }}</td>
                        </tr>
                        <tr v-if="lstPrecios.length === 0">
                            <td colspan="2" class="text-center" v-if="iCargandoPrecios === 0">No hay precios registrados</td>
                            <td colspan="2" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando precios</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorialOfertas" tabindex="-1" role="dialog" aria-labelledby="modalHistorialOfertasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialOfertasLabel">Historial de ofertas @{{ producto.nombre_es }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <select class="form-control" v-model="iMesOfertas">
                            <option value="0">ENE</option>
                            <option value="1">FEB</option>
                            <option value="2">MAR</option>
                            <option value="3">ABR</option>
                            <option value="4">MAY</option>
                            <option value="5">JUN</option>
                            <option value="6">JUL</option>
                            <option value="7">AGO</option>
                            <option value="8">SET</option>
                            <option value="9">OCT</option>
                            <option value="10">NOV</option>
                            <option value="11">DIC</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-control" v-model="iAnioOfertas">
                            <option v-for="anio in lstAniosOfertas" :value="anio.value">@{{ anio.value }}</option>
                        </select>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Oferta</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fin</th>
                            <th class="text-center">Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="oferta in lstOfertas">
                            <td>
                                <span v-if="oferta.monto">S/ @{{ oferta.monto.toFixed(2) }}</span>
                                <span v-else="oferta.porcentaje">@{{ oferta.porcentaje.toFixed(2) }}%</span>
                            </td>
                            <td class="text-center">@{{ oferta.fecha_inicio }}</td>
                            <td class="text-center">@{{ oferta.fecha_vencimiento }}</td>
                            <td class="text-center">@{{ oferta.fecha_reg }}</td>
                        </tr>
                        <tr v-if="lstOfertas.length === 0">
                            <td colspan="4" class="text-center" v-if="iCargandoOfertas === 0">No hay ofertas registradas</td>
                            <td colspan="4" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando ofertas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorialPromociones" tabindex="-1" role="dialog" aria-labelledby="modalHistorialPromocionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialPromocionesLabel">Historial de promociones @{{ producto.nombre_es }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <select class="form-control" v-model="iMesPromociones">
                            <option value="0">ENE</option>
                            <option value="1">FEB</option>
                            <option value="2">MAR</option>
                            <option value="3">ABR</option>
                            <option value="4">MAY</option>
                            <option value="5">JUN</option>
                            <option value="6">JUL</option>
                            <option value="7">AGO</option>
                            <option value="8">SET</option>
                            <option value="9">OCT</option>
                            <option value="10">NOV</option>
                            <option value="11">DIC</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <select class="form-control" v-model="iAnioPromociones">
                            <option v-for="anio in lstAniosPromociones" :value="anio.value">@{{ anio.value }}</option>
                        </select>
                    </div>
                </div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Promoci&oacute;n</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Fin</th>
                            <th class="text-center">Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="promocion in lstPromociones">
                            <td>
                                <span v-if="promocion.monto">S/ @{{ promocion.monto.toFixed(2) }}</span>
                                <span v-else="promocion.porcentaje">@{{ promocion.porcentaje.toFixed(2) }}%</span>
                            </td>
                            <td class="text-center">@{{ promocion.fecha_inicio }}</td>
                            <td class="text-center">@{{ promocion.fecha_vencimiento }}</td>
                            <td class="text-center">@{{ promocion.fecha_reg }}</td>
                        </tr>
                        <tr v-if="lstPromociones.length === 0">
                            <td colspan="4" class="text-center" v-if="iCargandoPromociones === 0">No hay promociones registradas</td>
                            <td colspan="4" class="text-center" v-else><i class="fas fa-circle-notch fa-spin"></i> Cargando promociones</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
