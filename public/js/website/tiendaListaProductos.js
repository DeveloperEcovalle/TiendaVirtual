let pagina = lstUrlParams.get('pagina');
let iPaginaSeleccionada = pagina === null ? 0 : parseInt(pagina);

let categoriasSeleccionadas = lstUrlParams.get('categorias');
let lstCategoriasSeleccionadas = categoriasSeleccionadas === null ? [] : categoriasSeleccionadas.split(',');

let orden = lstUrlParams.get('orden');
let sOrden = orden === null ? 'popular' : orden;

let vueTiendaListaProductos = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargando: 1,

        sBuscar: '',

        pagina: {},

        lstCarritoCompras: [],
        iAgregandoAlCarrito: 0,
        iProductoId: 0,
        sOrden: sOrden,

        iCargandoCategorias: 1,
        lstCategorias: [],
        lstCategoriasSeleccionadas: lstCategoriasSeleccionadas,

        iCargandoProductos: 1,
        lstProductos: [],

        iTotalProductos: 0,
        iItemsPorPagina: 6,
        iPaginaSeleccionada: iPaginaSeleccionada,

        lstBanners: [],
    },
    computed: {
        iTotalPaginas: function () {
            return Math.ceil(this.iTotalProductos / this.iItemsPorPagina);
        },
        lstPaginas: function () {
            if (this.iTotalPaginas <= 6) {
                return Array.from(Array(this.iTotalPaginas).keys());
            }

            let iPaginaInicio = 0;
            let iPaginaFin = this.iTotalPaginas - 1;

            let lstPaginas;
            let lstPaginasInicio, lstPaginasMedio, lstPaginasFin;

            lstPaginasInicio = [iPaginaInicio, iPaginaInicio + 1];
            lstPaginasFin = [iPaginaFin - 1, iPaginaFin];

            if (this.iPaginaSeleccionada >= 4 && this.iPaginaSeleccionada <= iPaginaFin - 4) {
                lstPaginasMedio = [-1, this.iPaginaSeleccionada - 1, this.iPaginaSeleccionada, this.iPaginaSeleccionada + 1, -1];
            } else {
                lstPaginasMedio = [];
                if (this.iPaginaSeleccionada <= iPaginaInicio + 3) {
                    for (let i = 2; i <= this.iPaginaSeleccionada + 1; i++) {
                        lstPaginasMedio.push(i);
                    }
                    lstPaginasMedio.push(-1);
                } else {
                    lstPaginasMedio.push(-1);
                    for (let i = this.iPaginaSeleccionada - 1; i <= iPaginaFin - 2; i++) {
                        lstPaginasMedio.push(i);
                    }
                }
            }

            lstPaginas = lstPaginasInicio.concat(lstPaginasMedio, lstPaginasFin);
            return lstPaginas;
        },
        iIndiceInicio: function () {
            return this.iPaginaSeleccionada * this.iItemsPorPagina;
        },
        iIndiceFin: function () {
            return (this.iPaginaSeleccionada + 1) * this.iItemsPorPagina;
        },
        iIndiceInicioMuestra: function () {
            return this.iIndiceInicio + 1;
        },
        iIndiceFinMuestra: function () {
            return this.iIndiceInicioMuestra + this.lstProductos.length - 1;
        },
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

                    $this.ajaxListarPagina().then(() =>
                        $this.ajaxListarCategorias()
                            .then(() => $this.ajaxListarProductos().then(() => {
                                $this.actualizarCantidadesProductos();
                                $this.actualizarLstProductos();
                            }))
                    );
                });
            });
    },
    watch: {
        lstCategoriasSeleccionadas: function () {
            let $this = this;
            $this.iPaginaSeleccionada = 0;
            $this.ajaxListarProductos().then(() => {
                $this.actualizarCantidadesProductos();
                $this.actualizarLstProductos();
                $this.actualizarUrl();
            });
        },
        iPaginaSeleccionada: function () {
            let $this = this;
            $this.ajaxListarProductos().then(() => {
                $this.actualizarCantidadesProductos();
                $this.actualizarLstProductos();
                $this.actualizarUrl();
            });
        },
        sOrden: function () {
            let $this = this;
            $this.iPaginaSeleccionada = 0;
            $this.ajaxListarProductos().then(() => {
                $this.actualizarCantidadesProductos();
                $this.actualizarLstProductos();
                $this.actualizarUrl();
            });
        }
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        onSelectAutocompleteProducto: function (e, ui) {
            let producto = JSON.parse(JSON.stringify(ui.item));
            
            window.location = `/tienda/producto/${producto.id}`;
            e.preventDefault();
        },
        onChangeAutocompleteProducto: function (e, ui) {
            let producto = ui.item;
            let sNombre = this.locale === 'es' ? producto.nombre_es : producto.nombre_en;
            //this.sBuscar = sNombre;
            this.sBuscar = producto.nombre_es;
            e.preventDefault();
        },
        renderProducto: function (ul, item) {
            let producto = item;
            //console.log(producto);
            let sNombre = this.locale === 'es' ? producto.nombre_es : producto.nombre_en;
            let sRutaImagen = producto.imagenes.length > 0 ? producto.imagenes[0].ruta : '';
            let stock_guia = '';
            if(producto.stock_actual > 0)
            {
                stock_guia = `<button class="btn btn-primary btn-sm">EN STOCK</button>`;
            }else{
                stock_guia = `<button class="btn btn-danger btn-sm">AGOTADO</button>`;
            }
            return $('<li>')
                .append(`<div class="p-3 overflow-hidden" style="max-width: 500px; width: 100%">
                    <div class="row justify-content-between">
                    <div class="col-3"><img class="img-fluid img-thumbnail" src="${sRutaImagen}"></div>
                    <div class="col-9">
                    <a href="/tienda/producto/${producto.id}" class="font-weight-bold text-dark px-0">${sNombre}</a>
                    <div class="row justify-content-between">
                    <div class="col-6"><p class="m-0 h4 text-amarillo-ecovalle font-weight-bold">S/ ${producto.precio_actual.monto.toFixed(2)}</p></div>
                    <div class="col-6">`+stock_guia+`</div>
                    </div>
                    </div></div></div>`)
                .appendTo(ul);
        },
        actualizarUrl: function () {
            let sCategorias = this.lstCategoriasSeleccionadas.length === 0 ? '' : ('&categorias=' + this.lstCategoriasSeleccionadas.join());
            let sUrl = '/tienda?pagina=' + this.iPaginaSeleccionada + '&orden=' + this.sOrden + sCategorias;
            window.history.replaceState({}, 'Ecovalle | Tienda', sUrl);
        },
        navegarAnterior: function () {
            if (this.iPaginaSeleccionada > 0) {
                this.iPaginaSeleccionada--;
            }
        },
        navegarSiguiente: function () {
            if (this.iPaginaSeleccionada + 1 < this.iTotalPaginas) {
                this.iPaginaSeleccionada++;
            }
        },
        ajaxListarPagina: function () {
            let $this = this;
            $this.iCargando = 1;
            return axios.post('/tienda/ajax/listarPagina')
                .then(response => {
                    $this.pagina = response.data.data.pagina;
                    $this.lstBanners = response.data.data.lstBanners;
                })
                .then(() => $this.iCargando = 0);
        },
        ajaxListarCategorias: function () {
            let $this = this;
            $this.iCargandoCategorias = 1;
            return axios.post('/tienda/ajax/listarCategorias')
                .then(response => $this.lstCategorias = response.data.data.lstCategorias)
                .then(() => $this.iCargandoCategorias = 0);
        },
        ajaxListarProductos: function () {
            let $this = this;
            $this.iCargandoProductos = 1;
            $this.lstProductos = [];

            let formData = new FormData();
            for (let categoriaSeleccionada of $this.lstCategoriasSeleccionadas) {
                formData.append('lstCategoriasSeleccionadas[]', categoriaSeleccionada);
            }
            formData.append('sOrden', $this.sOrden);
            formData.append('iPaginaSeleccionada', $this.iPaginaSeleccionada);
            formData.append('iItemsPorPagina', $this.iItemsPorPagina);

            return axios.post('/tienda/ajax/listarProductos', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let data = respuesta.data;
                        $this.iTotalProductos = data.iTotalProductos;
                        $this.lstProductos = data.lstProductos;
                    }
                })
                .then(() => $this.iCargandoProductos = 0);
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
                            lstCarrito: vueTiendaListaProductos.lstCarritoCompras,
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
                                vueTiendaListaProductos.ajaxDisminuirCantidadProductoCarritoA(producto);
                            },
                            ajaxAumentarCantidadProductoCarritoModal: function (producto) {
                                vueTiendaListaProductos.ajaxAumentarCantidadProductoCarritoA(producto);
                            },
                        }
                    });
                });
            });
        },
        ajaxDisminuirCantidadProductoCarritoA: function (producto) {
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

                        $this.guardarLstCarritoCompras($this.lstCarritoCompras);
                    }
                });
        },
        ajaxAumentarCantidadProductoCarritoA: function (producto) {
            let $this = this;
            let iProductoId = producto.id;
            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
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

                            $this.actualizarCantidadesProductos();
                            $this.actualizarLstProductos();

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
                            let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                            producto.cantidad = producto.cantidad + 1; 
                            detalle.cantidad = detalle.cantidad + 1;
                            detalle.producto.cantidad = detalle.cantidad;

                            $this.actualizarCantidadesProductos();
                            $this.actualizarLstProductos();

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
                toastr.error($this.lstCarritoCompras[iIndiceDetalleCarrito].producto.stock_actual + ' en stock.');
            }
        },
        changeCantidad: function(producto,i){
            let $this = this;
            let iProductoId = producto.id;
            let iIndiceDetalleCarrito = $this.lstCarritoCompras.findIndex(detalle => detalle.producto_id === iProductoId);
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
                if(cant <= $this.lstCarritoCompras[iIndiceDetalleCarrito].producto.stock_actual)
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
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cantidad)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                                detalle.cantidad = cant;
                                detalle.producto.cantidad = cant;
                                //$this.actualizarLstProductos();
        
                                $this.guardarLstCarritoCompras();
                            }
                        });
                }else{
                    let cant_aux = $this.lstCarritoCompras[iIndiceDetalleCarrito].producto.stock_actual;
                    ajaxWebsiteAumentarCantidadProductoCarritoCant(iProductoId,cant_aux)
                        .then(response => {
                            let respuesta = response.data;
                            if (respuesta.result === result.success) {
                                let detalle = $this.lstCarritoCompras[iIndiceDetalleCarrito];
                                detalle.cantidad = cant_aux;
                                detalle.producto.cantidad = cant_aux;
                                //$this.actualizarLstProductos();
        
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
                    toastr.error($this.lstCarritoCompras[iIndiceDetalleCarrito].producto.stock_actual +' en stock.');
                    $('#cantidad'+i.toString()).val(cant_aux);
                }                    
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
            $(".carousel").carousel();
        });
    },
});
