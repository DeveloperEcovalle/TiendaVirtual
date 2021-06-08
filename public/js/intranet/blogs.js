let summerNoteConfig = {
    //airMode: true,
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
    fontNames: ['Arial'],
    minHeight: 200,
    height: 300
};

listarMenus(function (lstModulos, lstMenus) {
    let sPeriodo = $cookies.get('sPeriodoBlog');
    sPeriodo = sPeriodo ? sPeriodo : 'Diario';

    let iDia = $cookies.get('iDiaBlog');
    iDia = iDia ? iDia : new Date().getDate();

    let iMes = $cookies.get('iMesBlog');
    iMes = iMes ? iMes : new Date().getMonth();

    let iAnio = $cookies.get('iAnioBlog');
    iAnio = iAnio ? iAnio : new Date().getFullYear();

    let vueBlogs = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstAnios: [],

            sPeriodo: sPeriodo,
            iDia: iDia,
            iMes: iMes,
            iAnio: iAnio,
            lstBlogs: [],
            iIdSeleccionado: 0,
            iError: 0,
        },
        computed: {
            lstDias: function () {
                let iDiaMax = parseInt(new Date(this.iAnio, parseInt(this.iMes) + 1, 0).getDate());
                return new Array(iDiaMax).fill().map((item, index) => index + 1);
            },
            lFechaDesde: function () {
                switch (this.sPeriodo) {
                    case 'Diario': {
                        return new Date(this.iAnio, this.iMes, this.iDia, 0, 0, 0, 0).getTime();
                    }
                    case 'Mensual': {
                        return new Date(this.iAnio, this.iMes, 1, 0, 0, 0, 0).getTime();
                    }
                }
            },
            lFechaHasta: function () {
                switch (this.sPeriodo) {
                    case 'Diario': {
                        return new Date(this.iAnio, this.iMes, this.iDia, 23, 59, 59, 999).getTime();
                    }
                    case 'Mensual' : {
                        return new Date(this.iAnio, parseInt(this.iMes) + 1, 0, 23, 59, 59, 999).getTime();
                    }
                }
            }
        },
        mounted: function () {
            let $this = this;

            $this.ajaxListarAnios(function () {
                $this.ajaxListar($this.cargarPanel);
            });
        },
        methods: {
            ajaxListarAnios: function (onSuccess) {
                let $this = this;

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/pagina-web/blog/ajax/listarAnios',
                    dataType: 'json',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let lstAnios = respuesta.data.lstAnios;
                            $this.lstAnios = lstAnios;

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
            ajaxListar: function (onSuccess) {
                let $this = this;

                $cookies.set('sPeriodoBlog', $this.sPeriodo, 8);
                $cookies.set('iDiaBlog', $this.iDia, 8);
                $cookies.set('iMesBlog', $this.iMes, 8);
                $cookies.set('iAnioBlog', $this.iAnio, 8);

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/pagina-web/blog/ajax/listar',
                    data: {lFechaDesde: $this.lFechaDesde, lFechaHasta: $this.lFechaHasta},
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstBlogs = data.lstBlogs;

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
                    case 'blog': {
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
                $('#panel').load('/intranet/app/pagina-web/blog/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelNuevo: function () {
                let $this = this;
                $('#panel').load('/intranet/app/pagina-web/blog/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            imagen: null,
                            lstCategorias: [],
                            iInsertando: 0,
                            sResumen: '',
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
                            let $this = this;
                            $('#sContenido').summernote(summerNoteConfig);

                            $this.ajaxListarCategorias();
                        },
                        methods: {
                            cambiarImagen: function (event) {
                                let input = event.target;
                                this.imagen = input.files[0];
                            },
                            ajaxListarCategorias: function () {
                                let $this = this;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/nuevo/listarCategorias',
                                    dataType: 'json',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstCategorias = respuesta.data.lstCategorias;
                                        }
                                    },
                                });
                            },
                            ajaxInsertarCategoria: function () {
                                let $this = this;

                                let frmNuevaCategoria = document.getElementById('frmNuevaCategoria');
                                let formData = new FormData(frmNuevaCategoria);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/nuevo/insertarCategoria',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarCategorias();
                                            $('#modalNuevaCategoria').modal('hide');
                                            $('#frmNuevaCategoria')[0].reset();
                                        }
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                });
                            },
                            ajaxEliminarCategoria: function (id) {
                                let $this = this;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/nuevo/eliminarCategoria',
                                    data: {id: id},
                                    dataType: 'json',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarCategorias();
                                            $('#frmNuevaCategoria')[0].reset();
                                            $('#modalNuevaCategoria').modal('hide');
                                        }
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                });
                            },
                            ajaxInsertar: function () {
                                let $this = this;

                                if ($('#sContenido').summernote('isEmpty')) {
                                    toastr.clear();
                                    toastr.options = {
                                        iconClasses: {
                                            error: 'bg-danger',
                                            info: 'bg-info',
                                            success: 'bg-success',
                                            warning: 'bg-warning',
                                        },
                                    };
                                    toastr[result.error]('Contenido es un campo requerido.');
                                    return;
                                }

                                $this.iInsertando = 1;

                                let frmNuevo = document.getElementById('frmNuevo');
                                let formData = new FormData(frmNuevo);
                                formData.append('contenido', $('#sContenido').summernote('code'));

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/insertar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iInsertando = 0;

                                        if (respuesta.result === result.success) {
                                            $this.imagen = null;
                                            frmNuevo.reset();
                                            $('#sContenido').summernote('code', '');
                                            vueBlogs.ajaxListar();
                                        }

                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        $this.iInsertando = 0;

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            ajaxCancelar: function () {
                                vueBlogs.panelListar(function () {
                                    vueBlogs.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'BLOGS', '/intranet/app/pagina-web/blog');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'BLOGS', '/intranet/app/pagina-web/blog/nuevo');
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let iIndice = $this.lstBlogs.findIndex((banner) => banner.id === parseInt(iId));
                let blog = Object.assign({}, $this.lstBlogs[iIndice]);

                $('#panel').load('/intranet/app/pagina-web/blog/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            blog: blog,
                            imagen: null,
                            lstCategorias: [],
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
                            $('#sContenido').summernote(summerNoteConfig);

                            $('#sContenido').summernote('code', this.blog.contenido);

                            this.ajaxListarCategorias();
                        },
                        methods: {
                            ajaxListarCategorias: function () {
                                let $this = this;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/editar/listarCategorias',
                                    dataType: 'json',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstCategorias = respuesta.data.lstCategorias;
                                        }
                                    },
                                });
                            },
                            ajaxInsertarCategoria: function () {
                                let $this = this;

                                let frmNuevaCategoria = document.getElementById('frmNuevaCategoria');
                                let formData = new FormData(frmNuevaCategoria);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/editar/insertarCategoria',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarCategorias();
                                            frmNuevaCategoria.reset();
                                            $('#modalNuevaCategoria').modal('hide');
                                        }
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                });
                            },
                            ajaxEliminarCategoria: function (id) {
                                let $this = this;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/editar/eliminarCategoria',
                                    data: {id: id},
                                    dataType: 'json',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarCategorias();
                                            $('#frmNuevaCategoria')[0].reset();
                                            $('#modalNuevaCategoria').modal('hide');
                                        }
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                });
                            },
                            cambiarImagen: function (event) {
                                let input = event.target;
                                this.imagen = input.files[0];
                            },
                            ajaxActualizar: function () {
                                let $this = this;

                                if ($('#sContenido').summernote('isEmpty')) {
                                    toastr.clear();
                                    toastr.options = {
                                        iconClasses: {
                                            error: 'bg-danger',
                                            info: 'bg-info',
                                            success: 'bg-success',
                                            warning: 'bg-warning',
                                        },
                                    };
                                    toastr[result.error]('Contenido es un campo requerido.');
                                    return;
                                }

                                $this.iActualizando = 1;

                                let frmEditar = document.getElementById('frmEditar');
                                let formData = new FormData(frmEditar);
                                formData.append('id', iId);
                                formData.append('contenido', $('#sContenido').summernote('code'));

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/actualizar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iActualizando = 0;

                                        if (respuesta.result === result.success) {
                                            vueBlogs.ajaxListar();
                                        }

                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        $this.iActualizando = 0;

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            ajaxEliminar: function (iId) {
                                let $this = this;
                                $this.iEliminando = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/blog/ajax/eliminar',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        $this.iEliminando = 0;

                                        if (respuesta.result === result.success) {
                                            vueBlogs.ajaxListar(function () {
                                                vueBlogs.panelListar(function () {
                                                    vueBlogs.iIdSeleccionado = 0;
                                                    window.history.replaceState(null, 'BLOGS', '/intranet/app/pagina-web/blog');
                                                });
                                            });
                                        }
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        $this.iEliminando = 0;

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            ajaxCancelar: function () {
                                vueBlogs.panelListar(function () {
                                    vueBlogs.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'BLOGS', '/intranet/app/pagina-web/blog');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = blog.id;
                    window.history.replaceState(null, 'BLOGS', `/intranet/app/pagina-web/blog/${blog.id}/editar`);
                });
            }
        }
    });
});
