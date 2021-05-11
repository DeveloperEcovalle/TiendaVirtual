$(document).ready(function () {
    listarMenus(function (lstModulos, lstMenus) {
        let vueGaleriaImagenes = new Vue({
            el: '#wrapper',
            data: {
                lstModulos: lstModulos,
                lstMenus: lstMenus,
                iError: 0,

                iCargandoImagenes: 1,
                lstImagenes: [],
            },
            computed: {},
            mounted: function () {
                let $this = this;
                console.log($this.lstMenus);
                let myDropzone = new Dropzone('.dropzone', {
                    url: '/intranet/app/pagina-web/galeria-imagenes/ajax/insertarImagen',
                    paramName: 'imagen',
                    acceptedFiles: 'image/*',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                });

                myDropzone.on('success', function () {
                    myDropzone.removeAllFiles();
                    $this.ajaxListarImagenes();
                });

                $this.ajaxListarImagenes();
            },
            methods: {
                ajaxListarImagenes: function () {
                    let $this = this;
                    $this.iCargandoImagenes = 1;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/galeria-imagenes/ajax/listarImagenes',
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.lstImagenes = respuesta.data.lstImagenes;
                            } else {
                                toastr[respuesta.result](respuesta.mensaje);
                            }
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        },
                        complete: function () {
                            $this.iCargandoImagenes = 0;
                        }
                    });
                },
                copiarPortapapeles: function (sRutaImagen) {
                    navigator.clipboard.writeText(sRutaImagen).then(function () {
                        toastr[result.success]('Enlace copiado al portapapeles.');
                    }, function () {
                        toastr[result.error]('No se pudo copiar el enlace al portapapeles.');
                    });
                },
                ajaxEliminarImagen: function (id, i) {
                    let $this = this;

                    $.ajax({
                        type: 'post',
                        url: '/intranet/app/pagina-web/galeria-imagenes/ajax/eliminarImagen',
                        data: {id: id},
                        success: function (respuesta) {
                            if (respuesta.result === result.success) {
                                $this.lstImagenes.splice(i, 1);
                            }
                            toastr[respuesta.result](respuesta.mensaje);
                        },
                        error: function (respuesta) {
                            let sHtmlMensaje = sHtmlErrores(respuesta.responseJSON.errors);
                            toastr[result.error](sHtmlMensaje);
                        },
                    });
                }
            }
        });
    });
});
