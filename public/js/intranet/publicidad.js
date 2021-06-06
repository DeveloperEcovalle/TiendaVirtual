$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vuePublicidads = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                lstPublicidads: [],
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
                        url: '/intranet/app/configuracion/publicidad/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstPublicidads = data.lstPublicidads;

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
                        case 'publicidad': {
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
                    $('#panel').load('/intranet/app/configuracion/publicidad/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/configuracion/publicidad/ajax/panelNuevo', function () {
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
                                    formData.append('imagen', $this.imagen);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/publicidad/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                vuePublicidads.ajaxListar();
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
                                    vuePublicidads.panelListar(function () {
                                        vueBanners.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'BANNERS', '/intranet/app/configuracion/publicidad');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'BANNERS', '/intranet/app/configuracion/publicidad/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstPublicidads.findIndex((publicidad) => publicidad.id === parseInt(iId));
                    let publicidad = Object.assign({}, $this.lstPublicidads[iIndice]);

                    $('#panel').load('/intranet/app/configuracion/publicidad/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                publicidad: publicidad,
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            methods: {
                                changeActivo: function (event) {
                                    this.publicidad.estado = event.target.checked ? 1 : 0;
                                },
                                ajaxActualizar: function () {
                                    let $this = this;
                                    $this.iActualizando = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/publicidad/ajax/actualizar',
                                        data: $('#frmEditar').serialize() + '&id=' + iId,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                $('#frmEditar')[0].reset();
                                                vuePublicidads.ajaxListar();
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
                                        url: '/intranet/app/configuracion/publicidad/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vuePublicidads.ajaxListar(function () {
                                                    vuePublicidads.panelListar(function () {
                                                        vuePublicidads.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'PUBLICIDADS', '/intranet/app/configuracion/publicidad');
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
                                    vuePublicidads.panelListar(function () {
                                        vuePublicidads.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PUBLICIDADS', '/intranet/app/configuracion/publicidad');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = publicidad.id;
                        window.history.replaceState(null, 'PUBLICIDADS', `/intranet/app/configuracion/publicidad/${publicidad.id}/editar`);
                    });
                }
            }
        });
    });
});
