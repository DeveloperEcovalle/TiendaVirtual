$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueLibroR = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                empresa: {
                    ruta_imagen_libro: ''
                },
                nuevaImagenLibro: null,
                iActualizandoImagenLibro: 0,
                iActualizandoRuc: 0,
                iActualizandoRazon: 0,
                iActualizandoMensaje: 0,
            },
            computed: {
                sNombreNuevaImagen: function () {
                    if (this.nuevaImagenLibro === null) {
                        return 'Buscar archivo';
                    }
                    return this.nuevaImagenLibro.name.split('\\').pop();
                },
                sContenidoNuevaImagen: function () {
                    if (this.nuevaImagenLibro === null) {
                        return null;
                    }
                    return URL.createObjectURL(this.nuevaImagenLibro);
                }
            },
            mounted: function () {
                this.ajaxListar();
            },
            methods: {
                cambiarImagen: function (event) {
                    let input = event.target;
                    this.nuevaImagenLibro = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/libro-reclamaciones/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                console.log(respuesta.data);
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
                ajaxActualizarImagenLibro: function () {
                    let $this = this;
                    $this.iActualizandoImagenLibro = 1;

                    let frmEditarImagenLibro = document.getElementById('frmEditarImagenLibro');
                    let formData = new FormData(frmEditarImagenLibro);

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/libro-reclamaciones/ajax/actualizarImagenLibro',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            $this.iActualizandoImagenLibro = 0;

                            if (respuesta.result === result.success) {
                                $this.empresa.ruta_imagen_libro = respuesta.data.sNuevaRutaImagen;

                                frmEditarImagenLibro.reset();
                                $this.nuevaImagenLibro = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoImagenLibro = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarRuc: function () {
                    let $this = this;
                    $this.iActualizandoRuc = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/libro-reclamaciones/ajax/actualizarRuc',
                        data: $('#frmEditarRuc').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoRuc = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoRuc = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarMensaje: function () {
                    let $this = this;
                    $this.iActualizandoMensaje = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/libro-reclamaciones/ajax/actualizarMensaje',
                        data: $('#frmEditarMensaje').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoMensaje = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoMensaje = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
                ajaxActualizarRazon: function () {
                    let $this = this;
                    $this.iActualizandoRazon = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/libro-reclamaciones/ajax/actualizarRazon',
                        data: $('#frmEditarRazon').serialize(),
                        success: function (respuesta) {
                            $this.iActualizandoRazon = 0;
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoRazon = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                },
            }
        });
    });
});