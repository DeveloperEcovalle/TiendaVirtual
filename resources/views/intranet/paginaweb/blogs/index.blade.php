@extends('intranet.layout_sidebar')

@section('title', 'BANNERS')

@section('head')
    <link href="/css/website.css" rel="stylesheet">
@endsection

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-8 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-8 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>P&aacute;gina web</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Blog</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-2">
                    <button class="btn btn-info float-right mt-2" data-toggle="modal" data-target="#modalActualizar"><i class="fas fa-edit"></i> Actualizar</button>
                </div>
                <div class="col-2">
                    <button class="btn btn-primary float-right mt-2" v-on:click="panelNuevo"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center bg-white" style="width: 50px"><i class="fas fa-calendar-alt fa-2x"></i></th>
                            <th class="bg-white">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select class="form-control" v-model="sPeriodo" v-on:change="ajaxListar()" v-cloak>
                                            <option>Diario</option>
                                            <option>Mensual</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" v-if="sPeriodo === 'Diario'">
                                        <select class="form-control" v-model="iDia" v-on:change="ajaxListar()" v-cloak>
                                            <option v-for="dia of lstDias">@{{ dia }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" v-model="iMes" v-on:change="ajaxListar()" v-cloak>
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
                                    <div class="col-md-2">
                                        <select class="form-control" v-model="iAnio" v-on:change="ajaxListar()" v-cloak>
                                            <option v-for="anio in lstAnios" :value="anio.value">@{{ anio.value }}</option>
                                        </select>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                </table>
                <table class="table table-bordered table-hover" id="tblBlogs">
                    <thead>
                        <tr>
                            <th class="bg-primary">Categor&iacute;a</th>
                            <th class="bg-primary">T&iacute;tulo</th>
                            <th class="bg-primary">Resumen</th>
                            <th class="bg-primary">Autor</th>
                            <th class="bg-primary text-center">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="blog of lstBlogs" v-bind:class="{selected: iIdSeleccionado === blog.id}" v-on:click="panelEditar(blog.id)" style="cursor: pointer;" v-cloak>
                            <td>@{{ blog.categoria.nombre_espanol }}</td>
                            <td>@{{ blog.titulo }}</td>
                            <td class="w-50">@{{ blog.resumen }}</td>
                            <td>@{{ blog.usuario.persona.nombres }}</td>
                            <td class="text-center">@{{ blog.fecha_reg }}</td>
                        </tr>
                        <tr v-if="lstBlogs.length === 0" v-cloak>
                            <td colspan="5" class="text-center">No hay datos para mostrar</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 col-md-4 p-0" id="panel">
        </div>
    </div>
    <div class="row m-0 justify-content-center" v-else v-cloak>
        <div class="col-12 col-md-10 col-lg-8 pt-5">
            <h3 class="text-danger text-center font-bold">
                <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                Ocurri&oacute; un error inesperado.&nbsp;
                Volver a cargar la p&aacute;gina deber&iacute;a solucionar el problema.<br>
                Si el error persiste, comun&iacute;quese con el administrador del sistema.
            </h3>
        </div>
    </div>

    <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="modalActualizarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalActualizarLabel">Blog</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-4" id="layoutLeft">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="ibox ">
                                    <div class="ibox-title">
                                        <h5>Imagen de portada</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevaImagen + ')' }" v-if="nuevaImagenPortada"></div>
                                        <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + pagina.ruta_imagen_portada + ')' }" v-else></div>
                                        <form role="form" class="mt-4" id="frmEditarImagenPortada" v-on:submit.prevent="ajaxActualizarImagenPortada" v-cloak>
                                            <div class="form-group row">
                                                <label class="col-form-label col-md-3">Cambiar imagen de portada:</label>
                                                <div class="col-md-9">
                                                    <div class="custom-file">
                                                        <input id="aImagen" type="file" class="custom-file-input" name="imagen_de_portada" v-on:change="cambiarImagen">
                                                        <label for="aImagen" class="custom-file-label">@{{ sNombreNuevaImagen }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-form-label col-md-3">Enlace imagen de portada:</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="enlace_de_imagen_de_portada" autocomplete="off" v-model="pagina.enlace_imagen_portada">
                                                </div>
                                            </div>
                                            <div class="form-group text-right mb-0">
                                                <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizandoImagenPortada === 1">
                                                    <span v-if="iActualizandoImagenPortada === 0">Guardar</span>
                                                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="ibox ">
                                    <div class="ibox-title">
                                        <h5>Baner publicitario</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + sContenidoNuevoBaner + ')' }" v-if="nuevoBaner"></div>
                                        <div class="masthead" v-bind:style="{ backgroundImage: 'url(' + pagina.ruta_baner_publicitario + ')' }" v-else></div>
                                        <form role="form" class="mt-4" id="frmEditarBaner" v-on:submit.prevent="ajaxActualizarBaner" v-cloak>
                                            <div class="form-group row">
                                                <label class="col-form-label col-md-3">Cambiar baner:</label>
                                                <div class="col-md-9">
                                                    <div class="custom-file">
                                                        <input id="aImagenB" type="file" class="custom-file-input" name="baner_publicitario" v-on:change="cambiarBaner">
                                                        <label for="aImagenB" class="custom-file-label">@{{ sNombreNuevoBaner }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-form-label col-md-3">Enlace baner: </label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="enlace_de_baner" autocomplete="off" v-model="pagina.enlace_baner_publicitario">
                                                </div>
                                            </div>
                                            <div class="form-group text-right mb-0">
                                                <button type="submit" class="btn btn-primary" v-bind:disabled="iActualizandoBaner === 1">
                                                    <span v-if="iActualizandoBaner === 0">Guardar</span>
                                                    <span v-else><i class="fas fa-circle-notch fa-spin"></i> Por favor, espere...</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/intranet/blogs.js?cvcn=14"></script>
@endsection
