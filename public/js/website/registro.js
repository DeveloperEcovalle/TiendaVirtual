let vueRegistro = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        lstCarritoCompras: [],
        sTipoDocumento: 'DNI', //1
        sMensaje: '',
        sPassword: '',
        sCPassword: '',
        lstUbigeo: [],
        sDepartamento: '',
        sProvincia: '',
        sDistrito: '',
        iRegistrando: 0
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
        }).then(() => {
            $this.ajaxListarDatos();
        })
    },
    methods: {
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
                        location.reload();
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
        }
    }
});