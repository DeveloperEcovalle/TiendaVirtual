listarMenus(function (lstModulos, lstMenus) {
    let vueAgencias = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,

            lstAgencias: [],
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
                    url: '/intranet/app/configuracion/agencias/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstAgencias = data.lstAgencias;

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
                    case 'agencias': {
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
                $('#panel').load('/intranet/app/configuracion/agencias/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelNuevo: function () {
                let $this = this;
                $('#panel').load('/intranet/app/configuracion/agencias/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            lstPerfiles: [],
                            iInsertando: 0,
                        },
                        mounted: function () {
                            let $this = this;
                            /*$.ajax({
                                type: 'post',
                                url: '/intranet/app/configuracion/agencias/ajax/nuevo/listarPerfiles',
                                success: function (respuesta) {
                                    if (respuesta.result === result.success) {
                                        $this.lstPerfiles = respuesta.data.lstPerfiles;
                                    }
                                },
                                error: function (respuesta) {
                                    $this.iInsertando = 0;

                                    let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                    toastr[result.error](sHtmlMensaje);
                                }
                            });*/
                        },
                        methods: {
                            ajaxInsertar: function () {
                                let $this = this;

                                $this.iInsertando = 1;

                                let frmNuevo = document.getElementById('frmNuevo');
                                let formData = new FormData(frmNuevo);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/agencias/ajax/insertar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iInsertando = 0;

                                        if (respuesta.result === result.success) {
                                            $this.imagen = null;
                                            frmNuevo.reset();
                                            vueAgencias.ajaxListar();
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
                                vueAgencias.panelListar(function () {
                                    vueAgencias.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'AGENCIAS', '/intranet/app/configuracion/agencias');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'AGENCIAS', '/intranet/app/configuracion/agencias/nuevo');
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let iIndice = $this.lstAgencias.findIndex((agencia) => agencia.id === parseInt(iId));
                let agencia = Object.assign({}, $this.lstAgencias[iIndice]);

                $('#panel').load('/intranet/app/configuracion/agencias/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            agencia: agencia,
                            lstPerfiles: [],
                            iActualizando: 0,
                            iActualizandoContrasena: 0,
                            iEliminando: 0
                        },
                        mounted: function () {
                            let $this = this;
                            /*$.ajax({
                                type: 'post',
                                url: '/intranet/app/configuracion/agencias/ajax/editar/listarPerfiles',
                                success: function (respuesta) {
                                    if (respuesta.result === result.success) {
                                        $this.lstPerfiles = respuesta.data.lstPerfiles;
                                    }
                                },
                                error: function (respuesta) {
                                    $this.iInsertando = 0;

                                    let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                    toastr[result.error](sHtmlMensaje);
                                }
                            });*/
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
                                    url: '/intranet/app/configuracion/agencias/ajax/actualizar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iActualizando = 0;

                                        if (respuesta.result === result.success) {
                                            vueAgencias.ajaxListar();
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
                                    url: '/intranet/app/configuracion/agencias/ajax/eliminar',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        $this.iEliminando = 0;

                                        if (respuesta.result === result.success) {
                                            vueAgencias.ajaxListar(function () {
                                                vueAgencias.panelListar(function () {
                                                    vueAgencias.iIdSeleccionado = 0;
                                                    window.history.replaceState(null, 'AGENCIAS', '/intranet/app/configuracion/agencias');
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
                                vueAgencias.panelListar(function () {
                                    vueAgencias.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'AGENCIAS', '/intranet/app/configuracion/agencias');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = agencia.id;
                    window.history.replaceState(null, 'AGENCIAS', `/intranet/app/configuracion/agencias/${agencia.id}/editar`);
                });
            }
        }
    });
});
