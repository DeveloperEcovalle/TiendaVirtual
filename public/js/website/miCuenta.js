/*let pagina = lstUrlParams.get('pagina');
let iPaginaSeleccionada = pagina === null ? 0 : parseInt(pagina);

let iMenu = lstUrlParams.get('menu');
let iMenuSeleccionado = iMenu === null ? 0 : parseInt(iMenu);*/

let culqi = function () {
    if (Culqi.token) { // ¡Objeto Token creado exitosamente!
        let sToken = Culqi.token.id;
        if(vueMiCuentaLista.iIdPedidoSeleccionado != 0 && vueMiCuentaLista.iIdPedidoSeleccionado != '')
        {
            let iIndice = vueMiCuentaLista.lstPedidos.findIndex((order) => order.id === parseInt(vueMiCuentaLista.iIdPedidoSeleccionado));
            let order = Object.assign({}, vueMiCuentaLista.lstPedidos[iIndice]);
            let fTotalCulqi = Math.round(((order.subtotal * 100) + (order.delivery * 100)) * 10) / 10;
            let sEmail = order.email;
            let iId = order.id;
            let formData = new FormData();
            formData.append('token', sToken);
            formData.append('amount', fTotalCulqi);
            formData.append('email', sEmail);
            vueMiCuentaLista.iPagando = 1;
            axios({
                url: '/mi-cuenta/ajax/crearCargo/'+iId,
                method: 'post',
                data: formData
            })
            .then(response => {
                let rpta = response.data;
                if(rpta.result == 'success')
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
                    toastr.info(rpta.mensaje);
                    vueMiCuentaLista.panelOrders();

                    vueMiCuentaLista.iPagando = 0;
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
                    toastr.error(rpta.mensaje);
                    vueMiCuentaLista.panelOrders();
                    vueMiCuentaLista.iPagando = 0;
                }
            })
            .catch(error => {
                vueMiCuentaLista.iPagando = 0;
                console.log(error);
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
                vueMiCuentaLista.panelOrders();
            });
        }
        else
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
            toastr.error('Ocurrio un error por favor, por favor recargar la pagina o elegir nuevamente la compra a pagar');
}

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

let vueMiCuentaLista = new Vue({
    el: '#content',
    data: {
        locale: 'es',

        iMenuSeleccionado: 0,
        bClienteEnSesion : {},
        lstPedidos: [],
        iIdPedidoSeleccionado: 0,
        iPagando: 0,

        iCargandoPanel: 0,
        lstCarritoCompras: []
    },
    created: function () {
        this.ajaxListar(this.cargarPanel);
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListar: function(onSuccess){
            let $this = this;
            ajaxWebsiteLocale()
                .then(response => {
                    let respuesta = response.data;
                    $this.locale = respuesta.data.locale;
                }).then(() => {
                    ajaxWebsiteListarCarritoCompras().then(response => {
                        let respuesta = response.data;
                        let data = respuesta.data;
                        let bClienteEnSesion = data.bClienteEnSesion;
                        if(bClienteEnSesion == null)
                        {
                            location = '/index';
                        }

                        $this.bClienteEnSesion = bClienteEnSesion;
                        let sApellido_2 = bClienteEnSesion.apellido_2 === null ? '' : bClienteEnSesion.apellido_2;
                        $this.bClienteEnSesion['apellidos'] = bClienteEnSesion.apellido_1 + ' ' + sApellido_2;
                        let lstCarritoComprasServer = data.lstCarrito;
                        //let bClienteEnSesion = data.bClienteEnSesion;

                        let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');

                        let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

                        $this.lstCarritoCompras = lstCarritoCompras;

                        if (onSuccess) {
                            onSuccess();
                        }
                    });
                });
        },
        cargarPanel: function () {
            let $this = this;

            let iMenu = lstUrlParams.get('menu');
            let iMenuSeleccionado = iMenu === null ? 0 : parseInt(iMenu);
            switch (iMenuSeleccionado) {
                case 0: {
                    $this.panelDesk();
                    break;
                }
                case 1: {
                    $this.panelAccount();
                    break;
                }
                case 2: {
                    $this.panelAddress();
                    break;
                }
                case 3: {
                    $this.panelOrders();
                    break;
                }
                default:{
                    $this.panelDesk();
                    break;
                }
            }
        },
        panelDesk: function(onSuccess){
            $this = this;
            $this.iCargandoPanel = 1;
            $('#panel').load('/mi-cuenta/ajax/panelDesk', function () {
                let vueDesk = new Vue({
                    el: '#panel',
                    data: {
                    },
                    methods: {
                        ajaxSalir: () => ajaxSalir(),
                    }
                });
                setTimeout(() => {
                    $this.iCargandoPanel= 0;
                }, 2000);
                $this.iMenuSeleccionado = 0;
                if (onSuccess) {
                    onSuccess();
                }
                let sUrl = '/mi-cuenta?menu=' + $this.iMenuSeleccionado;
                window.history.replaceState({}, 'Ecovalle | Mi Cuenta', sUrl);
            });
        },
        panelAccount: function(){
            $this = this;
            $this.iCargandoPanel = 1;
            $('#panel').load('/mi-cuenta/ajax/panelAccount', function () {
                let vueAccount = new Vue({
                    el: '#panel',
                    data: {
                        iActualizando: 0,
                        sMensaje: '',
                        user: vueMiCuentaLista.bClienteEnSesion,
                    },
                    mounted: function () {
                        let $this = this;
                    },
                    methods: {
                        actualizarAccount: function(){
                            $this = this;
                            let formAccount = document.getElementById('frmAccount');
                            let formData = new FormData(formAccount);

                            $this.iActualizando = 1;
                            axios.post('/mi-cuenta/ajax/actualizarAccount', formData)
                            .then(response => {
                                toastr.clear();
                                toastr.options = {
                                    iconClasses: {
                                        error: 'bg-danger',
                                        info: 'bg-info',
                                        success: 'bg-success',
                                        warning: 'bg-warning',
                                    },
                                };
                                let respuesta = response.data;
                                if (respuesta.result === result.success) {
                                    localStorage.removeItem('datosEnvio');
                                    localStorage.removeItem('datosRecojo');
                                    localStorage.removeItem('datosDelivery');
                                    toastr.success(respuesta.mensaje);
                                    $('#password_actual').val('');
                                    $('#password_nueva').val('');
                                    $('#password_confirm').val('');
                                    vueMiCuentaLista.ajaxListar();
                                }
                                else if(respuesta.result === result.warning)
                                {
                                    toastr.warning(sHtmlErrores(respuesta.data.errors));
                                }
                                else {
                                    toastr.error(sHtmlErrores(respuesta.data.errors));
                                }
                            })
                            .then(() => $this.iActualizando = 0);
                        },
                        clickactionPassword: function(id,id2) {
                            var tipo = document.getElementById(id);
                            if(tipo.type == "password"){
                                $('#'+id2).removeClass('fa fa-eye');
                                $('#'+id2).addClass('fa fa-eye-slash');
                                tipo.type = "text";
                            }else{
                                $('#'+id2).removeClass('fa fa-eye-slash');
                                $('#'+id2).addClass('fa fa-eye');
                                tipo.type = "password";
                            }
                        }
                    }
                });
                setTimeout(() => {
                    $this.iCargandoPanel= 0;
                }, 2000);
                $this.iMenuSeleccionado = 1;
                let sUrl = '/mi-cuenta?menu=' + $this.iMenuSeleccionado;
                window.history.replaceState({}, 'Ecovalle | Mi Cuenta', sUrl);
            });
        },
        panelAddress: function(){
            $this = this;
            $this.iCargandoPanel = 1;
            let sDireccion  = $this.bClienteEnSesion.direccion ? $this.bClienteEnSesion.direccion : '';
            let sDepartamentoSeleccionado =  $this.bClienteEnSesion.ubigeo ?  $this.bClienteEnSesion.ubigeo.departamento : '';
            let sProvinciaSeleccionada =  $this.bClienteEnSesion.ubigeo ?  $this.bClienteEnSesion.ubigeo.provincia : '';
            let sDistritoSeleccionado =  $this.bClienteEnSesion.ubigeo ?  $this.bClienteEnSesion.ubigeo.distrito : '';
            $('#panel').load('/mi-cuenta/ajax/panelAddress', function () {
                let vueAddress = new Vue({
                    el: '#panel',
                    data: {
                        iActualizando: 0,
                        sMensaje: '',
                        user: {
                            direccion: sDireccion,
                            departamento: sDepartamentoSeleccionado,
                            provincia: sProvinciaSeleccionada,
                            distrito: sDistritoSeleccionado
                        },
                        lstUbigeo: [],
                    },
                    computed: {
                        lstDepartamentos: function () {
                            let lst = [];
                            for (let ubigeo of this.lstUbigeo) {
                                if (lst.findIndex((departamento) => departamento === ubigeo.departamento) === -1) {
                                    lst.push(ubigeo.departamento);
                                }
                            }
                            return lst;
                        },
                        lstProvincias: function () {
                            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.user.departamento);
                            let lst = [];
                            for (let ubigeo of lstUbigeoFiltrado) {
                                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                                    lst.push(ubigeo.provincia);
                                }
                            }
                            return lst;
                        },
                        lstDistritos: function () {
                            return this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.user.departamento && ubigeo.provincia === this.user.provincia);
                        },
                    },
                    mounted: function () {
                        let $this = this;
                        $this.ajaxListarDatos();
                    },
                    methods: {
                        actualizarAddress: function(){
                            $this = this;
                            let formAccount = document.getElementById('frmAddress');
                            let formData = new FormData(formAccount);

                            $this.iActualizando = 1;
                            axios.post('/mi-cuenta/ajax/actualizarAddress', formData)
                            .then(response => {
                                toastr.clear();
                                toastr.options = {
                                    iconClasses: {
                                        error: 'bg-danger',
                                        info: 'bg-info',
                                        success: 'bg-success',
                                        warning: 'bg-warning',
                                    },
                                };
                                let respuesta = response.data;
                                if (respuesta.result === result.success) {
                                    localStorage.removeItem('datosEnvio');
                                    localStorage.removeItem('datosRecojo');
                                    localStorage.removeItem('datosDelivery');
                                    toastr.success(respuesta.mensaje);
                                    vueMiCuentaLista.ajaxListar();
                                }
                                else if(respuesta.result === result.warning)
                                {
                                    toastr.warning(sHtmlErrores(respuesta.data.errors));
                                }
                                else {
                                    toastr.error(sHtmlErrores(respuesta.data.errors));
                                }
                            })
                            .then(() => $this.iActualizando = 0);
                        },
                        ajaxListarDatos: function() {
                            axios.get('/registro/ajax/listarDatos')
                                .then(response => {
                                    let respuesta = response.data;
                                    this.lstUbigeo = respuesta.data.lstUbigeo;
                                })
                        },
                    }
                });
                setTimeout(() => {
                    $this.iCargandoPanel= 0;
                }, 2000);
                $this.iMenuSeleccionado = 2;
                let sUrl = '/mi-cuenta?menu=' + $this.iMenuSeleccionado;
                window.history.replaceState({}, 'Ecovalle | Mi Cuenta', sUrl);
            });
        },
        panelOrders: function(){
            $this = this;
            $this.iCargandoPanel = 1;
            $('#panel').load('/mi-cuenta/ajax/panelOrders', function () {
                let vueOrders = new Vue({
                    el: '#panel',
                    data: {
                        iCargandoOrders: 0,
                        sBuscar: '',
                        iIdSeleccionado: 0,
                        sMensajeError: '',
                        lstOrders: [],
                    },
                    computed: {
                        lstOrdersFiltrado: function () {
                            return this.lstOrders.filter(order =>
                                order.codigo.includes(this.sBuscar)
                            );
                        },
                    },
                    mounted: function(){
                        let $this = this;
                        $this.ajaxListarDatos();
                    },
                    methods: {
                        ajaxListarDatos: function(){
                            this.iCargandoOrders = 1;
                            axios.get('/mi-cuenta/ajax/listarOrders')
                                .then(response => {
                                    let respuesta = response.data;
                                    this.lstOrders = respuesta.data.lstOrders;
                                    vueMiCuentaLista.lstPedidos = respuesta.data.lstOrders;
                                }).then(() => this.iCargandoOrders = 0);
                        },
                        panelShow: function(iId){
                            $('#pedido').load('/mi-cuenta/ajax/panelShow', function () {

                                let iIndice = vueOrders.lstOrders.findIndex((order) => order.id === parseInt(iId));
                                let order = Object.assign({}, vueOrders.lstOrders[iIndice]);
                                let vueShow = new Vue({
                                    el: '#pedido',
                                    data: {
                                        order: order,
                                    },
                                });
                                $('#modalOrder').modal('show');
                            });
                            this.iIdSeleccionado = iId;
                        },
                        mostrarModalPago: function (iId) {
                            this.sMensajeError = '';
                            let iIndice = vueOrders.lstOrders.findIndex((order) => order.id === parseInt(iId));
                            let order = Object.assign({}, vueOrders.lstOrders[iIndice]);

                            vueMiCuentaLista.iIdPedidoSeleccionado = iId;

                            let fTotalCulqi = Math.round(((order.subtotal * 100) + (order.delivery * 100)) * 10) / 10;

                            Culqi.settings({
                                title: 'Ecovalle',
                                currency: 'PEN',
                                description: 'Pedido Ecovalle',
                                amount: fTotalCulqi
                            });

                            Culqi.open();
                        },
                    }
                });
                setTimeout(() => {
                    $this.iCargandoPanel= 0;
                }, 2000);
                $this.iMenuSeleccionado = 3;
                let sUrl = '/mi-cuenta?menu=' + $this.iMenuSeleccionado;
                window.history.replaceState({}, 'Ecovalle | Mi Cuenta', sUrl);
            });
        }
    },
});
