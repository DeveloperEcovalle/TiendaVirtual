listarMenus(function (lstModulos, lstMenus) {
    let vueRecepcion = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            iError: 0,

            empresa: [],

            iActualizando: 0,
        },
        mounted: function () {
            this.ajaxListar();
        },
        methods: {
            ajaxListar: function (onSuccess) {
                let $this = this;
                $.ajax({
                    type: 'post',
                    url: '/intranet/app/configuracion/recepcion/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.empresa = data.empresa;

                            if (onSuccess) {
                                onSuccess();
                            }
                        }
                    },
                    error: function (respuesta) {
                        $this.iError = 1;
                    }
                });
            },
            ajaxActualizar: function () {
                let $this = this;
                $this.iActualizando = 1;

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/configuracion/recepcion/ajax/actualizar',
                    data: $('#frmEditar').serialize(),
                    success: function (respuesta) {
                        $this.iActualizando = 0;
                        toastr[respuesta.result](respuesta.mensaje);
                    },
                    error: function (respuesta) {
                        $this.iActualizando = 0;

                        if (respuesta.result === result.success) {
                            $this.ajaxListar();
                        }

                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                        toastr[result.error](sHtmlMensaje);
                    }
                });
            },
        }
    });
});
