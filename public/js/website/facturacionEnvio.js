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
        lstAgencias: [],

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
            sAgencia: '',
            sUbigeo: '',
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
                && this.datosEnvio.sTipoDoc.trim().length > 0
                && this.datosEnvio.sAgencia.trim().length > 0
                && this.datosEnvio.sDocumento.trim().length > 0;
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
                && this.datosDelivery.sApellidos.trim().length > 0
                && this.datosDelivery.sEmail.trim().length > 0
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
            return this.datosEnvio.sTipoDoc == 'DNI' && this.datosEnvio.sNombres.trim().length > 0 && this.datosEnvio.sApellidos.trim().length > 0;
        },
        bVerificaRuc: function(){
            return this.datosEnvio.sTipoDoc == 'RUC' && this.datosEnvio.sRazon.trim().length > 0
        },
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
            let sApellido_2 = bClienteEnSesion.apellido_2 === null ? '' : bClienteEnSesion.apellido_2;
            if(bClienteEnSesion == null)
            {
                localStorage.removeItem('datosEnvio');
                localStorage.removeItem('datosRecojo');
                localStorage.removeItem('datosDelivery');
                $('#modalInicioSesion').modal('show'); 
            }
            else{
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

                // let cookiedatosDelivery = $cookies.get('datosDelivery');
                // let datosDelivery = cookiedatosDelivery ? cookiedatosDelivery : this.datosDelivery;
                // $this.datosDelivery = datosDelivery;
                // $this.datosDelivery.sOpcion = 0;
                $this.guardarCookieDatos();
            }

            let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
            let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

            /*let cookiedatosEnvio = $cookies.get('datosEnvio');
            let datosEnvio = cookiedatosEnvio ? cookiedatosEnvio : this.datosEnvio;
            $this.datosEnvio = datosEnvio;
            $this.datosEnvio.sOpcion = 1;

            let cookiedatosRecojo = $cookies.get('datosRecojo');
            let datosRecojo = cookiedatosRecojo ? cookiedatosRecojo : this.datosRecojo;
            $this.datosRecojo = datosRecojo;
            $this.datosRecojo.sOpcion = 0;

            let cookiedatosDelivery = $cookies.get('datosDelivery');
            let datosDelivery = cookiedatosDelivery ? cookiedatosDelivery : this.datosDelivery;
            $this.datosDelivery = datosDelivery;
            $this.datosDelivery.sOpcion = 0;*/

            $this.lstCarritoCompras = lstCarritoCompras;
            
            $this.guardarLstCarritoCompras();
            $this.ajaxListarPreciosEnvio();
            
            $this.iCargando = 0;
            $this.ajaxListarDatosFacturacion();
            //$('#modalEditarDireccionEnvio').modal('show'); 
        }).then(() => {
            if(this.bClienteEnSesion != null)
            {
                let cookiedatosEnvio = $cookies.get('datosEnvio');
                cookiedatosEnvio.sDocumento != '' ? this.iDireccionEnvioEstablecida = 1 : this.iDireccionEnvioEstablecida = 0;
    
                let cookiedatosRecojo = $cookies.get('datosRecojo');
                cookiedatosRecojo.sDocumento != '' ? this.iRecojoEstablecido = 1 : this.iRecojoEstablecido = 0;
    
                let cookiedatosDelivery = $cookies.get('datosDelivery');
                cookiedatosDelivery.sDocumento != '' ? this.iDeliveryEstablecido = 1 : this.iDeliveryEstablecido = 0;
            }

            if ($this.lstCarritoCompras.length === 0) {
                location = '/carrito-compras';
            }
        }).then(()=>{
            this.verificaDatos();
        }));
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        sDeliveryFn: function(){
            this.sDelivery = 0;
            this.sNNacional = 1;
            this.sRTienda = 1;

            this.datosDelivery.sOpcion = 1;
            this.datosRecojo.sOpcion = 0;
            this.datosEnvio.sOpcion = 0;

            $cookies.set('datosDelivery', this.datosDelivery, 12);
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $cookies.set('datosEnvio', this.datosEnvio, 12);

            //$('#modalEditarDelivery').modal('show');
        },
        sNNacionalFn: function(){
            this.sNNacional = 0;
            this.sDelivery = 1;
            this.sRTienda = 1;

            this.datosDelivery.sOpcion = 0;
            this.datosRecojo.sOpcion = 0;
            this.datosEnvio.sOpcion = 1;

            $cookies.set('datosDelivery', this.datosDelivery, 12);
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $cookies.set('datosEnvio', this.datosEnvio, 12);

            //$('#modalEditarDireccionEnvio').modal('show'); 
        },
        sRTiendaFn: function(){
            this.sDelivery = 1;
            this.sRTienda = 0;
            this.sNNacional = 1;

            this.datosDelivery.sOpcion = 0;
            this.datosRecojo.sOpcion = 1;
            this.datosEnvio.sOpcion = 0;

            $cookies.set('datosDelivery', this.datosDelivery, 12);
            $cookies.set('datosRecojo', this.datosRecojo, 12);
            $cookies.set('datosEnvio', this.datosEnvio, 12);

            //$('#modalEditarRecojo').modal('show');
        },
        cambiarTipoDoc: function(){
            switch (this.datosEnvio.sTipoDoc) {
                case 'DNI':
                    $('#documento').removeAttr('minlength','11');
                    $('#documento').removeAttr('maxlength','11');
                    $('#documento').attr('minlength','8');
                    $('#documento').attr('maxlength','8');
                    $('#razon_social').attr('required',false);
                    $('#nombres').attr('required',true);
                    $('#apellidos').attr('required',true);
                    this.datosEnvio.sDocumento = '';
                    this.datosEnvio.sNombres = '';
                    this.datosEnvio.sApellidos = '';
                    this.datosEnvio.sRazon = '';
                    break;
                case 'RUC':
                    $('#documento').removeAttr('minlength','8');
                    $('#documento').removeAttr('maxlength','8');
                    $('#documento').attr('minlength','11');
                    $('#documento').attr('maxlength','11');
                    $('#razon_social').attr('required',true);
                    $('#nombres').attr('required',false);
                    $('#apellidos').attr('required',false);
                    this.datosEnvio.sDocumento = '';
                    this.datosEnvio.sNombres = '';
                    this.datosEnvio.sApellidos = '';
                    this.datosEnvio.sRazon = '';
                    break;
                default:
                    $('#documento').removeAttr('minlength','11');
                    $('#documento').removeAttr('maxlength','11');
                    $('#documento').attr('minlength','8');
                    $('#documento').attr('maxlength','8');
                    this.datosEnvio.sDocumento = '';
                    this.datosEnvio.sNombres = '';
                    this.datosEnvio.sApellidos = '';
                    this.datosEnvio.sRazon = '';
                    break;
            }
        },
        rcambiarTipoDoc: function(){
            switch (this.datosRecojo.rTipoDoc) {
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
            switch (this.datosDelivery.dTipoDoc) {
                case 'DNI':
                    $('#ddocumento').removeAttr('minlength','11');
                    $('#ddocumento').removeAttr('maxlength','11');
                    $('#ddocumento').attr('minlength','8');
                    $('#ddocumento').attr('maxlength','8');
                    this.datosDelivery.sDocumento = '';
                    this.datosDelivery.sNombres = '';
                    this.datosDelivery.sApellidos = '';
                    break;
                default:
                    $('#ddocumento').removeAttr('minlength','11');
                    $('#ddocumento').removeAttr('maxlength','11');
                    $('#ddocumento').attr('minlength','8');
                    $('#ddocumento').attr('maxlength','8');
                    this.datosDelivery.sDocumento = '';
                    this.datosDelivery.sNombres = '';
                    this.datosDelivery.sApellidos = '';
                    break;
            }
        },
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
                                this.datosEnvio.sRazon = respuesta.data.razonSocial;
                                this.datosEnvio.sDepartamento = respuesta.data.departamento;
                                this.datosEnvio.sProvincia = respuesta.data.provincia;
                                this.datosEnvio.sDistrito = respuesta.data.distrito;
                                this.datosEnvio.sDireccion = respuesta.data.direccion;
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
        ajaxListarPreciosEnvio: function () {
            let $this = this;
            axios.get('/facturacion-envio/ajax/listarPreciosEnvio').then(response => {
                $this.lstPreciosEnvioNacional = response.data.data.lstPreciosEnvio.filter(direccion => direccion.provincia !== 'TRUJILLO');
                $this.lstPreciosDelivery = response.data.data.lstPreciosEnvio.filter(tarifa => tarifa.provincia === 'TRUJILLO');
                $this.lstAgencias = response.data.data.lstAgencias;
                console.log(response.data.data);
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
        confirmarFacturacion: function(){
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
        }
    }
});
