let sPathName = location.pathname;
let lstPathName = sPathName.split('/');

let iProductoId = lstPathName.pop();

let vueTiendaProducto = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargandoProducto: 1,

        lstCarritoCompras: [],
        producto: {
            precio_actual: {
                monto: 0
            },
            oferta_vigente: {
                porcentaje: null,
                monto: null
            }
        },
        iImagenSeleccionada: -1,
        sRutaImagenSeleccionada: '',

        iCargandoProductosRelacionados: 1,
        iIndiceProductosRelacionadosInicio: 0,
        //iTotalPaginasProductosRelacionados: 0,
        lstProductosRelacionados: [],

        iAgregandoAlCarrito: 0,
        iProductoId: 0,
        cantidad: 0,
    },
    computed: {
        lstCarouselProductos: function () {
            return chunk(this.lstProductosRelacionados, 4);
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
                    // let direccionEnvio = $cookies.get('direccionEnvio');
                    // console.log(direccionEnvio);

                    let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                    $this.lstCarritoCompras = lstCarritoCompras;
                    $this.guardarLstCarritoCompras();

                    $this.ajaxListarProducto().then(() => {
                        $this.actualizarCantidadProducto();
                        $this.ajaxListarProductosRelacionados().then(() => {
                            $this.actualizarCantidadesProductosRelacionados();
                            $this.actualizarLstProductosRelacionados();
                        });
                    });
                });
            });
    },
    methods: {
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListarProducto: function () {
            let formData = new FormData();
            formData.append('iProductoId', iProductoId);

            let $this = this;
            return axios.post('/tienda/producto/ajax/listarProducto', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let producto = respuesta.data.producto;
                        $this.producto = producto;

                        if (producto.imagenes.length > 0) {
                            $this.iImagenSeleccionada = 0;
                            $this.sRutaImagenSeleccionada = producto.imagenes[0].ruta;
                        }
                    }
                })
                .then(() => $this.iCargandoProducto = 0);
        },
        ajaxListarProductosRelacionados: function () {
            let formData = new FormData();
            formData.append('iProductoId', this.producto.id);

            let $this = this;
            return axios.post('/tienda/producto/ajax/listarProductosRelacionados', formData)
                .then(response => $this.lstProductosRelacionados = response.data.data.lstProductosRelacionados)
                .then(() => $this.iCargandoProductosRelacionados = 0);
        },

        ajaxAgregarAlCarrito: function (producto) {
            let $this = this;
            $this.iAgregandoAlCarrito = 1;
            $this.iProductoId = producto.id;

            ajaxWebsiteAgregarAlCarrito(producto, this.actualizarLstProductosRelacionados, this.lstCarritoCompras, this.guardarLstCarritoCompras)
                .then(() => {
                    $this.iAgregandoAlCarrito = 0;
                    $this.iProductoId = 0;
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
                        // this.cantidad = this.cantidad - 1;
                        $this.actualizarLstProductosRelacionados();

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad - 1;
                        detalle.producto.cantidad = detalle.cantidad;

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
            if(producto.cantidad + 1 <= producto.stock_actual)
            {
                ajaxWebsiteAumentarCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad + 1;
                        // this.cantidad = this.cantidad + 1;
                        $this.actualizarLstProductosRelacionados();

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad + 1;
                        detalle.producto.cantidad = detalle.cantidad;

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
                toastr.error('Stock insuficiente');
            }
        },

        actualizarLstProductosRelacionados: function () {
            this.lstProductosRelacionados = [...this.lstProductosRelacionados];
        },
        actualizarCantidadesProductosRelacionados: function () {
            for (let detalle of this.lstCarritoCompras) {
                let iIndiceProducto = this.lstProductosRelacionados.findIndex(producto => producto.id === detalle.producto_id);
                let cantidad = iIndiceProducto === -1 ? 0 : detalle.cantidad;
                if (iIndiceProducto > -1) {
                    this.lstProductosRelacionados[iIndiceProducto].cantidad = cantidad;
                }
            }
        },
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
        actualizarCantidadProducto: function () {
            let lstDetalle = this.lstCarritoCompras.filter(detalle => detalle.producto_id === this.producto.id);
            if (lstDetalle.length > 0) {
                let detalle = lstDetalle[0];
                this.producto.cantidad = detalle.cantidad;
                this.cantidad = this.producto.cantidad;
                //console.log(this.producto.cantidad);
            }
        },

        changeCantidad: function(producto)
        {
            var cantidad = $('#cantidad').val();
            if(cant == '' || cant == null)
            {
                cant = 1;
            }
            var cant = parseInt(cantidad);
            if(isNaN(cant))
            {
                cant = parseInt('1');
            }

            if(cantidad != '')
            {
                if(cant <= producto.stock_actual)
                {
                    let iProductoId = producto.id;
                    let $this = this;
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cantidad)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                producto.cantidad = cant;
                                // this.cantidad = this.cantidad + 1;
                                $this.actualizarLstProductosRelacionados();

                                let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                                let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                                detalle.cantidad = cant;
                                detalle.producto.cantidad = cant;

                                $this.guardarLstCarritoCompras();
                            }
                        });
                }else{
                    let iProductoId = producto.id;
                    let $this = this;
                    let cant_aux = producto.stock_actual;
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cant_aux)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                producto.cantidad = cant_aux;
                                // this.cantidad = this.cantidad + 1;
                                $this.actualizarLstProductosRelacionados();

                                let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                                let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                                detalle.cantidad = cant_aux;
                                detalle.producto.cantidad = cant_aux;

                                $this.guardarLstCarritoCompras();
                            }
                        });
                    toastr.clear();
                    toastr.options = {
                        iconClasses: {
                            error: 'bg-danger',
                            info: 'bg-info',
                            success: 'bg-success',
                            warning: 'bg-warning',
                        },
                    };
                    toastr.error('Stock insuficiente');
                }                    
            }
        },

        /*blurCant : function(producto)
        {
            let iProductoId = producto.id;
            let $this = this;
            ajaxWebsiteAumentarCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad;
                        // this.cantidad = this.cantidad + 1;
                        $this.actualizarLstProductosRelacionados();

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = producto.cantidad;
                        detalle.producto.cantidad = producto.cantidad;

                        $this.guardarLstCarritoCompras();
                    }
                });
        }*/

    }
});

function fnExplota(){
    $('.modal-container').addClass('active');
    $('.modal-container').addClass('explota');
    $('.btn-round').removeClass('d-none');
    $('.reduce-container').attr('onclick','fnReduce()');
}

function fnReduce()
{
    $('.modal-container').removeClass('active');
    $('.btn-round').addClass('d-none');
    $('.reduce-container').removeAttr('onclick','fnReduce()');
}


