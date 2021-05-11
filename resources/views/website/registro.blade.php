@extends('website.layout')

@section('title', 'Registro')

@section('content')
    <section class="py-5">
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
                            <input type="email" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['Email'] }}" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center justify-content-md-between">
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Password'] }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['Password'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Confirm password'] }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['Confirm password'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['Name'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group" v-if="sTipoDocumento == 1">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['Last Name'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" :required="sTipoDocumento == 1" placeholder="{{ $lstTraduccionesRegistro['Last Name'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['address'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['address'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Departamento <span class="text-danger">*</span></label>
                            <select name="departamento" class="form-control" required="required"></select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Provincia <span class="text-danger">*</span></label>
                            <select name="provincia" class="form-control" required="required"></select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Distrito <span class="text-danger">*</span></label>
                            <select name="distrito" class="form-control" required="required"></select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">Documento</label>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <select class="form-control" v-model="sTipoDocumento">
                                        <option value="1">DNI</option>
                                        <option value="6">RUC</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-8">
                                    <input class="form-control" :maxlength="sTipoDocumento == 1 ? 8 : 11" required="required" placeholder="{{ $lstTraduccionesRegistro['ID Number'] }}" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['gender'] }}</label>
                            <select name="provincia" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['landline_phone'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['landline_phone'] }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['cell_phone'] }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" required="required" placeholder="{{ $lstTraduccionesRegistro['cell_phone'] }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-11 col-md-5">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ $lstTraduccionesRegistro['date_of_birth'] }}</label>
                            <input type="date" class="form-control" required="required" autocomplete="off">
                        </div>
                    </div>

                    <div class="col-11 col-md-12">
                        <div class="form-group mt-5" v-cloak>
                            <div v-icheck>
                                <label class="m-0">
                                    <input type="checkbox" required="required">&nbsp;{!! $lstTraduccionesRegistro['accept'] !!}
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
    <script src="/js/website/registro.js?cvcn=14"></script>
@endsection
