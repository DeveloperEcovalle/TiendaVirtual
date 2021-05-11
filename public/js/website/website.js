const result = {
    success: 'success',
    warning: 'warning',
    error: 'error'
};

const $cookies = {
    set: function (sClave, sValor, iHorasExpiracion) {
        let dAhora = new Date();
        let cookie = {sValor: sValor, lExpiracion: dAhora.getTime() + (iHorasExpiracion * 60 * 60 * 1000)};
        localStorage.setItem(sClave, JSON.stringify(cookie));
    },

    get: function (sClave) {
        let sCookie = localStorage.getItem(sClave)
        if (!sCookie) {
            return null;
        }

        let cookie = JSON.parse(sCookie);

        if (cookie.lExpiracion === null) {
            return cookie.sValor;
        }

        let dAhora = new Date();
        if (dAhora.getTime() > cookie.lExpiracion) {
            localStorage.removeItem(sClave);
            return null;
        }

        return cookie.sValor;
    }
};

const lstUrlParams = new URLSearchParams(location.search);

const culqiEcovalle = {publicKeyTest: ' pk_test_4a577548e16f8563'};

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

let sHtmlErrores = function (lstErrores) {
    let sHtmlMensaje = '';
    Object.values(lstErrores).forEach(lstError => {
        lstError.forEach(sError => sHtmlMensaje += (sError.charAt(0).toUpperCase() + sError.slice(1) + '<br>'));
    });
    return sHtmlMensaje;
};

$('.link-locale').click(function (event) {
    event.preventDefault();

    let locale = $(this).attr('data-locale');

    axios.post('/language/' + locale).then(response => {
        let respuesta = response.data;
        if (respuesta.result === result.success) {
            location.reload();
        }
    });
});

Vue.directive('star-rating', {
    inserted: function (el, binding) {
        let options = binding.value || {};

        let readOnly = options.readOnly ? options.readOnly : false;
        let rating = options.rating ? options.rating : 0;

        $(el).starrr({
            max: 5,
            readOnly: readOnly,
            rating: rating,
        });
    }
});

Vue.directive('icheck', {
    inserted: function (el, binding) {
        let options = binding.value || {};

        let type = options.type ? options.type : 'checkbox';

        $(el).unbind();
        $(el).iCheck({checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green'}).on('ifToggled', function (e) {
            let input = $(el).find(`input[type=${type}]`)[0];
            let event = new Event('change', {target: input, cancelable: true});
            input.dispatchEvent(event);
            event.preventDefault();
        });
    }
});

Vue.directive('autocomplete', {
    inserted: function (el, binding) {
        let options = binding.value || {};

        let url = options.url;
        let appendTo = options.appendTo;
        let select = options.select;
        let change = options.change;
        let renderItem = options.renderItem;

        $(el).autocomplete({
            source: function (request, response) {
                let formData = new FormData();
                formData.append('texto', request.term);

                axios.post(url, formData)
                    .then(res => response(res.data.data))
                    .catch(error => toastr[result.error](error.response.data.error));
                /*$.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data: {texto: request.term},
                    success: function (respuesta) {
                        response(respuesta.data);
                    },
                    error: function (e) {
                        toastr['error'](e.responseText);
                    }
                });*/
            },
            appendTo: appendTo,
            minLength: 2,
            select: select,
            change: change
        }).autocomplete('instance')._renderItem = renderItem;
    }
});

let ajaxWebsiteLocale = () => {
    return axios.post('/ajax/locale');
    /*$.ajax({
        type: 'post',
        url: '/ajax/locale',
        dataType: 'json',
        success: function (respuesta) {
            if (respuesta.result === result.success) {
                if (onSuccess) {
                    onSuccess(respuesta);
                }
            }
        },
    });*/
};

let ajaxWebsiteListarCarritoCompras = function () {
    return axios.post('/ajax/listarCarrito');
};

let mostrarMensajeProductoAgregado = () => {
    toastr.clear();
    toastr.options = {
        'closeButton': false, 'debug': false, 'newestOnTop': false,
        'progressBar': false, 'positionClass': 'toast-top-right', 'preventDuplicates': true, 'onclick': null,
        'showDuration': '300', 'hideDuration': '1000', 'timeOut': 0, 'extendedTimeOut': 0,
        'showEasing': 'swing', 'hideEasing': 'linear', 'showMethod': 'fadeIn', 'hideMethod': 'fadeOut'
    };

    toastr[result.success](`<p class="text-center font-weight-bold text-ecovalle-2">Producto agregado correctamente al carrito de compras.</p>
    <div class="text-center mt-2">
    <button class="btn btn-sm btn-ecovalle mr-3" onclick="toastr.clear()">Continuar comprando</button>
    <a class="btn btn-sm btn-amarillo" href="/carrito-compras">Ver carrito de compras</a>
    </div>`);
};

let ajaxWebsiteAgregarAlCarrito = function (producto, actualizarLstProductos, lstCarritoCompras, guardarLstCarritoCompras) {
    let formData = new FormData();
    formData.append('iProductoId', producto.id);

    return axios.post('/ajax/agregarAlCarrito', formData).then(response => {
        let respuesta = response.data;
        if (respuesta.result === result.success) {
            mostrarMensajeProductoAgregado();

            producto.cantidad = 1;
            actualizarLstProductos();

            let detalle = {cliente_id: null, cantidad: 1, producto_id: producto.id, producto: producto};
            lstCarritoCompras.push(detalle);

            guardarLstCarritoCompras();
        }
    });
};

let ajaxWebsiteEliminarDelCarrito = function (iProductoId) {
    let formData = new FormData();
    formData.append('iProductoId', iProductoId);
    return axios.post('/ajax/eliminarDelCarrito', formData);
};

let ajaxWebsiteDisminuirCantidadProductoCarrito = function (iProductoId) {
    let formData = new FormData();
    formData.append('iProductoId', iProductoId);
    return axios.post('/ajax/disminuirCantidadProductoCarrito', formData);
};

let ajaxWebsiteAumentarCantidadProductoCarrito = function (iProductoId) {
    let formData = new FormData();
    formData.append('iProductoId', iProductoId);
    return axios.post('/ajax/aumentarCantidadProductoCarrito', formData);
};

let ajaxWebsiteAumentarCantidadProductoCarritoCant = function (iProductoId,iCantidad) {
    let formData = new FormData();
    formData.append('iProductoId', iProductoId);
    formData.append('iCantidad', iCantidad);
    return axios.post('/ajax/aumentarCantidadProductoCarritoCantidad', formData);
};

let ajaxSetLocale = locale => {
    let sUrl = '/language/' + locale;
    axios.post(sUrl).then(response => {
        let respuesta = response.data;
        if (respuesta.result === result.success) {
            location.reload();
        }
    });
};

let vueInicioSesion = new Vue({
    el: '#inicioSesion',
    data: {
        iComprobando: 0,
        sMensaje: null,
        sClase: null
    },
    computed: {
        sLstCarritoCompras: function () {
            let lstCarritoCompras = $cookies.get('lstCarritoCompras');

            let sLstCarritoCompras = '';
            for (let detalle of lstCarritoCompras) {
                sLstCarritoCompras += detalle.producto_id + ';' + detalle.cantidad + '|'
            }

            return sLstCarritoCompras.substring(0, sLstCarritoCompras.length - 1);
        }
    },
    methods: {
        ajaxIngresar: function () {
            this.iComprobando = 1;

            let frmIniciarSesion = document.getElementById('frmIniciarSesion');
            let formData = new FormData(frmIniciarSesion);
            formData.append('sLstCarritoCompras', this.sLstCarritoCompras);

            let $this = this;
            axios.post('/iniciar-sesion/ajax/ingresar', formData)
                .then(response => {
                    let respuesta = response.data;
                    if (respuesta.result === result.success) {
                        location.reload();
                    } else {
                        $this.iComprobando = 0;
                        $this.sClase = respuesta.result === result.error ? 'alert-danger' : ('alert-' + respuesta.result);
                        $this.sMensaje = respuesta.mensaje;
                    }
                })
                .catch(error => {
                    $this.iComprobando = 0;
                    $this.sClase = 'alert-danger';
                    $this.sMensaje = `Ocurrió un error inesperado.
                    Intentar una vez más debería solucionar el problema;
                    de no ser así, comuníquese con el administrador del sistema.`;
                });

            /*$.ajax({
                type: 'post',
                url: '/iniciar-sesion/ajax/ingresar',
                data: $('#frmIniciarSesion').serialize() + '&sLstCarritoCompras=' + $this.sLstCarritoCompras,
                dataType: 'json',
                success: function (respuesta) {
                    if (respuesta.result === result.success) {
                        location.reload();
                    } else {
                        $this.iComprobando = 0;
                        $this.sClase = respuesta.result === result.error ? 'alert-danger' : ('alert-' + respuesta.result);
                        $this.sMensaje = respuesta.mensaje;
                    }
                },
                error: function () {
                    $this.iComprobando = 0;
                    $this.sClase = 'alert-danger';
                    $this.sMensaje = 'Ocurrió un error inesperado. Intentar una vez más debería solucionar el problema; de no ser así, comuníquese con el administrador del sistema.';
                }
            });*/
        }
    }
});

let chunk = (array, size) => Array.from({length: Math.ceil(array.length / size)}, (v, i) => array.slice(i * size, i * size + size));

function autocompletar()
{
    const inputSearch = document.querySelector("#inputSearch");
    let indexFocus = -1;

    inputSearch.addEventListener("input", function(){
        const search_p = this.value;
        cerrarLista();
        if(search_p == '')
        {
            cerrarLista();
        }
        if(!search_p) return false;
        const divList = document.createElement("div");
        divList.setAttribute("id", this.id + "-lista-autocompletar");
        divList.setAttribute("class", "lista-autocompletar-items");
        this.parentNode.appendChild(divList);

        /*if(arreglo.length == 0) return false;
        arreglo.forEach(item =>{
            if(item.substr(0, search_p.length) == search_p)
            {
                const elementoLista = document.createElement('div');
                elementoLista.innerHTML = `<strong>${item.substr(0,search_p.length)}</strong>${item.substr(search_p.length)}`;
                divList.appendChild(elementoLista);
            }
        });*/

        var arr = [];
        $.ajax({
            type: 'post',
            url: '/tienda/ajax/buscarProducto',
            data: {'texto':search_p},
            dataType: 'json',
            success: function (respuesta) {
                if(respuesta.result == "success")
                {
                    arr = respuesta.data;
                    arr.forEach(item =>{
                        const elementoLista = document.createElement('div');
                        elementoLista.setAttribute("class", "div-item");
                        //elementoLista.innerHTML = `<strong>${item.nombre_es}</strong>`;
                        if(item.stock_actual > 0)
                        {
                            stock_guia = `<button class="btn btn-primary btn-sm">EN STOCK</button>`;
                        }else{
                            stock_guia = `<button class="btn btn-danger btn-sm">AGOTADO</button>`;
                        }
                        let sNombre = item.nombre_es;
                        let sRutaImagen = item.imagenes.length > 0 ? item.imagenes[0].ruta : '';
                        elementoLista.innerHTML = `<div class="row justify-content-between">
                                                    <div class="col-3"><img class="img-fluid img-thumbnail" src="${sRutaImagen}"></div>
                                                    <div class="col-9">
                                                    <a href="/tienda/producto/${item.id}" class="font-weight-bold text-dark px-0">${sNombre}</a>
                                                    <div class="row justify-content-between">
                                                    <div class="col-6"><p class="m-0 h4 text-amarillo-ecovalle font-weight-bold">S/ ${item.precio_actual.monto.toFixed(2)}</p></div>
                                                    <div class="col-6">`+stock_guia+`</div>
                                                    </div>
                                                    </div>
                                                    </div>`;
                        
                        elementoLista.addEventListener('click', function(){
                            //inputSearch.value = this.innerText;
                            cerrarLista();
                            window.location = `/tienda/producto/${item.id}`;
                        });
                        divList.appendChild(elementoLista);
                        //console.log(item.nombre_es);
                    });
                }
            },
            error: function () {
                toastr.clear();
                toastr.options = {
                    iconClasses: {
                        error: 'bg-danger',
                        info: 'bg-info',
                        success: 'bg-success',
                        warning: 'bg-warning',
                    },
                };
                toastr.error('Ninguna coincidencia');
            }
        });

    });

    inputSearch.addEventListener('keydown', function(e){
        const divList = document.querySelector('#' + this.id + '-lista-autocompletar');
        let items;

        if(divList){
            items = divList.querySelectorAll('.div-item'); 

            switch (e.keyCode) {
                case 40: //abajo
                    indexFocus++;
                    if(indexFocus > items.length-1) indexFocus = items.length - 1;
                    break;
                case 38: //arriba
                    indexFocus--;
                    if(indexFocus < 0) indexFocus = 0;
                    break;
                case 13: //enter
                    e.preventDefault();
                    items[indexFocus].click();
                    indexFocus = -1;
                    break;
                default:
                    break;
            }
            seleccionar(items, indexFocus);
            return false;
        }
    });

    document.addEventListener('click', function(){
        cerrarLista();
    });
}

function seleccionar(lista, indexFocus){
    if(!lista || indexFocus == -1) return false;
    lista.forEach(x =>{x.classList.remove('autocompletar-active')});
    lista[indexFocus].classList.add("autocompletar-active");
}

function cerrarLista(){
    const items = document.querySelectorAll(".lista-autocompletar-items");
    items.forEach(item =>{item.parentNode.removeChild(item);});
    indexFocus = -1;
}
