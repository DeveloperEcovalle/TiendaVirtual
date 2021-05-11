$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueQuienesSomos = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstSecciones: [],
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
                        url: '/intranet/app/pagina-web/secciones-quienes-somos/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstSecciones = data.lstSecciones;

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
                        case 'secciones-quienes-somos': {
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
                    $('#panel').load('/intranet/app/pagina-web/secciones-quienes-somos/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/pagina-web/secciones-quienes-somos/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                sTipo: 0,
                                lstTipos: ['SOLO TEXTO', 'IMAGEN A LA IZQUIERDA', 'IMAGEN A LA DERECHA', 'VIDEO A LA IZQUIERDA'],
                                imagen: null,
                                sNuevoEnlaceVideo: '',
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
                                },
                                sEnlaceVideo: function () {
                                    if (this.sNuevoEnlaceVideo.length > 0) {
                                        return this.sNuevoEnlaceVideo.replace('watch?v=', 'embed/');
                                    }
                                    return '';
                                }
                            },
                            mounted: function () {
                                $('#sContenidoES').summernote({
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

                                $('#sContenidoEN').summernote({
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
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
                                ajaxInsertar: function () {
                                    let $this = this;

                                    if ($('#sContenidoES').summernote('isEmpty')) {
                                        toastr[result.error]('Contenido en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sContenidoEN').summernote('isEmpty')) {
                                        toastr[result.error]('Contenido en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);
                                    formData.append('contenido_es', $('#sContenidoES').summernote('code'));
                                    formData.append('contenido_en', $('#sContenidoEN').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/secciones-quienes-somos/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                $this.sTipo = 0;
                                                $this.sNuevoEnlaceVideo = '';
                                                $('#sContenidoES').summernote('code', '');
                                                $('#sContenidoEN').summernote('code', '');
                                                vueQuienesSomos.ajaxListar();
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
                                    vueQuienesSomos.panelListar(function () {
                                        vueQuienesSomos.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'SECCIONES QUIÉNES SOMOS', '/intranet/app/pagina-web/secciones-quienes-somos');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'SECCIONES QUIÉNES SOMOS', '/intranet/app/pagina-web/secciones-quienes-somos/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstSecciones.findIndex((seccion) => seccion.id === parseInt(iId));
                    let seccion = Object.assign({}, $this.lstSecciones[iIndice]);

                    $('#panel').load('/intranet/app/pagina-web/secciones-quienes-somos/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                seccion: seccion,
                                lstTipos: ['SOLO TEXTO', 'IMAGEN A LA IZQUIERDA', 'IMAGEN A LA DERECHA', 'VIDEO A LA IZQUIERDA'],
                                imagen: null,
                                sNuevoEnlaceVideo: '',
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
                                },
                                sEnlaceVideo: function () {
                                    if (this.sNuevoEnlaceVideo.length > 0) {
                                        return this.sNuevoEnlaceVideo.replace('watch?v=', 'embed/');
                                    }
                                    return this.seccion.enlace_video ? this.seccion.enlace_video : '';
                                }
                            },
                            mounted: function () {
                                $('#sContenidoES').summernote({
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

                                $('#sContenidoEN').summernote({
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

                                $('#sContenidoES').summernote('code', this.seccion.contenido_es);
                                $('#sContenidoEN').summernote('code', this.seccion.contenido_en);
                            },
                            methods: {
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
                                ajaxActualizar: function () {
                                    let $this = this;

                                    if ($('#sContenidoES').summernote('isEmpty')) {
                                        toastr[result.error]('Contenido en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sContenidoEN').summernote('isEmpty')) {
                                        toastr[result.error]('Contenido en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);
                                    formData.append('contenido_es', $('#sContenidoES').summernote('code'));
                                    formData.append('contenido_en', $('#sContenidoEN').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/secciones-quienes-somos/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                vueQuienesSomos.ajaxListar();
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
                                        url: '/intranet/app/pagina-web/secciones-quienes-somos/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueQuienesSomos.ajaxListar(function () {
                                                    vueQuienesSomos.panelListar(function () {
                                                        vueQuienesSomos.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'SECCIONES QUIÉNES SOMOS', '/intranet/app/pagina-web/secciones-quienes-somos');
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
                                    vueQuienesSomos.panelListar(function () {
                                        vueQuienesSomos.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'SECCIONES QUIÉNES SOMOS', '/intranet/app/pagina-web/secciones-quienes-somos');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = seccion.id;
                        window.history.replaceState(null, 'SECCIONES QUIÉNES SOMOS', `/intranet/app/pagina-web/secciones-quienes-somos/${seccion.id}/editar`);
                    });
                }
            }
        });
    });
});
