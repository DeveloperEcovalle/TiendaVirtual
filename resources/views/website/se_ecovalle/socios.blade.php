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
    <div id="sidebargps">
        <div id="gpsmenu" @click="showSidebar()" data-show="0">
            <i class="fa fa-bars fa-2x" aria-hidden="true"></i>
        </div>
        <div class="siderbarmenu">
            <div class="btnexitgpsmenu" @click="showSidebar()">
                <i class="fa fa-caret-left fa-2x" aria-hidden="true"></i>
            </div>
            <div class="bodymenugps">
                <div id="listacliente">
                    <div class="input-group" style="width:280px;">
                        <input type="text" class="form-control" v-model="search" placeholder="Buscar cliente">
                        <div class="input-group-append">
                            <button class="btn" style="background-color:#02793C;" type="button">
                                <i class="fa fa-search" style="color:white;"></i>
                            </button>
                        </div>
                    </div>
                    <br>
                    <div class="contenedor_gps">
                        <div v-for="post in filteredList" id="contenedorcliente">
                            <div class="row" style="width:280px;padding-top:5px;">
                                <div class="col-lg-12" v-on:click="marcadorcliente(post.nombre)" style="cursor: pointer;">
                                    <i class="fa fa-user" aria-hidden="true" style="color:green;margin-right:5px;"></i>@{{ post . nombre }}
                                </div>
                            </div>
                            <hr style="width:260px;">
                        </div>
                    </div>
                </div>
                <div id="contenidocliente" style="padding: 0px;margin:0px;display:none;">
                    <img src="{{ asset('img/tipos-de-tiendas.jpg') }}" class="imgpscliente">
                    <div class="titulogps">
                            <div class="onegrid"><i class="fa fa-arrow-left" aria-hidden="true" @click="menugeneral()"></i></div>
                            <div class="twogrid" @click="vermarcador()">6G E.I.R.L.</div>
                            <div class="threegrid" @click="generarRuta()"><i class="fa fa-map" aria-hidden="true"></i></div>
                    </div>
                    <!--<div class="direcciongps">
                        nombre:
                        <br>
                        AVILA REYES WILMER ISMAEL
                    </div>-->
                    <div class="nclientgps">
                       <p style="margin:0px;color:rgb(189, 13, 13);">Detalles del Mapa:</p>
                       <p id="direccion" style="margin:0px 0px 0px 13px;">Av. Nicolás de Piérola 1382, Trujillo 13011</p>
                       <p style="margin:0px;color:rgb(189, 13, 13);">Numero:</p>
                        <p id="numero" style="margin:0px 0px 0px 13px;"> +51 1 6763987</p>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>
    <section class="py-4" v-if="iCargando === 0">
        <div class="container-xl" v-html="locale === 'es' ? pagina.contenido_espanol : pagina.contenido_ingles"></div>
        <div class="container-xl">
            <div class="py-5">
                <h2 class="h3 font-weight-bold titulo-subrayado text-center mb-4">Beneficios</h2>
                <div class="row justify-content-center">
                    <div class="col-11 col-md-4" v-for="beneficio in lstBeneficios" style="cursor: pointer;" v-on:click="ajaxPopup(beneficio)">
                        <div class="px-md-5 text-center">
                            <img class="img-fluid" :src="beneficio.ruta_imagen">
                            <h5 class="text-ecovalle-2 font-weight-bold text-center">@{{ beneficio.nombre }}</h5>
                            <div class="module text-limit">
                                <p class="text-center">@{{ beneficio.descripcion }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="container-xl pb-4">
        <div class="row">
            <div class="col-lg-12">
                <div id="mapa" style="width:100%;height:500px;">
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
