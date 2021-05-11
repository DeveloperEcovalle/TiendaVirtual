$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueContactanos = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                empresa: {
                    ruta_imagen_contactanos: '',
                    telefonos: []
                },
                nuevaImagenContactanos: null,
                iActualizandoImagenContactanos: 0,

                sNuevoEnlaceMapa: '',
                iActualizandoEnlaceMapa: 0,

                sIcono: 'fas fa-phone',
                sTelefono: '',
                iInsertandoTelefono: 0,

                iActualizandoDireccion: 0,
                iActualizandoRedesSociales: 0,
                iActualizandoCorreo: 0,
            },
            computed: {
                sNombreNuevaImagen: function () {
                    if (this.nuevaImagenContactanos === null) {
                        return 'Buscar archivo';
                    }
                    return this.nuevaImagenContactanos.name.split('\\').pop();
                },
                sContenidoNuevaImagen: function () {
                    if (this.nuevaImagenContactanos === null) {
                        return null;
                    }
                    return URL.createObjectURL(this.nuevaImagenContactanos);
                },
                sEnlaceMapa: function () {
                    if (this.sNuevoEnlaceMapa.length > 0) {
                        return this.sNuevoEnlaceMapa;
                    }
                    return this.empresa.enlace_mapa;
                }
            },
            mounted: function () {
                this.ajaxListar();
            },
            methods: {
                cambiarImagen: function (event) {
                    let input = event.target;
                    this.nuevaImagenContactanos = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/listar',
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
                ajaxActualizarDireccion: function () {
                    let $this = this;
                    $this.iActualizandoDireccion = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/actualizarDireccion',
                        data: $('#frmEditarDireccion').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoDireccion = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoDireccion = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarRedesSociales: function () {
                    let $this = this;
                    $this.iActualizandoRedesSociales = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/actualizarRedesSociales',
                        data: $('#frmEditarRedesSociales').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoRedesSociales = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoRedesSociales = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarCorreoContactanos: function () {
                    let $this = this;
                    $this.iActualizandoCorreo = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/actualizarCorreo',
                        data: $('#frmEditarCorreo').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoCorreo = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoCorreo = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarImagenContactanos: function () {
                    let $this = this;
                    $this.iActualizandoImagenContactanos = 1;

                    let frmEditarImagenContactanos = document.getElementById('frmEditarImagenContactanos');
                    let formData = new FormData(frmEditarImagenContactanos);

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/actualizarImagenContactanos',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            $this.iActualizandoImagenContactanos = 0;

                            if (respuesta.result === result.success) {
                                $this.empresa.ruta_imagen_contactanos = respuesta.data.sNuevaRutaImagen;

                                frmEditarImagenContactanos.reset();
                                $this.nuevaImagenContactanos = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoImagenContactanos = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarEnlaceMapa: function () {
                    let $this = this;
                    $this.iActualizandoEnlaceMapa = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/actualizarEnlaceMapa',
                        data: $('#frmEditarEnlaceMapa').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoEnlaceMapa = 0;

                            if (respuesta.result === result.success) {
                                $this.empresa.enlace_mapa = respuesta.data.sNuevoEnlaceMapa;
                                $this.sNuevoEnlaceMapa = '';
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoEnlaceMapa = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                setsIcono: function (sIcono) {
                    this.sIcono = sIcono;
                    $('#modalIcono').modal('hide');
                },
                ajaxInsertarTelefono: function () {
                    let $this = this;
                    $this.iInsertandoTelefono = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/insertarTelefono',
                        data: $('#frmInsertarTelefono').serialize(),
                        success: function (respuesta) {
                            $this.iInsertandoTelefono = 0;
                            if (respuesta.result === result.success) {
                                let id = respuesta.data.id;

                                $this.empresa.telefonos.push({
                                    id: id,
                                    icono: $this.sIcono,
                                    numero: $this.sTelefono
                                });

                                $this.sTelefono = '';
                            }
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iInsertandoTelefono = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxEliminarTelefono: function (iId) {
                    let $this = this;
                    $this.iInsertandoTelefono = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/contactanos/ajax/eliminarTelefono',
                        data: {id: iId},
                        success: function (respuesta) {
                            $this.iInsertandoTelefono = 0;
                            if (respuesta.result === result.success) {
                                $this.ajaxListar();
                            }
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iInsertandoTelefono = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
            }
        });
    });
});
