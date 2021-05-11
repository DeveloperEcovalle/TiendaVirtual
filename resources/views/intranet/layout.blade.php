<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="iModuloId" content="{{ $iModuloId }}">

        <title>ECOVALLE | SISTEMA DE GESTI&Oacute;N | @yield('title')</title>
        <link rel="icon" href="{{asset('img/ecologo.ico')}}" />

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/07952e76a1.js" crossorigin="anonymous"></script>
        <link href="/js/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
        <link href="/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
        <link href="/css/plugins/dropzone/basic.css" rel="stylesheet">
        <link href="/css/plugins/dropzone/dropzone.css" rel="stylesheet">
        <link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">
        <link href="/css/plugins/summernote/summernote-bs4.css" rel="stylesheet">
        <link href="/css/animate.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        @yield('head')
        <link href="/css/intranet.css" rel="stylesheet">
    </head>

    <body class="top-navigation fixed-nav md-skin">

        <div id="wrapper" class="overflow-hidden">
            <div id="page-wrapper" class="gray-bg overflow-hidden">
                <div class="row border-bottom white-bg">
                    <nav class="navbar navbar-expand-lg navbar-fixed-top" role="navigation">
                        <a href="#" class="navbar-brand">ECOVALLE</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fas fa-bars"></i>
                        </button>

                        <div class="navbar-collapse collapse" id="navbar">
                            <ul class="nav navbar-nav mr-auto">
                                <li v-for="modulo of lstModulos" v-bind:class="modulo.id == {{ $iModuloId }} ? 'active' : ''" v-cloak>
                                    <a aria-expanded="false" role="button" v-bind:href="modulo.submenus[0].enlace"><i v-bind:class="modulo.icono"></i> @{{ modulo.nombre }}</a>
                                </li>
                            </ul>
                            <ul class="nav navbar-top-links navbar-right">
                                <li>
                                    <a href="/intranet/salir">
                                        <i class="fa fa-sign-out"></i> Salir
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="row border-bottom white-bg">
                    <nav class="navbar navbar-expand-lg subnavbar-fixed-top" role="navigation">
                        <div class="nav navbar py-0" id="subnavbar">
                            <ul class="nav navbar-nav mr-auto">
                                <li v-for="menu of lstMenus" v-bind:class="menu.id == {{ $iMenuId }} ? 'active' : ''" v-cloak>
                                    <a aria-expanded="false" role="button" v-bind:href="menu.enlace">@{{ menu.nombre }}</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="wrapper wrapper-content m-l-n m-r-n p-0" id="layoutContent">
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Mainly scripts -->
        <script src="/js/jquery-3.1.1.min.js"></script>
        <script src="/js/popper.min.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/plugins/jquery-ui/jquery-ui.min.js"></script>
        <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="/js/plugins/datapicker/bootstrap-datepicker.js"></script>
        <script src="/js/plugins/dropzone/dropzone.js"></script>
        <script src="/js/plugins/summernote/summernote-bs4.js"></script>
        <script src="/js/plugins/toastr/toastr.min.js"></script>

        <!-- Custom and plugin javascript -->
        <script src="/js/inspinia.js"></script>
        <script src="/js/plugins/pace/pace.min.js"></script>
        <script src="/js/plugins/axios/axios.js"></script>
        <script src="/js/plugins/vue/vue.js"></script>
        <script src="/js/intranet/intranet.js?cvcn=14"></script>

        @yield('js')

    </body>

</html>
