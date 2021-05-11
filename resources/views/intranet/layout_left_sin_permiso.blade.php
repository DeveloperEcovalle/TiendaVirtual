@extends('intranet.layout')

@section('title', 'NO TIENE LOS PERMISOS NECESARIOS')

@section('content')
    <div class="row m-0 justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 pt-5">
            <h3 class="text-warning text-center font-bold pt-5">
                <i class="far fa-hand-paper fa-2x mb-2"></i><br>
                No tiene permiso para acceder a esta parte del sistema.
            </h3>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            listarMenus(function (lstModulos, lstMenus) {
                let vueLayout = new Vue({
                    el: '#wrapper',
                    data: {
                        lstModulos: lstModulos,
                        lstMenus: lstMenus,
                        lstBanners: [],
                        iError: 0,
                    }
                });
            });
        });
    </script>
@endsection
