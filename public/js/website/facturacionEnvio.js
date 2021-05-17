let culqi = function () {
    if (Culqi.token) { // ¡Objeto Token creado exitosamente!
        let sToken = Culqi.token.id;

        vueFacturacionEnvio.iPagando = 1;

        let formData = new FormData();
        formData.append('token', sToken);
        formData.append('amount', vueFacturacionEnvio.fTotalCulqi);
        formData.append('email', vueFacturacionEnvio.formData.sCorreo);
        formData.append('tipo_de_comprobante', vueFacturacionEnvio.formData.iTipoComprobanteId);
        formData.append('tipo_de_documento', vueFacturacionEnvio.formData.iTipoDocumentoId);
        formData.append('numero_de_documento', vueFacturacionEnvio.formData.sNumeroDocumento);
        formData.append('nombres', vueFacturacionEnvio.formData.sNombres);
        formData.append('apellidos', vueFacturacionEnvio.formData.sApellidos);
        formData.append('razon_social', vueFacturacionEnvio.formData.sRazonSocial);
        formData.append('direccion_de_envio', vueFacturacionEnvio.formData.sDireccion);
        formData.append('detalles', vueFacturacionEnvio.formData.sDetallesCarritoCompras);

        axios.post('/carrito-compras/ajax/crearCargo', formData)
            .then(response => {
                let respuesta = response.data;
                vueFacturacionEnvio.sEtapaCompra = 'resumen';
                vueFacturacionEnvio.respuestaPago = respuesta;
                vueFacturacionEnvio.iPagando = 0;

                vueFacturacionEnvio.lstCarritoCompras = [];
                vueFacturacionEnvio.guardarLstCarritoCompras();
            })
            .catch(error => {
                let respuesta = error.response.data;
                let message = JSON.parse(respuesta.responseJSON.message);
                vueFacturacionEnvio.sMensajeError = message.merchant_message;
                vueFacturacionEnvio.iPagando = 0;
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

let vueFacturacionEnvio = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        locale: 'es',
        lstCarritoCompras: [],
        sBuscar:'',
        sBuscard:'',

        sNNacional: 0,
        sDelivery: 1,
        sRTienda: 1,

        iCargandoDatosFacturacion: 0,
        lstUbigeo: [],

        lstTiposComprobante: [],
        lstPreciosEnvioNacional: [],
        lstPreciosDelivery: [],

        direccionEnvio: {
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sRazon: '',
            sTelefono: '',
            sCorreo: '',
            sDepartamento: '',
            sProvincia: '',
            sDistrito: '',
            iUbigeoId: 0,
            sDireccion: '',
        },

        datosRecojo: {
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sTelefono: '',
        },
        
        datosDelivery: {
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sTelefono: '',
            sDepartamento: 'LA LIBERTAD',
            sProvincia: 'TRUJILLO',
            sDistrito: '',
            sDireccion: '',
        }, 

        iDireccionEnvioEstablecida: 0,
        iDireccionEnvioConfirmada: 0,

        iDeliveryEstablecido: 0,
        iDeliveryConfirmado: 0,

        iRecojoEstablecido: 0,
        iRecojoConfirmado: 0,

        rTipoDoc:'',
        iCargandoConsultaApir: 0,

        dTipoDoc:'',
        iCargandoConsultaApid: 0,

        sTipoDoc:'',
        iCargandoConsultaApi: 0,

        iPagando: 0,
        sMensajeError: '',

        respuestaPago: null
    },
    computed: {
        lstPreciosDeliveryFiltrado: function () {
            return this.lstPreciosDelivery.filter(tarifa =>
                tarifa.distrito.toLowerCase().includes(this.sBuscard.toLowerCase())
            );
        },
        lstPreciosEnvioNacionalFiltrado: function () {
            return this.lstPreciosEnvioNacional.filter(tarifa =>
                tarifa.departamento.toLowerCase().includes(this.sBuscar.toLowerCase())
                || tarifa.provincia.toLowerCase().includes(this.sBuscar.toLowerCase())
                || tarifa.distrito.toLowerCase().includes(this.sBuscar.toLowerCase())
            );
        },
        bDireccionEnvioValida: function () {
            return this.direccionEnvio.sTelefono.trim().length > 0
                && this.direccionEnvio.sCorreo.trim().length > 0
                && this.direccionEnvio.sDepartamento.trim().length > 0
                && this.direccionEnvio.sProvincia.trim().length > 0
                && this.direccionEnvio.sDistrito.trim().length > 0
                && this.direccionEnvio.sDireccion.trim().length > 0
                && this.direccionEnvio.sDocumento.trim().length > 0;
        },
        bRecojoValida: function () {
            return this.datosRecojo.sDocumento.trim().length > 0
                && this.datosRecojo.sNombres.trim().length > 0
                && this.datosRecojo.sApellidos.trim().length > 0
                && this.datosRecojo.sTelefono.trim().length > 0;
        },
        bDeliveryValida: function () {
            return this.datosDelivery.sDocumento.trim().length > 0
                && this.datosDelivery.sNombres.trim().length > 0
                && this.datosDelivery.sApellidos.trim().length > 0
                && this.datosDelivery.sTelefono.trim().length > 0
                && this.datosDelivery.sDireccion.trim().length > 0
                && this.datosDelivery.sDepartamento.trim().length > 0
                && this.datosDelivery.sProvincia.trim().length > 0
                && this.datosDelivery.sDistrito.trim().length > 0;
        },
        /*bDestinoEncontrado: function () {
            return this.lstPreciosEnvioNacional.findIndex(precioEnvio => precioEnvio.departamento === this.formData.sDepartamento) > -1;
        },*/
        bVerificaDni: function() {
            return this.sTipoDoc == 'DNI' && this.direccionEnvio.sNombres.trim().length > 0 && this.direccionEnvio.sApellidos.trim().length > 0;
        },
        bVerificaRuc: function(){
            return this.sTipoDoc == 'RUC' && this.direccionEnvio.sRazon.trim().length > 0
        },
        fDelivery: function () {
            if(this.sNNacional == 0)
            {
                for (let precioEnvio of this.lstPreciosEnvioNacional) {
                    if (this.direccionEnvio.sDepartamento !== ''
                        && this.direccionEnvio.sProvincia !== ''
                        && this.direccionEnvio.sDistrito !== ''
                        && precioEnvio.distrito === this.direccionEnvio.sDistrito
                        && precioEnvio.provincia === this.direccionEnvio.sProvincia
                        && precioEnvio.departamento === this.direccionEnvio.sDepartamento) return precioEnvio.tarifa;
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
            //TODO this.formData YA NO EXISTE
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
                if (lst.findIndex(departamento => departamento === ubigeo.departamento) === -1 && ubigeo.estado === 'ACTIVO') {
                    lst.push(ubigeo.departamento);
                }
            }
            return lst;
        },
        lstProvincias: function () {
            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.direccionEnvio.sDepartamento);
            let lst = [];
            for (let ubigeo of lstUbigeoFiltrado) {
                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1  && ubigeo.estado === 'ACTIVO' && ubigeo.provincia != 'TRUJILLO') {
                    lst.push(ubigeo.provincia);
                }
            }
            if(this.direccionEnvio.sDepartamento == '')
            {
                this.direccionEnvio.sProvincia = '';
                this.direccionEnvio.sProvincia = '';
            }
            return lst;
        },
        lstDistritos: function () {
            if(this.direccionEnvio.sProvincia == '')
            {
                this.direccionEnvio.sDistrito = '';
                this.direccionEnvio.sDistrito = '';
            }
            return this.lstUbigeo.filter(ubigeo =>
                ubigeo.departamento === this.direccionEnvio.sDepartamento
                && ubigeo.provincia === this.direccionEnvio.sProvincia
                && ubigeo.estado === 'ACTIVO');
        },
        lstDistritosD: function () {
            return this.lstUbigeo.filter(ubigeo =>
                ubigeo.departamento === 'LA LIBERTAD'
                && ubigeo.provincia === 'TRUJILLO'
                && ubigeo.estado === 'ACTIVO');
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

            $this.lstCarritoCompras = lstCarritoCompras;
            $this.guardarLstCarritoCompras();
            $this.ajaxListarPreciosEnvio();
            $this.iCargando = 0;

            $this.ajaxListarDatosFacturacion();
            $('#modalEditarDireccionEnvio').modal('show'); 
        }).then(() => {
            if ($this.lstCarritoCompras.length === 0) {
                location = '/carrito-compras';
            }
        }));
    },
    methods: {
        sDeliveryFn: function(){
            this.sDelivery = 0;
            this.sNNacional = 1;
            this.sRTienda = 1;

            $('#modalEditarDelivery').modal('show');
        },
        sNNacionalFn: function(){
            this.sNNacional = 0;
            this.sDelivery = 1;
            this.sRTienda = 1;
            $('#modalEditarDireccionEnvio').modal('show'); 
        },

        sRTiendaFn: function(){
            this.sDelivery = 1;
            this.sRTienda = 0;
            this.sNNacional = 1;
            $('#modalEditarRecojo').modal('show');
        },

        cambiarTipoDoc: function(){
            switch (this.sTipoDoc) {
                case 'DNI':
                    $('#documento').removeAttr('minlength','11');
                    $('#documento').removeAttr('maxlength','11');
                    $('#documento').attr('minlength','8');
                    $('#documento').attr('maxlength','8');
                    $('#razon_social').attr('required',false);
                    $('#nombres').attr('required',true);
                    $('#apellidos').attr('required',true);
                    this.direccionEnvio.sDocumento = '';
                    this.direccionEnvio.sNombres = '';
                    this.direccionEnvio.sApellidos = '';
                    this.direccionEnvio.sRazon = '';
                    this.direccionEnvio.sTelefono = '';
                    this.direccionEnvio.sCorreo = '';
                    this.direccionEnvio.sDepartamento = '';
                    this.direccionEnvio.sProvincia = '';
                    this.direccionEnvio.sDistrito = '';
                    this.direccionEnvio.sDireccion = '';
                    break;
                case 'RUC':
                    $('#documento').removeAttr('minlength','8');
                    $('#documento').removeAttr('maxlength','8');
                    $('#documento').attr('minlength','11');
                    $('#documento').attr('maxlength','11');
                    $('#razon_social').attr('required',true);
                    $('#nombres').attr('required',false);
                    $('#apellidos').attr('required',false);
                    this.direccionEnvio.sDocumento = '';
                    this.direccionEnvio.sNombres = '';
                    this.direccionEnvio.sApellidos = '';
                    this.direccionEnvio.sRazon = '';
                    this.direccionEnvio.sTelefono = '';
                    this.direccionEnvio.sCorreo = '';
                    this.direccionEnvio.sDepartamento = '';
                    this.direccionEnvio.sProvincia = '';
                    this.direccionEnvio.sDistrito = '';
                    this.direccionEnvio.sDireccion = '';
                    break;
                default:
                    $('#documento').removeAttr('minlength','11');
                    $('#documento').removeAttr('maxlength','11');
                    $('#documento').attr('minlength','8');
                    $('#documento').attr('maxlength','8');
                    this.direccionEnvio.sDocumento = '';
                    this.direccionEnvio.sNombres = '';
                    this.direccionEnvio.sApellidos = '';
                    this.direccionEnvio.sRazon = '';
                    this.direccionEnvio.sTelefono = '';
                    this.direccionEnvio.sCorreo = '';
                    this.direccionEnvio.sDepartamento = '';
                    this.direccionEnvio.sProvincia = '';
                    this.direccionEnvio.sDistrito = '';
                    this.direccionEnvio.sDireccion = '';
                    break;
            }
        },

        rcambiarTipoDoc: function(){
            switch (this.rTipoDoc) {
                case 'DNI':
                    $('#rdocumento').removeAttr('minlength','11');
                    $('#rdocumento').removeAttr('maxlength','11');
                    $('#rdocumento').attr('minlength','8');
                    $('#rdocumento').attr('maxlength','8');
                    $('#d').attr('required',true);
                    $('#rapellidos').attr('required',true);
                    this.datosRecojo.sDocumento = '';
                    this.datosRecojo.sNombres = '';
                    this.datosRecojo.sApellidos = '';
                    break;
                default:
                    $('#rdocumento').removeAttr('minlength','11');
                    $('#rdocumento').removeAttr('maxlength','11');
                    $('#rdocumento').attr('minlength','8');
                    $('#rdocumento').attr('maxlength','8');
                    this.datosRecojo.sDocumento = '';
                    this.datosRecojo.sNombres = '';
                    this.datosRecojo.sApellidos = '';
                    break;
            }
        },

        dcambiarTipoDoc: function(){
            switch (this.dTipoDoc) {
                case 'DNI':
                    $('#ddocumento').removeAttr('minlength','11');
                    $('#ddocumento').removeAttr('maxlength','11');
                    $('#ddocumento').attr('minlength','8');
                    $('#ddocumento').attr('maxlength','8');
                    this.datosDelivery.sDocumento = '';
                    this.datosDelivery.sNombres = '';
                    this.datosDelivery.sApellidos = '';
                    this.datosDelivery.sTelefono = '';
                    this.datosDelivery.sDireccion = '';
                    break;
                default:
                    $('#ddocumento').removeAttr('minlength','11');
                    $('#ddocumento').removeAttr('maxlength','11');
                    $('#ddocumento').attr('minlength','8');
                    $('#ddocumento').attr('maxlength','8');
                    this.datosDelivery.sDocumento = '';
                    this.datosDelivery.sNombres = '';
                    this.datosDelivery.sApellidos = '';
                    this.datosDelivery.sTelefono = '';
                    this.datosDelivery.sDireccion = '';
                    break;
            }
        },

        ajaxConsultaApi: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;
            if(this.sTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.direccionEnvio.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.sTipoDoc == 'DNI' && this.direccionEnvio.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(this.sTipoDoc == 'RUC' && this.direccionEnvio.sDocumento.length < 11)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de ruc';
            }

            if(verifica)
            {
                this.iCargandoConsultaApi = 1;
                formData.append('tipo_documento',this.sTipoDoc);
                formData.append('documento',this.direccionEnvio.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.sTipoDoc == 'DNI')
                            {
                                this.direccionEnvio.sNombres = respuesta.data.nombres;
                                this.direccionEnvio.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.sTipoDoc == 'RUC')
                            {
                                this.direccionEnvio.sRazon = respuesta.data.razonSocial;
                                this.direccionEnvio.sDepartamento = respuesta.data.departamento;
                                this.direccionEnvio.sProvincia = respuesta.data.provincia;
                                this.direccionEnvio.sDistrito = respuesta.data.distrito;
                                this.direccionEnvio.sDireccion = respuesta.data.direccion;
                            }
                            if(this.sTipoDoc == '')
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
                                toastr.warning('Seleccione tipo de documento');
                            }
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
                            toastr.warning('No se encontraron resultados');
                        }
                        this.iCargandoConsultaApi = 0;
                    })
                    .then(() => this.iCargandoConsultaApi = 0);
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
                toastr.error(mensaje);
            }
        },

        ajaxConsultaApir: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;
            if(this.rTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.datosRecojo.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.sTipoDoc == 'DNI' && this.datosRecojo.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(verifica)
            {
                this.iCargandoConsultaApir = 1;
                formData.append('tipo_documento',this.rTipoDoc);
                formData.append('documento',this.datosRecojo.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.rTipoDoc == 'DNI')
                            {
                                this.datosRecojo.sNombres = respuesta.data.nombres;
                                this.datosRecojo.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.rTipoDoc == '')
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
                                toastr.warning('Seleccione tipo de documento');
                            }
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
                            toastr.warning('No se encontraron resultados');
                        }
                        this.iCargandoConsultaApir = 0;
                    })
                    .then(() => this.iCargandoConsultaApir = 0);
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
                toastr.error(mensaje);
            }
        },

        ajaxConsultaApid: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;
            if(this.dTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.datosDelivery.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.dTipoDoc == 'DNI' && this.datosDelivery.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(verifica)
            {
                this.iCargandoConsultaApid = 1;
                formData.append('tipo_documento',this.dTipoDoc);
                formData.append('documento',this.datosDelivery.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.dTipoDoc == 'DNI')
                            {
                                this.datosDelivery.sNombres = respuesta.data.nombres;
                                this.datosDelivery.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.dTipoDoc == '')
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
                                toastr.warning('Seleccione tipo de documento');
                            }
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
                            toastr.warning('No se encontraron resultados');
                        }
                        this.iCargandoConsultaApid = 0;
                    })
                    .then(() => this.iCargandoConsultaApid = 0);
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
                toastr.error(mensaje);
            }
        },

        ajaxListarPreciosEnvio: function () {
            let $this = this;
            axios.get('/facturacion-envio/ajax/listarPreciosEnvio').then(response => {
                $this.lstPreciosEnvioNacional = response.data.data.lstPreciosEnvio.filter(direccion => direccion.provincia !== 'TRUJILLO');
                $this.lstPreciosDelivery = response.data.data.lstPreciosEnvio.filter(tarifa => tarifa.provincia === 'TRUJILLO')
            });
        },

        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },

        confirmarDireccionEnvio: function () {
            this.iDireccionEnvioEstablecida = 1;
            //$cookies.set('direccionEnvio', this.direccionEnvio, 12);
            $('#modalEditarDireccionEnvio').modal('hide');
        },

        confirmarRecojo: function () {
            this.iRecojoEstablecido = 1;
            //$cookies.set('Recojo', this.Recojo, 12);
            $('#modalEditarRecojo').modal('hide');
        },

        confirmarDelivery: function () {
            this.iDeliveryEstablecido = 1;
            //$cookies.set('Recojo', this.Recojo, 12);
            $('#modalEditarDelivery').modal('hide');
        },

        ajaxListarDatosFacturacion: function () {
            let $this = this;
            $this.iCargandoDatosFacturacion = 1;

            axios.post('/facturacion-envio/ajax/listarDatosFacturacion').then(response => {
                let respuesta = response.data;
                if (respuesta.result === result.success) {
                    let data = respuesta.data;
                    $this.lstTiposComprobante = data.lstTiposComprobante;
                    $this.lstUbigeo = data.lstUbigeo;
                    /*if ($this.lstTiposComprobante.length > 0) {
                        $this.formData.iTipoComprobanteId = $this.lstTiposComprobante[0].id;
                    }*/

                    if (data.cliente) {
                        let cliente = data.cliente;
                        let persona = cliente.persona;
                        $this.direccionEnvio.sNombres = persona.nombres;
                        $this.direccionEnvio.sApellidos = persona.apellido_1 + ' ' + persona.apellido_2;
                        $this.direccionEnvio.sDireccion = cliente.direccion;
                        $this.direccionEnvio.sCorreo = cliente.correo;

                        if (cliente.ubigeo) {
                            let ubigeo = cliente.ubigeo;
                            $this.direccionEnvio.sDepartamento = ubigeo.departamento;
                            $this.direccionEnvio.sProvincia = ubigeo.provincia;
                            $this.direccionEnvio.sDistrito = ubigeo.distrito;
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
        },

    }
});
