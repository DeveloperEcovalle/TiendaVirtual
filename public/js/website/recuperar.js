let vueRegistro = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        lstCarritoCompras: [],
        sMensaje: '',
        sError: 0,
        iComprobando: 0,
        sClase: '',
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

        })
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxEnviar: function () {
            let $this = this;
            $this.iComprobando = 1;
            $this.sMensaje = '';
            $this.sClase = '';

            let frmRestablecer = document.getElementById('frmRestablecer');
            let formData = new FormData(frmRestablecer);

            axios.post('/olvide-mi-contrasena/ajax/enviar', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.sClase = 'alert-' + respuesta.result;
                        $this.sMensaje = respuesta.mensaje;
                    } else {
                        $this.sClase = 'alert-' + respuesta.result;
                        $this.sMensaje = respuesta.mensaje;
                    }
                })
                .catch(error => {
                    let sHtmlMensaje = sHtmlErrores(error.responseJSON.errors);
                    $this.sMensaje = sHtmlMensaje;
                })
                .then(() => $this.iComprobando = 0);
        },
        guardarLstCarritoCompras: function () {
            $cookies.set('lstCarritoCompras', this.lstCarritoCompras, 12);
        },
    }
});