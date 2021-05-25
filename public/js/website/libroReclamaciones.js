vueLibroReclamaciones = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargando: 1,
        iCargandoLR: 0,
        iEnviandoReclamo: 0,
        pagina: {},
        lstCarritoCompras: [],
        lstUbigeo: [],

        sNombres: '', //
        sApellidos: '', //
        sTelefono: '', //
        sOTelefono: '', //
        sDireccion: '', //
        sLote: '', //
        sDepInt: '', //
        sUrbanizacion: '', //
        sReferencia: '', //
        sDepartamento: '', //
        sProvincia: '', //
        sDistrito: '', //
        sTDocumento: '', //
        sNDocumento: '', //
        sEmail: '', //
        sMBien: '', //
        sBContratado: '', //
        sDescripcion: '', //
        sNPedido: '', //
        sTReclamo: '', //
        sDetalle: '', //
        sPedido: '', //
        sDetalleo: '', //

        respuesta: null,

        iEnviandoMensaje: 0,
    },
    computed: {
        bFormularioCorrecto: function () {
            return this.sNombres.trim().length > 0
                && this.sApellidos.trim().length > 0
                && this.sTelefono.trim().length > 0
                && this.sDireccion.trim().length > 0
                && this.sLote.trim().length > 0
                && this.sTDocumento.trim().length > 0
                && this.sNDocumento.trim().length > 0
                && this.sDepartamento.trim().length > 0
                && this.sProvincia.trim().length > 0
                && this.sDistrito.trim().length > 0
                && this.sBContratado.trim().length > 0
                && this.sDescripcion.trim().length > 0
                && this.sTReclamo.trim().length > 0;
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
            let lstUbigeoFiltrado = this.lstUbigeo.filter(ubigeo => ubigeo.departamento === this.sDepartamento);
            let lst = [];
            for (let ubigeo of lstUbigeoFiltrado) {
                if (lst.findIndex((provincia) => provincia === ubigeo.provincia) === -1) {
                    lst.push(ubigeo.provincia);
                }
            }
            if(this.sDepartamento == '')
            {
                this.sProvincia = '';
                this.sDistrito = '';
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

                $this.ajaxListar();
                $this.ajaxListarUbigeo();
            });
        });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListar: function (onSuccess) {
            let $this = this;
            $.ajax({
                type: 'post',
                url: '/libro-reclamaciones/ajax/listar',
                dataType: 'json',
                success: function (respuesta) {
                    if (respuesta.result === result.success) {
                        $this.pagina = respuesta.data.pagina;
                        if (onSuccess) {
                            onSuccess();
                        }
                    }
                },
                complete: function () {
                    $this.iCargandoPP = 0;
                }
            });
        },
        ajaxListarUbigeo: function () {
            let $this = this;
            axios.post('/facturacion-envio/ajax/listarDatosFacturacion').then(response => {
                let respuesta = response.data;
                if (respuesta.result === result.success) {
                    let data = respuesta.data;
                    $this.lstUbigeo = data.lstUbigeo;
                }
            });
        },
        ajaxEnviarReclamo: function () {
            let $this = this;
            $this.iEnviandoReclamo = 1;

            let frmEnviarReclamo = document.getElementById('frmEnviarReclamo');
            let formData = new FormData(frmEnviarReclamo);

            // for( var pair of formData.entries())
            // {
            //     console.log(pair[0]);
            //     console.log(pair[1]);
            // }

            axios.post('/libro-reclamaciones/ajax/enviar', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {

                        $this.sNombres = '';
                        $this.sApellidos = '';
                        $this.sTelefono = '';
                        $this.sOTelefono = '';
                        $this.sDireccion = '';
                        $this.sLote = '';
                        $this.sDepInt = '';
                        $this.sUrbanizacion = '';
                        $this.sReferencia = '';
                        $this.sDepartamento = '';
                        $this.sProvincia = '';
                        $this.sDistrito = '';
                        $this.sTDocumento = '';
                        $this.sNDocumento = '';
                        $this.sEmail = '';
                        $this.sMBien = '';
                        $this.sBContratado = '';
                        $this.sDescripcion = '';
                        $this.sNPedido = '';
                        $this.sTReclamo = '';
                        $this.sDetalle = '';
                        $this.sPedido = '';
                        $this.sDetalleo = '';
                        frmEnviarReclamo.reset();

                        setTimeout(function () {
                            $this.respuesta = null;
                        }, 4000);
                    }
                    $this.respuesta = respuesta;
                })
                .catch(error => {
                    $this.iEnviandoReclamo = 0;
                    $this.respuesta = {result: 'danger', mensaje: 'Ocurrió un error inesperado. Por favor, inténtelo nuevamente.'};
                })
                .then(() => $this.iEnviandoReclamo = 0);
        }
    }
});