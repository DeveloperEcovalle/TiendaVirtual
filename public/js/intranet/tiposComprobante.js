$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueTiposComprobante = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstTiposComprobante: [],
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
                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstTiposComprobante = data.lstTiposComprobante;

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
                        case 'tipos-comprobante': {
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
                    $('#panel').load('/intranet/app/configuracion/tipos-comprobante/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/configuracion/tipos-comprobante/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                lstTiposComprobanteSunat: [],
                                sComprobanteSunatAsociado: '',
                                iInsertando: 0,
                            },
                            computed: {
                                sSerieEjemplo: function () {
                                    let iIndice = this.lstTiposComprobanteSunat.findIndex((comprobanteSunat) => comprobanteSunat.codigo === this.sComprobanteSunatAsociado);
                                    if (iIndice === -1) {
                                        return '****';
                                    }
                                    return this.lstTiposComprobanteSunat[iIndice].validacion_ejemplo;
                                }
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/tipos-comprobante/ajax/nuevo/listarTiposComprobanteSunat',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstTiposComprobanteSunat = respuesta.data.lstSunatTiposComprobante;
                                        }
                                    },
                                    error: function (respuesta) {
                                        $this.iInsertando = 0;

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            methods: {
                                ajaxInsertar: function () {
                                    let $this = this;

                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.sComprobanteSunatAsociado = '';
                                                frmNuevo.reset();
                                                vueTiposComprobante.ajaxListar();
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
                                    vueTiposComprobante.panelListar(function () {
                                        vueTiposComprobante.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'TIPOS DE COMPROBANTE', '/intranet/app/configuracion/tipos-comprobante');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'TIPOS DE COMPROBANTE', '/intranet/app/configuracion/tipos-comprobante/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstTiposComprobante.findIndex((tipoComprobante) => tipoComprobante.id === parseInt(iId));
                    let tipoComprobante = Object.assign({}, $this.lstTiposComprobante[iIndice]);

                    $('#panel').load('/intranet/app/configuracion/tipos-comprobante/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                tipoComprobante: tipoComprobante,
                                lstTiposComprobanteSunat: [],
                                iActualizando: 0,
                                iInsertandoSerie: 0,
                                iEliminandoSerie: 0,
                                iSerieIdEliminar: 0,
                                iEliminando: 0
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/tipos-comprobante/ajax/editar/listarTiposComprobanteSunat',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstTiposComprobanteSunat = respuesta.data.lstSunatTiposComprobante;
                                        }
                                    },
                                    error: function (respuesta) {
                                        $this.iInsertando = 0;

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            methods: {
                                ajaxActualizar: function () {
                                    let $this = this;

                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                vueTiposComprobante.ajaxListar();
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
                                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vueTiposComprobante.ajaxListar(function () {
                                                    vueTiposComprobante.panelListar(function () {
                                                        vueTiposComprobante.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'TIPOS DE COMPROBANTE', '/intranet/app/configuracion/tipos-comprobante');
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
                                ajaxInsertarSerie: function (iId) {
                                    let $this = this;
                                    $this.iInsertandoSerie = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/insertarSerie',
                                        data: $('#frmAgregarSerie').serialize() + '&id=' + iId,
                                        success: function (respuesta) {
                                            $this.iInsertandoSerie = 0;

                                            if (respuesta.result === result.success) {
                                                vueTiposComprobante.ajaxListar(function () {
                                                    vueTiposComprobante.panelEditar(iId);
                                                });
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            $this.iInsertandoSerie = 0;

                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxEliminarSerie: function (iTipoComprobanteId, iSerieId) {
                                    let $this = this;
                                    $this.iEliminandoSerie = 1;
                                    $this.iSerieIdEliminar = iSerieId;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/configuracion/tipos-comprobante/ajax/eliminarSerie',
                                        data: {id: iSerieId},
                                        success: function (respuesta) {
                                            $this.iEliminandoSerie = 0;
                                            $this.iSerieIdEliminar = 0;

                                            if (respuesta.result === result.success) {
                                                vueTiposComprobante.ajaxListar(function () {
                                                    vueTiposComprobante.panelEditar(iTipoComprobanteId);
                                                });
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            $this.iEliminandoSerie = 0;
                                            $this.iSerieIdEliminar = 0;

                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxCancelar: function () {
                                    vueTiposComprobante.panelListar(function () {
                                        vueTiposComprobante.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'TIPOS DE COMPROBANTE', '/intranet/app/configuracion/tipos-comprobante');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = tipoComprobante.id;
                        window.history.replaceState(null, 'TIPOS DE COMPROBANTE', `/intranet/app/configuracion/tipos-comprobante/${tipoComprobante.id}/editar`);
                    });
                }
            }
        });
    });
});
