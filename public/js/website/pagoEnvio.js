let culqi = function () {
    if (Culqi.token) { // ¡Objeto Token creado exitosamente!
        let sToken = Culqi.token.id;
        var sTipoCompra = '';
        var sTipoDoc = '';
        var sDocumento = '';
        var sCliente = '';
        var sTelefono = '';
        var sEmail = '';
        var sSubTotal = vuePagoEnvio.fSubtotal;
        var sDelivery = vuePagoEnvio.fDelivery;
        var sTipoComprobante = '';
        var sDireccion = '';
        var sAgencia = '';
        var sDepartamento = '';
        var sProvincia = '';
        var sDistrito = '';
        var sRecoge = '';
        var sRecogeDocumento = '';
        var sRecogeTelefono = '';

        if(vuePagoEnvio.datosEnvio.sOpcion == 1)
        {
            sTipoCompra = 'ENVÍO NIVEL NACIONAL',
            sTipoDoc = vuePagoEnvio.datosEnvio.sTipoDoc;
            sDocumento = vuePagoEnvio.datosEnvio.sDocumento;
            if(sTipoDoc == 'DNI'){
                sCliente = vuePagoEnvio.datosEnvio.sNombres + ' ' + vuePagoEnvio.datosEnvio.sApellidos;
            }else{
                sCliente = vuePagoEnvio.datosEnvio.sNombres;
            }
            sTelefono = vuePagoEnvio.datosEnvio.sTelefono;
            sEmail = vuePagoEnvio.datosEnvio.sEmail;
            sDireccion = vuePagoEnvio.datosEnvio.sDireccion;
            sRecoge = vuePagoEnvio.datosEnvio.sRecoge.sRazonSocial;
            sRecogeDocumento = vuePagoEnvio.datosEnvio.sRecoge.sDocumento;
            sRecogeTelefono = vuePagoEnvio.datosEnvio.sRecoge.sTelefono;
            sAgencia = vuePagoEnvio.datosEnvio.sAgencia;
            sDepartamento = vuePagoEnvio.datosEnvio.sDepartamento;
            sProvincia = vuePagoEnvio.datosEnvio.sProvincia;
            sDistrito = vuePagoEnvio.datosEnvio.sDistrito;
            sTipoComprobante = vuePagoEnvio.datosEnvio.sTipoComprobante;
        }

        if(vuePagoEnvio.datosRecojo.sOpcion == 1)
        {
            sTipoCompra = 'RECOJO EN TIENDA',
            sTipoDoc = vuePagoEnvio.datosRecojo.rTipoDoc;
            sDocumento = vuePagoEnvio.datosRecojo.sDocumento;
            sCliente = vuePagoEnvio.datosRecojo.sNombres + ' ' + vuePagoEnvio.datosRecojo.sApellidos;
            sTelefono = vuePagoEnvio.datosRecojo.sTelefono;
            sEmail = vuePagoEnvio.datosRecojo.sEmail;
            sTipoComprobante = vuePagoEnvio.datosRecojo.sTipoComprobante;
        }

        if(vuePagoEnvio.datosDelivery.sOpcion == 1)
        {
            sTipoCompra = 'DELIVERY TRUJILLO',
            sTipoDoc = vuePagoEnvio.datosDelivery.dTipoDoc;
            sDocumento = vuePagoEnvio.datosDelivery.sDocumento;
            sCliente = vuePagoEnvio.datosDelivery.sNombres + ' ' + vuePagoEnvio.datosDelivery.sApellidos;
            sTelefono = vuePagoEnvio.datosDelivery.sTelefono;
            sEmail = vuePagoEnvio.datosDelivery.sEmail;
            sDireccion = vuePagoEnvio.datosDelivery.sDireccion;
            sDepartamento = vuePagoEnvio.datosDelivery.sDepartamento;
            sProvincia = vuePagoEnvio.datosDelivery.sProvincia;
            sDistrito = vuePagoEnvio.datosDelivery.sDistrito;
            sTipoComprobante = vuePagoEnvio.datosDelivery.sTipoComprobante;
        }

        let carrito = [];
        for(let producto of vuePagoEnvio.lstCarritoCompras){
            let objeto = {'id': producto.producto_id, 'cantidad': producto.cantidad};
            carrito.push(objeto);
        }

        let formData = new FormData();
        formData.append('token', sToken);
        formData.append('amount', vuePagoEnvio.fTotalCulqi);
        formData.append('email', sEmail);
        formData.append('detalles', JSON.stringify(carrito));
        formData.append('tipo_compra', sTipoCompra)
        formData.append('tipo_comprobante', sTipoComprobante);
        formData.append('tipo_documento', sTipoDoc);
        formData.append('documento', sDocumento);
        formData.append('cliente', sCliente);
        formData.append('telefono', sTelefono);
        formData.append('direccion', sDireccion);
        formData.append('recoge', sRecoge);
        formData.append('recoge_documento', sRecogeDocumento);
        formData.append('recoge_telefono', sRecogeTelefono);
        formData.append('agencia', sAgencia);
        formData.append('subtotal', sSubTotal);
        formData.append('delivery', sDelivery);
        formData.append('departamento', sDepartamento);
        formData.append('provincia', sProvincia);
        formData.append('distrito', sDistrito);
        vuePagoEnvio.iPagando = 1;
        axios.post('/pago-envio/ajax/crearCargo', formData)
            .then(response => {
                let respuesta = response.data;
                if(respuesta.result == 'success')
                {
                    axios({
                        url: '/pago-envio/ajax/crearVenta',
                        method: 'post',
                        data: formData
                      })
                      .then(function (response) {
                        let respuesta = response.data;
                        if (respuesta.result === 'success') {
                            vuePagoEnvio.lstCarritoCompras = [];
                            vuePagoEnvio.datosEnvio.sOpcion = 0;
                            vuePagoEnvio.datosDelivery.sOpcion = 0;
                            vuePagoEnvio.datosRecojo.sOpcion = 0;
                            $cookies.set('datosEnvio', vuePagoEnvio.datosEnvio, 12);
                            $cookies.set('datosDelivery', vuePagoEnvio.datosDelivery, 12); 
                            $cookies.set('datosRecojo', vuePagoEnvio.datosRecojo, 12);
                            vuePagoEnvio.guardarLstCarritoCompras();
                            toastr.clear();
                            toastr.options = {
                                iconClasses: {
                                    error: 'bg-danger',
                                    info: 'bg-info',
                                    success: 'bg-success',
                                    warning: 'bg-warning',
                                },
                            };
                            toastr.info(respuesta.mensaje);
                            setTimeout(() => {
                                vuePagoEnvio.iPagado = 1;
                                vuePagoEnvio.iPagando = 0;
                                //location = '/tienda';
                            }, 3000);
                        } else {
                            vuePagoEnvio.iPagado = 0;
                            setTimeout(() => {
                                vuePagoEnvio.iPagando = 0;
                            }, 3000);
                            toastr.clear();
                            toastr.options = {
                               iconClasses: {
                                    error: 'bg-danger',
                                    info: 'bg-info',
                                    success: 'bg-success',
                                    warning: 'bg-warning',
                                },
                            };
                            toastr.error(respuesta.mensaje);
                        }
                    })
                    .catch(error => {
                        let respuesta = error.response.data;
                        let message = JSON.parse(respuesta.message);
                        toastr.clear();
                        toastr.options = {
                            iconClasses: {
                                error: 'bg-danger',
                                info: 'bg-info',
                                success: 'bg-success',
                                warning: 'bg-warning',
                            },
                        };
                        toastr.error(message.merchant_message);
                        vuePagoEnvio.iPagando = 0;
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
                    toastr.error(respuesta.mensaje);
                    console.log(respuesta.mensaje);
                }
            })
            .catch(error => {
                let respuesta = error.response.data;
                let message = JSON.parse(respuesta.message);
                vuePagoEnvio.sMensajeError = message.merchant_message;
                toastr.clear();
                toastr.options = {
                    iconClasses: {
                        error: 'bg-danger',
                        info: 'bg-info',
                        success: 'bg-success',
                        warning: 'bg-warning',
                    },
                };
                toastr.error(message.merchant_message);
                vuePagoEnvio.iPagando = 0;
            });
    } else {
        toastr.clear();
        toastr.options = {
            iconClasses: {
                error: 'bg-danger',
                info: 'bg-info',
                success: 'bg-success',
                warning: 'bg-warning',
            },
        };
        toastr.error(Culqi.error.user_message);
    }
};

Culqi.publicKey = culqiEcovalle.publicKeyTest; // Configura tu llave pública
Culqi.options({
    style: {
        logo: 'https://ecovalle.pe/img/logo_ecovalle_240x240.png',
        maincolor: '#009D65',
    }
});

let vuePagoEnvio = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        locale: 'es',
        lstCarritoCompras: [],

        iCargandoDatosFacturacion: 0,
        lstPreciosEnvioNacional: [],
        lstPreciosDelivery: [],

        sNNacional: 1,
        sDelivery: 1,
        sRTienda: 1,

        datosEnvio: {
            sCabecera: 'NN',
            sTipoDoc: '',
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sRazon: '',
            sEmail: '',
            sTelefono: '',
            sDepartamento: '',
            sProvincia: '',
            sDistrito: '',
            sDireccion: '',
            sRecoge: {
                sDocumento: '',
                sRazonSocial: '',
                sTelefono: '',
            },
            sAgencia: '',
            sOpcion: 0,
        },

        datosRecojo: {
            sCabecera: 'RT',
            rTipoDoc: '',
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sEmail: '',
            sTelefono: '',
            sOpcion: 0,
        },
        
        datosDelivery: {
            sCabecera: 'ED',
            dTipoDoc: '',
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sEmail: '',
            sTelefono: '',
            sDepartamento: 'LA LIBERTAD',
            sProvincia: 'TRUJILLO',
            sDistrito: '',
            sDireccion: '',
            sOpcion: 0,
        },
        iPagando: 0,
        iPagado: 0,
        sMensajeError: '',

        respuestaPago: null
    },
    computed: {
        fDelivery: function () {
            if(this.sNNacional == 0)
            {
                for (let precioEnvio of this.lstPreciosEnvioNacional) {
                    if (this.datosEnvio.sDepartamento !== ''
                        && this.datosEnvio.sProvincia !== ''
                        && this.datosEnvio.sDistrito !== ''
                        && precioEnvio.distrito === this.datosEnvio.sDistrito
                        && precioEnvio.provincia === this.datosEnvio.sProvincia
                        && precioEnvio.departamento === this.datosEnvio.sDepartamento) return precioEnvio.tarifa;
                }
                return 0;
            }

            if(this.sDelivery == 0)
            {
                for (let precioDelivery of this.lstPreciosDelivery) {
                    if (this.datosDelivery.sDepartamento !== ''
                        && this.datosDelivery.sProvincia !== ''
                        && this.datosDelivery.sDistrito !== ''
                        && precioDelivery.distrito === this.datosDelivery.sDistrito
                        && precioDelivery.provincia === this.datosDelivery.sProvincia
                        && precioDelivery.departamento === this.datosDelivery.sDepartamento) return precioDelivery.tarifa;
                }
                return 0;
            }

            return 0;
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
        fTotal: function () {
            return this.fSubtotal + this.fDelivery;
        },
        fTotalCulqi: function () {
            return Math.round(((this.fSubtotal * 100) + (this.fDelivery * 100)) * 10) / 10;
        },
        sDetallesCarritoCompras: function () {
            let sDetalles = '';
            for (let detalle of this.lstCarritoCompras) {
                sDetalles += `${detalle.producto_id};${detalle.cantidad}|`;
            }
            return sDetalles.substr(0, sDetalles.length - 1);
        }
    },
    mounted: function () {
        let $this = this;
        ajaxWebsiteLocale().then(response => {
            let respuesta = response.data;
            $this.locale = respuesta.data.locale;
        }).then(() => ajaxWebsiteListarCarritoCompras().then(response => {
            let respuesta = response.data;
            let data = respuesta.data;

            let lstCarritoComprasServer = data.lstCarrito;
            //let bClienteEnSesion = data.bClienteEnSesion;

            let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
            let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

            let cookiedatosEnvio = $cookies.get('datosEnvio');
            let datosEnvio = cookiedatosEnvio ? cookiedatosEnvio : this.datosEnvio;
            $this.datosEnvio = datosEnvio;
            $this.datosEnvio.sOpcion == 1 ? this.sNNacional = 0 : this.sNNacional = 1;

            let cookiedatosRecojo = $cookies.get('datosRecojo');
            let datosRecojo = cookiedatosRecojo ? cookiedatosRecojo : this.datosRecojo;
            $this.datosRecojo = datosRecojo;
            $this.datosRecojo.sOpcion == 1 ? this.sRTienda = 0 : this.sRTienda = 1;

            let cookiedatosDelivery = $cookies.get('datosDelivery');
            let datosDelivery = cookiedatosDelivery ? cookiedatosDelivery : this.datosDelivery;
            $this.datosDelivery = datosDelivery;
            $this.datosDelivery.sOpcion == 1 ? this.sDelivery = 0 : this.sDelivery = 1;

            $this.lstCarritoCompras = lstCarritoCompras;
            $this.guardarLstCarritoCompras();
            $this.ajaxListarPreciosEnvio();
            $this.iCargando = 0;

        }).then(() => {
            if ($this.lstCarritoCompras.length === 0) {
                location = '/carrito-compras';
            }
            if($this.datosEnvio.sOpcion == 0 && $this.datosRecojo.sOpcion == 0 && $this.datosDelivery.sOpcion == 0)
            {
                location = '/facturacion-envio';
            }
        }));
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
        ajaxListarPreciosEnvio: function () {
            let $this = this;
            axios.get('/facturacion-envio/ajax/listarPreciosEnvio').then(response => {
                $this.lstPreciosEnvioNacional = response.data.data.lstPreciosEnvio.filter(direccion => direccion.provincia !== 'TRUJILLO');
                $this.lstPreciosDelivery = response.data.data.lstPreciosEnvio.filter(tarifa => tarifa.provincia === 'TRUJILLO')
            });
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
                    $this.respuestaPago = respuesta;
                    $this.iPagando = 0;

                    //$this.lstCarritoCompras = [];
                    $this.guardarLstCarritoCompras();
                })
                .catch(error => {
                    let respuesta = error.response.data;
                    $this.sMensajeError = sHtmlErrores(respuesta.errors);
                    $this.iPagando = 0;
                });
        },
        ajaxEnviarConstanciaTransferencia: function () {
        },
    }
});
