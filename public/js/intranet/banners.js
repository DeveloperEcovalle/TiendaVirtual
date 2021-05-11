$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueBanners = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                lstBanners: [],
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
                        url: '/intranet/app/pagina-web/banners/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstBanners = data.lstBanners;

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
                        case 'banners': {
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
                    $('#panel').load('/intranet/app/pagina-web/banners/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/pagina-web/banners/ajax/panelNuevo', function () {
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
                                        url: '/intranet/app/pagina-web/banners/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                vueBanners.ajaxListar();
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
                                    vueBanners.panelListar(function () {
                                        vueBanners.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'BANNERS', '/intranet/app/pagina-web/banners');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'BANNERS', '/intranet/app/pagina-web/banners/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstBanners.findIndex((banner) => banner.id === parseInt(iId));
                    let banner = Object.assign({}, $this.lstBanners[iIndice]);

                    $('#panel').load('/intranet/app/pagina-web/banners/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                banner: banner,
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            methods: {
                                changeActivo: function (event) {
                                    this.banner.activo = event.target.checked ? 1 : 0;
                                },
                                changeMedio: function (event) {
                                    this.banner.medio = event.target.checked ? 1 : 0;
                                },
                                ajaxActualizar: function () {
                                    let $this = this;
                                    $this.iActualizando = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/pagina-web/banners/ajax/actualizar',
                                        data: $('#frmEditar').serialize() + '&id=' + iId,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                $('#frmEditar')[0].reset();
                                                vueBanners.ajaxListar();
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
                                        url: '/intranet/app/pagina-web/banners/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueBanners.ajaxListar(function () {
                                                    vueBanners.panelListar(function () {
                                                        vueBanners.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'BANNERS', '/intranet/app/pagina-web/banners');
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
                                    vueBanners.panelListar(function () {
                                        vueBanners.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'BANNERS', '/intranet/app/pagina-web/banners');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = banner.id;
                        window.history.replaceState(null, 'BANNERS', `/intranet/app/pagina-web/banners/${banner.id}/editar`);
                    });
                }
            }
        });
    });
});
