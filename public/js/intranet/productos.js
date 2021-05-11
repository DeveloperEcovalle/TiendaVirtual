$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let summernoteConfig = {
            //airMode: true,
            fontNames: ['Arial'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                //['insert', ['link', 'picture']],
            ],
            minHeight: 200,
            height: 200
        };

        let vueProductos = new Vue({
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
                    return this.lstProductos.filter(producto =>
                        producto.nombre_es.toLowerCase().includes(this.sBuscar.toLowerCase())
                        || producto.descripcion_es.toLowerCase().includes(this.sBuscar.toLowerCase())
                        || producto.categorias.findIndex(categoria => categoria.nombre_es.toLowerCase().includes(this.sBuscar.toLowerCase())) > -1
                        || producto.lineas.findIndex(linea => linea.nombre_espanol.toLowerCase().includes(this.sBuscar.toLowerCase())) > -1
                    );
                },
            },
            mounted: function () {
                this.ajaxListar(this.cargarPanel);
            },
            methods: {
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/gestion-productos/productos/ajax/listar',
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
                        case 'productos': {
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
                    $('#panel').load('/intranet/app/gestion-productos/productos/ajax/panelListar', function () {
                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                },
                panelNuevo: function () {
                    let $this = this;
                    $('#panel').load('/intranet/app/gestion-productos/productos/ajax/panelNuevo', function () {
                        let vueNuevo = new Vue({
                            el: '#panel',
                            data: {
                                lstLineas: [],
                                lstCategorias: [],
                                iInsertando: 0,
                                lstPdfs: []
                            },
                            mounted: function () {
                                let $this = this;
                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/productos/ajax/nuevo/listarData',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;
                                            $this.lstLineas = data.lstLineas;
                                            $this.lstCategorias = data.lstCategorias;
                                        }
                                    },
                                });

                                $('#sBeneficiosEspanol').summernote(summernoteConfig);
                                $('#sBeneficiosIngles').summernote(summernoteConfig);
                                $('#sDescripcionEspanol').summernote(summernoteConfig);
                                $('#sDescripcionIngles').summernote(summernoteConfig);
                                $('#sModoUsoEspanol').summernote(summernoteConfig);
                                $('#sModoUsoIngles').summernote(summernoteConfig);

                                $('.note-editor').addClass('b-r-sm border');
                            },
                            methods: {
                                agregarDocumento: function () {
                                    this.lstPdfs.push({name: 'Buscar archivo'});
                                },
                                eliminarDocumento: function (i) {
                                    this.lstPdfs.splice(i, 1);
                                },
                                changeDocumento: function (event, i) {
                                    let input = event.target;
                                    this.lstPdfs[i] = input.files[0];

                                    let lstPdfs = [...this.lstPdfs];
                                    this.lstPdfs = [];
                                    this.lstPdfs = lstPdfs;
                                },
                                ajaxInsertar: function () {
                                    let $this = this;

                                    if ($('#sBeneficiosEspanol').summernote('isEmpty')) {
                                        toastr[result.error]('Beneficios en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sBeneficiosIngles').summernote('isEmpty')) {
                                        toastr[result.error]('Beneficios en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sDescripcionEspanol').summernote('isEmpty')) {
                                        toastr[result.error]('Descripción en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sDescripcionIngles').summernote('isEmpty')) {
                                        toastr[result.error]('Descripción en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sModoUsoEspanol').summernote('isEmpty')) {
                                        toastr[result.error]('Modo de uso en espa&ntilde;ol es un campo requerido.');
                                        return;
                                    }

                                    if ($('#sModoUsoIngles').summernote('isEmpty')) {
                                        toastr[result.error]('Modo de uso en ingl&eacute;s es un campo requerido.');
                                        return;
                                    }

                                    $this.iInsertando = 1;

                                    let frmNuevo = document.getElementById('frmNuevo');
                                    let formData = new FormData(frmNuevo);
                                    formData.append('beneficios_en_espanol', $('#sBeneficiosEspanol').summernote('code'));
                                    formData.append('beneficios_en_ingles', $('#sBeneficiosIngles').summernote('code'));
                                    formData.append('descripcion_en_espanol', $('#sDescripcionEspanol').summernote('code'));
                                    formData.append('descripcion_en_ingles', $('#sDescripcionIngles').summernote('code'));
                                    formData.append('modo_de_uso_en_espanol', $('#sModoUsoEspanol').summernote('code'));
                                    formData.append('modo_de_uso_en_ingles', $('#sModoUsoIngles').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/insertar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                $this.imagen = null;
                                                frmNuevo.reset();
                                                vueProductos.ajaxListar();
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
                                    vueProductos.panelListar(function () {
                                        vueProductos.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PRODUCTOS', '/intranet/app/gestion-productos/productos');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = 0;
                        window.history.replaceState(null, 'PRODUCTOS', '/intranet/app/gestion-productos/productos/nuevo');
                    });
                },
                panelEditar: function (iId) {
                    let $this = this;

                    let iIndice = $this.lstProductos.findIndex((producto) => producto.id === parseInt(iId));
                    let producto = Object.assign({}, $this.lstProductos[iIndice]);

                    $('#panel').load('/intranet/app/gestion-productos/productos/ajax/panelEditar', function () {
                        let vueEditar = new Vue({
                            el: '#panel',
                            data: {
                                imagen: null,
                                lstCategorias: [],
                                lstCategoriasSeleccionadas: [],

                                lstLineas: [],
                                lstLineasSeleccionadas: [],

                                producto: producto,
                                lstImagenes: [],
                                iCargandoImagenes: 0,

                                iActualizando: 0,
                                iEliminando: 0,
                                lstPdfs: [],
                                iEliminandoDocumento: 0,

                                iInsertandoSubproducto: 0,
                                nuevoSubproducto: null,
                                sNuevoSubproducto: ''
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
                            mounted: function () {
                                let $this = this;

                                $('#sBeneficiosEspanol').summernote(summernoteConfig);
                                $('#sBeneficiosIngles').summernote(summernoteConfig);
                                $('#sDescripcionEspanol').summernote(summernoteConfig);
                                $('#sDescripcionIngles').summernote(summernoteConfig);
                                $('#sModoUsoEspanol').summernote(summernoteConfig);
                                $('#sModoUsoIngles').summernote(summernoteConfig);

                                $('.note-editor').addClass('b-r-sm border');

                                $('#sBeneficiosEspanol').summernote('code', this.producto.beneficios_es);
                                $('#sBeneficiosIngles').summernote('code', this.producto.beneficios_en);
                                $('#sDescripcionEspanol').summernote('code', this.producto.descripcion_es);
                                $('#sDescripcionIngles').summernote('code', this.producto.descripcion_en);
                                $('#sModoUsoEspanol').summernote('code', this.producto.modo_uso_es);
                                $('#sModoUsoIngles').summernote('code', this.producto.modo_uso_en);

                                $.ajax({
                                    type: 'post',
                                    url: '/intranet/app/gestion-productos/productos/ajax/editar/listarData',
                                    success: function (respuesta) {
                                        if (respuesta.result === result.success) {
                                            let data = respuesta.data;

                                            $this.lstCategorias = data.lstCategorias;
                                            for (let categoria of $this.producto.categorias) {
                                                $this.lstCategoriasSeleccionadas.push(categoria.id);
                                            }

                                            $this.lstLineas = data.lstLineas;
                                            for (let linea of $this.producto.lineas) {
                                                $this.lstLineasSeleccionadas.push(linea.id);
                                            }
                                        }
                                    },
                                });

                                let myDropzone = new Dropzone('.dropzone', {
                                    url: '/intranet/app/gestion-productos/productos/ajax/insertarImagen',
                                    paramName: 'imagen',
                                    acceptedFiles: 'image/*',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                });

                                myDropzone.on('success', function () {
                                    myDropzone.removeAllFiles();
                                    $this.ajaxListarImagenes();
                                });

                                $this.ajaxListarImagenes();
                            },
                            methods: {
                                onSelectAutocompleteProducto: function (e, ui) {
                                    this.nuevoSubproducto = JSON.parse(JSON.stringify(ui.item.entidad));
                                    this.sNuevoSubproducto = this.nuevoSubproducto.nombre_es;
                                    e.preventDefault();
                                },
                                onChangeAutocompleteProducto: function (e, ui) {
                                    if (ui.item === null) {
                                        this.nuevoSubproducto = null;
                                    }
                                    e.preventDefault();
                                },
                                ajaxAgregarSubproducto: function () {
                                    let $this = this;

                                    if ($this.nuevoSubproducto === null) {
                                        toastr[result.warning]('Seleccione un producto de la lista de resultados de búsqueda.');
                                        return;
                                    }

                                    $this.iInsertandoSubproducto = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/editar/insertarSubproducto',
                                        data: {iProductoId: $this.producto.id, iSubproductoId: $this.nuevoSubproducto.id},
                                        dataType: 'json',
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                let subproducto = JSON.parse(JSON.stringify($this.nuevoSubproducto));
                                                $this.producto.subproductos.push(subproducto);
                                                $this.nuevoSubproducto = null;
                                                $this.sNuevoSubproducto = '';
                                            }
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        },
                                        complete: function () {
                                            $this.iInsertandoSubproducto = 0;
                                        }
                                    });
                                },
                                cambiarImagen: function (event) {
                                    let input = event.target;
                                    this.imagen = input.files[0];
                                },
                                agregarDocumento: function () {
                                    this.lstPdfs.push({name: 'Buscar archivo'});
                                },
                                eliminarDocumento: function (i) {
                                    this.lstPdfs.splice(i, 1);
                                },
                                changeDocumento: function (event, i) {
                                    let input = event.target;
                                    this.lstPdfs[i] = input.files[0];

                                    let lstPdfs = [...this.lstPdfs];
                                    this.lstPdfs = [];
                                    this.lstPdfs = lstPdfs;
                                },
                                ajaxListarImagenes: function () {
                                    let $this = this;
                                    $this.iCargandoImagenes = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/listarImagenes',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                $this.lstImagenes = respuesta.data.lstImagenes;
                                            } else {
                                                toastr[respuesta.result](respuesta.mensaje);
                                            }
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        },
                                        complete: function () {
                                            $this.iCargandoImagenes = 0;
                                        }
                                    });
                                },
                                ajaxEliminarImagen: function (id, i) {
                                    let $this = this;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/eliminarImagen',
                                        data: {id: id},
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                $this.lstImagenes.splice(i, 1);
                                            }
                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        }
                                    });
                                },
                                ajaxEliminarDocumento: function (id, i) {
                                    let $this = this;
                                    $this.iEliminandoDocumento = 1;

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/eliminarDocumento',
                                        data: {id: id},
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                producto.documentos.splice(i, 1);
                                            }
                                            toastr[respuesta.result](respuesta.mensaje);
                                        },
                                        error: function (respuesta) {
                                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                                            toastr[result.error](sHtmlMensaje);
                                        },
                                        complete: function () {
                                            $this.iEliminandoDocumento = 0;
                                        }
                                    });
                                },
                                ajaxActualizar: function () {
                                    let $this = this;
                                    $this.iActualizando = 1;

                                    let frmEditar = document.getElementById('frmEditar');
                                    let formData = new FormData(frmEditar);
                                    formData.append('id', iId);
                                    formData.append('beneficios_en_espanol', $('#sBeneficiosEspanol').summernote('code'));
                                    formData.append('beneficios_en_ingles', $('#sBeneficiosIngles').summernote('code'));
                                    formData.append('descripcion_en_espanol', $('#sDescripcionEspanol').summernote('code'));
                                    formData.append('descripcion_en_ingles', $('#sDescripcionIngles').summernote('code'));
                                    formData.append('modo_de_uso_en_espanol', $('#sModoUsoEspanol').summernote('code'));
                                    formData.append('modo_de_uso_en_ingles', $('#sModoUsoIngles').summernote('code'));

                                    $.ajax({
                                        type: 'post',
                                        url: '/intranet/app/gestion-productos/productos/ajax/actualizar',
                                        data: formData,
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                vueProductos.ajaxListar(function () {
                                                    vueProductos.panelEditar(iId);
                                                });
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
                                        url: '/intranet/app/gestion-productos/productos/ajax/eliminar',
                                        data: {id: iId},
                                        success: function (respuesta) {
                                            if (respuesta.result === result.success) {
                                                vueProductos.ajaxListar(function () {
                                                    vueProductos.panelListar(function () {
                                                        vueProductos.iIdSeleccionado = 0;
                                                        window.history.replaceState(null, 'PRODUCTOS', '/intranet/app/gestion-productos/productos');
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
                                    vueProductos.panelListar(function () {
                                        vueProductos.iIdSeleccionado = 0;
                                        window.history.replaceState(null, 'PRODUCTOS', '/intranet/app/gestion-productos/productos');
                                    });
                                }
                            }
                        });

                        $this.iIdSeleccionado = producto.id;
                        window.history.replaceState(null, 'PRODUCTOS', `/intranet/app/gestion-productos/productos/${producto.id}/editar`);
                    });
                }
            }
        });
    });
});
