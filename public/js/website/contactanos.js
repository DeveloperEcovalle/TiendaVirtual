let vueContactanos = new Vue({
    el: '#content',
    data: {
        sNombres: '',
        sApellidos: '',
        sEmail: '',
        sTelefono: '',
        sAsunto: '',
        sMensaje: '',

        respuesta: null,

        iEnviandoMensaje: 0,
        lstCarritoCompras: []
    },
    computed: {
        bFormularioCorrecto: function () {
            return this.sNombres.trim().length > 0
                && this.sApellidos.trim().length > 0
                && this.sEmail.trim().length > 0
                && this.sTelefono.trim().length > 0
                && this.sAsunto.trim().length > 0
                && this.sMensaje.trim().length > 0
                && this.sMensaje.trim().length <= 150;
        }
    },
    mounted: function () {
        let $this = this;
        ajaxWebsiteLocale()
            .then(response => {
                let respuesta = response.data;
                $this.locale = respuesta.data.locale;
            });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxEnviarMensaje: function () {
            let $this = this;
            $this.iEnviandoMensaje = 1;

            let frmEnviarMensaje = document.getElementById('frmEnviarMensaje');
            let formData = new FormData(frmEnviarMensaje);

            axios.post('/contactanos/ajax/enviar', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        $this.sNombres = '';
                        $this.sApellidos = '';
                        $this.sEmail = '';
                        $this.sTelefono = '';
                        $this.sAsunto = '';
                        $this.sMensaje = '';

                        frmEnviarMensaje.reset();

                        setTimeout(function () {
                            $this.respuesta = null;
                        }, 4000);
                    }

                    $this.respuesta = respuesta;
                })
                .catch(error => {
                    $this.iEnviandoMensaje = 0;
                    $this.respuesta = {result: 'danger', mensaje: 'Ocurrió un error inesperado. Por favor, inténtelo nuevamente.'};
                })
                .then(() => $this.iEnviandoMensaje = 0);
        }
    }
});
