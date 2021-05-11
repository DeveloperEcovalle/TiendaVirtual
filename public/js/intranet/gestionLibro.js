listarMenus(function (lstModulos, lstMenus) {
    let vueLibro = new Vue({
        el: '#wrapper',
        data: {
            sBuscar: '',
            lstModulos: lstModulos,
            lstMenus: lstMenus,
            lstLibro: [],
            iError: 0,
        },
        computed: {
            lstLibroFiltrado: function () {
                return this.lstLibro.filter(reclamo =>
                    reclamo.nombres.toLowerCase().includes(this.sBuscar.toLowerCase())
                    || reclamo.apellidos.includes(this.sBuscar.toLowerCase())
                    || reclamo.codigo.includes(this.sBuscar)
                );
            },
        },
        mounted: function () {
            this.ajaxListar();
        },
        methods: {
            ajaxListar: function (onSuccess) {
                let $this = this;
                $.ajax({
                    type: 'post',
                    url: '/intranet/app/libro-reclamaciones/libro/ajax/listar',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstLibro = data.lstLibro;

                            //console.log($this.lstLibro);

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
            ajaxDownload: function(id) {
                $.ajax({
                    type: 'get',
                    url: '/intranet/app/libro-reclamaciones/libro/ajax/download/'+id,
                    success: function (respuesta) {
                        toastr.options = {
                            iconClasses: {
                                error: 'bg-danger',
                                info: 'bg-info',
                                success: 'bg-success',
                                warning: 'bg-warning',
                            },
                        };
                        toastr.success('Descargando ...');
                    },
                    error: function (respuesta) {
                        toastr.clear();
                        toastr.options = {
                            iconClasses: {
                                error: 'bg-danger',
                                info: 'bg-info',
                                success: 'bg-success',
                                warning: 'bg-warning',
                            },
                        };
                        toastr.error('Ocurrió un error, vuelva a intentar.');
                    }
                });
            }
        }
    });
});