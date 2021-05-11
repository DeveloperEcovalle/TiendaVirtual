$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueProveedores = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,

                lstProveedores: [],
                iIdSeleccionado: 0,
                iError: 0,

                sBuscar: '',
            },
            computed: {
                lstProveedoresFiltrados: function () {
                    return this.lstProveedores.filter(proveedor =>
                        proveedor.persona.nombres.toLowerCase().includes(this.sBuscar)
                        || proveedor.persona.documentos.findIndex(documento => documento.numero.includes(this.sBuscar)) > -1
                    );
                }
            },
            mounted: function () {
                this.ajaxListar(this.cargarPanel);
            },
            methods: {
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/personas/proveedores/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.lstProveedores = data.lstProveedores;

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
                        case 'proveedores': {
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
                    $('#panel').load('/intranet/app/personas/proveedores/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/personas/proveedores/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                sTipoDocumentoCodigo: '',
                                lstTiposDocumento: [],
                                sNumeroDocumento: '',
                                iConsultandoDocumento: 0,
                                lstDocumentos: [],
                                sNombres: '',
                                sApellidoPaterno: '',
                                sApellidoMaterno: '',
                                sHabido: null,
                                lstUbigeo: [],
                                sDepartamentoSeleccionado: '',
                                sProvinciaSeleccionada: '',
                                sDireccion: '',
                                iInsertando: 0
                            },
                            computed: {
                                tipoDocumentoSeleccionado: function () {
                                    let iIndice = this.lstTiposDocumento.findIndex((tipoDocumento) => tipoDocumento.codigo === this.sTipoDocumentoCodigo);
                                    if (iIndice === -1) {
                                        return {};
                                    }

                                    return this.lstTiposDocumento[iIndice];
                                },
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
                                    let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamentoSeleccionado);
                                    let lst = [];
                                    for (let ubigeo of lstUbigeoFiltrado) {
                                        if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                                            lst.push(ubigeo.provincia);
                                        }
                                    }
                                    return lst;
                                },
                                lstDistritos: function () {
                                    return this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamentoSeleccionado && ubigeo.provincia === this.sProvinciaSeleccionada);
                                },
                                sLstDocumentos: function () {
                                    let sLst = '';
                                    for (let documento of this.lstDocumentos) {
                                        sLst += documento.sunat_06_codigo + ';' + documento.numero + '|';
                                    }
                                    return sLst.substring(0, sLst.length - 1);
                                }
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/personas/proveedores/ajax/nuevo/listarData',
                                    success: function (respuesta) {
                                        let data = respuesta.data;
                                        $this.lstTiposDocumento = data.lstTiposDocumento;
                                        $this.lstUbigeo = data.lstUbigeo;
                                        $this.sTipoDocumentoCodigo = data.lstTiposDocumento[0].codigo;
                                    }
                                });
                            },
                            methods: {
                                agregarDocumento: function () {
                                    let tipoDocumento = JSON.parse(JSON.stringify(this.tipoDocumentoSeleccionado));
                                    let regexp = new RegExp(tipoDocumento.validacion);

                                    if (!regexp.test(this.sNumeroDocumento)) {
                                        toastr[result.warning](`${this.sNumeroDocumento} no coincide con el formato v&aacute;lido del tipo de documento seleccionado.`);
                                        return;
                                    }

                                    if (this.sNumeroDocumento.length !== tipoDocumento.longitud) {
                                        toastr[result.warning](`${this.sNumeroDocumento} debe contener ${tipoDocumento.longitud} caracteres.`);
                                        return;
                                    }

                                    let iIndice = this.lstDocumentos.findIndex((documento) => documento.sunat_06_codigo === this.sTipoDocumentoCodigo);
                                    if (iIndice > -1) {
                                        toastr[result.warning](`Un documento del tipo ${tipoDocumento.abreviatura} ya se encuentra en la lista.`);
                                        return;
                                    }

                                    let nuevoDocumento = {
                                        sunat_06_codigo: this.sTipoDocumentoCodigo,
                                        tipo_documento: tipoDocumento,
                                        numero: this.sNumeroDocumento
                                    };

                                    if (this.lstDocumentos.length === 0 && this.sTipoDocumentoCodigo === '6') {
                                        let $this = this;
                                        $this.iConsultandoDocumento = 1;

                                        if (this.sTipoDocumentoCodigo === '6') {
                                            $.ajax({
                                                type: 'post',
                                                url: '/intranet/app/personas/proveedores/ajax/nuevo/consultarRuc',
                                                data: {'numero_de_documento': this.sNumeroDocumento},
                                                success: function (respuesta) {
                                                    if (respuesta.result === 'success') {
                                                        let data = respuesta.data;
                                                        if (data && data.ruc.trim() !== '') {
                                                            if ($this.sNombres === '') {
                                                                $this.sNombres = data.nombre_o_razon_social;
                                                            }

                                                            if ($this.sDireccion === '') {
                                                                $this.sDireccion = data.direccion;
                                                            }

                                                            $this.sHabido = `${data.estado} - ${data.condicion}`;
                                                        }
                                                    }
                                                },
                                                complete: function () {
                                                    $this.iConsultandoDocumento = 0;
                                                }
                                            });
                                        }
                                    }

                                    this.lstDocumentos.push(nuevoDocumento);
                                    this.sTipoDocumentoCodigo = this.lstTiposDocumento[0].codigo;
                                    this.sNumeroDocumento = '';
                                },
                                eliminarDocumento: function (iIndice) {
                                    this.lstDocumentos.splice(iIndice, 1);
                                },
                                restablecerHabido: function () {
                                    if (this.sNombres.length === 0) {
                                        this.sHabido = null;
                                    }
                                },
                                ajaxInsertar: function () {
                                    let $this = this;

                                    if ($this.lstDocumentos.length === 0) {
                                        toastr[result.warning]('Debe agregar por lo menos un documento a la lista.');
                                        return;
                                    }

                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);
                                    formData.append('lista_documentos', $this.sLstDocumentos);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/personas/proveedores/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                $this.lstDocumentos = [];
                                                $this.sTipoDocumentoCodigo = $this.lstTiposDocumento[0].codigo;
                                                $this.sNombres = '';
                                                $this.sApellidoPaterno = '';
                                                $this.sApellidoMaterno = '';
                                                $this.sProvinciaSeleccionada = '';
                                                $this.sDepartamentoSeleccionado = '';
                                                $this.sDireccion = '';

                                                frmNuevo.reset();
                                                vueProveedores.ajaxListar();
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        },
                                        complete: function () {
                                            $this.iInsertando = 0;
                                        }
                                    });
                                },
                                ajaxCancelar: function () {
                                    vueProveedores.panelListar(function () {
                                        vueProveedores.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PROVEEDORES', '/intranet/app/personas/proveedores');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'PROVEEDORES', '/intranet/app/personas/proveedores/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstProveedores.findIndex((proveedor) => proveedor.id === parseInt(iId));
                    let proveedor = Object.assign({}, $this.lstProveedores[iIndice]);
                    let sDepartamentoSeleccionado = proveedor.ubigeo ? proveedor.ubigeo.departamento : '';
                    let sProvinciaSeleccionada = proveedor.ubigeo ? proveedor.ubigeo.provincia : '';
                    let sDistritoSeleccionado = proveedor.ubigeo ? proveedor.ubigeo.id : '';

                    $('#panel').load('/intranet/app/personas/proveedores/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                proveedor: proveedor,
                                sHabido: '',
                                sTipoDocumentoCodigo: '',
                                lstTiposDocumento: [],
                                lstUbigeo: [],
                                sDepartamentoSeleccionado: sDepartamentoSeleccionado,
                                sProvinciaSeleccionada: sProvinciaSeleccionada,
                                sDistritoSeleccionado: sDistritoSeleccionado,
                                sNumeroDocumento: '',
                                iConsultandoDocumento: 0,
                                iActualizando: 0,
                                iEliminando: 0
                            },
                            computed: {
                                tipoDocumentoSeleccionado: function () {
                                    let iIndice = this.lstTiposDocumento.findIndex((tipoDocumento) => tipoDocumento.codigo === this.sTipoDocumentoCodigo);
                                    if (iIndice === -1) {
                                        return {};
                                    }

                                    return this.lstTiposDocumento[iIndice];
                                },
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
                                    let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamentoSeleccionado);
                                    let lst = [];
                                    for (let ubigeo of lstUbigeoFiltrado) {
                                        if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                                            lst.push(ubigeo.provincia);
                                        }
                                    }
                                    return lst;
                                },
                                lstDistritos: function () {
                                    return this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamentoSeleccionado && ubigeo.provincia === this.sProvinciaSeleccionada);
                                },
                                sLstDocumentos: function () {
                                    let sLst = '';
                                    for (let documento of this.proveedor.persona.documentos) {
                                        sLst += documento.sunat_06_codigo + ';' + documento.numero + '|';
                                    }
                                    return sLst.substring(0, sLst.length - 1);
                                }
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/personas/proveedores/ajax/editar/listarData',
                                    success: function (respuesta) {
                                        let data = respuesta.data;
                                        $this.lstTiposDocumento = data.lstTiposDocumento;
                                        $this.lstUbigeo = data.lstUbigeo;
                                        $this.sTipoDocumentoCodigo = data.lstTiposDocumento[0].codigo;
                                    }
                                });
                            },
                            methods: {
                                agregarDocumento: function () {
                                    let tipoDocumento = JSON.parse(JSON.stringify(this.tipoDocumentoSeleccionado));
                                    let regexp = new RegExp(tipoDocumento.validacion);

                                    if (!regexp.test(this.sNumeroDocumento)) {
                                        toastr[result.warning](`${this.sNumeroDocumento} no coincide con el formato v&aacute;lido del tipo de documento seleccionado.`);
                                        return;
                                    }

                                    if (this.sNumeroDocumento.length !== tipoDocumento.longitud) {
                                        toastr[result.warning](`${this.sNumeroDocumento} debe contener ${tipoDocumento.longitud} caracteres.`);
                                        return;
                                    }

                                    let iIndice = this.proveedor.persona.documentos.findIndex((documento) => documento.sunat_06_codigo === this.sTipoDocumentoCodigo);
                                    if (iIndice > -1) {
                                        toastr[result.warning](`Un documento del tipo ${tipoDocumento.abreviatura} ya se encuentra en la lista.`);
                                        return;
                                    }

                                    let nuevoDocumento = {
                                        sunat_06_codigo: this.sTipoDocumentoCodigo,
                                        tipo_documento: tipoDocumento,
                                        numero: this.sNumeroDocumento
                                    };

                                    if (this.proveedor.persona.documentos.length === 0 && (this.sTipoDocumentoCodigo === '1' || this.sTipoDocumentoCodigo === '6')) {
                                        let $this = this;
                                        $this.iConsultandoDocumento = 1;

                                        if (this.sTipoDocumentoCodigo === '1') {
                                            $.ajax({
                                                type: 'post',
                                                url: '/intranet/app/personas/proveedores/ajax/editar/consultarDni',
                                                data: {'numero_de_documento': this.sNumeroDocumento},
                                                success: function (respuesta) {
                                                    if (respuesta.result === result.success) {
                                                        if (respuesta.data) {
                                                            let data = respuesta.data;

                                                            if ($this.proveedor.persona.nombres === '') {
                                                                $this.proveedor.persona.nombres = data.nombres;
                                                            }

                                                            if ($this.proveedor.persona.apellido_1 === '') {
                                                                $this.proveedor.persona.apellido_1 = data.apellido_paterno;
                                                            }

                                                            if ($this.proveedor.persona.apellido_2 === '') {
                                                                $this.proveedor.persona.apellido_2 = data.apellido_materno;
                                                            }
                                                        }

                                                        $this.sHabido = null;
                                                    }
                                                },
                                                complete: function () {
                                                    $this.iConsultandoDocumento = 0;
                                                }
                                            });
                                        }

                                        if (this.sTipoDocumentoCodigo === '6') {
                                            $.ajax({
                                                type: 'post',
                                                url: '/intranet/app/personas/proveedores/ajax/editar/consultarRuc',
                                                data: {'numero_de_documento': this.sNumeroDocumento},
                                                success: function (respuesta) {
                                                    if (respuesta.result === 'success') {
                                                        let data = respuesta.data;
                                                        if (data && data.ruc.trim() !== '') {
                                                            if ($this.proveedor.persona.nombres === '') {
                                                                $this.proveedor.persona.nombres = data.nombre_o_razon_social;
                                                            }

                                                            if ($this.proveedor.direccion === '') {
                                                                $this.proveedor.direccion = data.direccion;
                                                            }

                                                            $this.sHabido = `${data.estado} - ${data.condicion}`;
                                                        }
                                                    }
                                                },
                                                complete: function () {
                                                    $this.iConsultandoDocumento = 0;
                                                }
                                            });
                                        }
                                    }

                                    this.proveedor.persona.documentos.push(nuevoDocumento);
                                    this.sTipoDocumentoCodigo = this.lstTiposDocumento[0].codigo;
                                    this.sNumeroDocumento = '';
                                },
                                eliminarDocumento: function (iIndice) {
                                    this.proveedor.persona.documentos.splice(iIndice, 1);
                                },
                                restablecerHabido: function () {
                                    if (this.proveedor.persona.nombres.length === 0) {
                                        this.sHabido = null;
                                    }
                                },
                                ajaxActualizar: function () {
                                    let $this = this;
                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);
                                    formData.append('lista_documentos', $this.sLstDocumentos);

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/personas/proveedores/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                vueProveedores.ajaxListar();
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
                                ajaxEliminar: function (iId) {
                                    let $this = this;
                                    $this.iEliminando = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/personas/proveedores/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                vueProveedores.ajaxListar(function () {
                                                    vueProveedores.panelListar(function () {
                                                        vueProveedores.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'PROVEEDORES', '/intranet/app/personas/proveedores');
                                                    });
                                                });
                                            }

                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        },
                                        complete: function () {
                                            $this.iEliminando = 0;
                                        }
                                    });
                                },
                                ajaxCancelar: function () {
                                    vueProveedores.panelListar(function () {
                                        vueProveedores.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PROVEEDORES', '/intranet/app/personas/proveedores');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = proveedor.id;
                        window.history.replaceState(null, 'PROVEEDORES', `/intranet/app/personas/proveedores/${proveedor.id}/editar`);
                    });
                }
            }
        });
    });
});
