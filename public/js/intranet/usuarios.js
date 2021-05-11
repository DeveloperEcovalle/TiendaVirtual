listarMenus(function (lstModulos, lstMenus) {
    let vueUsuarios = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,

            lstUsuarios: [],
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
                    url: '/intranet/app/configuracion/usuarios/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstUsuarios = data.lstUsuarios;

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
                    case 'usuarios': {
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
                $('#panel').load('/intranet/app/configuracion/usuarios/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelNuevo: function () {
                let $this = this;
                $('#panel').load('/intranet/app/configuracion/usuarios/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            lstPerfiles: [],
                            iInsertando: 0,
                        },
                        mounted: function () {
                            let $this = this;
                            $.ajax({
                                type: 'post',
                                url: '/intranet/app/configuracion/usuarios/ajax/nuevo/listarPerfiles',
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
                                    url: '/intranet/app/configuracion/usuarios/ajax/insertar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iInsertando = 0;

                                        if (respuesta.result === result.success) {
                                            $this.imagen = null;
                                            frmNuevo.reset();
                                            vueUsuarios.ajaxListar();
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
                                vueUsuarios.panelListar(function () {
                                    vueUsuarios.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'USUARIOS', '/intranet/app/configuracion/usuarios');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'USUARIOS', '/intranet/app/configuracion/usuarios/nuevo');
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let iIndice = $this.lstUsuarios.findIndex((usuario) => usuario.id === parseInt(iId));
                let usuario = Object.assign({}, $this.lstUsuarios[iIndice]);

                $('#panel').load('/intranet/app/configuracion/usuarios/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            usuario: usuario,
                            lstPerfiles: [],
                            iActualizando: 0,
                            iActualizandoContrasena: 0,
                            iEliminando: 0
                        },
                        mounted: function () {
                            let $this = this;
                            $.ajax({
                                type: 'post',
                                url: '/intranet/app/configuracion/usuarios/ajax/editar/listarPerfiles',
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
                                    url: '/intranet/app/configuracion/usuarios/ajax/actualizar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iActualizando = 0;

                                        if (respuesta.result === result.success) {
                                            vueUsuarios.ajaxListar();
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
                            ajaxActualizarContrasena: function () {
                                let $this = this;

                                $this.iActualizandoContrasena = 1;

                                let frmEditar = document.getElementById('frmCambiarContrasena');
                                let formData = new FormData(frmEditar);
                                formData.append('id', iId);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/usuarios/ajax/actualizarContrasena',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iActualizandoContrasena = 0;

                                        if (respuesta.result === result.success) {
                                            $('#modalCambiarContrasena').modal('hide');
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        $this.iActualizandoContrasena = 0;

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
                                    url: '/intranet/app/configuracion/usuarios/ajax/eliminar',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        $this.iEliminando = 0;

                                        if (respuesta.result === result.success) {
                                            vueUsuarios.ajaxListar(function () {
                                                vueUsuarios.panelListar(function () {
                                                    vueUsuarios.iIdSeleccionado = 0;
                                                    window.history.replaceState(null, 'USUARIOS', '/intranet/app/configuracion/usuarios');
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
                                vueUsuarios.panelListar(function () {
                                    vueUsuarios.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'USUARIOS', '/intranet/app/configuracion/usuarios');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = usuario.id;
                    window.history.replaceState(null, 'USUARIOS', `/intranet/app/configuracion/usuarios/${usuario.id}/editar`);
                });
            }
        }
    });
});
