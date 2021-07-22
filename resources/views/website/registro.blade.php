@extends('website.layout')

@section('title', 'Registro')

@section('content')
    <section class="py-2">
        <div class="container-xl">
            <form role="form" id="frmRegistro" v-on:submit.prevent="ajaxRegistrar()">
                <div class="row pt-5 pb-3 justify-content-center justify-content-md-between">
                    <div class="col-11">
                        <h1 class="h2 font-weight-bold text-uppercase text-ecovalle-2 mb-4">{{ $lstTraduccionesRegistro['Registration'] }}</h1>
                        <p><span class="text-danger">*</span> {{ $lstTraduccionesRegistro['required_fields'] }}</p>
                    </div>
                </div>
                <div class="row justify-content-center justify-content-md-between">
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Email'] . ' (' . $lstTraduccionesRegistro['this_will_be_your_user'] . ')' }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['Email'] }}" name="correo" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center justify-content-md-between">
                    <div class="col-11 col-md-5">
                        <div class="form-group" v-cloak>
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Password'] }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password1" :class="sPassword != sCPassword ? 'is-invalid' : ''" v-model="sPassword" name="password"  required="required" placeholder="{{ $lstTraduccionesRegistro['Password'] }}" autocomplete="off" minlength="6">
                                <span class="input-group-append"><button class="btn btn-password" value="0" v-on:click="clickactionPassword('password1','pass1')" type="button"><i id="pass1" class="fa fa-eye"></i> </button></span>
                            </div>
                            <span v-if="sPassword != sCPassword">
                                <strong style="color: red;font-size: 12px;">Contraseñas diferentes</strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group" v-cloak>
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Confirm password'] }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password2" :class="sPassword != sCPassword ? 'is-invalid' : ''" required="required" name="cpassword" v-model="sCPassword" placeholder="{{ $lstTraduccionesRegistro['Confirm password'] }}" autocomplete="off">
                                <span class="input-group-append"><button class="btn btn-password" value="0" v-on:click="clickactionPassword('password2','pass2')" type="button"><i id="pass2" class="fa fa-eye"></i> </button></span>
                            </div>
                            <span v-if="sPassword != sCPassword">
                                <strong style="color: red;font-size: 12px;">Contraseñas diferentes</strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Documento <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <select class="form-control" name="tipo_documento" v-model="sTipoDocumento" required>
                                        <option value="DNI">DNI</option> <!-- 1 -->
                                        <option value="RUC">RUC</option> <!-- 6 -->
                                    </select>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="input-group">
                                        <input class="form-control" name="documento" v-model="sDocumento" :maxlength="sTipoDocumento == 'DNI' ? 8 : 11" required placeholder="{{ $lstTraduccionesRegistro['ID Number'] }}" autocomplete="off">
                                        <span v-if="iConsultandoApi === 0" class="input-group-append"><button class="btn btn-ecovalle-2" v-on:click.prevent="ajaxConsultaApi()"><i class="fa fa-search"></i> </button></span>
                                        <span v-else  class="input-group-append"><button class="btn btn-ecovalle-2" disabled><i class="fas fa-circle-notch fa-spin"></i> </button></span>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" v-model="sNombres" name="nombres" placeholder="{{ $lstTraduccionesRegistro['Name'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group" v-if="sTipoDocumento == 'DNI'">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Last Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="sApellidos" :required="sTipoDocumento == 1" placeholder="{{ $lstTraduccionesRegistro['Last Name'] }}" name="apellidos" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['address'] }} <span class="text-danger">*</span></label>
                            <input type="text" name="direccion" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['address'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
                            <select name="departamento" class="form-control" v-model="sDepartamento" required="required">
                                <option value="">Seleccionar</option>
                                <option v-for="departamento in lstDepartamentos" :value="departamento">@{{departamento}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Provincia <span class="text-danger">*</span></label>
                            <select name="provincia" v-model="sProvincia" class="form-control" required="required">
                                <option value="">Seleccionar</option>
                                <option v-for="provincia in lstProvincias" :value="provincia">@{{provincia}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Distrito <span class="text-danger">*</span></label>
                            <select name="distrito" class="form-control" v-model="sDistrito" required="required">
                                <option value="">Seleccionar</option>
                                <option v-for="distrito in lstDistritos" :value="distrito.distrito">@{{distrito.distrito}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['gender'] }}</label>
                            <select name="genero" class="form-control">
                                <option value="">Seleccionar</option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['cell_phone'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" name="telefono" placeholder="{{ $lstTraduccionesRegistro['cell_phone'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['landline_phone'] }}</label>
                            <input type="text" class="form-control" name="telefono_fijo" placeholder="{{ $lstTraduccionesRegistro['landline_phone'] }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['date_of_birth'] }}</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-11 col-md-12">
                        <div class="form-group mt-5">
                            <div v-icheck>
                                <label class="m-0">
                                    <input type="checkbox" name="accept_term_cond" required>&nbsp;@{{ locale === 'es' ? 'Aceptar' : 'Accept' }} <a href="/terminos-condiciones">{!! $lstTraduccionesRegistro['accept_term_cond'] !!}</a>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-11 col-md-12">
                        <div class="form-group" v-cloak>
                            <div v-icheck>
                                <label class="m-0">
                                    <input type="checkbox" name="acepto_terminos_y_condiciones_y_politica_de_privacidad" required>&nbsp;{!! $lstTraduccionesRegistro['accept'] !!}
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-danger" v-if="sMensaje.length > 0" v-html="sMensaje" v-cloak></div>
                        <div class="form-group my-4 text-center" v-cloak>
                            <button type="submit" class="btn btn-xl btn-ecovalle text-uppercase px-5 py-2" :disabled="iRegistrando === 1">
                                <span v-if="iRegistrando === 0">{{ $lstTraduccionesRegistro['register'] }}</span>
                                <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                            </button>
                            <p class="py-3">* {{ $lstTraduccionesRegistro['clicking_register'] }}</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/registro.js?n=1"></script>
@endsection
