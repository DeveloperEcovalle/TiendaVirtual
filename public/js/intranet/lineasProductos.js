listarMenus(function (lstModulos, lstMenus) {
    let vueLineas = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstLineas: [],
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
                    url: '/intranet/app/gestion-productos/lineas/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstLineas = data.lstLineas;

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
                    case 'lineas': {
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
                $('#panel').load('/intranet/app/gestion-productos/lineas/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelNuevo: function () {
                let $this = this;
                $('#panel').load('/intranet/app/gestion-productos/lineas/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            imagen: null,
                            iInsertando: 0
                        },
                        computed: {
                            sNombreImagen: function () {
                                if (this.imagen === null) {
                                    return 'Buscar archivo';
                                }
                                return this.imagen.name.split('\\').pop();
                            },
                            sContenidoImagen: function () {
                                if (this.imagen === null) {
                                    return null;
                                }
                                return URL.createObjectURL(this.imagen);
                            },
                        },
                        mounted: function () {
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
                                height: 200
                            };

                            $('#sContenidoEspanol').summernote(summernoteConfig);
                            $('#sContenidoIngles').summernote(summernoteConfig);
                        },
                        methods: {
                            cambiarImagen: function (event) {
                                let input = event.target;
                                this.imagen = input.files[0];
                            },
                            ajaxInsertar: function () {
                                let $this = this;

                                $this.iInsertando = 1;

                                let frmNuevo = document.getElementById('frmNuevo');
                                let formData = new FormData(frmNuevo);
                                formData.append('contenido_en_espanol', $('#sContenidoEspanol').summernote('code'));
                                formData.append('contenido_en_ingles', $('#sContenidoIngles').summernote('code'));

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/lineas/ajax/insertar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.imagen = null;
                                            frmNuevo.reset();
                                            vueLineas.ajaxListar();
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iInsertando = 0;
                                    }
                                });
                            },
                            ajaxCancelar: function () {
                                vueLineas.panelListar(function () {
                                    vueLineas.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'LÍNEAS DE PRODUCTOS', '/intranet/app/gestion-productos/lineas');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'LÍNEAS DE PRODUCTOS', '/intranet/app/gestion-productos/lineas/nuevo');
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let iIndice = $this.lstLineas.findIndex((linea) => linea.id === parseInt(iId));
                let linea = Object.assign({}, $this.lstLineas[iIndice]);

                $('#panel').load('/intranet/app/gestion-productos/lineas/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            imagen: null,
                            linea: linea,
                            iActualizando: 0,
                            iEliminando: 0
                        },
                        computed: {
                            sNombreImagen: function () {
                                if (this.imagen === null) {
                                    return 'Buscar archivo';
                                }
                                return this.imagen.name.split('\\').pop();
                            },
                            sContenidoImagen: function () {
                                if (this.imagen === null) {
                                    return null;
                                }
                                return URL.createObjectURL(this.imagen);
                            },
                        },
                        mounted: function () {
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
                                height: 200
                            };

                            $('#sContenidoEspanol').summernote(summernoteConfig);
                            $('#sContenidoIngles').summernote(summernoteConfig);

                            $('#sContenidoEspanol').summernote('code', linea.contenido_espanol);
                            $('#sContenidoIngles').summernote('code', linea.contenido_ingles);
                        },
                        methods: {
                            cambiarImagen: function (event) {
                                let input = event.target;
                                this.imagen = input.files[0];
                            },
                            ajaxActualizar: function () {
                                let $this = this;

                                $this.iActualizando = 1;

                                let frmEditar = document.getElementById('frmEditar');
                                let formData = new FormData(frmEditar);
                                formData.append('id', iId);
                                formData.append('contenido_en_espanol', $('#sContenidoEspanol').summernote('code'));
                                formData.append('contenido_en_ingles', $('#sContenidoIngles').summernote('code'));

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/lineas/ajax/actualizar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            vueLineas.ajaxListar();
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iActualizando = 0;
                                    }
                                });
                            },
                            ajaxEliminar: function (iId) {
                                let $this = this;
                                $this.iEliminando = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/lineas/ajax/eliminar',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        $this.iEliminando = 0;

                                        if (respuesta.result === result.success) {
                                            vueLineas.ajaxListar(function () {
                                                vueLineas.panelListar(function () {
                                                    vueLineas.iIdSeleccionado = 0;
                                                    window.history.replaceState(null, 'LÍNEAS DE PRODUCTOS', '/intranet/app/gestion-productos/lineas');
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
                                vueLineas.panelListar(function () {
                                    vueLineas.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'LÍNEAS DE PRODUCTOS', '/intranet/app/gestion-productos/lineas');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = linea.id;
                    window.history.replaceState(null, 'LÍNEAS DE PRODUCTOS', `/intranet/app/gestion-productos/lineas/${linea.id}/editar`);
                });
            }
        }
    });
});
