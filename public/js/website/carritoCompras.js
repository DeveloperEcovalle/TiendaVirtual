let culqi = function () {
    if (Culqi.token) { // ¡Objeto Token creado exitosamente!
        let sToken = Culqi.token.id;

        vueCarritoCompras.iPagando = 1;

        let formData = new FormData();
        formData.append('token', sToken);
        formData.append('amount', vueCarritoCompras.fTotalCulqi);
        formData.append('email', vueCarritoCompras.formData.sCorreo);
        formData.append('tipo_de_comprobante', vueCarritoCompras.formData.iTipoComprobanteId);
        formData.append('tipo_de_documento', vueCarritoCompras.formData.iTipoDocumentoId);
        formData.append('numero_de_documento', vueCarritoCompras.formData.sNumeroDocumento);
        formData.append('nombres', vueCarritoCompras.formData.sNombres);
        formData.append('apellidos', vueCarritoCompras.formData.sApellidos);
        formData.append('razon_social', vueCarritoCompras.formData.sRazonSocial);
        formData.append('direccion_de_envio', vueCarritoCompras.formData.sDireccion);
        formData.append('detalles', vueCarritoCompras.formData.sDetallesCarritoCompras);

        axios.post('/carrito-compras/ajax/crearCargo', formData)
            .then(response => {
                let respuesta = response.data;
                vueCarritoCompras.sEtapaCompra = 'resumen';
                vueCarritoCompras.respuestaPago = respuesta;
                vueCarritoCompras.iPagando = 0;

                vueCarritoCompras.lstCarritoCompras = [];
                vueCarritoCompras.guardarLstCarritoCompras();
            })
            .catch(error => {
                let respuesta = error.response.data;
                let message = JSON.parse(respuesta.responseJSON.message);
                vueCarritoCompras.sMensajeError = message.merchant_message;
                vueCarritoCompras.iPagando = 0;
            });
    } else {
        alert(Culqi.error.user_message);
    }
};

Culqi.publicKey = culqiEcovalle.publicKeyTest; // Configura tu llave pública
Culqi.options({
    style: {
        logo: 'https://ecovalle.pe/img/logo_ecovalle_240x240.png',
        maincolor: '#009D65',
    }
});

let vueCarritoCompras = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        locale: 'es',
        lstCarritoCompras: [],

        iCargandoDatosFacturacion: 0,
        sEtapaCompra: 'carrito',

        lstUbigeo: [],

        lstTiposComprobante: [],
        lstPreciosEnvio: [],

        formData: {
            iTipoComprobanteId: 0,
            iTipoDocumentoId: '1',
            sNumeroDocumento: '',
            sNombres: '',
            sApellidos: '',
            sRazonSocial: '',
            sPersonaQueRecibe: '',
            sDepartamento: '',
            sProvincia: '',
            sDistrito: '',
            sDireccion: '',
            sTelefono: '',
            sCorreo: '',
            sNotas: '',
            sMedioPago: 'TARJETA',
            bTerminosCondiciones: false,
        },

        iPagando: 0,
        sMensajeError: '',

        respuestaPago: null
    },
    computed: {
        bFormularioCorrecto: function () {
            return this.formData.bTerminosCondiciones
                && this.formData.sNombres.trim().length > 0
                && this.formData.sApellidos.trim().length > 0
                && this.formData.sDepartamento !== ''
                && this.formData.sProvincia !== ''
                && this.formData.sDistrito !== ''
                && this.formData.sDireccion.trim().length > 0
                && this.formData.sTelefono.trim().length > 0
                && this.formData.sCorreo.trim().length > 0
                && this.lstPreciosEnvio.findIndex(precioEnvio => precioEnvio.departamento === this.formData.sDepartamento
                    && precioEnvio.provincia === this.formData.sProvincia && precioEnvio.distrito === this.formData.sDistrito) > -1;
        },
        bDestinoEncontrado: function () {
            return this.lstPreciosEnvio.findIndex(precioEnvio => precioEnvio.departamento === this.formData.sDepartamento) > -1;
        },
        fDelivery: function () {
            for (let precioEnvio of this.lstPreciosEnvio) {
                if (this.formData.sDepartamento !== ''
                    && this.formData.sProvincia === ''
                    && this.formData.sDistrito === ''
                    && precioEnvio.departamento === this.formData.sDepartamento) return precioEnvio.precio;


                else if (this.formData.sDepartamento !== ''
                    && this.formData.sProvincia !== ''
                    && this.formData.sDistrito === ''
                    && precioEnvio.provincia === this.formData.sProvincia
                    && precioEnvio.departamento === this.formData.sDepartamento) return precioEnvio.precio;

                else if (this.formData.sDepartamento !== ''
                    && this.formData.sProvincia !== ''
                    && this.formData.sDistrito !== ''
                    && precioEnvio.distrito === this.formData.sDistrito
                    && precioEnvio.provincia === this.formData.sProvincia
                    && precioEnvio.departamento === this.formData.sDepartamento) return precioEnvio.precio;
            }
            return 0;
        },
        fSubtotal: function () {
            let fSubtotal = 0;
            for (let detalle of this.lstCarritoCompras) {
                let producto = detalle.producto;
                let fPrecio = producto.oferta_vigente === null ? producto.precio_actual.monto :
                    (producto.oferta_vigente.porcentaje ? (producto.precio_actual.monto * (100 - producto.oferta_vigente.porcentaje) / 100) : (producto.precio_actual.monto - producto.oferta_vigente.monto));
                fSubtotal += detalle.cantidad * fPrecio;
            }
            return fSubtotal;
        },
        fTotal: function () {
            return this.fSubtotal + this.fDelivery;
        },
        fTotalCulqi: function () {
            return (this.fSubtotal + this.fDelivery) * 100;
        },
        lstTiposDocumento: function () {
            let lstTipoComprobante = this.lstTiposComprobante.filter(tipoComprobante => tipoComprobante.id == this.formData.iTipoComprobanteId);
            if (lstTipoComprobante.length === 0) {
                return [];
            }

            let tipoComprobante = lstTipoComprobante[0];
            return tipoComprobante.tipo_comprobante_sunat.tipos_documento;
        },
        lstDepartamentos: function () {
            let lst = [];
            for (let ubigeo of this.lstUbigeo) {
                if (lst.findIndex(departamento => departamento === ubigeo.departamento) === -1) {
                    lst.push(ubigeo.departamento);
                }
            }
            return lst;
        },
        lstProvincias: function () {
            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.formData.sDepartamento);
            let lst = [];
            for (let ubigeo of lstUbigeoFiltrado) {
                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                    lst.push(ubigeo.provincia);
                }
            }
            return lst;
        },
        lstDistritos: function () {
            return this.lstUbigeo.filter(ubigeo =>
                ubigeo.departamento === this.formData.sDepartamento
                && ubigeo.provincia === this.formData.sProvincia);
        },
        sDetallesCarritoCompras: function () {
            let sDetalles = '';
            for (let detalle of this.lstCarritoCompras) {
                sDetalles += `${detalle.producto_id};${detalle.cantidad}|`;
            }
            return sDetalles.substr(0, sDetalles.length - 1);
        }
    },
    watch: {
        sEtapaCompra: function (sNewEtapaCompra) {
            if (sNewEtapaCompra === 'facturacion')
                this.ajaxListarDatosFacturacion();
        },
        'formData.iTipoComprobanteId': function () {
            let codigo = this.lstTiposDocumento.length > 0 ? this.lstTiposDocumento[0].codigo : 0;
            this.formData.iTipoDocumentoId = codigo;
        }
    },
    mounted: function () {
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
                $this.guardarLstCarritoCompras();
                $this.iCargando = 0;

                $this.ajaxListarPreciosEnvio();
            });
        });
    },
    methods: {
        ajaxListarPreciosEnvio: function () {
            let $this = this;
            axios.post('/carrito-compras/ajax/listarPreciosEnvio')
                .then(response => {
                    let respuesta = response.data;
                    $this.lstPreciosEnvio = respuesta.data.lstPreciosEnvio;
                })
        },
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
            let iProductoId = producto.id;
            let $this = this;
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

        ajaxListarDatosFacturacion: function () {
            let $this = this;
            $this.iCargandoDatosFacturacion = 1;

            axios.post('/carrito-compras/ajax/listarDatosFacturacion').then(response => {
                let respuesta = response.data;
                if (respuesta.result === result.success) {
                    let data = respuesta.data;
                    $this.lstTiposComprobante = data.lstTiposComprobante;
                    $this.lstUbigeo = data.lstUbigeo;

                    if ($this.lstTiposComprobante.length > 0) {
                        $this.formData.iTipoComprobanteId = $this.lstTiposComprobante[0].id;
                    }

                    if (data.cliente) {
                        let cliente = data.cliente;
                        let persona = cliente.persona;
                        $this.formData.sNombres = persona.nombres;
                        $this.formData.sApellidos = persona.apellido_1 + ' ' + persona.apellido_2;
                        $this.formData.sDireccion = cliente.direccion;
                        $this.formData.sCorreo = cliente.correo;

                        if (cliente.ubigeo) {
                            let ubigeo = cliente.ubigeo;
                            $this.formData.sDepartamento = ubigeo.departamento;
                            $this.formData.sProvincia = ubigeo.provincia;
                            $this.formData.sDistrito = ubigeo.id;
                        }
                    }
                }
            }).then(() => $this.iCargandoDatosFacturacion = 0);
        },
        mostrarModalPago: function () {
            this.sMensajeError = '';

            Culqi.settings({
                title: 'Ecovalle',
                currency: 'PEN',
                description: 'Pedido Ecovalle',
                amount: this.fTotalCulqi
            });

            Culqi.open();
        },
        ajaxEnviarContanciaYapePlin: function () {
            this.iPagando = 1;

            let formData = new FormData();
            formData.append('amount', this.fTotal);
            formData.append('email', this.formData.sCorreo);
            formData.append('tipo_de_comprobante', this.formData.iTipoComprobanteId);
            formData.append('tipo_de_documento', this.formData.iTipoDocumentoId);
            formData.append('numero_de_documento', this.formData.sNumeroDocumento);
            formData.append('nombres', this.formData.sNombres);
            formData.append('apellidos', this.formData.sApellidos);
            formData.append('razon_social', this.formData.sRazonSocial);
            formData.append('direccion_de_envio', this.formData.sDireccion);
            formData.append('detalles', this.formData.sDetallesCarritoCompras);

            let $this = this;
            axios.post('/carrito-compras/ajax/enviarConstanciaYapePlin', formData)
                .then(response => {
                    let respuesta = response.data;
                    $this.sEtapaCompra = 'resumen';
                    $this.respuestaPago = respuesta;
                    $this.iPagando = 0;

                    $this.lstCarritoCompras = [];
                    $this.guardarLstCarritoCompras();
                })
                .catch(error => {
                    let respuesta = error.response.data;
                    $this.sMensajeError = sHtmlErrores(respuesta.errors);
                    $this.iPagando = 0;
                });
        },
        ajaxEnviarConstanciaTransferencia: function () {
        }
    }
});
