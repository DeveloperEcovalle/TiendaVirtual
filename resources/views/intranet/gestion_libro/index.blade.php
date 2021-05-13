@extends('intranet.layout')

@section('title', 'LIBRO')

@section('content')
    <div class="row m-0" v-if="iError === 0">
        <div class="col-12 col-md-12 p-0">
            <div class="border-bottom border-right d-flex white-bg">
                <div class="col-8 py-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-none d-md-block">
                            <a href="#" v-on:click.prevent>Ecovalle</a>
                        </li>
                        <li class="breadcrumb-item d-none d-md-block">
                            <a>Gesti&oacute;n de Libro de Reclamaciones</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Libro</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" v-model="sBuscar" class="form-control" placeholder="Buscar por codigo, nombres ó apellidos">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tblLibro">
                        <thead>
                            <tr>
                                <th class="bg-primary">Nombres</th>
                                <th class="bg-primary">Apellidos</th>
                                <th class="bg-primary">Documento</th>
                                <th class="bg-primary">C&oacute;digo</th>
                                <th class="bg-primary">Direcci&oacute;n</th>
                                <th class="bg-primary" style="width: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr v-for="reclamo of lstLibroFiltrado" v-cloak>
                                <td>@{{ reclamo.nombres }}</td>
                                <td>@{{ reclamo.apellidos }}</td>
                                <td>@{{ reclamo.numero_documento }}</td>
                                <td>@{{ reclamo.codigo }}</td>
                                <td>@{{ reclamo.direccion }}</td>
                                <td><a :href="'/intranet/app/libro-reclamaciones/libro/ajax/download/'+reclamo.id" class="btn btn-sm btn-danger btn-block"><i class="fa fa-file-pdf-o"></i> PDF</a></td>
                            </tr>
                            <tr v-if="lstLibroFiltrado.length === 0" v-cloak>
                                <td colspan="6" class="text-center">No hay datos para mostrar</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
@endsection

@section('js')
    <script src="/js/intranet/gestionLibro.js?cvcn=14"></script>
@endsection
