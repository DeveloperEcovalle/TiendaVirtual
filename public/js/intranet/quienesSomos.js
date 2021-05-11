$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueQuienesSomos = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                pagina: {
                    ruta_imagen_portada: ''
                },
                nuevaImagenPortada: null,
                iActualizandoImagenPortada: 0,

                iActualizandoContenidoEspanol: 0,
                iActualizandoContenidoIngles: 0,
            },
            computed: {
                sNombreNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return 'Buscar archivo';
                    }
                    return this.nuevaImagenPortada.name.split('\\').pop();
                },
                sContenidoNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return null;
                    }
                    return URL.createObjectURL(this.nuevaImagenPortada);
                }
            },
            mounted: function () {
                let $this = this;

                let summernoteConfig = {
                    styleTags: [
                        'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
                    ],
                    fontNames: ['Arial'],
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']],
                    ],
                    minHeight: 200,
                    height: 300
                };

                $('#sContenidoEspanol').summernote(summernoteConfig);
                $('#sContenidoIngles').summernote(summernoteConfig);

                this.ajaxListar(function () {
                    if ($this.pagina) {
                        $('#sContenidoEspanol').summernote('code', $this.pagina.contenido_espanol);
                        $('#sContenidoIngles').summernote('code', $this.pagina.contenido_ingles);
                    }
                });
            },
            methods: {
                cambiarImagen: function (event) {
                    let input = event.target;
                    this.nuevaImagenPortada = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/quienes-somos/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.pagina = respuesta.data.pagina;

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
                ajaxActualizarImagenPortada: function () {
                    let $this = this;
                    $this.iActualizandoImagenPortada = 1;

                    let frmEditarImagenPortada = document.getElementById('frmEditarImagenPortada');
                    let formData = new FormData(frmEditarImagenPortada);

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/quienes-somos/ajax/actualizarImagenPortada',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.pagina.ruta_imagen_portada = respuesta.data.sNuevaRutaImagen;

                                frmEditarImagenPortada.reset();
                                $this.nuevaImagenPortada = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoImagenPortada = 0;
                        }
                    });
                },
                ajaxActualizarContenidoEspanol: function () {
                    let $this = this;

                    if ($('#sContenidoEspanol').summernote('isEmpty')) {
                        toastr[result.error]('El contenido en español es requerido.');
                        return;
                    }

                    $this.iActualizandoContenidoEspanol = 1;

                    let formData = new FormData();
                    formData.append('contenido_en_espanol', $('#sContenidoEspanol').summernote('code'));

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/quienes-somos/ajax/actualizarContenidoEspanol',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoContenidoEspanol = 0;
                        }
                    });
                },
                ajaxActualizarContenidoIngles: function () {
                    let $this = this;

                    if ($('#sContenidoIngles').summernote('isEmpty')) {
                        toastr[result.error]('El contenido en inglés es requerido.');
                        return;
                    }

                    $this.iActualizandoContenidoIngles = 1;

                    let formData = new FormData();
                    formData.append('contenido_en_ingles', $('#sContenidoIngles').summernote('code'));

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/quienes-somos/ajax/actualizarContenidoIngles',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoContenidoIngles = 0;
                        }
                    });
                }
            }
        });
    });
});
