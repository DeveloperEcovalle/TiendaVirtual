let vuePoliticaPrivacidad = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargandoPP: 1,
        iCargando: 1,
        pagina: {},
        lstCarritoCompras: []
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
            });
        });
    },
    methods: {
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListar: function (onSuccess) {
            let $this = this;
            $.ajax({
                type: 'post',
                url: '/politica-privacidad/ajax/listar',
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
    }
});
