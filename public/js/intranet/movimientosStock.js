listarMenus(function (lstModulos, lstMenus) {
    let sPeriodo = $cookies.get('sPeriodoMS');
    sPeriodo = sPeriodo ? sPeriodo : 'Diario';

    let iDia = $cookies.get('iDiaMS');
    iDia = iDia ? iDia : new Date().getDate();

    let iMes = $cookies.get('iMesMS');
    iMes = iMes ? iMes : new Date().getMonth();

    let iAnio = $cookies.get('iAnioMS');
    iAnio = iAnio ? iAnio : new Date().getFullYear();

    let vueMovimientosStock = new Vue({
        el: '#wrapper',
        data: {
            lstModulos: lstModulos,
            lstMenus: lstMenus,

            sBuscar: '',
            lstAnios: [],

            sPeriodo: sPeriodo,
            iDia: iDia,
            iMes: iMes,
            iAnio: iAnio,

            lstMovimientos: [],
            iIdSeleccionado: 0,
            iError: 0,
        },
        computed: {
            lstDias: function () {
                let iDiaMax = parseInt(new Date(this.iAnio, parseInt(this.iMes) + 1, 0).getDate());
                return new Array(iDiaMax).fill().map((item, index) => index + 1);
            },
            lFechaDesde: function () {
                switch (this.sPeriodo) {
                    case 'Diario': {
                        return new Date(this.iAnio, this.iMes, this.iDia, 0, 0, 0, 0).getTime();
                    }
                    case 'Mensual' : {
                        return new Date(this.iAnio, this.iMes, 1, 0, 0, 0, 0).getTime();
                    }
                }
            },
            lFechaHasta: function () {
                switch (this.sPeriodo) {
                    case 'Diario': {
                        return new Date(this.iAnio, this.iMes, this.iDia, 23, 59, 59, 999).getTime();
                    }
                    case 'Mensual' : {
                        return new Date(this.iAnio, parseInt(this.iMes) + 1, 0, 23, 59, 59, 999).getTime();
                    }
                }
            }
        },
        mounted: function () {
            let $this = this;
            $this.ajaxListarAnios(function () {
                $this.ajaxListar($this.cargarPanel);
            });
        },
        methods: {
            ajaxListarAnios: function (onSuccess) {
                let $this = this;

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/gestion-inventario/movimientos-stock/ajax/listarAnios',
                    dataType: 'json',
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let lstAnios = respuesta.data.lstAnios;
                            $this.lstAnios = lstAnios;

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
            ajaxListar: function (onSuccess) {
                let $this = this;

                $cookies.set('sPeriodoMS', $this.sPeriodo, 8);
                $cookies.set('iDiaMS', $this.iDia, 8);
                $cookies.set('iMesMS', $this.iMes, 8);
                $cookies.set('iAnioMS', $this.iAnio, 8);

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/gestion-inventario/movimientos-stock/ajax/listar',
                    data: {lFechaDesde: $this.lFechaDesde, lFechaHasta: $this.lFechaHasta},
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstMovimientos = data.lstMovimientos;

                            if (onSuccess) {
                                onSuccess();
                            }
                        }
                    },
                    error: function () {
                        $this.iError = 1;
                    }
                });
            },
            cargarPanel: function () {
                let $this = this;
                let sUrl = location.pathname;
                let lstUrl = sUrl.split('/');

                let sLastPath = lstUrl.pop();
                sLastPath = sLastPath.length === 0 ? lstUrl.pop() : sLastPath;

                switch (sLastPath) {
                    case 'movimientos-stock': {
                        $this.panelListar();
                        break;
                    }
                    case 'editar': {
                        let iId = lstUrl.pop();
                        $this.panelEditar(iId);
                        break;
                    }
                }
            },
            panelListar: function (onSuccess) {
                $('#panel').load('/intranet/app/gestion-inventario/movimientos-stock/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let lstMovimiento = $this.lstMovimientos.filter(movimiento => movimiento.id === parseInt(iId));
                let movimiento = lstMovimiento[0];

                $('#panel').load('/intranet/app/gestion-inventario/movimientos-stock/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            movimiento: movimiento,
                        },
                    });

                    $this.iIdSeleccionado = movimiento.id;
                    window.history.replaceState(null, 'MOVIMIENTOS DE STOCK', `/intranet/app/gestion-inventario/movimientos-stock/${movimiento.id}/editar`);
                });
            }
        }
    });
});
