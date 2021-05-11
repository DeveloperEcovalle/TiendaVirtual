@extends('intranet.layout')

@section('title', 'KARDEX')

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
                            <a>Gesti&oacute;n de inventario</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Kárdex</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="p-4" id="layoutLeft">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Buscar producto por nombre"
                           v-autocomplete="{ url: '/intranet/app/gestion-inventario/kardex/ajax/autocompletarProductos', select: onSelectAutocompleteProducto, change: onChangeAutocompleteProducto }">
                </div>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="bg-primary text-center">Fecha</th>
                            <th class="bg-primary text-center">Tipo</th>
                            <th class="bg-primary">Descripci&oacute;n</th>
                            <th class="bg-primary text-center">Entrada</th>
                            <th class="bg-primary text-center">Salida</th>
                            <th class="bg-primary text-center">Saldo</th>
                            <th class="bg-primary text-center">Acumulado</th>
                            <th class="bg-primary text-center">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        <tr v-for="kardex of lstKardex"
                            :class="{ 'text-success': kardex.tipo === 'E', 'text-danger': kardex.tipo === 'S' }" style="cursor: pointer;" v-cloak>
                            <td class="text-center text-dark">@{{ kardex.fecha_reg }}</td>
                            <td class="text-center">@{{ kardex.tipo === 'E' ? 'ENTRADA' : 'SALIDA' }}</td>
                            <td>@{{ kardex.descripcion }}</td>
                            <td class="text-center font-weight-bold"><span v-if="kardex.entrada_cantidad > 0">@{{ kardex.entrada_cantidad }}</span></td>
                            <td class="text-center font-weight-bold"><span v-if="kardex.salida_cantidad > 0">@{{ kardex.salida_cantidad }}</span></td>
                            <td class="text-center font-weight-bold">@{{ kardex.saldo_cantidad }}</td>
                            <td class="text-center text-dark">@{{ kardex.acumulado_cantidad }}</td>
                            <td class="text-center font-weight-bold text-dark">@{{ kardex.usuario.username }}</td>
                        </tr>
                        <tr v-if="lstKardex.length === 0" v-cloak>
                            <td colspan="8" class="text-center">No hay datos para mostrar</td>
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
@endsection

@section('js')
    <script src="/js/intranet/kardex.js?cvcn=14"></script>
@endsection
