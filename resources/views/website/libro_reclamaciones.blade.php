@extends('website.layout')

@section('title', 'Libro Reclamaciones')

@section('content')

    <section class="h-35">
        <img src="{{ $empresa->ruta_imagen_libro }}" class="w-100 h-100">
    </section>

    <div class="container-xl" v-if="iCargandoLR === 0" v-cloak>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>
    
    <section class="py-4">
        <div class="container-xl pb-5">
            <h1 class="h4 text-ecovalle-2 font-weight-bold">{!! $lstLocalesLR['Virtual Complaints Book'] !!}</h1>
            <p class="text-justity" v-cloak>
                {{ session()->get('locale') === 'es' ? $empresa->mensaje_libro_reclamaciones_es :  $empresa->mensaje_libro_reclamaciones_en}}<br>
                @{{ locale === 'es' ? 'Razón Social:' : "Business name:" }} {{ $empresa->razon_social }}<br>
                RUC: {{ $empresa->ruc_empresa }}<br>
                @{{ locale === 'es' ? 'Dirección:' : "Address:" }} {{ $empresa->direccion }}
            </p>
            <p class="text-amarillo-ecovalle d-none" v-cloak><b>@{{ locale === 'es' ? 'Reclamo N°' : "Claim No." }} </b></p>

            <form v-on:submit.prevent="ajaxEnviarReclamo" id="frmEnviarReclamo" v-cloak>
                <h2 class="h4 text-ecovalle-2 font-weight-bold mb-2 mt-4">{!! $lstLocalesLR['Identification of the complaining consumer'] !!}</h2>
                <div class="hr-line-dashed"></div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombres" v-model="sNombres" required="required" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Last Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="apellidos" v-model="sApellidos" required="required" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Phone'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="telefono" v-model="sTelefono" required="required" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Another phone'] }}</label>
                            <input type="text" class="form-control" name="otelefono" v-model="sOTelefono" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Address'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="direccion" v-model="sDireccion" required="required" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Lot'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="lote" v-model="sLote" required="required" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['DeptInt'] }}</label>
                            <input type="text" class="form-control" name="dept_int" v-model="sDepInt" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Urbanization'] }}</label>
                            <input type="text" class="form-control" name="urbanizacion" v-model="sUrbanizacion" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Reference'] }}</label>
                            <input type="text" class="form-control" name="referencia" v-model="sReferencia" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Departament'] }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="departamento" required="required" v-model="sDepartamento">
                                <option value="" selected> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option v-for="departamento in lstDepartamentos" :value="departamento">@{{ departamento }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Province'] }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="provincia"  v-model="sProvincia" required="required">
                                <option value=""> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option v-for="provincia in lstProvincias" :value="provincia">@{{ provincia }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['District'] }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="distrito" v-model="sDistrito" required="required">
                                <option value="" selected> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{ distrito.distrito }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Document type'] }} <span class="text-danger">*</span></label>
                            <select class="form-control" name="tipo_documento" v-model="sTDocumento" required>
                                <option value="" selected> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option value="DNI" selected>DNI</option>
                                <option value="Carné de Extranjería" selected>Carné de Extranjería</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Document number'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="numero_documento" v-model="sNDocumento" minlength="8" required>
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Email'] }}</label>
                            <input type="email" class="form-control" name="email" v-model="sEmail" autocomplete="off">
                        </div>
                    </div>
                </div>

                <h2 class="h4 text-ecovalle-2 font-weight-bold mb-2 mt-4">{!! $lstLocalesLR['Identification of the contracted asset'] !!}</h2>
                <div class="hr-line-dashed"></div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Amount of the good object of claim'] }}</label>
                            <input type="text" class="form-control" name="monto_bien" v-model="sMBien">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{!! $lstLocalesLR['Identification of the contracted asset'] !!} <span class="text-danger">*</span></label>
                            <select class="form-control" name="bien_contratado" v-model="sBContratado" required>
                                <option value="" selected> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option value="Producto"> @{{ locale === 'es' ? "Producto" : "Product" }}</option>
                                <option value="Servicio"> @{{ locale === 'es' ? "Servicio" : "Service" }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Description'] }}  <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="descripcion" v-model="sDescripcion" required="required" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <h2 class="h4 text-ecovalle-2 font-weight-bold mb-2 mt-4">{!! $lstLocalesLR['Claim detail'] !!}</h2>
                <div class="hr-line-dashed"></div>

                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Order number'] }}</label>
                            <input type="text" class="form-control" name="numero_pedido" v-model="sNPedido">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{!! $lstLocalesLR['Claim type'] !!} <span class="text-danger">*</span></label>
                            <select class="form-control" name="tipo_reclamo" v-model="sTReclamo" required="required">
                                <option value="" selected> @{{ locale === 'es' ? "Seleccionar" : "To Select" }}</option>
                                <option value="Reclamo"> @{{ locale === 'es' ? "Reclamo" : "Claim" }}</option>
                                <option value="Queja"> @{{ locale === 'es' ? "Queja" : "Complain" }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Detail'] }}</label>
                            <textarea class="form-control" name="detalle" v-model="sDetalle" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['Order'] }}</label>
                            <textarea class="form-control" name="pedido" v-model="sPedido" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-lg-12">
                        <div class="form-group alert-success p-3 amy-auto">
                            <p v-if="locale === 'es'" class="text-verde-ecovalle p-0 m-0" style="font-size: 10px;font-weight: 900">
                                <b>RECLAMO:</b> Disconformidad relacionada a los productos.<br>
                                <b>QUEJA:</b> Disconformidad relacionada a los productos o servicios o malestar o descontento respecto a la atención al público. 
                            </p>
                            <p v-else class="text-verde-ecovalle p-0 m-0" style="font-size: 10px;font-weight: 900">
                                <b>CLAIM:</b> Non-conformity related to the products.<br>
                                <b>COMPLAIN:</b> Disagreement related to products or services or discomfort or dissatisfaction with the attention to the public. 
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="h4 text-ecovalle-2 font-weight-bold mb-2 mt-4">{!! $lstLocalesLR['Actions taken by the provider'] !!}</h2>
                <div class="hr-line-dashed"></div>
                <div class="row ml-2 mr-2">
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <label class="text-ecovalle-2">{{ $lstLocalesLR['DetailO'] }}</label>
                            <textarea class="form-control" name="detalleo" v-model="sDetalleo" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            
                        </div>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-12" v-if="respuesta">
                        <p class="p-2 rounded text-white" :class="'bg-' + respuesta.result">@{{ respuesta.mensaje }}</p>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-xl btn-ecovalle" :disabled="!bFormularioCorrecto">
                            <span v-if="iEnviandoReclamo === 0">{{ $lstLocalesLR['Send'] }}</span>
                            <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    @parent
@endsection

@section('js')
    <script src="/js/website/libroReclamaciones.js?cvcn=14"></script>
@endsection
