$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        var markers = [];
        var map;
        let vueServicios = new Vue({
            el: "#wrapper",
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                search: "",
                iError: 0,
                iCargando: 1,
                lstclientes: [],
                checkcliente: [],

                pagina: {
                    ruta_imagen_portada: "",
                },
                nuevaImagenPortada: null,
                iActualizandoImagenPortada: 0,

                iActualizandoContenidoEspanol: 0,
                iActualizandoContenidoIngles: 0,
            },
            computed: {
                sNombreNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return "Buscar archivo";
                    }
                    return this.nuevaImagenPortada.name.split("\\").pop();
                },
                sContenidoNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return null;
                    }
                    return URL.createObjectURL(this.nuevaImagenPortada);
                },
                filteredList() {
                    var gps_cliente = this.lstclientes.filter((post) => {
                        return post.nombre
                            .toLowerCase()
                            .includes(this.search.toLowerCase());
                    });
                    if (gps_cliente.length <= 17) {
                        $(".contenedor_gps").css(
                            "height",
                            gps_cliente.length * 30 + "px"
                        );
                    } else {
                        $(".contenedor_gps").css("height", "600px");
                    }
                    return gps_cliente;
                },
            },
            mounted: function () {
                map = new google.maps.Map(document.getElementById("mapa"), {
                    zoom: 15,
                    center: { lat: -8.1092027, lng: -79.0244529 },
                    gestureHandling: "greedy",
                    zoomControl: false,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    draggable: true,
                });
                var datos;
                axios
                    .get("https://erpecovalle.ga/api/clientes/direccion")
                    .then(function (response) {
                        datos = response;
                    })
                    .then(() => $this.Listardirecciones(datos));

                let $this = this;

                let summernoteConfig = {
                    styleTags: ["p", "h1", "h2", "h3", "h4", "h5", "h6"],
                    fontNames: ["Arial"],
                    toolbar: [
                        ["style", ["style"]],
                        ["font", ["bold", "underline", "clear"]],
                        ["fontname", ["fontname"]],
                        ["color", ["color"]],
                        ["para", ["ul", "ol", "paragraph"]],
                        ["table", ["table"]],
                        ["insert", ["link", "picture"]],
                        ["view", ["fullscreen", "codeview"]],
                    ],
                    minHeight: 200,
                    height: 300,
                };

                $("#sContenidoEspanol").summernote(summernoteConfig);
                $("#sContenidoIngles").summernote(summernoteConfig);

                this.ajaxListar(function () {
                    if ($this.pagina) {
                        $("#sContenidoEspanol").summernote(
                            "code",
                            $this.pagina.contenido_espanol
                        );
                        $("#sContenidoIngles").summernote(
                            "code",
                            $this.pagina.contenido_ingles
                        );
                    }
                });
            },
            methods: {
                guardarposiciones: function () {
                    toastr.clear();
                    toastr.options = {
                        iconClasses: {
                            error: "bg-danger",
                            info: "bg-info",
                            success: "bg-success",
                            warning: "bg-warning",
                        },
                    };
                    if (this.iCargando === 0) {
                        var datos_c = [];

                        for (let i = 0; i < this.lstclientes.length; i++) {
                            datos_c.push({
                                id:this.lstclientes[i].id,
                                direccion: this.lstclientes[i].direccion,
                                lat: this.lstclientes[i].lat,
                                lng: this.lstclientes[i].lng,
                                nombre: this.lstclientes[i].nombre,
                                checked: this.lstclientes[i].checked,
                            });
                        }
                        var data_gps = JSON.stringify(datos_c);

                      axios
                            .post(
                                "https://erpecovalle.ga/api/posiciciones/clientes",
                                {
                                    lista: data_gps,
                                }
                            )
                            .then(function (response) {
                                console.log(response);
                            })
                            .catch(function (error) {
                                console.log(error);
                            })
                            .then(function () {
                                // always executed
                            });
                        toastr.success("Registro con exito");
                    } else {
                        toastr.warning("Espere que carga la lista de clientes");
                    }
                },
                vermarcador: function (nombre) {
                    var position = -1;
                    for (let t = 0; t < markers.length; t++) {
                        if (markers[t].nombre == nombre) {
                            position = t;
                        }
                    }
                    if (position == -1) {
                        toastr.clear();
                        toastr.options = {
                            iconClasses: {
                                error: "bg-danger",
                                info: "bg-info",
                                success: "bg-success",
                                warning: "bg-warning",
                            },
                        };
                        toastr.warning("No hay posicion en el erp");
                    } else {
                        var contentString =
                            "<div>Nombre del Cliente:" + nombre + "<br></div>";
                        var infowindow = new google.maps.InfoWindow({
                            content: contentString,
                            width: 192,
                            height: 100,
                        });
                        infowindow.open(map, markers[position].marker);
                    }

                    // info_.push(infowindow);
                },
                check: function (e) {
                    if ($("#checkall").is(":checked")) {
                        for (let i = 0; i < this.lstclientes.length; i++) {
                            this.lstclientes[i].checked = true;
                        }
                    } else {
                        for (let j = 0; j < this.lstclientes.length; j++) {
                            this.lstclientes[j].checked = false;
                        }
                    }
                },
                Listardirecciones: async function (datos) {
                    var data = datos.data;
                    var data_cliente = [];

                    for (let i = 0; i < data.length; i++) {
                        /*var coordenadas = await axios.get(
                            "https://maps.googleapis.com/maps/api/geocode/json?address=" +
                                data[i].direccion +
                                "&key=AIzaSyAS6qv64RYCHFJOygheJS7DvBDYB0iV2wI"
                        );*/
                        if (data[i].lat != null) {
                            const image = {
                                url: "https://erpecovalle.ga/img/gps_ecovalle.png",
                                // This marker is 20 pixels wide by 32 pixels high.
                                scaledSize: new google.maps.Size(40, 40),
                                // The origin for this image is (0, 0).
                            };
                            marker = new google.maps.Marker({
                                position: {
                                    lat: parseFloat(data[i].lat),
                                    lng: parseFloat(data[i].lng),
                                },
                                map: map,
                                icon: image,
                            });
                            markers.push({
                                nombre: data[i].nombre,
                                marker: marker,
                            });
                        }
                        var sel = false;

                        if (data[i].ver == 1) {
                            sel = true;
                        }

                        data_cliente.push({
                            id:data[i].id,
                            direccion: data[i].direccion,
                            lat: data[i].lat,
                            lng: data[i].lng,
                            nombre: data[i].nombre,
                            checked: sel,
                        });
                    }
                    console.log(markers);
                    this.iCargando = 0;
                    this.lstclientes = data_cliente;
                },
                cambiarImagen: function (event) {
                    let input = event.target;
                    this.nuevaImagenPortada = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: "post",
                        url: "/intranet/app/pagina-web/socios/ajax/listar",

                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.pagina = respuesta.data.pagina;

                                if (onSuccess) {
                                    onSuccess();
                                }
                            }
                        },
                        error: function (respuesta) {
                            $this.iError = 1;
                        },
                    });
                },
                ajaxActualizarImagenPortada: function () {
                    let $this = this;
                    $this.iActualizandoImagenPortada = 1;

                    let frmEditarImagenPortada = document.getElementById(
                        "frmEditarImagenPortada"
                    );
                    let formData = new FormData(frmEditarImagenPortada);

                    $.ajax({
                        type: "post",
                        url: "/intranet/app/pagina-web/socios/ajax/actualizarImagenPortada",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.pagina.ruta_imagen_portada =
                                    respuesta.data.sNuevaRutaImagen;

                                frmEditarImagenPortada.reset();
                                $this.nuevaImagenPortada = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(
                                respuesta.responseJSON.errors
                            );
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoImagenPortada = 0;
                        },
                    });
                },
                ajaxActualizarContenidoEspanol: function () {
                    let $this = this;

                    if ($("#sContenidoEspanol").summernote("isEmpty")) {
                        toastr[result.error](
                            "El contenido en español es requerido."
                        );
                        return;
                    }

                    $this.iActualizandoContenidoEspanol = 1;

                    let formData = new FormData();
                    formData.append(
                        "contenido_en_espanol",
                        $("#sContenidoEspanol").summernote("code")
                    );

                    $.ajax({
                        type: "post",
                        url: "/intranet/app/pagina-web/socios/ajax/actualizarContenidoEspanol",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(
                                respuesta.responseJSON.errors
                            );
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoContenidoEspanol = 0;
                        },
                    });
                },
                ajaxActualizarContenidoIngles: function () {
                    let $this = this;

                    if ($("#sContenidoIngles").summernote("isEmpty")) {
                        toastr[result.error](
                            "El contenido en inglés es requerido."
                        );
                        return;
                    }

                    $this.iActualizandoContenidoIngles = 1;

                    let formData = new FormData();
                    formData.append(
                        "contenido_en_ingles",
                        $("#sContenidoIngles").summernote("code")
                    );

                    $.ajax({
                        type: "post",
                        url: "/intranet/app/pagina-web/socios/ajax/actualizarContenidoIngles",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(
                                respuesta.responseJSON.errors
                            );
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iActualizandoContenidoIngles = 0;
                        },
                    });
                },
            },
        });
    });
});
