@extends('website.layout')

@section('title', 'Recursos Humanos')

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
                <li class="breadcrumb-item">{{ $lstLocales['Be Ecovalle'] }}</li>
                <li class="breadcrumb-item active" aria-current="page">
                    @{{ locale === 'es' ? pagina.nombre_espanol : pagina.nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>

    <section class="py-4" v-if="iCargando === 0">
        <div class="pb-4" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>

    <section class="pt-5 pb-4 bg-ecovalle-6">
        <div class="row justify-content-center">
            <div class="col-10 col-md-6 col-lg-5">
                <h2 class="h3 text-center mb-5 text-androgyne">{{ $lstTraduccionesRecursosHumanos['learn_the_oportunities'] }}</h2>
                <form v-on:submit.prevent="ajaxEnviarMensaje" id="frmRecursosHumanos">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="apellidos_y_nombres" placeholder="{{ $lstTraduccionesRecursosHumanos['last_name_and_name'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="asunto" placeholder="{{ $lstTraduccionesRecursosHumanos['subject_position_to_apply_for'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="file" accept="image/*,application/pdf" class="form-control" name="archivo_adjunto" placeholder="{{ $lstTraduccionesRecursosHumanos['attach_file'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <button class="btn btn-amarillo text-uppercase" :disabled="iEnviandoMensaje === 1">
                                <span v-if="iEnviandoMensaje === 0">{{ $lstTraduccionesRecursosHumanos['apply_here'] }}</span>
                                <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                            </button>
                        </div>
                        <div class="col-md-12" v-if="respuesta">
                            <p class="p-2 rounded text-white text-center" :class="'bg-' + respuesta.result">@{{ respuesta.mensaje }}</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="/js/website/recursosHumanos.js?cvcn=14"></script>
@endsection
