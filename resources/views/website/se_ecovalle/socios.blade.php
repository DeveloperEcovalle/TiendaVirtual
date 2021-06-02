@extends('website.layout')

@section('title', 'Socios')
@section('izipay')
    <link href="/css/gps.css" rel="stylesheet">
@endsection
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
                    @{{ locale === 'es' ? pagina . nombre_espanol : pagina . nombre_ingles }}
                </li>
            </ol>
        </nav>
    </div>

    <section class="py-4" v-if="iCargando === 0">
        <div class="container-xl pb-4" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
    </section>
    <div class="container-xl pb-4">
        <div class="row">
            <div class="col-lg-9">
                <div id="mapa" style="width:100%;height:500px;">

                </div>
            </div>
            <div class="col-lg-3">
                <div class="input-group">
                    <input type="text" class="form-control" v-model="search"
                        placeholder="Buscar cliente">
                    <div class="input-group-append">
                        <button class="btn" style="background-color:#02793C;" type="button">
                            <i class="fa fa-search" style="color:white;"></i>
                        </button>
                    </div>
                </div>
                <br>
                <div class="contenedor_gps websitegps">
                    <table class="table table-bordered table-hover">
                        <tr v-for="post in filteredList">
                            <td style="border:none;cursor: pointer;">
                                <i class="fa fa-arrow-circle-o-right" aria-hidden="true"
                                    v-on:click="generarRuta(post.nombre)"></i>
                            </td>
                            <td style="border:none;font-size: 12px;" v-on:click="vermarcador(post.nombre)">
                                @{{ post . nombre }}
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <section class="pt-5 pb-4 bg-ecovalle-6">
        <div class="row justify-content-center">
            <div class="col-10 col-md-6 col-lg-5">
                <h2 class="h3 text-center mb-5 text-androgyne">{{ $lstTraduccionesSocios['learn_the_oportunities'] }}</h2>
                <form v-on:submit.prevent="ajaxEnviarMensaje" id="frmSocio">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="apellidos_y_nombres"
                                    placeholder="{{ $lstTraduccionesSocios['last_name_and_name'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="razon_social_o_ruc"
                                    placeholder="{{ $lstTraduccionesSocios['company'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="ciudad"
                                    placeholder="{{ $lstTraduccionesSocios['city'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" name="celular"
                                    placeholder="{{ $lstTraduccionesSocios['phone'] }}" required="required">
                            </div>
                        </div>
                        <div class="col-md-12" v-if="respuesta">
                            <p class="p-2 rounded text-white text-center" :class="'bg-' + respuesta.result">
                                @{{ respuesta . mensaje }}</p>
                        </div>
                        <div class="col-md-12 text-center">
                            <button class="btn btn-amarillo mt-3 text-uppercase" :disabled="iEnviandoMensaje === 1">
                                <span v-if="iEnviandoMensaje === 0">{{ $lstTraduccionesSocios['join_here'] }}</span>
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
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAS6qv64RYCHFJOygheJS7DvBDYB0iV2wI"></script>
    <script src="/js/website/socios.js?cvcn=14"></script>
@endsection
