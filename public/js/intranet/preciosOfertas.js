listarMenus(function (lstModulos, lstMenus) {
    let vuePreciosOfertas = new Vue({
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
                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarProductos',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstProductos = data.lstProductos;

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
                    case 'precios-ofertas': {
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
                $('#panel').load('/intranet/app/gestion-productos/precios-ofertas/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let lstProducto = $this.lstProductos.filter(producto => producto.id === parseInt(iId));
                let producto = lstProducto[0];

                $('#panel').load('/intranet/app/gestion-productos/precios-ofertas/ajax/panelEditar', function () {
                    let date = new Date();
                    let iMes = date.getMonth();
                    let iAnio = date.getFullYear();

                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            producto: producto,

                            iCargandoUltimosPrecios: 1,
                            lstUltimosPrecios: [],

                            iCargandoPrecios: 1,
                            iMesPrecios: iMes,
                            iAnioPrecios: iAnio,
                            lstAniosPrecios: [],
                            lstPrecios: [],

                            sNuevoPrecio: '',
                            iInsertandoPrecio: 0,
                            iEliminandoPrecio: 1,

                            iCargandoUltimasOfertas: 1,
                            lstUltimasOfertas: [],

                            iCargandoOfertas: 1,
                            iMesOfertas: iMes,
                            iAnioOfertas: iAnio,
                            lstAniosOfertas: [],
                            lstOfertas: [],

                            sTipoOferta: '',
                            sFechaInicio: '',
                            sFechaVencimiento: '',
                            sNuevaOferta: '',
                            iInsertandoOferta: 0,
                            iEliminandoOferta: 1,

                            iCargandoUltimasPromociones: 1,
                            lstUltimasPromociones: [],

                            iCargandoPromociones: 1,
                            iMesPromociones: iMes,
                            iAnioPromociones: iAnio,
                            lstAniosPromociones: [],
                            lstPromociones: [],

                            sTipoPromocion: '',
                            sFechaInicioP: '',
                            sFechaVencimientoP: '',
                            sMinPromocion: '',
                            sMaxPromocion: '',
                            sNuevaPromocion: '',
                            sDescripcionPromocion: '',
                            iInsertandoPromocion: 0,
                            iEliminandoPromocion: 1
                        },
                        computed: {
                            sFechaDesdePrecios: function () {
                                let sDesde = this.iAnioPrecios + '-' + (parseInt(this.iMesPrecios) + 1).toString().padStart(2, '0') + '-01 00:00:00';
                                return sDesde;
                            },
                            sFechaHastaPrecios: function () {
                                let date = new Date(this.iAnioPrecios, (parseInt(this.iMesPrecios) + 1), 0);
                                let sHasta = this.iAnioPrecios + '-' + (parseInt(this.iMesPrecios) + 1).toString().padStart(2, '0') + '-' + date.getDate() + ' 23:59:59';
                                return sHasta;
                            },
                            sFechaDesdeOfertas: function () {
                                let sDesde = this.iAnioOfertas + '-' + (parseInt(this.iMesOfertas) + 1).toString().padStart(2, '0') + '-01 00:00:00';
                                return sDesde;
                            },
                            sFechaHastaOfertas: function () {
                                let date = new Date(this.iAnioOfertas, (parseInt(this.iMesOfertas) + 1), 0);
                                let sHasta = this.iAnioOfertas + '-' + (parseInt(this.iMesOfertas) + 1).toString().padStart(2, '0') + '-' + date.getDate() + ' 23:59:59';
                                return sHasta;
                            },

                            sFechaDesdePromociones: function () {
                                let sDesde = this.iAnioPromociones + '-' + (parseInt(this.iMesPromociones) + 1).toString().padStart(2, '0') + '-01 00:00:00';
                                return sDesde;
                            },
                            sFechaHastaPromociones: function () {
                                let date = new Date(this.iAnioPromociones, (parseInt(this.iMesPromociones) + 1), 0);
                                let sHasta = this.iAnioPromociones + '-' + (parseInt(this.iMesPromociones) + 1).toString().padStart(2, '0') + '-' + date.getDate() + ' 23:59:59';
                                return sHasta;
                            },
                        },
                        watch: {
                            iMesPrecios: function () {
                                this.ajaxListarPrecios();
                            },
                            iAnioPrecios: function () {
                                this.ajaxListarPrecios();
                            }
                        },
                        mounted: function () {
                            let $this = this;
                            $this.ajaxListarAnios(function () {
                                $this.ajaxListarUltimosPrecios();
                                $this.ajaxListarUltimasOfertas();
                                $this.ajaxListarUltimasPromociones();
                            });
                        },
                        methods: {
                            ajaxListarProducto: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarProducto',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            let producto = data.producto;

                                            $this.producto = producto;

                                            let iIndiceProducto = vuePreciosOfertas.lstProductos.findIndex(p => p.id === parseInt(iId));
                                            vuePreciosOfertas.lstProductos[iIndiceProducto].precio_actual = producto.precio_actual;
                                            vuePreciosOfertas.lstProductos[iIndiceProducto].oferta_vigente = producto.oferta_vigente;
                                            vuePreciosOfertas.lstProductos[iIndiceProducto].promocion_vigente = producto.promocion_vigente;
                                        }
                                    }
                                });
                            },
                            ajaxListarAnios: function (onSuccess) {
                                let $this = this;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarAnios',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstAniosPrecios = data.lstAniosPrecios;
                                            $this.lstAniosOfertas = data.lstAniosOfertas;
                                            $this.lstAniosPromociones = data.lstAniosPromociones;

                                            let date = new Date();
                                            let iAnioActual = date.getFullYear();
                                            let objAnio = {value: iAnioActual};

                                            if ($this.lstAniosPrecios.findIndex(a => a.value == iAnioActual) === -1) {
                                                $this.lstAniosPrecios.splice(0, 0, objAnio);
                                            }

                                            if ($this.lstAniosOfertas.findIndex(a => a.value == iAnioActual) === -1) {
                                                $this.lstAniosOfertas.splice(0, 0, objAnio);
                                            }

                                            if ($this.lstAniosPromociones.findIndex(a => a.value == iAnioActual) === -1) {
                                                $this.lstAniosPromociones.splice(0, 0, objAnio);
                                            }

                                            if (onSuccess) {
                                                onSuccess();
                                            }
                                        }
                                    }
                                });
                            },

                            ajaxListarUltimosPrecios: function (onSuccess) {
                                let $this = this;
                                $this.iCargandoUltimosPrecios = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarUltimosPrecios',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstUltimosPrecios = data.lstUltimosPrecios;

                                            if (onSuccess) {
                                                onSuccess();
                                            }
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoUltimosPrecios = 0;
                                    }
                                });
                            },
                            ajaxListarPrecios: function (onSuccess) {
                                let $this = this;
                                $this.lstPrecios = [];
                                $this.iCargandoPrecios = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarPrecios',
                                    data: {id: iId, fecha_desde: $this.sFechaDesdePrecios, fecha_hasta: $this.sFechaHastaPrecios},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstPrecios = data.lstPrecios;

                                            if (onSuccess) {
                                                onSuccess();
                                            }
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoPrecios = 0;
                                    }
                                });
                            },
                            ajaxInsertarPrecio: function () {
                                let $this = this;
                                $this.iInsertandoPrecio = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/insertarPrecio',
                                    data: {id: iId, nuevo_precio: $this.sNuevoPrecio},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.sNuevoPrecio = '';
                                            $this.ajaxListarUltimosPrecios(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iInsertandoPrecio = 0;
                                    }
                                });
                            },
                            ajaxEliminarPrecio: function (iPrecioId) {
                                let $this = this;
                                $this.iEliminandoPrecio = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/eliminarPrecio',
                                    data: {id: iPrecioId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarUltimosPrecios(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iEliminandoPrecio = 0;
                                    }
                                });
                            },

                            ajaxListarUltimasOfertas: function (onSuccess) {
                                let $this = this;
                                $this.iCargandoUltimasOfertas = 0;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarUltimasOfertas',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstUltimasOfertas = data.lstUltimasOfertas;

                                            if (onSuccess) {
                                                onSuccess();
                                            }
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoUltimasOfertas = 0;
                                    }
                                });
                            },
                            ajaxListarOfertas: function () {
                                let $this = this;
                                $this.iCargandoOfertas = 0;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarOfertas',
                                    data: {id: iId, fecha_desde: $this.sFechaDesdeOfertas, fecha_hasta: $this.sFechaHastaOfertas},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstOfertas = data.lstOfertas;
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoOfertas = 0;
                                    }
                                });
                            },
                            ajaxInsertarOferta: function () {
                                let $this = this;
                                $this.iInsertandoOferta = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/insertarOferta',
                                    data: {
                                        id: iId, tipo_de_oferta: $this.sTipoOferta,
                                        fecha_de_inicio: $this.sFechaInicio, fecha_de_vencimiento: $this.sFechaVencimiento,
                                        nueva_oferta: $this.sNuevaOferta
                                    },
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.sTipoOferta = '';
                                            $this.sFechaInicio = '';
                                            $this.sFechaVencimiento = '';
                                            $this.sNuevaOferta = '';

                                            $this.ajaxListarUltimasOfertas(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iInsertandoOferta = 0;
                                    }
                                });
                            },
                            ajaxEliminarOferta: function (iOfertaId) {
                                let $this = this;
                                $this.iEliminandoOferta = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/eliminarOferta',
                                    data: {id: iOfertaId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarUltimasOfertas(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iEliminandoOferta = 0;
                                    }
                                });
                            },

                            ajaxListarUltimasPromociones: function (onSuccess) {
                                let $this = this;
                                $this.iCargandoUltimasPromociones = 0;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarUltimasPromociones',
                                    data: {id: iId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstUltimasPromociones = data.lstUltimasPromociones;

                                            if (onSuccess) {
                                                onSuccess();
                                            }
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoUltimasPromociones = 0;
                                    }
                                });
                            },
                            ajaxListarPromociones: function () {
                                let $this = this;
                                $this.iCargandoPromociones = 0;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/listarPromociones',
                                    data: {id: iId, fecha_desde: $this.sFechaDesdePromociones, fecha_hasta: $this.sFechaHastaPromociones},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstPromociones = data.lstPromociones;
                                        }
                                    },
                                    complete: function () {
                                        $this.iCargandoPromociones = 0;
                                    }
                                });
                            },
                            ajaxInsertarPromocion: function () {
                                let $this = this;
                                $this.iInsertandoPromocion = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/insertarPromocion',
                                    data: {
                                        id: iId, tipo_de_promocion: $this.sTipoPromocion,
                                        fecha_de_inicio: $this.sFechaInicioP, fecha_de_vencimiento: $this.sFechaVencimientoP,
                                        nueva_promocion: $this.sNuevaPromocion,
                                        descripcion: $this.sDescripcionPromocion,
                                        min: $this.sMinPromocion,
                                        max: $this.sMaxPromocion,
                                    },
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.sTipoPromocion = '';
                                            $this.sFechaInicioP = '';
                                            $this.sFechaVencimientoP = '';
                                            $this.sNuevaPromocion = '';
                                            $this.sDescripcionPromocion = ''; 
                                            $this.sMinPromocion = '';
                                            $this.sMaxPromocion = '';

                                            $this.ajaxListarUltimasPromociones(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iInsertandoPromocion = 0;
                                    }
                                });
                            },
                            ajaxEliminarPromocion: function (iPromocionId) {
                                let $this = this;
                                $this.iEliminandoPromocion = 1;

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/precios-ofertas/ajax/eliminarPromocion',
                                    data: {id: iPromocionId},
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            $this.ajaxListarUltimasPromociones(function () {
                                                $this.ajaxListarProducto();
                                            });
                                        }

                                        toastr[respuesta.result](respuesta.mensaje);
                                    },
                                    error: function (respuesta) {
                                        let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                        toastr[result.error](sHtmlMensaje);
                                    },
                                    complete: function () {
                                        $this.iEliminandoPromocion = 0;
                                    }
                                });
                            },

                            ajaxCancelar: function () {
                                vuePreciosOfertas.panelListar(function () {
                                    vuePreciosOfertas.iIdSeleccionado = 0;
                                    window.history.replaceState(null, 'PRECIOS Y OFERTAS', '/intranet/app/gestion-productos/precios-ofertas');
                                });
                            }
                        }
                    });

                    $this.iIdSeleccionado = producto.id;
                    window.history.replaceState(null, 'PRODUCTOS', `/intranet/app/gestion-productos/precios-ofertas/${producto.id}/editar`);
                });
            }
        }
    });
});
