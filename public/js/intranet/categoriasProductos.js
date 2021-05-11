$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueCategorias = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                lstCategorias: [],
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
                        url: '/intranet/app/gestion-productos/categorias/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstCategorias = data.lstCategorias;

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
                        case 'categorias': {
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
                    $('#panel').load('/intranet/app/gestion-productos/categorias/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/gestion-productos/categorias/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                imagen: null,
                                imagenSeleccion: null,
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
                                sNombreArchivoSeleccion: function () {
                                    if (this.imagenSeleccion === null) {
                                        return 'Buscar archivo';
                                    }
                                    return this.imagenSeleccion.name.split('\\').pop();
                                },
                                sContenidoArchivoSeleccion: function () {
                                    if (this.imagenSeleccion === null) {
                                        return null;
                                    }
                                    return URL.createObjectURL(this.imagenSeleccion);
                                }
                            },
                            methods: {
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
                                cambiarImagenSeleccion: function (event) {
                                    let input = event.target;
                                    this.imagenSeleccion = input.files[0];
                                },
                                ajaxInsertar: function () {
                                    let $this = this;
                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/categorias/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                $this.imagenSeleccion = null;
                                                frmNuevo.reset();
                                                vueCategorias.ajaxListar();
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
                                    vueCategorias.panelListar(function () {
                                        vueCategorias.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'CATEGORÍAS DE PRODUCTOS', '/intranet/app/gestion-productos/categorias');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'CATEGORÍAS DE PRODUCTOS', '/intranet/app/gestion-productos/categorias/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstCategorias.findIndex((banner) => banner.id === parseInt(iId));
                    let categoria = Object.assign({}, $this.lstCategorias[iIndice]);

                    $('#panel').load('/intranet/app/gestion-productos/categorias/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                imagen: null,
                                imagenSeleccion: null,
                                categoria: categoria,
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
                                sNombreArchivoSeleccion: function () {
                                    if (this.imagenSeleccion === null) {
                                        return 'Buscar archivo';
                                    }
                                    return this.imagenSeleccion.name.split('\\').pop();
                                },
                                sContenidoArchivoSeleccion: function () {
                                    if (this.imagenSeleccion === null) {
                                        return null;
                                    }
                                    return URL.createObjectURL(this.imagenSeleccion);
                                }
                            },
                            methods: {
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
                                cambiarImagenSeleccion: function (event) {
                                    let input = event.target;
                                    this.imagenSeleccion = input.files[0];
                                },
                                ajaxActualizar: function () {
                                    let $this = this;
                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/categorias/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                $('#frmEditar')[0].reset();
                                                vueCategorias.ajaxListar();
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
                                        url: '/intranet/app/gestion-productos/categorias/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueCategorias.ajaxListar(function () {
                                                    vueCategorias.panelListar(function () {
                                                        vueCategorias.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'CATEGORÍAS DE PRODUCTOS', '/intranet/app/gestion-productos/categorias');
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
                                    vueCategorias.panelListar(function () {
                                        vueCategorias.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'CATEGORÍAS DE PRODUCTOS', '/intranet/app/gestion-productos/categorias');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = categoria.id;
                        window.history.replaceState(null, 'CATEGORÍAS DE PRODUCTOS', `/intranet/app/gestion-productos/categorias/${categoria.id}/editar`);
                    });
                }
            }
        });
    });
});
