let vueRegistro = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        lstCarritoCompras: [],
        sTipoDocumento: 'DNI', //1
        sMensaje: '',
        sPassword: '',
        sDocumento: '',
        sNombres: '',
        sApellidos: '',
        sCPassword: '',
        lstUbigeo: [],
        sDepartamento: '',
        sProvincia: '',
        sDistrito: '',
        iRegistrando: 0,
        iConsultandoApi: 0,
    },
    computed: {
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
            if(this.sDepartamento == '')
            {
                this.sProvincia = '';
            }
            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamento);
            let lst = [];
            for (let ubigeo of lstUbigeoFiltrado) {
                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                    lst.push(ubigeo.provincia);
                }
            }
            return lst;
        },
        lstDistritos: function () {
            if(this.sProvincia == '')
            {
                this.sDistrito = '';
            }
            return this.lstUbigeo.filter(ubigeo =>
                ubigeo.departamento === this.sDepartamento
                && ubigeo.provincia === this.sProvincia);
        },
    },
    mounted: function () {
        $this = this;
        ajaxWebsiteLocale().then(response => {
            let respuesta = response.data;
            $this.locale = respuesta.data.locale;

            let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');
            let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : this.lstCarritoCompras;

            $this.lstCarritoCompras = lstCarritoCompras;
            $this.guardarLstCarritoCompras();

        }).then(() => {
            $this.ajaxListarDatos();
        })
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxRegistrar: function () {
            let $this = this;
            $this.iRegistrando = 1;
            $this.sMensaje = '';

            let frmRegistro = document.getElementById('frmRegistro');
            let formData = new FormData(frmRegistro);

            /*for( var pair of formData.entries())
            {
                console.log(pair[0]);
                console.log(pair[1]);
            }*/

            axios.post('/registro/ajax/registrar', formData)
                .then(response => {
                    console.log(response);
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        if(respuesta.data != null) location = respuesta.data;
                        else location.reload();
                    } else {
                        $this.sMensaje = sHtmlErrores(respuesta.data.errors);
                    }
                })
                .catch(error => {
                    let sHtmlMensaje = sHtmlErrores(error.responseJSON.errors);
                    $this.sMensaje = sHtmlMensaje;
                })
                .then(() => $this.iRegistrando = 0);
        },
        ajaxListarDatos: function() {
            axios.get('/registro/ajax/listarDatos')
                .then(response => {
                    let respuesta = response.data;
                    this.lstUbigeo = respuesta.data.lstUbigeo;
                })
        },
        ajaxConsultaApi: function(){
            let formData = new FormData();
            var mensaje = '';
            var verifica = true;
            if(this.sTipoDocumento== '')
            {
                verifica = false;
                mensaje = 'Seleccionar tipo de documento';
            }

            if(this.sDocumento == '')
            {
                verifica = false;
                mensaje = 'Ingrese documento';
            }

            if(this.sTipoDocumento == 'DNI' && this.sDocumento.length != 8)
            {
                verifica = false;
                mensaje = 'DNI NO VÁLIDO';
            }

            if(this.sTipoDocumento == 'RUC' && this.sDocumento.length != 11)
            {
                verifica = false;
                mensaje = 'RUC NO VÁLIDO';
            }

            if(verifica)
            {
                this.iConsultandoApi = 1;
                formData.append('tipo_documento',this.sTipoDocumento);
                formData.append('documento',this.sDocumento);
                axios.post('/facturacion-envio/ajax/consultaApi', formData)
                    .then(response => {
                        let respuesta = response.data;
                        if (respuesta.result === result.success) {
                            if(this.sTipoDocumento == 'DNI')
                            {
                                this.sNombres = respuesta.data.nombres;
                                this.sApellidos = respuesta.data.apellidoPaterno + ' ' + respuesta.data.apellidoMaterno;
                            }

                            if(this.sTipoDocumento == 'RUC')
                            {
                                this.sNombres = respuesta.data.razonSocial;
                                /*this.sDepartamento = respuesta.data.departamento;
                                this.sProvincia = respuesta.data.provincia;
                                this.sDistrito = respuesta.data.distrito;
                                this.sDireccion = respuesta.data.direccion;*/
                            }

                            if(this.sTipoDocumento == '')
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
                        this.iConsultandoApi = 0;
                    })
                    .then(() => this.iConsultandoApi = 0);
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
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
    }
});