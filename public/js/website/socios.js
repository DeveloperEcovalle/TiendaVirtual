var map;
var markers = [];
let vueSocios = new Vue({
    el: "#content",
    data: {
        locale: "es",
        search: "",
        lstCarritoCompras: [],
        markersroute:[],
        ruta:[],
        lstclientes: [],
        iCargando: 1,
        pagina: {
            ruta_imagen_portada: "",
        },
        iEnviandoMensaje: 0,
        respuesta: null,
        lstBeneficios: []
    },
    computed: {
        filteredList() {
            var gps_cliente = this.lstclientes.filter((post) => {
                return post.nombre
                    .toLowerCase()
                    .includes(this.search.toLowerCase());
            });
            /*if (gps_cliente.length <= 7) {
                $(".contenedor_gps").css(
                    "height",
                    gps_cliente.length * 30 + "px"
                );
            } else {
                $(".contenedor_gps").css("height", "440px");
            }*/
            return gps_cliente;
        },
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
          /*  styles: [
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
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "administrative.province",
                    elementType: "geometry.stroke",
                    stylers: [
                        {
                            visibility: "on",
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
                            color: "#F8F9FA",
                        },
                    ],
                },
                {
                    featureType: "landscape.natural",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "poi",
                    elementType: "all",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "road",
                    elementType: "all",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "road",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit",
                    elementType: "labels.icon",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit.line",
                    elementType: "geometry",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit.line",
                    elementType: "labels.text",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit.station.airport",
                    elementType: "geometry",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "transit.station.airport",
                    elementType: "labels",
                    stylers: [
                        {
                            visibility: "on",
                        },
                    ],
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [
                        {
                            // color: "#F2F2F2",
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
            ],*/
        };
        var mapElement = document.getElementById("mapa");
        map = new google.maps.Map(mapElement, mapOptions);
        map.controls[google.maps.ControlPosition.LEFT_TOP].push(document.getElementById("sidebargps"));
        var datos;
        axios
            .get("https://erpecovalle.ga/api/clientes/direccion")
            .then(function (response) {
                datos = response.data;
            })
            .then(() => this.clientes(datos));

        //setting overlay color, etc.
        map.data.setStyle({
            fillColor: "#F8F9FA",
            strokeWeight: 1,
            strokeColor: "#EE9D32",
            fillOpacity: 1,
        });

        //map.data.loadGeoJson("https://erpecovalle.ga/api/mapa/peru");
        /* $.getJSON("/Json/departamentos.json", function(json) {
            map.data.addGeoJson(json);

        });*/
        /* var directionsDisplay = new google.maps.DirectionsRenderer({'draggable': false});
            var directionsService = new google.maps.DirectionsService();
         this.displayRoute("DRIVING", new google.maps.LatLng(-7.418596,-79.503464),
                                  new google.maps.LatLng(-7.418032,-79.510502),directionsService,directionsDisplay);
        map.data.loadGeoJson(
                departamentos);*/
    },
    methods: {
        menugeneral: function ()
        {
            $("#contenidocliente").css("display","none");
            $("#listacliente").css("display","block");
        },
        showSidebar: function(){
         var gpsmenu= $("#gpsmenu");
           if(gpsmenu.data("show")==0)
           {
             gpsmenu.data("show","1");
             gpsmenu.css("display","none");
             $(".siderbarmenu").css("display","block");
           }
           else
           {
            gpsmenu.data("show","0");
            gpsmenu.css("display","block");
            $(".siderbarmenu").css("display","none");
           }
        },
        eliminarRuta: function()
        {
            for (let i = 0; i < this.ruta.length; i++) {
                    this.ruta[i].direction.setMap(null);
            }
            for (let j = 0; j < this.markersroute.length; j++) {
                    this.markersroute[j].marker.setMap(null);
            }
        },
        generarRuta: function () {
            $this = this;
            var nombre=$("#contenidocliente .threegrid").data("nombre");
            var posicion= -1;
            for (let t = 0; t < markers.length; t++) {
                if (markers[t].nombre == nombre) {
                    posicion= t;
                }
            }

            if (navigator.geolocation) {
                const image = {
                    url: "http://127.0.0.1:8000/img/gpa_red_ecovalle.png",
                    scaledSize: new google.maps.Size(40, 40),
                };
                navigator.geolocation.getCurrentPosition(function (position) {
                    localStorage.latitud=position.coords.latitude;
                    localStorage.longitud=position.coords.longitude;
                    $this.eliminarRuta();
                    var directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true
                    });
                    var directionsService = new google.maps.DirectionsService();
                    $this.ruta.push({direction: directionsDisplay});
                    $this.displayRoute(
                        "DRIVING",
                        new google.maps.LatLng(localStorage.latitud,localStorage.longitud),
                        new google.maps.LatLng(markers[posicion].marker.getPosition().lat(),markers[posicion].marker.getPosition().lng()),
                        directionsService,
                        directionsDisplay
                    );
                });
            } else {
                console.log("sin permisos");
            }
        },
        vermarcador: function () {
            var position = -1;
            var nombre=$("#contenidocliente .threegrid").data("nombre");
            for (let t = 0; t < markers.length; t++) {
                if (markers[t].nombre == nombre) {
                    position = t;
                }
            }
            var contentString =
                "<div>Nombre del Cliente:" + nombre + "<br></div>";
            var infowindow = new google.maps.InfoWindow({
                content: contentString,
                width: 192,
                height: 100,
            });
            map.setCenter(markers[position].marker.getPosition());
            map.setZoom(18);
            infowindow.open(map, markers[position].marker);
        },
        clientes: function (datos) {
            var $this=this;
            const image = {
                url: "https://ecovalle.pe/img/marker.png",
                // This marker is 20 pixels wide by 32 pixels high.
                scaledSize: new google.maps.Size(40, 40),
                // The origin for this image is (0, 0).
            };
            var data = [];
            for (var i = 0; i < datos.length; i++) {
                if (datos[i].ver == 1) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(
                            datos[i].lat,
                            datos[i].lng
                        ),
                        map: map,
                        icon: image,
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                        $this.abrirmarcador(this);
                    });
                    data.push({
                        direccion: datos[i].direccion,
                        lat: datos[i].lat,
                        lng: datos[i].lng,
                        nombre: datos[i].nombre,
                        rutaimg:datos[i].ruta_logo,
                        numero:datos[i].celular_propietario
                    });
                    markers.push({ marker: marker, nombre: datos[i].nombre });
                }
            }
            this.lstclientes = data;
        },
        marcadorcliente:function(valor){
            var marker;
            markers.forEach((value,index,array)=>{
                if(value.nombre==valor)
                {
                   marker=array[index].marker;
                }
           });
           this.abrirmarcador(marker);
        }
        ,
        abrirmarcador: function(valor){
            var nombre="-1";
            markers.forEach((value,index,array)=>{
                 if(value.marker==valor)
                 {
                    nombre=array[index].nombre;
                 }
            });
            var data=this.lstclientes;
            var imagen;
            var direccion;
            var celular;
            data.forEach((value,index,array)=>{
                if(value.nombre==nombre)
                {
                    imagen=value.rutaimg;
                    direccion=value.direccion;
                    celular=value.numero
                }
            });
            $("#listacliente").css("display","none");
            $("#contenidocliente").css("display","block");
            var rutaimagen="https://erpecovalle.ga/storage/Clientes/img/default.png";
            if(imagen!=null){
               rutaimagen= "https://erpecovalle.ga/storage/"+imagen.slice(7)
            }

            $("#contenidocliente .imgpscliente").attr("src",rutaimagen);
            $("#contenidocliente .twogrid").html(nombre)
            $("#contenidocliente #direccion").html(direccion)
            $("#contenidocliente #numero").html(celular)
            $("#contenidocliente .threegrid").data("nombre",nombre);
            var gpsmenu= $("#gpsmenu");
           if(gpsmenu.data("show")==0)
           {
             gpsmenu.data("show","1");
             gpsmenu.css("display","none");
             $(".siderbarmenu").css("display","block");
           }


        },
        displayRoute(
            travel_mode,
            origin,
            destination,
            directionsService,
            directionsDisplay
        ) {
            $this=this;
            directionsService.route(
                {
                    origin: origin,
                    destination: destination,
                    travelMode: travel_mode,
                    avoidTolls: true,
                },
                function (response, status) {
                    if (status === "OK") {

                        var leg = response.routes[ 0 ].legs[ 0 ];
                        directionsDisplay.setMap(map);
                        directionsDisplay.setDirections(response);
                        $this.drawnmarkers(leg)
                    } else {
                        directionsDisplay.setMap(null);
                        directionsDisplay.setDirections(null);
                        alert("Could not display directions due to: " + status);
                    }
                }
            );
        },
        drawnmarkers:function(position){
            const imginicio= {
                url: "http://127.0.0.1:8000/img/inicioecovalle.png",
                scaledSize: new google.maps.Size(50, 50),
            };
            const imgfinal = {
                url: "http://127.0.0.1:8000/img/finalecovalle.png",
                scaledSize: new google.maps.Size(50, 50),
            };
            markerinicio = new google.maps.Marker({
                position: position.start_location,
                map: map,
                icon: imginicio})
            markerfinal= new google.maps.Marker({
                        position: position.end_location,
                        map: map,
                        icon: imgfinal})
                        this.markersroute.push({marker:markerinicio})
                        this.markersroute.push({marker:markerfinal});

        },
        ajaxSetLocale: (locale) => ajaxSetLocale(locale),
        ajaxListar: function () {
            let $this = this;
            axios
                .post("/se-ecovalle/socios/ajax/listar")
                .then((response) => {
                    $this.pagina = response.data.data.pagina;
                    $this.lstBeneficios = response.data.data.lstBeneficios;
                })
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
        ajaxPopup: function(beneficio) {
            var data = [
                {
                    username: beneficio.nombre,
                    
                    userWebsite_href: beneficio.ruta_enlace ? beneficio.ruta_enlace : '#',
                    
                    userAvatarUrl_img: beneficio.ruta_imagen,
                    
                    userLocation: beneficio.descripcion
                },
            ];
            
            $.magnificPopup.open({ 
                key: 'my-popup', 
                items: data,
                fixedContentPos: false,
                fixedBgPos: true,
                overflowY: 'auto',
                closeBtnInside: true,
                preloader: true,
                midClick: true,
                removalDelay: 500,
                mainClass: 'mfp-fade',
                type: 'inline',
                inline: {
                // Define markup. Class names should match key names.
                markup: '<div class="white-popup1"><div class="mfp-close"></div>'+
                            '<a class="mfp-userWebsite">'+
                            '<div class="mfp-userAvatarUrl img-popup"></div>'+
                            '<h2 class="mfp-username"></h2>'+
                            '</a>'+
                            '<div class="mfp-userLocation"></div>'+
                        '</div>'
                },
                gallery: {
                enabled: true 
                },
                callbacks: {
                markupParse: function(template, values, item) {
                    // optionally apply your own logic - modify "template" element based on data in "values"
                    // console.log('Parsing:', template, values, item);
                }
                }
            });
        }
    },
    updated: function () {
        this.$nextTick(function () {
            $(".carousel").carousel();
        });
    },
});
