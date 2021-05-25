let publicacion = lstUrlParams.get('publicacion');
let c = lstUrlParams.get('c');

let sEnlace = publicacion;
let iId = c;

let vuePublicacion = new Vue({
    el: '#content',
    data: {
        iCargandoPublicacion: 1,
        publicacion: {
            usuario: {
                persona: {}
            }
        },

        iCargandoUltimasPublicaciones: 1,
        lstUltimasPublicaciones: [],
        lstCarritoCompras: []
    },
    computed: {
        sAutor: function () {
            let persona = this.publicacion.usuario.persona;
            return persona.nombres + ' ' + (persona.apellido_1 || '');
        }
    },
    mounted: function () {
        let $this = this;

        ajaxWebsiteListarCarritoCompras().then(response => {
            let respuesta = response.data;
            let data = respuesta.data;

            let lstCarritoComprasServer = data.lstCarrito;
                //let bClienteEnSesion = data.bClienteEnSesion;

            let cookieLstCarritoCompras = $cookies.get('lstCarritoCompras');

            let lstCarritoCompras = cookieLstCarritoCompras && cookieLstCarritoCompras.length > 0 ? cookieLstCarritoCompras : lstCarritoComprasServer;

            $this.lstCarritoCompras = lstCarritoCompras;
        });
        $.ajax({
            type: 'post',
            url: '/blog/ajax/listarPublicacion',
            data: {publicacion: sEnlace, c: iId},
            dataType: 'json',
            success: function (respuesta) {
                if (respuesta.result === result.success) {
                    $this.publicacion = respuesta.data.publicacion;
                    $.ajax({
                        type: 'post',
                        url: '/blog/ajax/listarUltimasPublicaciones',
                        data: {iPublicacionId: iId},
                        dataType: 'json',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.lstUltimasPublicaciones = respuesta.data.lstUltimasPublicaciones;
                            }
                        },
                        complete: function () {
                            $this.iCargandoUltimasPublicaciones = 0;
                        }
                    });
                }
            },
            complete: function () {
                $this.iCargandoPublicacion = 0;
            }
        });
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
    }
});
