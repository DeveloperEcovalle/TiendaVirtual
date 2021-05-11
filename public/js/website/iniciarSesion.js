$(document).ready(function () {
    let vueIniciarSesion = new Vue({
        el: '#inicioSesion',
        data: {
            iComprobando: 0,
            sMensaje: null,
            sClase: null
        },
        methods: {
            ajaxIngresar: function () {
                let $this = this;
                $this.iComprobando = 1;

                $.ajax({
                    type: 'post',
                    url: '/iniciar-sesion/ajax/ingresar',
                    data: $('#frmIniciarSesion').serialize(),
                    dataType: 'json',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            location.reload();
                        } else {
                            $this.iComprobando = 0;
                            $this.sClase = respuesta.result === result.error ? 'alert-danger' : ('alert-' + respuesta.result);
                            $this.sMensaje = respuesta.mensaje;
                        }
                    },
                    error: function () {
                        $this.iComprobando = 0;
                        $this.sClase = 'alert-danger';
                        $this.sMensaje = 'Ocurrió un error inesperado. Intentar una vez más debería solucionar el problema; de no ser así, comuníquese con el administrador del sistema.';
                    }
                });
            }
        }
    });
});
