listarMenus(function (lstModulos, lstMenus) {
    let vuePreciosEnvio = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstPreciosEnvio: [],
            iIdSeleccionado: 0,
            iError: 0,
        },
        mounted: function () {
            let $this = this;
            this.ajaxListar().then(() => $this.cargarPanel());
        },
        methods: {
            ajaxListar: function () {
                let $this = this;

                return axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/listar')
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstPreciosEnvio = data.lstPreciosEnvio;
                        }
                    })
                    .catch(() => $this.iError = 1);
            },
            cargarPanel: function () {
                let $this = this;
                let sUrl = location.pathname;
                let lstUrl = sUrl.split('/');

                let sLastPath = lstUrl.pop();
                sLastPath = sLastPath.length === 0 ? lstUrl.pop() : sLastPath;
                switch (sLastPath) {
                    case 'precios-envio': {
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
                $('#panel').load('/intranet/app/gestion-inventario/precios-envio/ajax/panelListar');
                this.iIdSeleccionado = 0;
                window.history.replaceState(null, 'PRECIOS DE ENVÍO', '/intranet/app/gestion-inventario/precios-envio');
            },
            panelNuevo: function () {
                let $this = this;
                $('#panel').load('/intranet/app/gestion-inventario/precios-envio/ajax/panelNuevo', function () {
                    let vueNuevo = new Vue({
                        el: '#panel',
                        data: {
                            formData: {
                                sDepartamento: '',
                                sProvincia: '',
                                sDistrito: ''
                            },
                            lstUbigeo: [],
                            iInsertando: 0,
                        },
                        computed: {
                            lstDepartamentos: function () {
                                let lstDepartamentos = [];
                                for (let ubigeo of this.lstUbigeo) {
                                    if (lstDepartamentos.findIndex(departamento => departamento.departamento === ubigeo.departamento) === -1)
                                        lstDepartamentos.push(ubigeo);
                                }
                                return lstDepartamentos;
                            },
                            lstProvincias: function () {
                                let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.formData.sDepartamento);
                                let lstProvincias = [];
                                for (let ubigeo of lstUbigeoFiltrado) {
                                    if (lstProvincias.findIndex(provincia => provincia.provincia === ubigeo.provincia) === -1)
                                        lstProvincias.push(ubigeo);
                                }
                                return lstProvincias;
                            },
                            lstDistritos: function () {
                                return this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.formData.sDepartamento
                                    && ubigeo.provincia === this.formData.sProvincia);
                            },
                        },
                        mounted: function () {
                            let $this = this;
                            axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/listarUbigeo')
                                .then(response => {
                                    let respuesta = response.data;
                                    if (respuesta.result === result.success) {
                                        $this.lstUbigeo = respuesta.data.lstUbigeo;
                                    }
                                })
                                .catch(error => {
                                    let respuesta = error.response.data;
                                    let sHtmlMensaje = sHtmlErrores(respuesta.message);
                                    toastr[result.error](sHtmlMensaje);
                                });
                        },
                        methods: {
                            ajaxInsertar: function () {
                                let $this = this;

                                $this.iInsertando = 1;

                                let frmNuevo = document.getElementById('frmNuevo');
                                let formData = new FormData(frmNuevo);

                                let respuestaInsertar;
                                axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/insertar', formData)
                                    .then(response => {
                                        let respuesta = response.data;
                                        toastr[respuesta.result](respuesta.mensaje);
                                        respuestaInsertar = respuesta;
                                    })
                                    .catch(error => {
                                        let respuesta = error.response.data;
                                        let sHtmlMensaje = sHtmlErrores(respuesta.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    })
                                    .then(() => $this.iInsertando = 0)
                                    .then(() => {
                                        if (respuestaInsertar.result === result.success) {
                                            $this.imagen = null;
                                            formData.sDepartamento = '';
                                            formData.sProvincia = '';
                                            formData.sDistrito = '';
                                            vuePreciosEnvio.ajaxListar().then(() => vuePreciosEnvio.panelEditar(respuestaInsertar.data));
                                        }
                                    });
                            },
                            ajaxCancelar: function () {
                                vuePreciosEnvio.panelListar();
                            }
                        }
                    });

                    $this.iIdSeleccionado = 0;
                    window.history.replaceState(null, 'PRECIOS DE ENVÍO', '/intranet/app/gestion-inventario/precios-envio/nuevo');
                });
            },
            panelEditar: function (iId) {
                let iIndice = this.lstPreciosEnvio.findIndex(precioEnvio => precioEnvio.id === parseInt(iId));
                let precioEnvio = Object.assign({}, this.lstPreciosEnvio[iIndice]);

                let $this = this;
                $('#panel').load('/intranet/app/gestion-inventario/precios-envio/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            precioEnvio: precioEnvio,
                            lstUbigeo: [],
                            iActualizando: 0,
                            iActualizandoContrasena: 0,
                            iEliminando: 0
                        },
                        computed: {
                            lstDepartamentos: function () {
                                let lstDepartamentos = [];
                                for (let ubigeo of this.lstUbigeo) {
                                    if (lstDepartamentos.findIndex(departamento => departamento.departamento === ubigeo.departamento) === -1)
                                        lstDepartamentos.push(ubigeo);
                                }
                                return lstDepartamentos;
                            },
                            lstProvincias: function () {
                                let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.precioEnvio.departamento);
                                let lstProvincias = [];
                                for (let ubigeo of lstUbigeoFiltrado) {
                                    if (lstProvincias.findIndex(provincia => provincia.provincia === ubigeo.provincia) === -1)
                                        lstProvincias.push(ubigeo);
                                }
                                return lstProvincias;
                            },
                            lstDistritos: function () {
                                return this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.precioEnvio.departamento
                                    && ubigeo.provincia === this.precioEnvio.provincia);
                            },
                        },
                        mounted: function () {
                            let $this = this;
                            axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/listarUbigeo')
                                .then(response => {
                                    let respuesta = response.data;
                                    if (respuesta.result === result.success) {
                                        $this.lstUbigeo = respuesta.data.lstUbigeo;
                                    }
                                })
                                .catch(error => {
                                    let respuesta = error.response.data;
                                    let sHtmlMensaje = sHtmlErrores(respuesta.errors);
                                    toastr[result.error](sHtmlMensaje);
                                });
                        },
                        methods: {
                            ajaxActualizar: function () {
                                this.iActualizando = 1;

                                let frmEditar = document.getElementById('frmEditar');
                                let formData = new FormData(frmEditar);
                                formData.append('id', iId);

                                let $this = this;
                                let respuestaActualizar;
                                axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/actualizar', formData)
                                    .then(response => {
                                        let respuesta = response.data;
                                        toastr[respuesta.result](respuesta.mensaje);
                                        respuestaActualizar = respuesta;
                                    })
                                    .catch(error => {
                                        let respuesta = error.response.data;
                                        let sHtmlMensaje = sHtmlErrores(respuesta.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    })
                                    .then(() => $this.iActualizando = 0)
                                    .then(() => {
                                        if (respuestaActualizar.result === result.success)
                                            vuePreciosEnvio.ajaxListar();
                                    });
                            },
                            ajaxEliminar: function (iId) {
                                let $this = this;
                                $this.iEliminando = 1;

                                let formData = new FormData();
                                formData.append('id', iId);

                                let respuestaEliminar;
                                axios.post('/intranet/app/gestion-inventario/precios-envio/ajax/eliminar', formData)
                                    .then(response => {
                                        let respuesta = response.data;
                                        toastr[respuesta.result](respuesta.mensaje);
                                        respuestaEliminar = respuesta;
                                    })
                                    .catch(error => {
                                        let respuesta = error.response.data;
                                        let sHtmlMensaje = sHtmlErrores(respuesta.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    })
                                    .then(() => $this.iEliminando = 0)
                                    .then(() => {
                                        if (respuestaEliminar.result === result.success)
                                            vuePreciosEnvio.ajaxListar().then(vuePreciosEnvio.panelListar());
                                    });
                            },
                            ajaxCancelar: function () {
                                vuePreciosEnvio.panelListar();
                            }
                        }
                    });

                    $this.iIdSeleccionado = precioEnvio.id;
                    window.history.replaceState(null, 'PRECIOS DE ENVÍO', `/intranet/app/gestion-inventario/precios-envio/${precioEnvio.id}/editar`);
                });
            }
        }
    });
});
