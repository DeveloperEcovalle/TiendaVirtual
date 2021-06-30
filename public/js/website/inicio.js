let vueInicio = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        iAgregandoAlCarrito: 0,
        iProductoId: 0,

        locale: 'es',
        pagina: {},
        lstBanners: [],
        bannerMedio: [],
        lstCategorias: [],
        lstProductos: [],
        lstBlogs: [],

        lstCarritoCompras: [],

        iSuscribiendo: 0,
        respuestaCorreo: null
    },
    computed: {
        lstCarouselProductos: function () {
            return chunk(this.lstProductos, 4);
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
                    $this.iCargando = 0;

                    $this.ajaxListarData().then(() => {
                        $this.actualizarCantidadesProductos();
                        $this.actualizarLstProductos();
                    });
                });
            });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListarData: function () {
            let $this = this;
            return axios.post('/ajax/data')
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let data = respuesta.data;
                        $this.pagina = data.pagina;
                        $this.lstBanners = data.lstBanners;
                        $this.bannerMedio = data.bannerMedio;
                        $this.lstCategorias = data.lstCategorias;
                        $this.lstProductos = data.lstProductos;
                        $this.lstBlogs = data.lstBlogs;
                    }
                })
                .then(() => $this.iCargando = 0);
        },
        ajaxEnviarCorreoContactoContigo: function () {
            this.respuestaCorreo = null;

            let frmContactoContigo = document.getElementById('frmContactoContigo');
            let formData = new FormData(frmContactoContigo);

            let $this = this;
            $this.iSuscribiendo = 1;
            axios.post('/ajax/enviarCorreoContactoContigo', formData)
                .then(response => {
                    let respuesta = response.data;
                    $this.respuestaCorreo = respuesta;
                    if (respuesta.result === result.success) {
                        frmContactoContigo.reset();
                    }
                })
                .catch(error => {
                    let respuesta = error.response.data;
                    $this.respuestaCorreo = respuesta;
                    $this.respuestaCorreo.mensaje = sHtmlErrores(respuesta.errors);
                })
                .then(() => $this.iSuscribiendo = 0);
        },
        actualizarLstProductos: function () {
            this.lstProductos = [...this.lstProductos];
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
                                lstCarrito: vueInicio.lstCarritoCompras,
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
                                    vueInicio.ajaxDisminuirCantidadProductoCarrito(producto);
                                },
                                ajaxAumentarCantidadProductoCarritoModal: function (producto) {
                                    vueInicio.ajaxAumentarCantidadProductoCarritoModal(producto);
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
            ajaxWebsiteAumentarCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad + 1;
                        $this.actualizarLstProductos();

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad + 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.guardarLstCarritoCompras();
                    }
                });
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
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
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
