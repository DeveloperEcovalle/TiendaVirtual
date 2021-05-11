listarMenus(function (lstModulos, lstMenus) {
    let sPeriodo = $cookies.get('sPeriodoVentas');
    sPeriodo = sPeriodo ? sPeriodo : 'Diario';

    let iDia = $cookies.get('iDiaVentas');
    iDia = iDia ? iDia : new Date().getDate();

    let iMes = $cookies.get('iMesVentas');
    iMes = iMes ? iMes : new Date().getMonth();

    let iAnio = $cookies.get('iAnioVentas');
    iAnio = iAnio ? iAnio : new Date().getFullYear();

    let vueVentas = new Vue({
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

            lstVentas: [],
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
            console.log(this.lstModulos);
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
                    url: '/intranet/app/gestion-ventas/ventas/ajax/listarAnios',
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

                $cookies.set('sPeriodoVentas', $this.sPeriodo, 8);
                $cookies.set('iDiaVentas', $this.iDia, 8);
                $cookies.set('iMesVentas', $this.iMes, 8);
                $cookies.set('iAnioVentas', $this.iAnio, 8);

                $.ajax({
                    type: 'post',
                    url: '/intranet/app/gestion-ventas/ventas/ajax/listar',
                    data: {lFechaDesde: $this.lFechaDesde, lFechaHasta: $this.lFechaHasta},
                    success: function (respuesta) {
                        if (respuesta.result === result.success) {
                            let data = respuesta.data;
                            $this.lstVentas = data.lstVentas;

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
                    case 'ventas': {
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
                $('#panel').load('/intranet/app/gestion-ventas/ventas/ajax/panelListar', function () {
                    if (onSuccess) {
                        onSuccess();
                    }
                });
            },
            panelEditar: function (iId) {
                let $this = this;

                let lstVenta = $this.lstVentas.filter(venta => venta.id === parseInt(iId));
                let venta = lstVenta[0];

                $('#panel').load('/intranet/app/gestion-ventas/ventas/ajax/panelEditar', function () {
                    let vueEditar = new Vue({
                        el: '#panel',
                        data: {
                            venta: venta,
                        },
                    });

                    $this.iIdSeleccionado = venta.id;
                    window.history.replaceState(null, 'VENTAS', `/intranet/app/gestion-ventas/ventas/${venta.id}/editar`);
                });
            }
        }
    });
});
