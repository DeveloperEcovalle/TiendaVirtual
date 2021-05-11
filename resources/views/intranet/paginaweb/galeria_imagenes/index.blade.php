@extends('intranet.layout_sidebar')

@section('title', 'GALERÍA DE IMÁGENES')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-12 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>P&aacute;gina web</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Galería de imágenes</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="row">
                    <div class="col-12">
                        <form action="#" class="dropzone">
                            <div class="fallback">
                                <input name="file" type="file" multiple/>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row" v-cloak>
                    <div class="col-12">
                        <h4 class="mt-4">Im&aacute;genes agregadas</h4>
                    </div>
                    <div class="col-12 text-center" v-if="iCargandoImagenes === 1">
                        <i class="fas fa-circle-notch fa-spin"></i>
                    </div>
                    <div class="col-12" v-else>
                        <div class="row">
                            <div class="col-12 text-center" v-if="lstImagenes.length === 0">
                                <p>La galería de imágenes está vacía.</p>
                            </div>
                            <div class="col-sm-3 col-lg-2 mb-4 text-center" v-for="(imagen, i) in lstImagenes">
                                <div class="h-150 rounded-lg galeria" :style="{ backgroundImage: 'url(' + imagen.ruta + ')' }"></div>
                                <a class="d-block mt-1" href="#" title="Copiar enlace de la imagen al portapapeles" v-on:click="copiarPortapapeles(imagen.ruta)">
                                    <i class="fas fa-copy"></i>&nbsp;Copiar enlace
                                </a>
                                <a href="#" class="d-block text-danger" title="Eliminar imagen" v-on:click.prevent="ajaxEliminarImagen(imagen.id, i)">
                                    <i class="fas fa-trash-alt"></i> Eliminar imagen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 p-0" id="panel">
        </div>
    </div>
    <div class="row m-0 justify-content-center" v-else>
        <div class="col-12 col-md-10 col-lg-8 pt-5">
            <h3 class="text-danger text-center font-bold">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                Ocurri&oacute; un error inesperado.&nbsp;
                Volver a cargar la p&aacute;gina deber&iacute;a solucionar el problema.<br>
                Si el error persiste, comun&iacute;quese con el administrador del sistema.
            </h3>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/intranet/galeriaImagenes.js?cvcn=14"></script>
@endsection
