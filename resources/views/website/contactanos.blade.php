@extends('website.layout')

@section('title', 'Contáctanos')

@section('content')
    <section class="h-35">
        <img src="{{ $empresa->ruta_imagen_contactanos }}" class="w-100 h-100">
    </section>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['contact_us'] }}</li>
            </ol>
        </nav>
    </div>

    <section class="py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 pt-md-4 pt-lg-0 pb-5">
                    <div class="p-3 bg-light mb-4">
                        <h1 class="h5 text-center text-lg-left text-ecovalle-2 font-weight-bold">{!! $lstLocalesContactanos['Address'] !!}</h1>
                        <p class="mb-0 text-center text-lg-left">{{ $empresa->direccion }}</p>
                    </div>
                    @if($empresa->telefonos->count() > 0)
                        <div class="p-3 bg-light mb-4">
                            <h1 class="h5 text-center text-lg-left text-ecovalle-2 font-weight-bold">{!! $lstLocalesContactanos['Phones Ecovalle'] !!}</h1>
                            @foreach($empresa->telefonos as $telefono)
                                <p class="mb-0 text-center text-lg-left">
                                    <a class="nav-ecovalle" href="tel:{{ $telefono->numero }}"><i class="{{ $telefono->icono }}"></i>&nbsp;{{ $telefono->numero }}</a>
                                </p>
                            @endforeach
                        </div>
                    @endif
                    <div class="p-3 bg-light mb-4">
                        <h1 class="h5 text-center text-lg-left text-ecovalle-2 font-weight-bold mb-2">{!! $lstLocalesContactanos['Follow Us'] !!}</h1>
                        <p class="mb-0 text-center text-lg-left">
                            @if($empresa->enlace_facebook)
                                <a href="{{ $empresa->enlace_facebook }}" target="_blank" class="btn btn-sm btn-social-icon btn-facebook mr-1">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if($empresa->enlace_instagram)
                                <a href="{{ $empresa->enlace_instagram }}" target="_blank" class="btn btn-sm btn-social-icon btn-instagram mr-1">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($empresa->enlace_youtube)
                                <a href="{{ $empresa->enlace_youtube }}" target="_blank" class="btn btn-sm btn-social-icon btn-pinterest mr-1">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                            @if($empresa->enlace_tiktok)
                                <a href="{{ $empresa->enlace_tiktok }}" target="_blank" class="btn btn-sm btn-social-icon btn-github mr-1">
                                    <i class="fab fa-tiktok"></i>
                                </a>
                            @endif
                            @if($empresa->enlace_twitter)
                                <a href="{{ $empresa->enlace_twitter }}" target="_blank" class="btn btn-sm btn-social-icon btn-twitter mr-1">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if($empresa->enlace_linkedin)
                                <a href="{{ $empresa->enlace_linkedin }}" target="_blank" class="btn btn-sm btn-social-icon btn-linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-lg-8">
                    <h1 class="h4 text-ecovalle-2 font-weight-bold mb-4">{!! $lstLocalesContactanos['Keep in touch with us'] !!}</h1>
                    <form v-on:submit.prevent="ajaxEnviarMensaje" id="frmEnviarMensaje" v-cloak>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">{{ $lstLocalesContactanos['Subject'] }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="asunto" v-model="sAsunto" required="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">{{ $lstLocalesContactanos['Name'] }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nombres" v-model="sNombres" required="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">{{ $lstLocalesContactanos['Last Name'] }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="apellidos" v-model="sApellidos" required="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" v-model="sEmail" required="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">{!! $lstLocalesContactanos['Phone'] !!} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="telefono" v-model="sTelefono" required="required" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group overflow-hidden">
                                    <label class="d-block text-ecovalle-2">{{ $lstLocalesContactanos['Message'] }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="mensaje" rows="5" v-model="sMensaje" required="required" autocomplete="off"></textarea>
                                    <small class="float-left">{!! $lstLocalesContactanos['Minimum characters'] !!}: 150</small>
                                    <small class="float-right">{!! $lstLocalesContactanos['Total characters'] !!}: @{{ sMensaje.trim().length }}</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="text-ecovalle-2">{{ $lstLocalesContactanos['include_image'] }}</label>
                                    <input type="file" class="form-control px-1 py-1" name="imagen">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mt-2">
                                    <div v-icheck>
                                        <label class="m-0">
                                            <input type="checkbox" name="accept_term_cond" required>&nbsp;@{{ locale === 'es' ? 'Aceptar' : 'Accept' }} <a href="/terminos-condiciones">{!! $lstLocalesContactanos['accept_term_cond'] !!}</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" v-if="respuesta">
                                <p class="p-2 rounded text-white" :class="'bg-' + respuesta.result">@{{ respuesta.mensaje }}</p>
                            </div>
                            <div class="col-md-12 text-center text-lg-left pt-3">
                                <button class="btn btn-xl btn-ecovalle" type="submit" :disabled="!bFormularioCorrecto">
                                    <span v-if="iEnviandoMensaje === 0">{{ $lstLocalesContactanos['Send'] }}</span>
                                    <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-2 pb-5">
        <div class="container pb-4">
            {!! $empresa->enlace_mapa !!}
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/contactanos.js?cvcn=14"></script>
@endsection
