let vueCarritoCompras = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        locale: 'es',
        lstCarritoCompras: [],
    },
    computed: {
        iArticulos: function () {
            let iArticulos = 0;
            for (let detalle of this.lstCarritoCompras) {
                iArticulos += detalle.cantidad;
            }
            return iArticulos;
        },
        fDescuento: function () {
            let fDescuento = 0;
            for (let detalle of this.lstCarritoCompras) {
                let producto = detalle.producto;
                let fPromocion = producto.promocion_vigente === null ? 0.00 :
                    (producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max ? (producto.promocion_vigente.porcentaje ? ((producto.precio_actual.monto * producto.promocion_vigente.porcentaje) / 100) : (producto.promocion_vigente.monto)) : 0.00);
                fDescuento += detalle.cantidad * fPromocion;
            }
            return Math.round(fDescuento * 10) / 10;
        },
        fSubtotal: function () {
            let fSubtotal = 0;
            for (let detalle of this.lstCarritoCompras) {
                let producto = detalle.producto;
                let fPromocion = producto.promocion_vigente === null ? 0.00 :
                    (producto.cantidad >= producto.promocion_vigente.min && producto.cantidad <= producto.promocion_vigente.max ? (producto.promocion_vigente.porcentaje ? ((producto.precio_actual.monto * producto.promocion_vigente.porcentaje) / 100) : (producto.promocion_vigente.monto)) : 0.00);
                let fPrecio = (producto.oferta_vigente === null ? producto.precio_actual.monto :
                    (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto))) - fPromocion;
                fSubtotal += detalle.cantidad * fPrecio;
            }
            return Math.round(fSubtotal * 10) / 10;
        },
        fVentaValida: function() {
            return this.fSubtotal >= 50;
        },
        fTotal: function () {
            return this.fSubtotal + this.fDelivery;
        }
    },
    created: function () {
        let $this = this;
        ajaxWebsiteLocale().then(response => {
            let respuesta = response.data;
            $this.locale = respuesta.data.locale;
        }).then(() => {
            ajaxWebsiteListarCarritoCompras().then(response => {
                let respuesta = response.data;
                let data = respuesta.data;

                let lstCarritoComprasServer = data.lstCarrito;
                //let bClienteEnSesion = data.bClienteEnSesion;

                let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
                let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                $this.lstCarritoCompras = lstCarritoCompras;
                $this.iCargando = 0;
            }).then(() => this.guardarLstCarritoCompras());
        });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxActualizarCarritoCompras: function () {
            let $this = this;
            $this.iCargando = 1;
            $this.lstCarritoCompras = [];

            ajaxWebsiteListarCarritoCompras().then(response => {
                let respuesta = response.data;
                let lstCarritoComprasServer = respuesta.data.lstCarrito;

                let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
                let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                $this.lstCarritoCompras = lstCarritoCompras;
                $this.guardarLstCarritoCompras();

                $this.iCargando = 0;
            });
        },
        ajaxDisminuirCantidadProductoCarrito: function (producto, i) {
            let iProductoId = producto.id;
            let $this = this;
            ajaxWebsiteDisminuirCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let detalle = $this.lstCarritoCompras[i];
                        detalle.cantidad = detalle.cantidad - 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.guardarLstCarritoCompras($this.lstCarritoCompras);
                    }
                });
        },
        ajaxAumentarCantidadProductoCarrito: function (producto, i) {
            console.log(i);
            let iProductoId = producto.id;
            let $this = this;
            if(producto.cantidad + 1 === producto.stock_actual)
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
                toastr.info(producto.stock_actual + ' en stock.');

                var cantidad = producto.stock_actual;
                ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cantidad)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            producto.cantidad = cantidad;
                            // this.cantidad = this.cantidad + 1;

                            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                            let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                            detalle.cantidad = cantidad;
                            detalle.producto.cantidad = cantidad;

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
                            let detalle = $this.lstCarritoCompras[i];
                            detalle.cantidad = detalle.cantidad + 1;
                            detalle.producto.cantidad = detalle.cantidad;

                            $this.guardarLstCarritoCompras();
                        }
                    });
            }
            else{
                toastr.clear();
                toastr.options = {
                    iconClasses: {
                        error: 'bg-danger',
                        info: 'bg-info',
                        success: 'bg-success',
                        warning: 'bg-warning',
                    },
                };
                toastr.error($this.lstCarritoCompras[i].producto.stock_actual + ' en stock.');
            }
        },
        ajaxEliminarDelCarrito: function (producto, i) {
            let $this = this;
            let iProductoId = producto.id;

            ajaxWebsiteEliminarDelCarrito(iProductoId).then(response => {
                let respuesta = response.data;
                if (respuesta.result === result.success) {
                    $this.lstCarritoCompras.splice(i, 1);
                    $this.guardarLstCarritoCompras();
                }
            });
        },
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
        changeCantidad: function(producto,i)
        {
            var cantidad = $('#cantidad'+i.toString()).val();

            var cant = parseInt(cantidad);
            if(isNaN(cant))
            {
                cant = parseInt('1');
            }
            if(cant == 0)
            {
                cant = parseInt('1');
            }

            if(cantidad != '')
            {
                let iProductoId = producto.id;
                let $this = this;
                if(cant <= $this.lstCarritoCompras[i].producto.stock_actual)
                {
                    if(cant == 12)
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
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cant)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                let detalle = $this.lstCarritoCompras[i];
                                detalle.cantidad = cant;
                                detalle.producto.cantidad = cant;
        
                                $this.guardarLstCarritoCompras();
                            }
                        });
                }else{
                    let cant_aux = $this.lstCarritoCompras[i].producto.stock_actual;
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cant_aux)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                let detalle = $this.lstCarritoCompras[i];
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
                    toastr.error($this.lstCarritoCompras[i].producto.stock_actual +' en stock.');
                    $('#cantidad'+i.toString()).val(cant_aux);
                }                    
            }
        }
    }
});
