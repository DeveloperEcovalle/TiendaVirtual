let sPathName = location.pathname;
let lstPathName = sPathName.split('/');

let iProductoId = lstPathName.pop();

let vueTiendaProducto = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargandoProducto: 1,

        visibility: 0,
        iEnviandoResena: 0,
        sMensajeResena: '',
        sMensajeErrorStars: '',
        resena: {
            title: '',
            comment: '',
            stars: 0,
        },

        lstCarritoCompras: [],
        producto: {
            precio_actual: {
                monto: 0
            },
            oferta_vigente: {
                porcentaje: null,
                monto: null
            },
            promocion_vigente: {
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

        prueba: 0,
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

                    let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');

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
        ajaxSalir: () => ajaxSalir(),
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
                .then(() => {
                    $this.iCargandoProductosRelacionados = 0;
                    $('#productos-carousel').removeClass('d-none');
                });
        },
        ajaxAgregarAlCarrito: function (producto) {
            let $this = this;
            $this.iAgregandoAlCarrito = 1;
            $this.iProductoId = producto.id;

            ajaxWebsiteAgregarAlCarrito(producto, this.actualizarLstProductosRelacionados, this.lstCarritoCompras, this.guardarLstCarritoCompras)
                .then(() => {
                    $this.iAgregandoAlCarrito = 0;
                    $this.iProductoId = 0;
                }).then(() => {

                    $('#producto-modal').load('/tienda/producto/ajax/cargarPanel', function () {
                        let vuePanel = new Vue({
                            el: '#producto-modal',
                            data: {
                                lstCarrito: vueTiendaProducto.lstCarritoCompras,
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
                                    for (let detalle of vueTiendaProducto.lstCarritoCompras) {
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
                                    vueTiendaProducto.ajaxDisminuirCantidadProductoCarrito(producto);
                                },
                                ajaxAumentarCantidadProductoCarritoModal: function (producto) {
                                    vueTiendaProducto.ajaxAumentarCantidadProductoCarrito(producto);
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

                        $this.actualizarCantidadesProductosRelacionados();
                        $this.actualizarCantidadProducto();
                        $this.actualizarLstProductosRelacionados();

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

                            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                            let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                            detalle.cantidad = cantidad;
                            detalle.producto.cantidad = cantidad;
                            
                            $this.actualizarCantidadesProductosRelacionados();
                            $this.actualizarCantidadProducto();
                            $this.actualizarLstProductosRelacionados();
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

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad + 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.actualizarCantidadesProductosRelacionados();
                        $this.actualizarCantidadProducto();
                        $this.actualizarLstProductosRelacionados();
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
            }
        },
        changeCantidad: function(producto)
        {
            var cantidad = $('#cantidad').val();
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
                if(cant <= producto.stock_actual)
                {
                    if(cant == 12){
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
                    toastr.error(cant_aux + ' en stock.');
                }                    
            }
        },
        bVisibility: function()
        {
            if(this.visibility === 1)
            {
                this.visibility = 0;
            }
            else
            {
                this.visibility = 1;
            }
        },
        ajaxEnviarResena: function()
        {
            let $this = this;
            let formData = new FormData();
            formData.append('title', $this.resena.title);
            formData.append('comment', $this.resena.comment);
            formData.append('stars', $this.resena.stars);
            formData.append('productoId', $this.producto.id);


            let verificar = true;
            if($this.resena.stars === 0)
            {
                verificar = false;
                $this.sMensajeErrorStars = '¡¡Calificar!!'
            }
            
            if(verificar)
            {
                $this.iEnviandoResena = 1;
                //$this.iCargandoProducto = 1;
                axios.post('/ajax/calificarProducto', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.visibility = 0;
                        $this.resena.title = '';
                        $this.resena.comment = '';
                        $this.resena.stars = 0;
                        
                        $this.producto = respuesta.data.producto;
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
                    $this.sMensajeResena = respuesta.mensaje;
                })
                .then(() => {
                    $this.iEnviandoResena = 0;
                    //$this.iCargandoProducto = 0;
                });
            }
        }
    },
    updated: function () {
        this.$nextTick(function () {
            var options =  {"width": 350,"height":  370,"zoomWidth":350,"offset":{"vertical":0,"horizontal":0},"zoomPosition":"original"}
            new ImageZoom(document.getElementById("img-container"), options);   
            $(".carousel").carousel({
                interval: 3000
            });  
        });
    },
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


