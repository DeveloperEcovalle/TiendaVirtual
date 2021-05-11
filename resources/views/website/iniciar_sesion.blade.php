<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Ecovalle | Iniciar sesión</title>

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/07952e76a1.js" crossorigin="anonymous"></script>
        <link href="/css/website.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-xl">
            <div class="row justify-content-center" id="inicioSesion">
                <div class="col-lg-4 pt-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-9 pt-lg-5">
                            <a href="/"><img src="/img/logo_ecovalle.svg" class="img-fluid"></a>
                        </div>
                    </div>
                    <h5 class="text-center mt-4 mb-3">Iniciar sesi&oacute;n</h5>
                    <form role="form" id="frmIniciarSesion" v-on:submit.prevent="ajaxIngresar" v-cloak>
                        <div class="form-group">
                            <input class="form-control" type="email" name="email" placeholder="{{ $lstLocales['Email'] }}" required="required" autocomplete="off" v-on:keyup="sMensaje = null">
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="password" name="contrasena" placeholder="{{ $lstLocales['Password'] }}" required="required" autocomplete="off" v-on:keyup="sMensaje = null">
                        </div>
                        <div class="alert text-center p-2" :class="sClase" v-if="sMensaje">
                            @{{ sMensaje }}
                        </div>
                        <div class="form-group mb-1" v-cloak>
                            <button type="submit" class="btn btn-block btn-ecovalle" :disabled="iComprobando === 1">
                                <span v-if="iComprobando === 1"><i class="fas fa-circle-notch fa-spin"></i> Comprobando</span>
                                <span v-else>Iniciar sesi&oacute;n</span>
                            </button>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-12 pb-2 text-center">
                            <a class="nav-ecovalle-amarillo small" href="#">&iquest;Olvid&oacute; su contrase&ntilde;a?</a>
                        </div>
                        <div class="col-12 text-center">
                            <p class="small mt-1 mb-2">&iquest;No tiene una cuenta?</p>
                            <a class="btn btn-block btn-outline-ecovalle">Reg&iacute;strese aqu&iacute;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mainly scripts -->
        <script src="/js/jquery-3.1.1.min.js"></script>
        <script src="/js/popper.min.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
        <script src="/js/plugins/vue/vue.js"></script>
        <script src="/js/website/website.js"></script>
        <script src="/js/website/iniciarSesion.js?cvcn=14"></script>

    </body>
</html>
