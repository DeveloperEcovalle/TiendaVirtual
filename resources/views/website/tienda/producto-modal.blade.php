<div id="contenedor-producto" class="row position-fixed p-2 modal-producto">
    <div class="col-12">
        <div class="row">
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 p-1 m-0" style="zoom: 85%;">
                <img class="img-fluid img-thumbnail" :src="producto.producto.imagenes[0].ruta">
            </div>
            <div class="col-6 col-sm-6 col-md-6 col-lg-6 p-1 m-0" style="zoom: 85%;">
                <p class="font-weight-bold text-muted mb-1">ECOVALLE</p>
                <p class="font-weight-bold mb-1 text-ecovalle">@{{ producto.producto.nombre_es }}</p>
                <p class="font-weight-bold mb-1 text-ecovalle">S/. @{{ producto.producto.precio_actual.monto.toFixed(2) }}</p>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 p-1 m-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="button" class="btn btn-toast" v-on:click="ajaxDisminuirCantidadProductoCarrito(producto.producto)">
                            <i class="fas" :class="{ 'fa-minus': producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                        </button>
                        <button type="button" class="btn btn-toast" disabled>
                            @{{ producto.cantidad }}
                        </button>
                        <button type="button" v-on:click="ajaxAumentarCantidadProductoCarrito(producto.producto)" class="btn btn-toast">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-12 p-0 m-0">
                <div class="hr-compra-2"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 p-1 m-0 text-left">
                <p class="font-weight-bold mb-1 text-ecovalle">Total</p>
            </div>
            <div class="col-6 p-1 m-0 text-right">
                <p class="font-weight-bold mb-1 text-ecovalle">S/. @{{ producto.producto.precio_actual.monto.toFixed(2) }}</p>
            </div>
            <div class="col-md-6 p-1 m-0">
                <button type="button" v-on:click="removeModal" class="btn btn-sm btn-block btn-ecovalle-compra-modal mb-1">Continuar comprando</button>
            </div>
            <div class="col-md-6 p-1 m-0">
                <a class="btn btn-sm btn-block btn-amarillo-compra-modal" href="/carrito-compras">Procesar compra</a>
            </div>
        </div>
    </div>
</div>