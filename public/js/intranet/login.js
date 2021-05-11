$(document).ready(function () {
    let vueLogin = new Vue({
        el: '#login',
        data: {
            usuario: '',
            contrasena: '',

            iIniciandoSesion: 1,
            iVerificando: 0,
            iIngresando: 0,

            iErrorInicioSesion: 0,
            iErrorInesperado: 0,
            lstErrores: []
        },
        methods: {
            ajaxIngresar: function () {
                let $this = this;
                $this.iIniciandoSesion = 0;
                $this.iVerificando = 1;

                $.ajax({
                    type: 'post',
                    url: '/intranet/login/ajax/ingresar',
                    data: {usuario: $this.usuario, contrasena: $this.contrasena},
                    success: function (respuesta) {
                        $this.iVerificando = 0;
                        if (respuesta.result === result.success) {
                            $this.iIngresando = 1;
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        } else {
                            $this.iErrorInicioSesion = 1;
                            $this.iIniciandoSesion = 1;
                        }
                    },
                    error: function (respuesta) {
                        $this.iIniciandoSesion = 1;
                        $this.iVerificando = 0;
                        $this.iIngresando = 0;
                        $this.iErrorInesperado = 1;

                        $this.lstErrores = respuesta.responseJSON.errors;
                    }
                });
            }
        }
    });
});
