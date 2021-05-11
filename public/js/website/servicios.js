let vueServicios = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        lstCarritoCompras: [],

        iCargando: 1,
        pagina: {
            ruta_imagen_portada: '',
        },

        iEnviandoMensaje: 0,
        respuesta: null
    },
    mounted: function () {
        let $this = this;
        ajaxWebsiteLocale()
            .then(response => {
                let respuesta = response.data;
                $this.locale = respuesta.data.locale;
            })
            .then(() => $this.ajaxListar());
    },
    methods: {
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListar: function () {
            let $this = this;
            axios.post('/servicios/ajax/listar')
                .then(response => $this.pagina = response.data.data.pagina)
                .then(() => $this.iCargando = 0);
        },
        ajaxEnviarMensaje: function () {
            let $this = this;
            $this.iEnviandoMensaje = 1;

            let frmServicios = document.getElementById('frmServicios');
            let formData = new FormData(frmServicios);

            axios.post('/servicios/ajax/enviar', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        frmServicios.reset();
                        setTimeout(() => $this.respuesta = null, 4000);
                    }
                    $this.respuesta = respuesta;
                })
                .catch(error => {
                    $this.respuesta = {
                        result: 'danger',
                        mensaje: 'Ocurrió un error inesperado. Por favor, inténtelo nuevamente.'
                    };
                })
                .then(() => $this.iEnviandoMensaje = 0);
        }
    },
    updated: function () {
        this.$nextTick(function () {
            $('.carousel').carousel();
        });
    }
});
