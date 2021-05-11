listarMenus(function (lstModulos, lstMenus) {
    let vueKardex = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,

            sBuscar: '',
            producto: {},

            lstKardex: [],
            iError: 0,
        },
        methods: {
            onSelectAutocompleteProducto: function (e, ui) {
                this.producto = JSON.parse(JSON.stringify(ui.item.entidad));
                this.sBuscar = this.producto.nombre_es;
                this.ajaxListar();
                e.preventDefault();
            },
            onChangeAutocompleteProducto: function (e, ui) {
                if (ui.item === null) {
                    this.producto = null;
                }
                e.preventDefault();
            },
            ajaxListar: function (onSuccess) {
                let $this = this;

                if ($this.producto === null) {
                    $this.lstKardex = [];
                    return;
                }

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/gestion-inventario/kardex/ajax/listar',
                    data: {iProductoId: $this.producto.id},
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstKardex = data.lstKardex;

                            if (onSuccess) {
                                onSuccess();
                            }
                        }
                    },
                    error: function () {
                        $this.iError = 1;
                    }
                });
            }
        }
    });
});
