let sPathName = location.pathname;
let lstPathName = sPathName.split('/');

let iProductoId = lstPathName.pop();

let vueProductosFiltrados = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargando: 1,

        sBuscar: '',

        pagina: {},

        lstCarritoCompras: [],
        iAgregandoAlCarrito: 0,
        iProductoId: 0,

        iCargandoCategorias: 1,

        iCargandoProductos: 1,
        lstProductos: [],
        lstBlogs: [],

        iTotalProductos: 0,
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

                    let formData = new FormData();
                    formData.append('keyword',$('#sBuscar').val());
                    axios.post('/tienda/ajax/obtenerProductos',formData)
                        .then(response => {
                            $this.lstProductos = response.data.data.lstProductos;
                            $this.lstBlogs = response.data.data.lstBlogs;    
                            for(var i = 0; i < $this.lstProductos.length; i++)
                            {
                                var cantidad = 0;
                                for(var j = 0; j < $this.lstCarritoCompras.length; j++)
                                {
                                    if($this.lstProductos[i].id == $this.lstCarritoCompras[j].producto_id)
                                    {
                                        cantidad = $this.lstCarritoCompras[j].cantidad;
                                    }
                                }
                                $this.lstProductos[i]['cantidad'] = cantidad;
                            }
                        })
                        .then(() => $this.iCargandoProductos = 0);
                });
            });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        onSelectAutocompleteProducto: function (e, ui) {
            let producto = JSON.parse(JSON.stringify(ui.item));
            
            window.location = `/tienda/producto/${producto.id}`;
            e.preventDefault();
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
                                lstCarrito: vueProductosFiltrados.lstCarritoCompras,
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
                                    for (let detalle of vueProductosFiltrados.lstCarritoCompras) {
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
                                    vueProductosFiltrados.ajaxDisminuirCantidadProductoCarrito(producto);
                                },
                                ajaxAumentarCantidadProductoCarritoModal: function (producto) {
                                    vueProductosFiltrados.ajaxAumentarCantidadProductoCarrito(producto);
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

                            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                            let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                            detalle.cantidad = cantidad;
                            detalle.producto.cantidad = cantidad;
                            
                            $this.actualizarCantidadesProductos();
                            $this.actualizarLstProductos();
                            $this.guardarLstCarritoCompras();
                        }
                    });
            }
            else if(producto.cantidad + 1 < producto.stock_actual)
            {
                ajaxWebsiteAumentarCantidadProductoCarrito(iProductoId)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        producto.cantidad = producto.cantidad + 1;

                        let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
                        let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                        detalle.cantidad = detalle.cantidad + 1;
                        detalle.producto.cantidad = detalle.cantidad;

                        $this.actualizarCantidadesProductos();
                        $this.actualizarLstProductos();
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


