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
                        console.log(producto);
                        $this.producto = producto;
                        $this.producto['control_max'] = 1;
                        $this.producto['control_min'] = 1;

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
                    let iIndice = $this.lstCarritoCompras.findIndex((item) => item.producto_id === parseInt(producto.id));
                    let productoLocalizado = Object.assign({}, $this.lstCarritoCompras[iIndice]);

                    $('#producto-modal').load('/tienda/producto/ajax/cargarPanel', function () {
                        let vuePanel = new Vue({
                            el: '#producto-modal',
                            data: {
                                producto: productoLocalizado,
                            },
                            computed: {
                                
                            },
                            mounted: function () {
                                
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
                                ajaxDisminuirCantidadProductoCarrito: function (producto) {
                                    let iProductoId = producto.id;
                                    let $this = this;
                                    ajaxWebsiteDisminuirCantidadProductoCarrito(iProductoId)
                                        .then(response => {
                                            let respuesta = response.data;
                                            if (respuesta.result === result.success) {
                                                producto.cantidad = producto.cantidad - 1;
                                                $this.producto.cantidad = $this.producto.cantidad - 1;
                                                // this.cantidad = this.cantidad - 1;
                                                vueTiendaProducto.actualizarLstProductosRelacionados();
                        
                                                let iIndiceDetalleCarrito = vueTiendaProducto.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                                                let detalle = vueTiendaProducto.lstCarritoCompras[iIndiceDetalleCarrito];
                                                detalle.cantidad = detalle.cantidad - 1;
                                                detalle.producto.cantidad = detalle.cantidad;
                        
                                                if (detalle.cantidad === 0) {
                                                    modalProducto = document.getElementById('contenedor-producto');	
                                                    if (!modalProducto){
                                                        alert("El elemento selecionado no existe");
                                                    } else {
                                                        padre = modalProducto.parentNode;
                                                        padre.removeChild(modalProducto);
                                                    }
                                                    vueTiendaProducto.lstCarritoCompras.splice(iIndiceDetalleCarrito, 1);
                                                }
                        
                                                vueTiendaProducto.guardarLstCarritoCompras();
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
                                                    $this.producto.cantidad = cantidad;
                                                    // this.cantidad = this.cantidad + 1;
                                                    vueTiendaProducto.actualizarLstProductosRelacionados();
                        
                                                    let iIndiceDetalleCarrito = vueTiendaProducto.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                                                    let detalle = vueTiendaProducto.lstCarritoCompras[iIndiceDetalleCarrito];
                                                    detalle.cantidad = cantidad;
                                                    detalle.producto.cantidad = cantidad;
                        
                                                    vueTiendaProducto.guardarLstCarritoCompras();
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
                                                $this.producto.cantidad = $this.producto.cantidad + 1;
                                                // this.cantidad = this.cantidad + 1;
                                                vueTiendaProducto.actualizarLstProductosRelacionados();
                        
                                                let iIndiceDetalleCarrito = vueTiendaProducto.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                                                let detalle = vueTiendaProducto.lstCarritoCompras[iIndiceDetalleCarrito];
                                                detalle.cantidad = detalle.cantidad + 1;
                                                detalle.producto.cantidad = detalle.cantidad;
                        
                                                vueTiendaProducto.guardarLstCarritoCompras();                    
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
            modalProducto = document.getElementById('contenedor-producto');	
            if (!modalProducto){
                //
            } else {
                padre = modalProducto.parentNode;
                padre.removeChild(modalProducto);
            }
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
                            $this.actualizarLstProductosRelacionados();

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
                toastr.error(producto.stock_actual + ' en stock.');
            }

            modalProducto = document.getElementById('contenedor-producto');	
            if (!modalProducto){
                //
            } else {
                padre = modalProducto.parentNode;
                padre.removeChild(modalProducto);
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

            modalProducto = document.getElementById('contenedor-producto');	
            if (!modalProducto){
                //
            } else {
                padre = modalProducto.parentNode;
                padre.removeChild(modalProducto);
            }
        },
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


