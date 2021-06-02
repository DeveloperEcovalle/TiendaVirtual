<div class="col-12 p-4 py-2" style="background-color: #6BBD99;">
    <p>Hola <b>{{session('cliente')->persona->nombres}}</b> (¿no eres <b>{{session('cliente')->persona->nombres}}</b>? <a href="#" v-on:click.prevent="ajaxSalir()">Cerrar sesión</a>)

        Desde el panel de control de tu cuenta puedes ver tus pedidos recientes, gestionar tus datos de envío y facturación, editar tu contraseña y los detalles de tu cuenta.</p>
</div>