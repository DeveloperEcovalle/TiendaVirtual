let iLinea = lstUrlParams.get('linea');
let iLineaProductoId = iLinea ? parseInt(iLinea) : 0;

let vueLineasProductos = new Vue({
    el: '#content',
    data: {
        locale: 'es',

        iCargandoLineasProductos: 1,
        lstLineasProductos: [],
        iLineaProductoId: iLineaProductoId,

        iCargandoProductosRelacionados: 0,
        lstProductos: [],

        iCargando: 1,
        pagina: {
            ruta_imagen_portada: '',
        },

        iAgregandoAlCarrito: 0,
        iProductoId: 0,
        lstCarritoCompras: [],
    },
    computed: {
        lstCarouselProductos: function () {
            return chunk(this.lstProductos, 4);
        },
        lstLineasProductosConImagen: function () {
            return this.lstLineasProductos.filter(linea => linea.ruta_imagen !== null && linea.ruta_imagen.length > 0);
        },
        lstCarouselLineasProductosConImagen: function () {
            let lstLineasSubarray = this.lstLineasProductosConImagen.slice(0, this.lstLineasProductosConImagen.length);
            return chunk(lstLineasSubarray, 3);
        },
        lineaSeleccionada: function () {
            if (this.iLineaProductoId == 0) {
                return {contenido_espanol: '', contenido_ingles: ''};
            }

            let lstLineaSeleccionada = this.lstLineasProductos.filter(linea => linea.id == this.iLineaProductoId);
            return lstLineaSeleccionada[0];
        }
    },
    mounted: function () {
        let $this = this;

        ajaxWebsiteLocale()
            .then(response => {
                let respuesta = response.data;
                $this.locale = respuesta.data.locale;
            })
            .then(() => {
                ajaxWebsiteListarCarritoCompras().then(response => {
                    let respuesta = response.data;
                    let data = respuesta.data;

                    let lstCarritoComprasServer = data.lstCarrito;
                    //let bClienteEnSesion = data.bClienteEnSesion;

                    let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
                    let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                    $this.lstCarritoCompras = lstCarritoCompras;
                    $this.guardarLstCarritoCompras();

                    $this.ajaxListarLineasProductos();
                    $this.ajaxListar();

                    if ($this.iLineaProductoId != 0) {
                        $this.ajaxListarProductosRelacionados().then(() => {
                            $this.actualizarCantidadesProductos();
                            $this.actualizarLstProductos();
                        });
                    }
                });
            });

        /*ajaxWebsiteLocale(function (respuesta) {
            $this.locale = respuesta.data.locale;

            ajaxWebsiteListarCarritoCompras(function (respuestaCarritoCompras) {
                let data = respuestaCarritoCompras.data;

                let lstCarritoComprasServer = data.lstCarrito;
                //let bClienteEnSesion = data.bClienteEnSesion;

                let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
                let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                $this.lstCarritoCompras = lstCarritoCompras;
                $this.guardarLstCarritoCompras();

                $this.ajaxListarLineasProductos();
                $this.ajaxListar();

                if ($this.iLineaProductoId != 0) {
                    $this.ajaxListarProductosRelacionados($this.actualizarCantidadesProductos);
                }
            });
        });*/
    },
    watch: {
        iLineaProductoId: function (iNewLineaProductoId) {
            let sUrl = '/nosotros/lineas-productos?linea=' + iNewLineaProductoId;
            if (iNewLineaProductoId == 0) {
                this.lstProductos = [];
            } else {
                let $this = this;
                this.ajaxListarProductosRelacionados().then(() => {
                    $this.actualizarCantidadesProductos();
                    $this.actualizarLstProductos();
                });
            }
            window.history.replaceState({}, 'Ecovalle | Nosotros | Líneas de Productos', sUrl);
        }
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListarLineasProductos: function () {
            let $this = this;
            axios.post('/nosotros/lineas-productos/ajax/listarLineasProductos')
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.lstLineasProductos = respuesta.data.lstLineasProductos;
                    }
                })
                .then(() => $this.iCargandoLineasProductos = 0);
        },
        setiIdLineaProductoId: function (iLineaProductoId) {
            $('.i-checks').iCheck('update');
            this.iLineaProductoId = iLineaProductoId;
        },
        ajaxListar: function () {
            let $this = this;
            axios.post('/nosotros/lineas-productos/ajax/listar')
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.pagina = respuesta.data.pagina;
                    }
                })
                .then(() => $this.iCargando = 0);
        },
        ajaxListarProductosRelacionados: function () {
            this.iCargandoProductosRelacionados = 1;

            let formData = new FormData();
            formData.append('iLineaProductoId', this.iLineaProductoId);

            let $this = this;
            return axios.post('/nosotros/lineas-productos/ajax/listarProductosRelacionados', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.lstProductos = respuesta.data.lstProductos;
                    }
                })
                .then(() =>  $this.iCargandoProductosRelacionados = 0);
        },

        ajaxAgregarAlCarrito: function (producto) {
            let $this = this;
            $this.iAgregandoAlCarrito = 1;
            $this.iProductoId = producto.id;

            ajaxWebsiteAgregarAlCarrito(producto, this.actualizarLstProductos, this.lstCarritoCompras, this.guardarLstCarritoCompras)
                .then(() => {
                    $this.iAgregandoAlCarrito = 0;
                    $this.iProductoId = 0;
                }).then(() => {

                    $('#producto-modal').load('/tienda/producto/ajax/cargarPanel', function () {
                        let vuePanel = new Vue({
                            el: '#producto-modal',
                            data: {
                                lstCarrito: vueLineasProductos.lstCarritoCompras,
                            },
                            computed: {
                                fSubtotal: function () {                                 
                                    let fSubtotal = 0;
                                    for (let detalle of this.lstCarrito) {
                                        let producto = detalle.producto;
                                        let fPromocion = producto.promocion_vigente === null ? 0.00 :
                                            (producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max ? (producto.promocion_vigente.porcentaje ? ((producto.precio_actual.monto * producto.promocion_vigente.porcentaje) / 100) : (producto.promocion_vigente.monto)) : 0.00);
                                        let fPrecio = (producto.oferta_vigente === null ? producto.precio_actual.monto :
                                            (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto))) - fPromocion;
                                        fSubtotal += detalle.cantidad * fPrecio;
                                    }
                                    return Math.round(fSubtotal * 10) / 10;
                                },
                                fDescuento: function () {
                                    let fDescuento = 0;
                                    for (let detalle of vueLineasProductos.lstCarritoCompras) {
                                        let producto = detalle.producto;
                                        let fOferta = producto.oferta_vigente === null ? 0.00 : producto.oferta_vigente.porcentaje ? ((producto.precio_actual.monto * producto.oferta_vigente.porcentaje) / 100) : producto.oferta_vigente.monto ;
                        
                                        let fPromocion = producto.promocion_vigente === null ? 0.00 :
                                            (producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max ? (producto.promocion_vigente.porcentaje ? ((producto.precio_actual.monto * producto.promocion_vigente.porcentaje) / 100) : (producto.promocion_vigente.monto)) : 0.00);
                        
                                        let promocion = detalle.cantidad * fPromocion;
                                        let oferta = detalle.cantidad * fOferta;
                                        let total = promocion + oferta;
                                        fDescuento += total;
                                    }
                                    return Math.round(fDescuento * 10) / 10;
                                },
                            },
                            methods: {
                                removeModal: function(){
                                    modalProducto = document.getElementById('contenedor-producto');	
                                    if (!modalProducto){
                                        alert("El elemento selecionado no existe");
                                    } else {
                                        padre = modalProducto.parentNode;
                                        padre.removeChild(modalProducto);
                                    }
                                },
                                ajaxDisminuirCantidadProductoCarritoModal: function (producto) {
                                    vueLineasProductos.ajaxDisminuirCantidadProductoCarrito(producto);
                                    vueLineasProductos.actualizarLstProductos();
                                },
                                ajaxAumentarCantidadProductoCarritoModal: function (producto) {
                                    vueLineasProductos.ajaxAumentarCantidadProductoCarrito(producto);
                                    vueLineasProductos.actualizarLstProductos();
                                },
                            }
                        });
                    });
                });
        },
        ajaxDisminuirCantidadProductoCarrito: function (producto) {
            let iProductoId = producto.id;
            let $this = this;
            ajaxWebsiteDisminuirCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad - 1;

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad - 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.actualizarCantidadesProductos();
                        $this.actualizarLstProductos();

                        if (detalle.cantidad === 0) {
                            $this.lstCarritoCompras.splice(iIndiceDetalleCarrito, 1);
                        }

                        $this.guardarLstCarritoCompras();
                    }
                });
        },
        ajaxAumentarCantidadProductoCarrito: function (producto) {
            let iProductoId = producto.id;
            let $this = this;
            if(producto.cantidad + 1  === producto.stock_actual)
            {
                toastr.clear();
                toastr.options = {
                    iconClasses: {
                        error: 'bg-danger',
                        info: 'bg-info',
                        success: 'bg-success',
                        warning: 'bg-warning',
                    },
                };
                toastr.info(producto.stock_actual +' en stock.');

                var cantidad = producto.stock_actual;
                ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cantidad)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            producto.cantidad = cantidad;
                            // this.cantidad = this.cantidad + 1;
                            $this.actualizarLstProductos();

                            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                            let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                            detalle.cantidad = cantidad;
                            detalle.producto.cantidad = cantidad;

                            $this.actualizarCantidadesProductos();
                            $this.guardarLstCarritoCompras();
                        }
                    });
            }
            else if(producto.cantidad + 1 < producto.stock_actual)
            {
                if(producto.cantidad + 1 == 12)
                {
                    toastr.clear();
                    toastr.options = {
                        'closeButton': false, 'debug': false, 'newestOnTop': false,
                        'progressBar': false, 'positionClass': 'toast-top-right', 'preventDuplicates': true, 'onclick': null,
                        'showDuration': '300', 'hideDuration': '1000', 'timeOut': 0, 'extendedTimeOut': 0,
                        'showEasing': 'swing', 'hideEasing': 'linear', 'showMethod': 'fadeIn', 'hideMethod': 'fadeOut'
                    };
                
                    toastr[result.success](`<p class="text-center font-weight-bold text-ecovalle-2">Si decea al por mayor se le puede brindar a un mejor precio. ¡¡Contáctanos!!</p>
                    <div class="text-center mt-2">
                    <button class="btn btn-sm btn-ecovalle mr-3" onclick="toastr.clear()">Continuar comprando</button>
                    <a class="btn btn-sm btn-amarillo" href="/contactanos">Contactar</a>
                    </div>`);
                }
                ajaxWebsiteAumentarCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad + 1;
                        // this.cantidad = this.cantidad + 1;
                        $this.actualizarLstProductos();

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad + 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.actualizarCantidadesProductos();
                        $this.guardarLstCarritoCompras();                    
                    }
                });

            }else{
                toastr.clear();
                toastr.options = {
                    iconClasses: {
                        error: 'bg-danger',
                        info: 'bg-info',
                        success: 'bg-success',
                        warning: 'bg-warning',
                    },
                };
                toastr.error(producto.stock_actual + ' en stock.');
            }
        },
        actualizarLstProductos: function () {
            this.lstProductos = [...this.lstProductos];
        },
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
        actualizarCantidadesProductos: function () {
            for (let detalle of this.lstCarritoCompras) {
                let iIndiceProducto = this.lstProductos.findIndex(producto => producto.id === detalle.producto_id);
                if (iIndiceProducto > -1) {
                    let iCantidad = iIndiceProducto === -1 ? 0 : detalle.cantidad;
                    this.lstProductos[iIndiceProducto].cantidad = iCantidad;
                }
            }
        },
    },
    updated: function () {
        this.$nextTick(function () {
            $(".carousel").carousel({
                interval: 3000
            });
        });
    },
});
