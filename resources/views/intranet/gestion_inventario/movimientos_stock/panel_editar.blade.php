<div class="d-flex border-bottom white-bg">
    <div class="col-12 p-3 font-bold">Movimiento de stock</div>
</div>
<div class="p-4 bg-white border-top" id="layoutRight">
    <div class="form-group row">
        <label class="font-weight-bold col-4">Evento</label>
        <div class="col-8">
            <p>@{{ movimiento.evento }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Producto</label>
        <div class="col-8">
            <p>@{{ movimiento.producto.nombre_es }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Stock anterior</label>
        <div class="col-8">
            <p>@{{ movimiento.stock_anterior }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Cantidad movida</label>
        <div class="col-8">
            <p>@{{ movimiento.cantidad }}</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="font-weight-bold col-4">Stock actual</label>
        <div class="col-8">
            <p>@{{ movimiento.stock_actual }}</p>
        </div>
    </div>
</div>
