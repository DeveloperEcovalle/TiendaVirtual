let vueRegistro = new Vue({
    el: '#content',
    data: {
        lstCarritoCompras: [],
        sTipoDocumento: 1,
        sMensaje: '',
        iRegistrando: 0
    },
    mounted: function () {
    },
    methods: {
        ajaxRegistrar: function () {
            let $this = this;
            $this.iRegistrando = 1;
            $this.sMensaje = '';

            let frmRegistro = document.getElementById('frmRegistro');
            let formData = new FormData(frmRegistro);

            axios.post('/registro/ajax/registrar', formData)
                .then(respuesta => {
                    if (respuesta.result === result.success) {
                        location.reload();
                    } else {
                        $this.sMensaje = respuesta.mensaje;
                    }
                })
                .catch(error => {
                    let sHtmlMensaje = sHtmlErrores(error.responseJSON.errors);
                    $this.sMensaje = sHtmlMensaje;
                })
                .then(() => $this.iRegistrando = 0);
        }
    }
});