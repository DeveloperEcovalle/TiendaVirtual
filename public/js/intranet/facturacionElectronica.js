$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueFacturacionElectronica = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                empresa: {
                    ruta_imagen_contactanos: '',
                    telefonos: []
                },
                nuevoCertificado: null,

                iActualizandoUsuarioClaveSOL: 0,
                iActualizandoCertificadoDigital: 0,
            },
            computed: {
                sNombreNuevoCertificado: function () {
                    if (this.nuevoCertificado === null) {
                        return 'Buscar archivo';
                    }
                    return this.nuevoCertificado.name.split('\\').pop();
                },
            },
            mounted: function () {
                this.ajaxListar();
            },
            methods: {
                cambiarCertificado: function (event) {
                    let input = event.target;
                    this.nuevoCertificado = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/configuracion/facturacion-electronica/ajax/listar',
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
                ajaxActualizarUsuarioClaveSOL: function () {
                    let $this = this;
                    $this.iActualizandoUsuarioClaveSOL = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/configuracion/facturacion-electronica/ajax/actualizarUsuarioClaveSOL',
                        data: $('#frmEditarUsuarioClaveSOL').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoUsuarioClaveSOL = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoUsuarioClaveSOL = 0;

                            if (respuesta.result === result.success) {
                                $this.ajaxListar();
                            }

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarCertificadoDigital: function () {
                    let $this = this;
                    $this.iActualizandoCertificadoDigital = 1;

                    let frmEditarCertificadoDigital = document.getElementById('frmEditarCertificadoDigital');
                    let formData = new FormData(frmEditarCertificadoDigital);

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/configuracion/facturacion-electronica/ajax/actualizarCertificadoDigital',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            $this.iActualizandoCertificadoDigital = 0;

                            if (respuesta.result === result.success) {
                                $this.ajaxListar();

                                frmEditarCertificadoDigital.reset();
                                $this.nuevoCertificado = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoCertificadoDigital = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
            }
        });
    });
});
