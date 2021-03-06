listarMenus(function (lstModulos, lstMenus) {
    
    let vueCarrito = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstUbigeo: [],
            iIdSeleccionado: 0,
            iError: 0,
            sBuscar: '',
            sListarUbigeo: 1,
        },
        computed: {
            lstUbigeoFiltrados: function () {
                return this.lstUbigeo.filter(
                    ubigeo => ubigeo.departamento.toLowerCase().includes(this.sBuscar.toLowerCase())
                    || ubigeo.provincia.toLowerCase().includes(this.sBuscar.toLowerCase())
                    || ubigeo.distrito.toLowerCase().includes(this.sBuscar.toLowerCase()));
            },
        },
        mounted: function () {
            let $this = this;

            $this.ajaxListar($this.cargarPanel);
        },
        methods: {
            ajaxListar: function (onSuccess) {
                let $this = this;

                $.ajax({
                    type: 'get',
                    url: '/intranet/app/pagina-web/carrito-compras/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstUbigeo = data.lstUbigeo;

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
                console.log(lstUrl);

                let sLastPath = lstUrl.pop();
                sLastPath = sLastPath.length === 0 ? lstUrl.pop() : sLastPath;
                switch (sLastPath) {
                    case 'carrito-compras': {
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
                $('#panel').load('/intranet/app/pagina-web/carrito-compras/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let iIndice = $this.lstUbigeo.findIndex((ubigeo) => ubigeo.id === parseInt(iId));
                let ubigeo = Object.assign({}, $this.lstUbigeo[iIndice]);

                $('#panel').load('/intranet/app/pagina-web/carrito-compras/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            ubigeo: ubigeo,
                            iActualizando: 0,
                            iEliminando: 0
                        },
                        methods: {
                            ajaxActualizar: function () {
                                let $this = this;

                                $this.iActualizando = 1;

                                let frmEditar = document.getElementById('frmEditar');
                                let formData = new FormData(frmEditar);
                                formData.append('id', iId);
                                // for( var pair of formData.entries())
                                // {
                                //     console.log(pair[0]);
                                //     console.log(pair[1]);
                                // }

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/carrito-compras/ajax/actualizar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iActualizando = 0;

                                        if (respuesta.result === result.success) {
                                            vueCarrito.ajaxListar();
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
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };

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
                                    url: '/intranet/app/pagina-web/carrito-compras/ajax/eliminar',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        $this.iEliminando = 0;

                                        if (respuesta.result === result.success) {
                                            vueCarrito.ajaxListar(function () {
                                                vueCarrito.panelListar(function () {
                                                    vueCarrito.iIdSeleccionado = 0;
                                                    window.history.replaceState(null, 'CARRITO', '/intranet/app/pagina-web/carrito-compras');
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
                                vueCarrito.panelListar(function () {
                                    vueCarrito.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'CARRITO', '/intranet/app/pagina-web/carrito-compras');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = ubigeo.id;
                    window.history.replaceState(null, 'CARRITO', `/intranet/app/pagina-web/carrito-compras/${ubigeo.id}/editar`);
                });
            },
            panelNuevo: function () {

                let $this = this;
                $('#panel').load('/intranet/app/pagina-web/carrito-compras/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            lstUbigeo: vueCarrito.lstUbigeo,
                            departamento: '',
                            iInsertando: 0,
                        },
                        computed: {
                            lstDepartamentos: function () {
                                let lst = [];
                                for (let ubigeo of this.lstUbigeo) {
                                    if (lst.findIndex((departamento) => departamento === ubigeo.departamento) === -1) {
                                        lst.push(ubigeo.departamento);
                                    }
                                }
                                return lst;
                            },
                            lstProvincias: function () {
                                let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.departamento);
                                let lst = [];
                                for (let ubigeo of lstUbigeoFiltrado) {
                                    if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                                        lst.push(ubigeo.provincia);
                                    }
                                }
                                return lst;
                            },
                        },
                        methods: {
                            ajaxInsertar: function () {
                                let $this = this;

                                $this.iInsertando = 1;

                                let frmNuevo = document.getElementById('frmNuevo');
                                let formData = new FormData(frmNuevo);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/pagina-web/carrito-compras/ajax/insertar',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        $this.iInsertando = 0;

                                        if (respuesta.result === result.success) {
                                            vueCarrito.ajaxListar();
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
                                        toastr.clear();
                                        toastr.options = {
                                            iconClasses: {
                                                error: 'bg-danger',
                                                info: 'bg-info',
                                                success: 'bg-success',
                                                warning: 'bg-warning',
                                            },
                                        };

                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    }
                                });
                            },
                            ajaxCancelar: function () {
                                vueCarrito.panelListar(function () {
                                    vueCarrito.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'CARRITO', '/intranet/app/pagina-web/carrito-compras');
                                });
                            }
                        }
                    });
                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'CARRITO', '/intranet/app/pagina-web/carrito-compras/nuevo');
                });
            }
        }
    });
});
