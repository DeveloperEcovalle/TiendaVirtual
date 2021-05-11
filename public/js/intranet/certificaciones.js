$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueCertificaciones = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstCertificaciones: [],
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
                        url: '/intranet/app/pagina-web/certificaciones/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstCertificaciones = data.lstCertificaciones;

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
                        case 'certificaciones': {
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
                    $('#panel').load('/intranet/app/pagina-web/certificaciones/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/pagina-web/certificaciones/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                imagen: null,
                                iInsertando: 0
                            },
                            computed: {
                                sNombreArchivo: function () {
                                    if (this.imagen === null) {
                                        return 'Buscar archivo';
                                    }
                                    return this.imagen.name.split('\\').pop();
                                },
                                sContenidoArchivo: function () {
                                    if (this.imagen === null) {
                                        return null;
                                    }
                                    return URL.createObjectURL(this.imagen);
                                }
                            },
                            mounted: function () {
                                let summernoteConfig = {
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
                                };

                                $('#sDescripcionES').summernote(summernoteConfig);
                                $('#sDescripcionEN').summernote(summernoteConfig);

                                $('.note-editor').addClass('b-r-sm border px-3');
                            },
                            methods: {
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
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
                                        url: '/intranet/app/pagina-web/certificaciones/ajax/insertar',
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
                                                vueCertificaciones.ajaxListar();
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
                                    vueCertificaciones.panelListar(function () {
                                        vueCertificaciones.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'CERTIFICACIONES', '/intranet/app/pagina-web/certificaciones');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'CERTIFICACIONES', '/intranet/app/pagina-web/certificaciones/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstCertificaciones.findIndex((banner) => banner.id === parseInt(iId));
                    let certificacion = Object.assign({}, $this.lstCertificaciones[iIndice]);

                    $('#panel').load('/intranet/app/pagina-web/certificaciones/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                certificacion: certificacion,
                                imagen: null,
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            computed: {
                                sNombreArchivo: function () {
                                    if (this.imagen === null) {
                                        return 'Buscar archivo';
                                    }
                                    return this.imagen.name.split('\\').pop();
                                },
                                sContenidoArchivo: function () {
                                    if (this.imagen === null) {
                                        return null;
                                    }
                                    return URL.createObjectURL(this.imagen);
                                }
                            },
                            mounted: function () {
                                let summernoteConfig = {
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
                                };

                                $('#sDescripcionES').summernote(summernoteConfig);
                                $('#sDescripcionEN').summernote(summernoteConfig);

                                $('.note-editor').addClass('b-r-sm border px-3');

                                $('#sDescripcionES').summernote('code', this.certificacion.descripcion_es);
                                $('#sDescripcionEN').summernote('code', this.certificacion.descripcion_en);
                            },
                            methods: {
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
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
                                        url: '/intranet/app/pagina-web/certificaciones/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                vueCertificaciones.ajaxListar();
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
                                        url: '/intranet/app/pagina-web/certificaciones/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueCertificaciones.ajaxListar(function () {
                                                    vueCertificaciones.panelListar(function () {
                                                        vueCertificaciones.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'CERTIFICACIONES', '/intranet/app/pagina-web/certificaciones');
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
                                    vueCertificaciones.panelListar(function () {
                                        vueCertificaciones.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'CERTIFICACIONES', '/intranet/app/pagina-web/certificaciones');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = certificacion.id;
                        window.history.replaceState(null, 'CERTIFICACIONES', `/intranet/app/pagina-web/certificaciones/${certificacion.id}/editar`);
                    });
                }
            }
        });
    });
});
