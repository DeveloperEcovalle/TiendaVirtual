let vueServicios = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        iCargandoTC: 1,
        iCargando: 1,
        pagina: {
            ruta_imagen_portada: '',
        },
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

        /*ajaxWebsiteLocale(function (respuesta) {
            $this.locale = respuesta.data.locale;
            $this.ajaxListar();
        });*/
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        ajaxListar: function (onSuccess) {
            let $this = this;
            $.ajax({
                type: 'post',
                url: '/terminos-condiciones/ajax/listar',
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
                    $this.iCargandoTC = 0;
                }
            });
        },
    }
});
