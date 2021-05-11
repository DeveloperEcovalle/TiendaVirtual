$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vuePerfiles = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstPerfiles: [],
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
                        url: '/intranet/app/configuracion/perfiles/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstPerfiles = data.lstPerfiles;

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
                        case 'perfiles': {
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
                    $('#panel').load('/intranet/app/configuracion/perfiles/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/configuracion/perfiles/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                lstMenus: [],
                                iInsertando: 0,
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/perfiles/ajax/nuevo/listarPermisos',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstMenus = respuesta.data.lstMenus;
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
                                        url: '/intranet/app/configuracion/perfiles/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iInsertando = 0;

                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                vuePerfiles.ajaxListar();
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
                                    vuePerfiles.panelListar(function () {
                                        vuePerfiles.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PERFILES DE USUARIO', '/intranet/app/configuracion/perfiles');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'PERFILES DE USUARIO', '/intranet/app/configuracion/perfiles/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstPerfiles.findIndex((perfil) => perfil.id === parseInt(iId));
                    let perfil = Object.assign({}, $this.lstPerfiles[iIndice]);

                    $('#panel').load('/intranet/app/configuracion/perfiles/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                perfil: perfil,
                                lstMenus: [],
                                lstPermisosSeleccionados: [],
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/configuracion/perfiles/ajax/editar/listarPermisos',
                                    data: {iPerfilId: perfil.id},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.lstMenus = respuesta.data.lstMenus;
                                            for (menu of $this.lstMenus) {
                                                for (permiso of menu.permisos) {
                                                    if (permiso.perfilespermisos.length > 0) {
                                                        $this.lstPermisosSeleccionados.push(permiso.id);
                                                    }
                                                }
                                            }
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
                                        url: '/intranet/app/configuracion/perfiles/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            $this.iActualizando = 0;

                                            if (respuesta.result === result.success) {
                                                vuePerfiles.ajaxListar();
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
                                        url: '/intranet/app/configuracion/perfiles/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            $this.iEliminando = 0;

                                            if (respuesta.result === result.success) {
                                                vuePerfiles.ajaxListar(function () {
                                                    vuePerfiles.panelListar(function () {
                                                        vuePerfiles.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'PERFILES DE USUARIO', '/intranet/app/configuracion/perfiles');
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
                                    vuePerfiles.panelListar(function () {
                                        vuePerfiles.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PERFILES DE USUARIO', '/intranet/app/configuracion/perfiles');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = perfil.id;
                        window.history.replaceState(null, 'PERFILES DE USUARIO', `/intranet/app/configuracion/perfiles/${perfil.id}/editar`);
                    });
                }
            }
        });
    });
});
