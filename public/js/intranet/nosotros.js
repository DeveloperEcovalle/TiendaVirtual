$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueNosotros = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                empresa: {
                    ruta_imagen_portada: ''
                },
                nuevaImagenPortada: null,
                iActualizandoImagenPortada: 0,
            },
            computed: {
                sNombreNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return 'Buscar archivo';
                    }
                    return this.nuevaImagenPortada.name.split('\\').pop();
                },
                sContenidoNuevaImagen: function () {
                    if (this.nuevaImagenPortada === null) {
                        return null;
                    }
                    return URL.createObjectURL(this.nuevaImagenPortada);
                }
            },
            mounted: function () {
                this.ajaxListar();
            },
            methods: {
                cambiarImagen: function (event) {
                    let input = event.target;
                    this.nuevaImagenPortada = input.files[0];
                },
                ajaxListar: function (onSuccess) {
                    let $this = this;
                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/nosotros/ajax/listar',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                let data = respuesta.data;
                                $this.empresa = data.empresa;

                                if (onSuccess) {
                                    onSuccess();
                                }
                            }
                        },
                        error: function (respuesta) {
                            $this.iError = 1;
                        }
                    });
                },
                ajaxActualizarImagenPortada: function () {
                    let $this = this;
                    $this.iActualizandoImagenPortada = 1;

                    let frmEditarImagenPortada = document.getElementById('frmEditarImagenPortada');
                    let formData = new FormData(frmEditarImagenPortada);

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/nosotros/ajax/actualizarImagenPortada',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (respuesta) {
                            $this.iActualizandoImagenPortada = 0;

                            if (respuesta.result === result.success) {
                                $this.empresa.ruta_imagen_portada = respuesta.data.sNuevaRutaImagen;

                                frmEditarImagenPortada.reset();
                                $this.nuevaImagenPortada = null;
                            }

                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            $this.iActualizandoImagenPortada = 0;

                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        }
                    });
                }
            }
        });
    });
});
