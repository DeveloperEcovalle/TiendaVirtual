let vueSocios = new Vue({
    el: "#content",
    data: {
        locale: "es",
        lstCarritoCompras: [],

        iCargando: 1,
        pagina: {
            ruta_imagen_portada: "",
        },

        iEnviandoMensaje: 0,
        respuesta: null,
    },
    mounted: function () {
        let $this = this;
        ajaxWebsiteLocale()
            .then((response) => {
                let respuesta = response.data;
                $this.locale = respuesta.data.locale;
            })
            .then(() => $this.ajaxListar());
        var mapOptions = {
            center: new google.maps.LatLng(-9.28737, -75.168251),
            zoom: 5,
            minZoom: 5,
            zoomControl: true,
            disableDoubleClickZoom: true,
            mapTypeControl: false,
            scaleControl: true,
            scrollwheel: true,
            panControl: true,
            streetViewControl: false,
            draggable: true,
            overviewMapControl: true,
            overviewMapControlOptions: {
                opened: false,
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            styles: [
                {
                    featureType: "administrative",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "administrative.country",
                    elementType: "geometry.stroke",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "administrative.province",
                    elementType: "geometry.stroke",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "landscape",
                    elementType: "geometry",
                    stylers: [
                        {
                            visibility: "on",
                        },
                        {
                            color: "#F2F2F2",
                        },
                    ],
                },
                {
                    featureType: "landscape.natural",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "poi",
                    elementType: "all",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "road",
                    elementType: "all",
                    stylers: [
                        {
                            color: "#FF0000",
                        },
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "road",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "transit",
                    elementType: "labels.icon",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "transit.line",
                    elementType: "geometry",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "transit.line",
                    elementType: "labels.text",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "transit.station.airport",
                    elementType: "geometry",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "transit.station.airport",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [
                        {
                            color: "#F2F2F2",
                        },
                    ],
                },
                {
                    featureType: "water",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "off",
                        },
                    ],
                },
            ],
        };
        var mapElement = document.getElementById("mapa");
        var map = new google.maps.Map(mapElement, mapOptions);
        const image = {
            url: "https://erpecovalle.ga/img/gps_ecovalle.png",
            // This marker is 20 pixels wide by 32 pixels high.
            scaledSize: new google.maps.Size(40, 40),
            // The origin for this image is (0, 0).
        };
        axios
            .get("https://erpecovalle.ga/api/clientes/direccion")
            .then(function (response) {
                var datos = response.data;

                for (var i = 0; i < datos.length; i++) {
                    if (datos[i].ver == 1) {
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(
                                datos[i].latitud,
                                datos[i].longitud
                            ),
                            map: map,
                            icon: image,
                        });
                    }
                }
            });

        //setting overlay color, etc.
        map.data.setStyle({
            fillColor: "white",
            strokeWeight: 1,
            strokeColor: "#EE9D32",
            fillOpacity: 1,
        });

        map.data.loadGeoJson("https://erpecovalle.ga/api/mapa/peru");

        map.data.loadGeoJson(
            "https://ecovalle.pe/Json/departamentos.json"
        );
    },
    methods: {
        ajaxSetLocale: (locale) => ajaxSetLocale(locale),
        ajaxListar: function () {
            let $this = this;
            axios
                .post("/se-ecovalle/socios/ajax/listar")
                .then((response) => ($this.pagina = response.data.data.pagina))
                .then(() => ($this.iCargando = 0));
        },
        ajaxEnviarMensaje: function () {
            let $this = this;
            $this.iEnviandoMensaje = 1;

            let frmSocio = document.getElementById("frmSocio");
            let formData = new FormData(frmSocio);

            axios
                .post("/se-ecovalle/socios/ajax/enviar", formData)
                .then((response) => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        frmSocio.reset();
                        setTimeout(() => ($this.respuesta = null), 5000);
                    }
                    $this.respuesta = respuesta;
                })
                .catch((error) => {
                    $this.iEnviandoMensaje = 0;
                    $this.respuesta = {
                        result: "danger",
                        mensaje:
                            "Ocurrió un error inesperado. Por favor, inténtelo nuevamente.",
                    };
                })
                .then(() => ($this.iEnviandoMensaje = 0));
        },
    },
    updated: function () {
        this.$nextTick(function () {
            $(".carousel").carousel();
        });
    },
});
