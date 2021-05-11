<!DOCTYPE html>
<html>

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ECOVALLE | SISTEMA DE GESTI&Oacute;N | INICIAR SESI&Oacute;N</title>

        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/07952e76a1.js" crossorigin="anonymous"></script>

        <link href="/css/animate.css" rel="stylesheet">
        <link href="/css/style.css" rel="stylesheet">
        <link href="/css/intranet.css" rel="stylesheet">

    </head>

    <body class="gray-bg md-skin">

        <div class="middle-box text-center loginscreen" id="login">
            <div>
                <div>

                    <h1 class="logo-name">
                        <img src="/img/logo_ecovalle.png" alt="Logo Ecovalle" class="img-fluid">
                    </h1>

                </div>
                <h3 class="mt-4">Iniciar sesi&oacute;n</h3>
                <form class="m-t" role="form" v-on:submit.prevent="ajaxIngresar">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Nombre de usuario" maxlength="50" v-model="usuario" v-on:keypress="iErrorInesperado = 0">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Contrase&ntilde;a" maxlength="20" v-model="contrasena" v-on:keypress="iErrorInesperado = 0">
                    </div>
                    <div class="alert alert-danger" v-if="iErrorInicioSesion === 1" v-cloak>
                        Usuario y/o contrase&ntilde;a incorrecta.
                    </div>
                    <div class="alert alert-danger" v-if="iErrorInesperado === 1" v-cloak>
                        <p class="no-margins" v-for="lstError of lstErrores">
                            <span class="d-block" v-for="sError of lstError">@{{ sError.charAt(0).toUpperCase() + sError.slice(1) }}</span>
                        </p>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b" v-bind:disabled="iIniciandoSesion === 0" v-cloak>
                        <span v-if="iIniciandoSesion === 1">Iniciar sesi&oacute;n</span>
                        <span v-if="iVerificando === 1"><i class="fas fa-circle-notch fa-spin"></i>&nbsp;Verificando</span>
                        <span v-if="iIngresando === 1"><i class="fas fa-circle-notch fa-spin"></i>&nbsp;Ingresando</span>
                    </button>

                    <a href="#"><small>&iquest;Olvid&oacute; su contrase&ntilde;a?</small></a>
                    <p class="text-muted text-center mt-2 mb-1"><small>&iquest;No tiene una cuenta?</small></p>
                    <a class="btn btn-sm btn-white btn-block" href="#">Solicitar una cuenta</a>
                </form>
                <p class="m-t"><small>Ecovalle Sistema de Gesti&oacute;n</small></p>
            </div>
        </div>

        <!-- Mainly scripts -->
        <script src="/js/jquery-3.1.1.min.js"></script>
        <script src="/js/popper.min.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/plugins/vue/vue.js"></script>
        <script src="/js/intranet/intranet.js?cvcn=14"></script>
        <script src="/js/intranet/login.js?cvcn=14"></script>

    </body>

</html>
