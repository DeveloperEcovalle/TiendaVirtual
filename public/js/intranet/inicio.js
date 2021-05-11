$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueInicio = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                iActualizandoContenidoEspanol: 0,
                iActualizandoContenidoIngles: 0,
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
                    minHeight: 350,
                    height: 450
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
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/inicio/ajax/listar',
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
                        url: '/intranet/app/pagina-web/inicio/ajax/actualizarContenidoEspanol',
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
                        url: '/intranet/app/pagina-web/inicio/ajax/actualizarContenidoIngles',
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
