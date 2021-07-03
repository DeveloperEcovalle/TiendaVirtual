<div id="contenedor-producto" class="row modal-producto">
    <div class="col-12" v-if="lstCarrito.length === 0" v-cloak>
        <section class="pt-3 pb-3">
            <div class="container-xl py-3 my-3">
                <div class="row justify-content-center">
                    <div class="col-4 col-sm-2 col-md-5">
                        <h1 class="text-center h4 text-ecovalle-2">
                            <svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="shopping-cart" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="fa-shopping-cart fa-w-18 fa-2x">
                                <path fill="currentColor"
                                      d="M551.991 64H129.28l-8.329-44.423C118.822 8.226 108.911 0 97.362 0H12C5.373 0 0 5.373 0 12v8c0 6.627 5.373 12 12 12h78.72l69.927 372.946C150.305 416.314 144 431.42 144 448c0 35.346 28.654 64 64 64s64-28.654 64-64a63.681 63.681 0 0 0-8.583-32h145.167a63.681 63.681 0 0 0-8.583 32c0 35.346 28.654 64 64 64 35.346 0 64-28.654 64-64 0-17.993-7.435-34.24-19.388-45.868C506.022 391.891 496.76 384 485.328 384H189.28l-12-64h331.381c11.368 0 21.177-7.976 23.496-19.105l43.331-208C578.592 77.991 567.215 64 551.991 64zM240 448c0 17.645-14.355 32-32 32s-32-14.355-32-32 14.355-32 32-32 32 14.355 32 32zm224 32c-17.645 0-32-14.355-32-32s14.355-32 32-32 32 14.355 32 32-14.355 32-32 32zm38.156-192H171.28l-36-192h406.876l-40 192z"
                                      class=""></path>
                            </svg>
                        </h1>
                    </div>
                    <div class="col-12">
                        <h2 class="h5 text-center">Su carrito de compras est&aacute; vac&iacute;o</h2>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-12 m-0 p-1 carrito-productos">
        <div class="row minicart-shopping-list">
            <div class="col-12">
                <div class="row" v-for="producto in lstCarrito">
                    <div class="col-4 col-sm-4 col-md-4 col-lg-2 p-1 m-0 justify-content-between">
                        <a :href="'/tienda/producto/'+ producto.producto.id">
                            <img class="img-fluid-producto img-thumbnail" :src="producto.producto.imagenes[0].ruta">
                        </a>
                    </div>
                    <div class="col-8 col-sm-8 col-md-8 col-lg-6 p-1 m-0">
                        <p class="font-weight-bold text-muted mb-1 text-modal-producto d-none">ECOVALLE</p>
                        <p class="font-weight-bold mb-1 text-ecovalle text-modal-producto">@{{ producto.producto.nombre_es }}</p>
                        
                        <h4 class="small text-amarillo-ecovalle text-modal-producto font-weight-bold d-inline mr-2" v-if="producto.producto.oferta_vigente && producto.producto.promocion_vigente == null">
                            S/ @{{ (Math.round((producto.producto.oferta_vigente.porcentaje ? (producto.producto.precio_actual.monto * (100 - producto.producto.oferta_vigente.porcentaje) / 100) : (producto.producto.precio_actual.monto - producto.producto.oferta_vigente.monto)) * 10) / 10).toFixed(2) }}
                        </h4>
                        <h4 class="small text-amarillo-ecovalle text-modal-producto font-weight-bold d-inline" v-if="producto.producto.oferta_vigente == null && producto.producto.promocion_vigente == null">
                            S/ @{{ producto.producto.precio_actual.monto.toFixed(2) }}
                        </h4>
                        <h4 class="small text-muted text-modal-producto font-weight-bold d-inline" v-if="producto.producto.oferta_vigente && producto.producto.promocion_vigente == null" style="text-decoration:line-through;">
                            S/ @{{ producto.producto.precio_actual.monto.toFixed(2) }}
                        </h4>
                        <h4 class="small text-amarillo-ecovalle text-modal-producto font-weight-bold d-inline mr-2" v-if="producto.producto.oferta_vigente == null && producto.producto.promocion_vigente ">
                            <p class="d-inline" v-if="producto.producto.cantidad >= producto.producto.promocion_vigente.min && producto.producto.cantidad <= producto.producto.promocion_vigente.max">
                             S/ @{{ (Math.round((producto.producto.promocion_vigente.porcentaje ? (producto.producto.precio_actual.monto * (100 - producto.producto.promocion_vigente.porcentaje) / 100) : (producto.producto.precio_actual.monto - producto.producto.promocion_vigente.monto)) * 10) / 10).toFixed(2) }}
                            </p>
                         </h4>
                        <h4 class="small text-muted text-modal-producto font-weight-bold d-inline"  v-if="producto.producto.oferta_vigente == null && producto.producto.promocion_vigente" style="text-decoration:line-through;">
                            <p class="d-inline" v-if="producto.producto.cantidad >= producto.producto.promocion_vigente.min && producto.producto.cantidad <= producto.producto.promocion_vigente.max">
                                S/ @{{ producto.producto.precio_actual.monto.toFixed(2) }}
                            </p>
                        </h4>
        
                        <h4 class="small text-amarillo-ecovalle text-modal-producto font-weight-bold"  v-if="producto.producto.oferta_vigente == null && producto.producto.promocion_vigente">
                            <p class="d-inline" v-if="producto.producto.cantidad >= producto.producto.promocion_vigente.min && producto.producto.cantidad <= producto.producto.promocion_vigente.max">
                            </p>
                            <p class="d-inline" v-else>
                                S/ @{{ producto.producto.precio_actual.monto.toFixed(2) }}
                            </p>
                        </h4>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 p-1 m-0 text-center">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <button type="button" class="btn btn-toast text-modal-producto" v-on:click="ajaxDisminuirCantidadProductoCarritoModal(producto.producto)">
                                    <i class="fas" :class="{ 'fa-minus': producto.cantidad > 1, 'fa-trash-alt': producto.cantidad === 1 }"></i>
                                </button>
                                <button type="button" class="btn btn-toast text-modal-producto" disabled>
                                    @{{ producto.cantidad }}
                                </button>
                                <button type="button" v-on:click="ajaxAumentarCantidadProductoCarritoModal(producto.producto)" class="btn btn-toast text-modal-producto">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 carrito-total">
        <div class="row">
            <div class="col-12 p-0 m-0">
                <div class="hr-compra-2"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-6 pl-4 pr-4 m-0 text-left">
                <p class="font-weight-bold mb-0 text-ecovalle text-modal-total">Total</p>
            </div>
            <div class="col-6 pl-4 pr-4 m-0 text-right">
                <p class="font-weight-bold mb-0 text-ecovalle text-modal-total">S/. @{{ fSubtotal.toFixed(2) }}</p>
            </div>
            <div class="col-6 pl-4 pr-4 m-0 text-left">
                <p class="font-weight-bold text-muted mt-0 mb-1 text-modal-total">Ahorraste</p>
            </div>
            <div class="col-6 pl-4 pr-4 m-0 text-right">
                <p class="font-weight-bold text-muted mt-0 mb-1 text-modal-total">S/. @{{ fDescuento.toFixed(2) }}</p>
            </div>
            <div class="col-md-6 p-1 m-0">
                <button type="button" v-on:click="removeModal" class="btn btn-sm btn-block btn-ecovalle-compra-modal mb-1 text-modal-total">Continuar comprando</button>
            </div>
            <div class="col-md-6 p-1 m-0">
                <a class="btn btn-sm btn-block btn-amarillo-compra-modal text-modal-total" href="/carrito-compras">Procesar compra</a>
            </div>
        </div>
    </div>
</div>