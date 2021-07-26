@extends('website.layout')

@section('title', 'Servicios')

@section('content')
    <div class="container-xl py-5 text-center" v-if="iCargando === 1">
        <img class="my-5" src="/img/spinner.svg">
    </div>

    <section v-if="iCargando === 0">
        <a :href="pagina.enlace_imagen_portada" v-if="pagina.enlace_imagen_portada">
            <img :src="pagina.ruta_imagen_portada" class="w-100">
        </a>
        <img :src="pagina.ruta_imagen_portada" class="w-100" v-else>
    </section>

    <div class="container-xl" v-if="iCargando === 0" v-cloak>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>

    <section class="py-4" v-if="iCargando === 0">
        <div class="container-xl pb-4" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>

    <section class="pt-5 pb-4 bg-ecovalle-6">
        <div class="row justify-content-center">
            <div class="col-10 col-md-6 col-lg-5">
                <h2 class="h3 text-center mb-5 text-androgyne">{{ $lstTraduccionesServicios['we_will_help_you'] }}</h2>
                <form v-on:submit.prevent="ajaxEnviarMensaje" id="frmServicios">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="apellidos_y_nombres" placeholder="{{ $lstTraduccionesServicios['last_name_and_name'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="razon_social_o_ruc" placeholder="{{ $lstTraduccionesServicios['company'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="ciudad" placeholder="{{ $lstTraduccionesServicios['city'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="celular" placeholder="{{ $lstTraduccionesServicios['phone'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-12" v-if="respuesta">
                            <p class="p-2 rounded text-white text-center" :class="'bg-' + respuesta.result">@{{ respuesta.mensaje }}</p>
                        </div>
                        <div class="col-md-12 text-center">
                            <button class="btn btn-amarillo mt-3 text-uppercase" :disabled="iEnviandoMensaje === 1">
                                <span v-if="iEnviandoMensaje === 0">{{ $lstTraduccionesServicios['start_now'] }}</span>
                                <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/servicios.js?cvcn=14"></script>
@endsection
