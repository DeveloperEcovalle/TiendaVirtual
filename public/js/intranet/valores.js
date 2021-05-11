$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueValores = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstValores: [],
                iIdSeleccionado: 0,
                iError: 0,
            },
            mounted: function () {
                this.ajaxListar(this.cargarPanel);
            },
            methods: {
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/valores/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstValores = data.lstValores;

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
                cargarPanel: function () {
                    let $this = this;
                    let sUrl = location.pathname;
                    let lstUrl = sUrl.split('/');

                    let sLastPath = lstUrl.pop();
                    sLastPath = sLastPath.length === 0 ? lstUrl.pop() : sLastPath;
                    switch (sLastPath) {
                        case 'valores': {
                            $this.panelListar();
                            break;
                        }
                        case 'nuevo': {
                            $this.panelNuevo();
                            break;
                        }
                        case 'editar': {
                            let iId = lstUrl.pop();
                            $this.panelEditar(iId);
                            break;
                        }
                    }
                },
                panelListar: function (onSuccess) {
                    $('#panel').load('/intranet/app/pagina-web/valores/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/pagina-web/valores/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                iInsertando: 0
                            },
                            mounted: function () {
                                $('#sDescripcionES').summernote({
                                    airMode: true,
                                    toolbar: [
                                        ['style', ['style']],
                                        ['font', ['bold', 'underline', 'clear']],
                                        ['fontname', ['fontname']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture']],
                                    ],
                                    fontNames: ['Arial'],
                                    minHeight: 200,
                                    height: 400
                                });

                                $('#sDescripcionEN').summernote({
                                    airMode: true,
                                    toolbar: [
                                        ['style', ['style']],
                                        ['font', ['bold', 'underline', 'clear']],
                                        ['fontname', ['fontname']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture']],
                                    ],
                                    fontNames: ['Arial'],
                                    minHeight: 200,
                                    height: 400
                                });

                                $('.note-editor').addClass('b-r-sm border px-3');
                            },
                            methods: {
                                ajaxInsertar: function () {
                                    let $this = this;

                                    if ($('#sDescripcionES').summernote('isEmpty')) {
                                        toastr[result.error]('Descripci&oacute;n en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sDescripcionEN').summernote('isEmpty')) {
                                        toastr[result.error]('Descripci&oacute;n en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);
                                    formData.append('descripcion_es', $('#sDescripcionES').summernote('code'));
                                    formData.append('descripcion_en', $('#sDescripcionEN').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/valores/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                $('#sDescripcionES').summernote('code', '');
                                                $('#sDescripcionEN').summernote('code', '');
                                                vueValores.ajaxListar();
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            $this.iInsertando = 0;

                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxCancelar: function () {
                                    vueValores.panelListar(function () {
                                        vueValores.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'VALORES', '/intranet/app/pagina-web/valores');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'VALORES', '/intranet/app/pagina-web/valores/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstValores.findIndex((banner) => banner.id === parseInt(iId));
                    let valor = Object.assign({}, $this.lstValores[iIndice]);

                    $('#panel').load('/intranet/app/pagina-web/valores/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                valor: valor,
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            mounted: function () {
                                $('#sDescripcionES').summernote({
                                    airMode: true,
                                    toolbar: [
                                        ['style', ['style']],
                                        ['font', ['bold', 'underline', 'clear']],
                                        ['fontname', ['fontname']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture']],
                                    ],
                                    fontNames: ['Arial'],
                                    minHeight: 200,
                                    height: 400
                                });

                                $('#sDescripcionEN').summernote({
                                    airMode: true,
                                    toolbar: [
                                        ['style', ['style']],
                                        ['font', ['bold', 'underline', 'clear']],
                                        ['fontname', ['fontname']],
                                        ['color', ['color']],
                                        ['para', ['ul', 'ol', 'paragraph']],
                                        ['table', ['table']],
                                        ['insert', ['link', 'picture']],
                                    ],
                                    fontNames: ['Arial'],
                                    minHeight: 200,
                                    height: 400
                                });

                                $('.note-editor').addClass('b-r-sm border px-3');

                                $('#sDescripcionES').summernote('code', this.valor.descripcion_es);
                                $('#sDescripcionEN').summernote('code', this.valor.descripcion_en);
                            },
                            methods: {
                                ajaxActualizar: function () {
                                    let $this = this;

                                    if ($('#sDescripcionES').summernote('isEmpty')) {
                                        toastr[result.error]('Descripci&oacute;n en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sDescripcionEN').summernote('isEmpty')) {
                                        toastr[result.error]('Descripci&oacute;n en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);
                                    formData.append('descripcion_es', $('#sDescripcionES').summernote('code'));
                                    formData.append('descripcion_en', $('#sDescripcionEN').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/valores/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                vueValores.ajaxListar();
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            $this.iActualizando = 0;

                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxEliminar: function (iId) {
                                    let $this = this;
                                    $this.iEliminando = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/valores/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueValores.ajaxListar(function () {
                                                    vueValores.panelListar(function () {
                                                        vueValores.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'VALORES', '/intranet/app/pagina-web/valores');
                                                    });
                                                });
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            $this.iEliminando = 0;

                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxCancelar: function () {
                                    vueValores.panelListar(function () {
                                        vueValores.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'VALORES', '/intranet/app/pagina-web/valores');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = valor.id;
                        window.history.replaceState(null, 'VALORES', `/intranet/app/pagina-web/valores/${valor.id}/editar`);
                    });
                }
            }
        });
    });
});
