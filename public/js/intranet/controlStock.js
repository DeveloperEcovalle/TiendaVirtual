listarMenus(function (lstModulos, lstMenus) {
    let vueControlStock = new Vue({
        el: '#wrapper',
        data: {
            sBuscar: '',
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstProductos: [],
            iIdSeleccionado: 0,
            iError: 0,
        },
        computed: {
            lstProductosFiltrados: function () {
                return this.lstProductos.filter(producto => producto.nombre_es.toLowerCase().includes(this.sBuscar.toLowerCase()));
            },
        },
        mounted: function () {
            this.ajaxListarProductos(this.cargarPanel);
        },
        methods: {
            ajaxListarProductos: function (onSuccess) {
                let $this = this;
                $.ajax({
                    type: 'post',
                    url: '/intranet/app/gestion-inventario/control-stock/ajax/listarProductos',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstProductos = data.lstProductos;

                            if (onSuccess) {
                                onSuccess();
                            }
                        }
                    },
                    error: function () {
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
                    case 'control-stock': {
                        $this.panelListar();
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
                $('#panel').load('/intranet/app/gestion-inventario/control-stock/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let lstProducto = $this.lstProductos.filter(producto => producto.id === parseInt(iId));
                let producto = lstProducto[0];

                $('#panel').load('/intranet/app/gestion-inventario/control-stock/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            producto: producto,
                            sTipoAjuste: '',

                            iActualizando: 0,
                        },
                        methods: {
                            ajaxListarProducto: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-inventario/control-stock/ajax/listarProducto',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            let producto = data.producto;

                                            $this.producto = producto;

                                            let iIndiceProducto = vueControlStock.lstProductos.findIndex(p => p.id === parseInt(iId));
                                            vueControlStock.lstProductos[iIndiceProducto].stock_minimo = producto.stock_minimo;
                                            vueControlStock.lstProductos[iIndiceProducto].stock_actual = producto.stock_actual;
                                            vueControlStock.lstProductos[iIndiceProducto].stock_separado = producto.stock_separado;
                                        }
                                    }
                                });
                            },

                            ajaxActualizarStock: function () {
                                let $this = this;
                                $this.iActualizando = 1;

                                let frmActualizarStock = document.getElementById('frmActualizarStock');
                                let formData = new FormData(frmActualizarStock);
                                formData.append('id', $this.producto.id);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-inventario/control-stock/ajax/insertarAjuste',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.sTipoAjuste = '';
                                            $this.ajaxListarProducto();
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iActualizando = 0;
                                    }
                                });
                            },

                            ajaxCancelar: function () {
                                vueControlStock.panelListar(function () {
                                    vueControlStock.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'CONTROL DE STOCK', '/intranet/app/gestion-inventario/control-stock');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = producto.id;
                    window.history.replaceState(null, 'CONTROL DE STOCK', `/intranet/app/gestion-inventario/control-stock/${producto.id}/editar`);
                });
            }
        }
    });
});
