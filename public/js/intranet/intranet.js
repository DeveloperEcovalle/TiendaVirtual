const result = {
    success: 'success',
    warning: 'warning',
    error: 'error'
};

function isNumber(e) {
    return (e.charCode == 8 || e.charCode == 0 || e.charCode == 13) ? null : e.charCode >= 48 && e.charCode <= 57;
}

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

let listarMenus = function (onSuccess) {
    $.ajax({
        type: 'post',
        url: '/intranet/ajax/listar-menus',
        data: {iModuloId: $('meta[name=iModuloId]').attr('content')},
        success: function (respuesta) {
            if (respuesta.result === result.success) {
                let data = respuesta.data;
                
                let lstModulos = data.lstModulos;
                let lstMenus = data.lstMenus;

                if (onSuccess) {
                    onSuccess(lstModulos, lstMenus);
                }
            }
        },
        error: function (respuesta) {
            //TODO IMPLEMENTAR ACCION SI NO SE PUEDE LISTAR MENUS
        }
    });
};

Vue.directive('autocomplete', {
    inserted: function (el, binding) {
        let options = binding.value || {};

        let url = options.url;
        let appendTo = options.appendTo;
        let select = options.select;
        let change = options.change;

        $(el).autocomplete({
            source: function (request, response) {
                $.ajax({
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
                });
            },
            appendTo: appendTo,
            minLength: 2,
            select: select,
            change: change
        });
    }
});
