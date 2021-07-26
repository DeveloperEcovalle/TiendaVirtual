let vueFacturacionEnvio = new Vue({
    el: '#content',
    data: {
        iCargando: 1,
        locale: 'es',
        lstCarritoCompras: [],
        sBuscar:'',
        sBuscard:'',

        iCargandoDatosFacturacion: 0,
        lstUbigeo: [],
        lstTiposComprobante: [],

        lstPreciosEnvioNacional: [],
        lstPreciosDelivery: [],
        lstAgencias: [],

        datosEnvio: {
            sCabecera: 'NN',
            sTipoDoc: '',
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sEmail: '',
            sTelefono: '',
            sDepartamento: '',
            sProvincia: '',
            sDistrito: '',
            sDireccion: '',
            sAgencia: '',
            sTipoComprobante: '',
            sRecoge: {
                sDocumento: '',
                sRazonSocial: '',
                sTelefono: '',
            },
            sUbigeo: '',
            sOpcion: 0,
        },
        eatermcond: [],

        datosRecojo: {
            sCabecera: 'RT',
            rTipoDoc: '',
            sDocumento: '',
            sNombres: '',
            sApellidos: '',
            sEmail: '',
            sTelefono: '',
            sTipoComprobante: '',
            sOpcion: 0,
        },
        ratermcond: [],
        
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
            sTipoComprobante: '',
            sOpcion: 0,
        }, 
        datermcond: [],

        iDireccionEnvioEstablecida: 0,
        iDireccionEnvioConfirmada: 0,

        iDeliveryEstablecido: 0,
        iDeliveryConfirmado: 0,

        iRecojoEstablecido: 0,
        iRecojoConfirmado: 0,

        rTipoDoc:'',
        bClienteEnSesion: null,
        iCargandoConsultaApir: 0,

        dTipoDoc:'',
        iCargandoConsultaApid: 0,

        sTipoDoc:'',
        iCargandoConsultaApi: 0,
        sMensajeError: '',

        iCargandoConsultaApiRecoge: 0,

        sLocation: 'E',
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
            return this.datosEnvio.sTelefono.trim().length > 0
                && this.datosEnvio.sEmail.trim().length > 0
                && this.datosEnvio.sDepartamento.trim().length > 0
                && this.datosEnvio.sProvincia.trim().length > 0
                && this.datosEnvio.sDistrito.trim().length > 0
                && this.datosEnvio.sDireccion.trim().length > 0
                && this.datosEnvio.sRecoge.sRazonSocial.trim().length > 0
                && this.datosEnvio.sRecoge.sDocumento.trim().length > 0
                && this.datosEnvio.sRecoge.sTelefono.trim().length > 0
                && this.datosEnvio.sTipoDoc.trim().length > 0
                && this.datosEnvio.sAgencia.trim().length > 0
                && this.datosEnvio.sNombres.trim().length > 0;
        },
        bRecojoValida: function () {
            return this.datosRecojo.sDocumento.trim().length > 0
                && this.datosRecojo.rTipoDoc.trim().length > 0
                && this.datosRecojo.sNombres.trim().length > 0
                && this.datosRecojo.sApellidos.trim().length > 0
                && this.datosRecojo.sTelefono.trim().length > 0
                && this.datosRecojo.sEmail.trim().length > 0;
        },
        bDeliveryValida: function () {
            return this.datosDelivery.sDocumento.trim().length > 0
                && this.datosDelivery.dTipoDoc.trim().length > 0
                && this.datosDelivery.sNombres.trim().length > 0
                && this.datosDelivery.sEmail.trim().length > 0
                && this.datosDelivery.sTelefono.trim().length > 0
                && this.datosDelivery.sDireccion.trim().length > 0
                && this.datosDelivery.sDepartamento.trim().length > 0
                && this.datosDelivery.sProvincia.trim().length > 0
                && this.datosDelivery.sDistrito.trim().length > 0;

        },
        bVerificaDniE: function () {
            if(this.datosEnvio.sTipoDoc == 'DNI')
            {
                if(this.datosEnvio.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaDni: function() {
            if(this.datosEnvio.sTipoDoc == 'DNI')
            {
                if(this.datosEnvio.sApellidos.trim().length > 0 && this.datosEnvio.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaRuc: function(){
            if(this.datosEnvio.sTipoDoc == 'RUC')
            {
                if(this.datosEnvio.sDocumento.trim().length == 11) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaDniR: function () {
            if(this.datosRecojo.rTipoDoc == 'DNI')
            {
                if(this.datosRecojo.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaDniRecojo: function() {
            if(this.datosRecojo.rTipoDoc == 'DNI')
            {
                if(this.datosRecojo.sApellidos.trim().length > 0 && this.datosRecojo.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaRucRecojo: function(){
            if(this.datosRecojo.rTipoDoc == 'RUC')
            {
                if(this.datosRecojo.sDocumento.trim().length == 11) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaDniD: function () {
            if(this.datosDelivery.dTipoDoc == 'DNI')
            {
                if(this.datosDelivery.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaDniDelivery: function() {
            if(this.datosDelivery.dTipoDoc == 'DNI')
            {
                if(this.datosDelivery.sApellidos.trim().length > 0 && this.datosDelivery.sDocumento.trim().length == 8) return 1;
                else return 0;
            }
            else return 1;
        },
        bVerificaRucDelivery: function(){
            if(this.datosDelivery.dTipoDoc == 'RUC')
            {
                if(this.datosDelivery.sDocumento.trim().length == 11) return 1;
                else return 0;
            }
            else return 1;
        },
        fDelivery: function () {
            if(this.sLocation == 'E')
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

            if(this.sLocation == 'D')
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
            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.datosEnvio.sDepartamento);
            let lst = [];
            for (let ubigeo of lstUbigeoFiltrado) {
                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1  && ubigeo.estado === 'ACTIVO' && ubigeo.provincia != 'TRUJILLO') {
                    lst.push(ubigeo.provincia);
                }
            }
            if(this.datosEnvio.sDepartamento == '')
            {
                this.datosEnvio.sProvincia = '';
                this.datosEnvio.sProvincia = '';
            }
            return lst;
        },
        lstDistritos: function () {
            if(this.datosEnvio.sProvincia == '')
            {
                this.datosEnvio.sDistrito = '';
                this.datosEnvio.sDistrito = '';
            }
            return this.lstUbigeo.filter(ubigeo =>
                ubigeo.departamento === this.datosEnvio.sDepartamento
                && ubigeo.provincia === this.datosEnvio.sProvincia
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
        },
        bComprobanteEnvio: function(){
            return this.datosEnvio.sTipoComprobante.trim().length > 0;
        },
        bComprobanteRecojo: function(){
            return this.datosRecojo.sTipoComprobante.trim().length > 0;
        },
        bComprobanteDelivery: function(){
            return this.datosDelivery.sTipoComprobante.trim().length > 0;
        },
        bAgencia: function() {
            let $this = this;
            if($this.lstAgencias.length > 0)
            {
                if($this.datosEnvio.sAgencia != '')
                {
                    let iIndice = $this.lstAgencias.findIndex(agencia => agencia.nombre == $this.datosEnvio.sAgencia);
                    let agencia = $this.lstAgencias[iIndice];
                    return agencia.descripcion;
                }
            }
            return '';
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
            let bClienteEnSesion = data.bClienteEnSesion;
            $this.bClienteEnSesion = bClienteEnSesion;
            if(bClienteEnSesion == null)
            {
                localStorage.removeItem('datosEnvio');
                localStorage.removeItem('datosRecojo');
                localStorage.removeItem('datosDelivery');
                $('#modalInicioSesion').modal('show'); 
            }
            else{
                let sApellido_2 = bClienteEnSesion.apellido_2 === null ? '' : bClienteEnSesion.apellido_2;
                let cookiedatosEnvio = $cookies.get('datosEnvio');
                if(cookiedatosEnvio)
                {
                    $this.datosEnvio = cookiedatosEnvio;
                }else{
                    $this.datosEnvio.sTipoDoc = bClienteEnSesion.tipo_documento;
                    $this.datosEnvio.sDocumento = bClienteEnSesion.documento;
                    $this.datosEnvio.sNombres = bClienteEnSesion.nombres;
                    $this.datosEnvio.sApellidos = bClienteEnSesion.apellido_1 + ' ' + sApellido_2;
                    $this.datosEnvio.sEmail = bClienteEnSesion.correo;
                    $this.datosEnvio.sTelefono = bClienteEnSesion.telefono;
                    $this.datosEnvio.sUbigeo = bClienteEnSesion.ubigeo_id;
                    $this.datosEnvio.sDireccion = bClienteEnSesion.direccion;

                    $this.datosEnvio.sRecoge.sRazonSocial = bClienteEnSesion.nombres + ' ' + bClienteEnSesion.apellido_1 + ' ' + sApellido_2;
                    $this.datosEnvio.sRecoge.sDocumento = bClienteEnSesion.documento;
                    $this.datosEnvio.sRecoge.sTelefono = bClienteEnSesion.telefono;
                }
                $this.datosEnvio.sOpcion = 1;

                let cookiedatosRecojo = $cookies.get('datosRecojo');
                if(cookiedatosRecojo)
                {
                    $this.datosRecojo = cookiedatosRecojo;
                }else{
                    $this.datosRecojo.rTipoDoc = bClienteEnSesion.tipo_documento;
                    $this.datosRecojo.sDocumento = bClienteEnSesion.documento;
                    $this.datosRecojo.sNombres = bClienteEnSesion.nombres;
                    $this.datosRecojo.sApellidos = bClienteEnSesion.apellido_1 + ' ' + sApellido_2;
                    $this.datosRecojo.sEmail = bClienteEnSesion.correo;
                    $this.datosRecojo.sTelefono = bClienteEnSesion.telefono;
                }
                $this.datosRecojo.sOpcion = 0;

                let cookiedatosDelivery = $cookies.get('datosDelivery');
                if(cookiedatosDelivery)
                {
                    $this.datosDelivery = cookiedatosDelivery;
                }else{
                    $this.datosDelivery.dTipoDoc = bClienteEnSesion.tipo_documento;
                    $this.datosDelivery.sDocumento = bClienteEnSesion.documento;
                    $this.datosDelivery.sNombres = bClienteEnSesion.nombres;
                    $this.datosDelivery.sApellidos = bClienteEnSesion.apellido_1 + ' ' + sApellido_2;
                    $this.datosDelivery.sEmail = bClienteEnSesion.correo;
                    $this.datosDelivery.sDireccion = bClienteEnSesion.direccion;
                    $this.datosDelivery.sTelefono = bClienteEnSesion.telefono;
                }
                $this.datosDelivery.sOpcion = 0;

                $this.guardarCookieDatos();
            }

            let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
            let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

            $this.lstCarritoCompras = lstCarritoCompras;

            $this.ajaxListarPreciosEnvio();
            $this.ajaxListarDatosFacturacion();
            //$('#modalEditarDireccionEnvio').modal('show'); 
        }).then(() => {
            $this.guardarLstCarritoCompras();
            if(this.bClienteEnSesion != null)
            {
                let cookiedatosEnvio = $cookies.get('datosEnvio');
                cookiedatosEnvio.sDocumento != '' ? this.iDireccionEnvioEstablecida = 1 : this.iDireccionEnvioEstablecida = 0;
    
                let cookiedatosRecojo = $cookies.get('datosRecojo');
                cookiedatosRecojo.sDocumento != '' ? this.iRecojoEstablecido = 1 : this.iRecojoEstablecido = 0;
    
                let cookiedatosDelivery = $cookies.get('datosDelivery');
                cookiedatosDelivery.sDocumento != '' ? this.iDeliveryEstablecido = 1 : this.iDeliveryEstablecido = 0;
            }

            if ($this.lstCarritoCompras.length === 0 || !this.fVentaValida) {
                location = '/carrito-compras';
            }
        }).then(() => {
            this.verificaDatos();
            $this.iCargando = 0;
        }));
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxConsultaApi: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;
            if(this.datosEnvio.sTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.datosEnvio.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.datosEnvio.sTipoDoc == 'DNI' && this.datosEnvio.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(this.datosEnvio.sTipoDoc == 'RUC' && this.datosEnvio.sDocumento.length < 11)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de ruc';
            }

            if(verifica)
            {
                this.iCargandoConsultaApi = 1;
                formData.append('tipo_documento',this.datosEnvio.sTipoDoc);
                formData.append('documento',this.datosEnvio.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.datosEnvio.sTipoDoc == 'DNI')
                            {
                                this.datosEnvio.sNombres = respuesta.data.nombres;
                                this.datosEnvio.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.datosEnvio.sTipoDoc == 'RUC')
                            {
                                this.datosEnvio.sNombres = respuesta.data.razonSocial;
                                /*this.datosEnvio.sDepartamento = respuesta.data.departamento;
                                this.datosEnvio.sProvincia = respuesta.data.provincia;
                                this.datosEnvio.sDistrito = respuesta.data.distrito;
                                this.datosEnvio.sDireccion = respuesta.data.direccion;*/
                            }
                            if(this.datosEnvio.sTipoDoc == '')
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
            if(this.datosRecojo.rTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.datosRecojo.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.datosRecojo.rTipoDoc == 'DNI' && this.datosRecojo.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(verifica)
            {
                this.iCargandoConsultaApir = 1;
                formData.append('tipo_documento',this.datosRecojo.rTipoDoc);
                formData.append('documento',this.datosRecojo.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.datosRecojo.rTipoDoc == 'DNI')
                            {
                                this.datosRecojo.sNombres = respuesta.data.nombres;
                                this.datosRecojo.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.datosRecojo.rTipoDoc == 'RUC')
                            {
                                this.datosRecojo.sNombres = respuesta.data.razonSocial;
                            }
                            if(this.datosRecojo.rTipoDoc == '')
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
            if(this.datosDelivery.dTipoDoc == '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.datosDelivery.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.datosDelivery.dTipoDoc == 'DNI' && this.datosDelivery.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(verifica)
            {
                this.iCargandoConsultaApid = 1;
                formData.append('tipo_documento',this.datosDelivery.dTipoDoc);
                formData.append('documento',this.datosDelivery.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.datosDelivery.dTipoDoc == 'DNI')
                            {
                                this.datosDelivery.sNombres = respuesta.data.nombres;
                                this.datosDelivery.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }
                            if(this.datosDelivery.dTipoDoc == 'RUC')
                            {
                                this.datosDelivery.sNombres = respuesta.data.razonSocial;
                            }
                            if(this.datosDelivery.dTipoDoc == '')
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
        ajaxConsultaApiRecoge: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;

            if(this.datosEnvio.sRecoge.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.datosEnvio.sRecoge.sDocumento.length < 8)
            {
                verifica = false;
                mensaje = 'Faltan digitos al número de dni';
            }

            if(verifica)
            {
                this.iCargandoConsultaApiRecoge = 1;
                formData.append('tipo_documento','DNI');
                formData.append('documento',this.datosEnvio.sRecoge.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            this.datosEnvio.sRecoge.sRazonSocial = respuesta.data.nombres + ' ' + respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
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
                    })
                    .then(() => this.iCargandoConsultaApiRecoge = 0);
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
                $this.lstPreciosDelivery = response.data.data.lstPreciosEnvio.filter(tarifa => tarifa.provincia === 'TRUJILLO');
                $this.lstAgencias = response.data.data.lstAgencias;
            }).then(() => {
                let iIndice = this.lstPreciosEnvioNacional.findIndex(ubigeo => ubigeo.id === this.datosEnvio.sUbigeo);
                let ubigeo = $this.lstPreciosEnvioNacional[iIndice];
                if(ubigeo)
                {
                    this.datosEnvio.sDepartamento = ubigeo.departamento;
                    this.datosEnvio.sProvincia = ubigeo.provincia;
                    this.datosEnvio.sDistrito = ubigeo.distrito;
                }
            });
        },
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
        confirmarDireccionEnvio: function () {
            this.iDireccionEnvioEstablecida = 1;
            this.iDireccionEnvioConfirmada = 1;
            $cookies.set('datosEnvio', this.datosEnvio, 12);
            $('#modalEditarDireccionEnvio').modal('hide');
        },
        confirmarRecojo: function () {
            this.iRecojoEstablecido = 1;
            this.iRecojoConfirmado = 1;
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $('#modalEditarRecojo').modal('hide');
        },
        confirmarDelivery: function () {
            this.iDeliveryEstablecido = 1;
            this.iDeliveryConfirmado = 1;
            $cookies.set('datosDelivery', this.datosDelivery, 12);
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
                        $this.datosEnvio.sNombres = persona.nombres;
                        $this.datosEnvio.sApellidos = persona.apellido_1 + ' ' + persona.apellido_2;
                        $this.datosEnvio.sDireccion = cliente.direccion;
                        $this.datosEnvio.sEmail = cliente.correo;

                        if (cliente.ubigeo) {
                            let ubigeo = cliente.ubigeo;
                            $this.datosEnvio.sDepartamento = ubigeo.departamento;
                            $this.datosEnvio.sProvincia = ubigeo.provincia;
                            $this.datosEnvio.sDistrito = ubigeo.distrito;
                        }
                    }
                }
            }).then(() => $this.iCargandoDatosFacturacion = 0);
        },
        guardarCookieDatos: function(){
            $cookies.set('datosEnvio', this.datosEnvio, 12);
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $cookies.set('datosDelivery', this.datosDelivery, 12);
        },
        confirmarFacturacion: function(tipoCompra){
            if(tipoCompra == 'E')
            {
                this.datosDelivery.sOpcion = 0;
                this.datosRecojo.sOpcion = 0;
                this.datosEnvio.sOpcion = 1;
            }
            else if(tipoCompra == 'R')
            {
                this.datosDelivery.sOpcion = 0;
                this.datosRecojo.sOpcion = 1;
                this.datosEnvio.sOpcion = 0;
            }
            else{
                this.datosDelivery.sOpcion = 1;
                this.datosRecojo.sOpcion = 0;
                this.datosEnvio.sOpcion = 0;
            }
            $cookies.set('datosEnvio', this.datosEnvio, 12);
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $cookies.set('datosDelivery', this.datosDelivery, 12);
            location = '/pago-envio';
        },
        verificaDatos: function(){
            if(this.bDireccionEnvioValida && (this.bVerificaDni || this.bVerificaRuc))
            {
                this.iDireccionEnvioConfirmada = 1;
            }
            if(this.bRecojoValida)
            {
                this.iRecojoConfirmado = 1;
            }
            if(this.bDeliveryValida)
            {
                this.iDeliveryConfirmado = 1;
            }
        },
        fnComprobanteEnvio: function(value)
        {
            this.datosEnvio.sTipoComprobante = value;
        },
        fnComprobanteRecojo: function(value)
        {
            this.datosRecojo.sTipoComprobante = value;
        },
        fnComprobanteRecojo: function(value)
        {
            this.datosDelivery.sTipoComprobante = value;
        }
    },
});
