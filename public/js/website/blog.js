let pagina = lstUrlParams.get('pagina');
let iPaginaSeleccionada = pagina === null ? 0 : parseInt(pagina);

let iCategoria = lstUrlParams.get('categoria');
let iCategoriaSeleccionada = iCategoria === null ? 0 : parseInt(iCategoria);

let vueBlogLista = new Vue({
    el: '#content',
    data: {
        locale: 'es',
        lstCategorias: [],
        lstUltimasPublicaciones: [],

        iCargandoCategorias: 0,
        iCargandoPublicaciones: 1,
        iCategoriaSeleccionada: iCategoriaSeleccionada,
        lstPublicaciones: [],

        iTotalPublicaciones: 0,
        iItemsPorPagina: 6,
        iPaginaSeleccionada: iPaginaSeleccionada,
        lstCarritoCompras: []
    },
    computed: {
        iTotalPaginas: function () {
            return Math.ceil(this.iTotalPublicaciones / this.iItemsPorPagina);
        },
        lstPaginas: function () {
            if (this.iTotalPaginas <= 8) {
                return Array.from(Array(this.iTotalPaginas).keys());
            }

            let lstPaginas;
            let lstPaginasInicio, lstPaginasMedio, lstPaginasFin;

            if (this.iPaginaSeleccionada <= 3) {
                lstPaginasInicio = Array.from(Array(4).keys());
            } else {
                lstPaginasInicio = [0, 1];
            }

            if (this.iPaginaSeleccionada >= 4 && this.iPaginaSeleccionada <= this.iTotalPaginas - 5) {
                lstPaginasMedio = [-1, this.iPaginaSeleccionada - 1, this.iPaginaSeleccionada, this.iPaginaSeleccionada + 1];

                if (this.iPaginaSeleccionada < this.iTotalPaginas - 4) {
                    lstPaginasMedio.push(-1);
                }
            } else {
                lstPaginasMedio = [-1];
            }

            if (this.iPaginaSeleccionada >= this.iTotalPaginas - 4) {
                lstPaginasFin = [this.iTotalPaginas - 4, this.iTotalPaginas - 3, this.iTotalPaginas - 2, this.iTotalPaginas - 1];
            } else {
                lstPaginasFin = [this.iTotalPaginas - 2, this.iTotalPaginas - 1];
            }

            lstPaginas = lstPaginasInicio.concat(lstPaginasMedio, lstPaginasFin);
            return lstPaginas;
        },
        sBanner: function () {
            let sBannerDefault = '/img/blog.jpg';
            if (this.iCategoriaSeleccionada == 0 || this.lstCategorias.length === 0) {
                return sBannerDefault;
            }

            let lstCategoriaSeleccionada = this.lstCategorias.filter(categoria => categoria.id == this.iCategoriaSeleccionada);
            if (lstCategoriaSeleccionada.length === 0) {
                return sBannerDefault;
            }

            let categoriaSeleccionada = lstCategoriaSeleccionada[0];
            if (categoriaSeleccionada.ruta_imagen === null) {
                return sBannerDefault;
            }

            return categoriaSeleccionada.ruta_imagen;
        }
    },
    mounted: function () {
        let $this = this;
        ajaxWebsiteLocale()
            .then(response => {
                let respuesta = response.data;
                $this.locale = respuesta.data.locale;
            })
            .then(() => {
                $this.ajaxListarCategorias().then(() => {
                    $this.ajaxListarPublicaciones().then(() => {
                    });
                });
            });
    },
    watch: {
        iCategoriaSeleccionada: function () {
            let $this = this;

            $this.iPaginaSeleccionada = 0;
            $this.ajaxListarPublicaciones().then(() => $this.actualizarUrl());
        },
        iPaginaSeleccionada: function () {
            let $this = this;
            $this.ajaxListarPublicaciones().then(() => $this.actualizarUrl());
        }
    },
    methods: {
        ajaxSalir: () => ajaxSalir(),
        ajaxSetLocale: locale => ajaxSetLocale(locale),
        actualizarUrl: function () {
            let sUrl = '/blog?pagina=' + this.iPaginaSeleccionada + '&categoria=' + this.iCategoriaSeleccionada;
            window.history.replaceState({}, 'Ecovalle | Blog', sUrl);
        },
        navegarAnterior: function () {
            if (this.iPaginaSeleccionada > 0) {
                this.iPaginaSeleccionada--;
            }
        },
        navegarSiguiente: function () {
            if (this.iPaginaSeleccionada + 1 < this.iTotalPaginas) {
                this.iPaginaSeleccionada++;
            }
        },
        ajaxListarCategorias: function () {
            let $this = this;
            $this.iCargandoCategorias = 1;

            return axios.post('/blog/ajax/listarCategorias')
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let data = respuesta.data;
                        $this.lstCategorias = data.lstCategorias;
                        $this.lstUltimasPublicaciones = data.lstUltimasPublicaciones;
                    }
                })
                .then(() => $this.iCargandoCategorias = 0);
        },
        ajaxListarPublicaciones: function () {
            this.iCargandoPublicaciones = 1;

            let formData = new FormData();
            formData.append('iCategoriaSeleccionada', this.iCategoriaSeleccionada);
            formData.append('iPaginaSeleccionada', this.iPaginaSeleccionada);
            formData.append('iItemsPorPagina', this.iItemsPorPagina);

            let $this = this;
            return axios.post('/blog/ajax/listarPublicaciones', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        let data = respuesta.data;
                        $this.lstPublicaciones = data.lstPublicaciones;
                        $this.iTotalPublicaciones = data.iTotalPublicaciones;
                    }
                })
                .then(() => $this.iCargandoPublicaciones = 0);
        }
    },
});
