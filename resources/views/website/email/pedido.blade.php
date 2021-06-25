
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEDIDO DETALLE</title>
    <style>
        body{
            background-color: #f5f9f4;
            font-family: "Gill Sans Extrabold", Helvetica, sans-serif;
        }

        header {
            padding-top: 1%;
            text-align: center;
            background-color: #02793C;
            padding-bottom: 0%;
        }

        header img{
            width: 15%;
            height: 10%;
        }

        header .line-header{
            width: 100%;
            margin-top: 1%;
            height: calc(0.4em + 0.4vw) !important;
        }

        .line-header-1 {
            float: left;
            background-color: #0b965c;
            width: 80%;
            height: 100%;
        }

        .line-header-2 {
            float: left;
            background-color: #3fa33f;
            width: 15%;
            height: 100%;
        }

        .line-header-3 {
            float: left;
            background-color: #FF9D28;
            width: 5%;
            height: 100%;
        }

        .container {
            padding-top: 2vh;
            padding-bottom: 2vh;
            padding-left: 5%;
            padding-right: 5%;
            height: auto;
            background-color: #fff;
        }

        .card-title {
            width: 100%;
            height: auto;
            text-align: center;
        }

        .title {
            font-size: 2vw;
            font-weight: bolder;
        }

        .title-2 {
            font-size: 2vw !important;
            font-weight: bolder;
            margin-bottom: 1px;
        }

        .card-text {
            width: 100%;
            height: auto;
            text-align: justify;
        }

        .text {
            font-size: 1.5vw !important;
            font-weight: normal;
        }

        table {
            width: 100%;
        }

        .tbl-detalles tr .img {
            width: 20%;
            height: auto;
            padding-left: 1%;
            padding-right: 1%;
        }
        
        .image-producto {
            width: 50%;
        }
        .image-fluid {
            width: 100%;
            height: 100%;
        }

        .tbl-detalles tr .producto-descripcion {
            vertical-align: top;
            width: 60%;
            height: auto;
            padding-left: 1%;
            padding-right: 1%;
        }

        .descripcion {
            vertical-align: top;
            height: auto;
            width: 100%;
        }

        .title-producto {
            font-size: 1.5vw;
            font-weight: bold;
            margin: 2px;
        }

        .tbl-detalles tr .producto-precio {
            width: 20%;
            height: 20%;
            padding-left: 1%;
            padding-right: 1%;
        }

        .hr-divider {
            width: 100%;
            height: 0px;
            border-top: 3px solid #333;
            margin-top: 1%;
            margin-bottom: 1%;
        }

        .resum-total {
            font-size: 1.5vw;
            font-weight: normal;
            margin: 2px;
        }

        .total {
            font-size: 2vw;
            font-weight: bolder;
            margin: 2px;
            color: #02793C;
        }

        .tbl-total tr .contenido {
            height: auto;
            padding-left: 1%;
            padding-right: 1%;
            padding-bottom: 0%;

        }

        .title-envio {
            font-size: 1.5vw;
            font-weight: normal;
            margin-bottom: 0%;
        }

        .text-envio {
            font-size: 1.5vw !important;
            font-weight: normal;
            margin-top: 1px;
            margin-bottom: 1px;
        }

        .text-uppercase{
            text-transform: uppercase !important;
        }

        .br {
            margin-top: 1% !important;
            width: 100%;
        }

        .subcontainer {
            height: auto;
        }

        .btn-orden {
            text-decoration: none;
            outline: none;
            background-color: #FF9D28;
            color: #fff;
            padding: 1vw !important;
            font-size: 1.5vw !important;
            border-radius: 0.5vw !important;
            width: 90%;
            font-weight: bold;
        }

        .tbl-informacion {
            width: 100%;
        }

        .tbl-informacion tr td {
            width: 50%;
        }

        .card-footer{
            background-color: #02793C;
            height: auto;
            padding: 2%;
        }

        .tbl-footer {
            width: 100%;
        }

        .tbl-footer tr td {
            width: 50%;
        }

        .text-footer{
            margin: 0vw;
            font-size: 1.5vw;
            font-weight: bolder;
            color: #fff;
        }

        .btn-footer {
            text-decoration: none;
            outline: none;
            background-color: #FF9D28;
            color: #fff;
            padding: 0.5vw;
            font-size: 1.5vw;
            border-radius: 0.2vw;
            font-weight: bolder;
            text-align: center;
            width: 100%;
        }

        .card-contactanos{
            background-color: #fff;
            height: auto;
            padding: 2%;
        }

        .tbl-contacto {
            width: 100%;
        }

        .image {
            width: 40%;
            height: 40%;
        }

        .img-fluid {
            width: 60%;
            height: 100%;
        }

        .title-1 {
            font-size: 1.5vw; /*2vw*/
            font-weight: bolder;
        }

        .link {
            outline: none;
            text-decoration: none;
            color: black;
            font-size: 1.5vw; /*2vw*/
            font-weight: bolder;
        }

        .container-2 {
            padding-top: 0vh;
            padding-bottom: 2vh;
            padding-left: 10%;
            padding-right: 10%;
            height: auto;
        }

        .tbl-final {
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://www.ecovalle.pe/img/KJK56VJVdjsdjs2nvYYvkj.png" alt="">
        <div class="line-header">
            <div class="line-header-1"></div>
            <div class="line-header-2"></div>
            <div class="line-header-3"></div>
        </div>
    </header>
    <div class="container">
        <div class="card-title">
            <p class="title">¡Agradecemos su compra!</p>
        </div>
        <div class="card-text">
            <p class="text">
                Le enviaremos la informaci&oacute;n del env&iacute;o de su orden una vez sea confirmado el pago seg&uacute;n su preferencia.
            </p>
        </div>
        <p class="title-2">Compra {{ $venta->codigo }}</p>
        <div class="hr-divider"></div>
        <table class="tbl-detalles" cellpadding="0" cellspacing="0">
            @foreach ($venta->detalles as $detalle)
            <tr>
                <td class="img" align="center">
                    <div class="image-producto">
                        <img src="{{ 'https://www.ecovalle.pe/'.$detalle->producto->imagenes[0]->ruta }}" class="image-fluid" alt="Prdct">
                    </div>
                </td>
                <td class="producto-descripcion">
                    <div class="descripcion">
                        <p class="title-producto">{{ $detalle->producto->nombre_es}}</p>
                        <p class="title-producto text-uppercase">Cantidad: {{ $detalle->cantidad }}</p>
                    </div>
                </td>
                <td class="producto-precio" align="right">
                    <div class="precio">
                        <p class="title-producto">S/. {{ number_format(round((($detalle->precio_venta * $detalle->cantidad) * 10) / 10,1), 2) }}</p>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
        <div class="hr-divider"></div>
        <table class="tbl-total" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Subtotal</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. {{ number_format($venta->subtotal + $venta->descuento,2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Descuento</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. {{ number_format($venta->descuento,2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Env&iacute;o</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. {{ number_format($venta->delivery,2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Cup&oacute;n</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. 0.00</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td colspan="2">
                    <div class="hr-divider"></div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="total">Compra total</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="total">S/. {{ number_format($venta->subtotal + $venta->delivery,2) }}</p>
                </td>
            </tr>
        </table>
        <div class="subcontainer">
            <table class="tbl-informacion">
                <tr>
                    <td style="padding-right: 2%;">
                        <p class="title-2">Informaci&oacute;n de env&iacute;o</p>
                        <div class="hr-divider"></div>
                    </td>
                    <td style="padding-left: 2%;">
                        <p class="title-2">Informaci&oacute;n de pago</p>
                        <div class="hr-divider"></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 2%;">
                        <p class="title-envio">Compra: </p>
                        <p class="text-envio text-uppercase">{{ $venta->tipo_compra }}</p>
                        <div class="br"></div>
                        @if($venta->tipo_compra != 'RECOJO EN TIENDA')
                        <p class="title-envio">Enviado por:</p>
                        <p class="text-envio text-uppercase">{{ $venta->agencia }}</p>
                        @endif

                        @if($venta->tipo_compra != 'RECOJO EN TIENDA')
                        <p class="title-envio">Enviado a:</p>
                        @else
                        <p class="title-envio">Detalle:</p>
                        @endif
                        <div class="br"></div>
                        <p class="text-envio">{{ $venta->cliente }}</p>
                        <p class="text-envio">{{  $venta->direccion }}@if(!empty($venta->ubigeo)){{', '.$venta->ubigeo->distrito.', '. $venta->ubigeo->provincia.', '.$venta->ubigeo->departamento}}@endif</p>
                        <p class="text-envio">Per&uacute; (PE)</p>
                        <p class="text-envio">Cel. {{ $venta->telefono }}</p>
                    </td>
                    <td style="padding-left: 2%;">
                        <p class="title-envio">Pagado con:</p>
                        <p class="text-envio">Transferencia / Dep&oacute;sito</p>
                        <div class="br"></div>
                        @if($venta->tipo_compra != 'RECOJO EN TIENDA')
                        <p class="title-envio">Direcci&oacute;n de facturaci&oacute;n:</p>
                        <p class="text-envio">{{ $venta->cliente }}</p>
                        <p class="text-envio">{{  $venta->direccion }}@if(!empty($venta->ubigeo)){{', '.$venta->ubigeo->distrito.', '. $venta->ubigeo->provincia.', '.$venta->ubigeo->departamento}}@endif</p>
                        @else
                        <p class="title-envio">Facturaci&oacute;n:</p>
                        <p class="text-envio">{{ $venta->cliente }}</p>
                        @endif
                        <p class="text-envio">Per&uacute; (PE)</p>
                        <p class="text-envio">Cel. {{ $venta->telefono }}</p><br>
                        <div style="text-align: center;">
                            <a href="https://ecovalle.pe/mi-cuenta?menu=3" class="btn-orden">Ver orden de compra</a>
                        </div><br>
                    </td>
                </tr>
            </table>
        </div>
        <div class="hr-divider"></div>
        <div class="card-footer">
            <table class="tbl-footer">
                <tr>
                    <td>
                        <p class="text-footer">No se pierda las &uacute;ltimas promociones</p>
                    </td>
                    <td align="right">
                        <a href="https://www.ecovalle.pe/registro" class="btn-footer">Registrese ahora</a>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-contactanos">
            <table class="tbl-contacto" cellspacing="0">
                <tr>
                    <td style="width: 20vw" align="right">
                        <div class="image">
                            <img class="img-fluid" src="https://ecovalle.pe/img/5a4525f5546ddca7e1fcbc86.png" alt="Contactanos">
                        </div>
                    </td>
                    <td style="width: 30vw" align="left">
                        <p class="title-1">¿A&uacute;n necesitas ayuda?</p>
                    </td>
                    <td style="width: 20vw" align="left">
                        <div style="display: flex; align-items: center;">
                            <a href="https://ecovalle.pe/contactanos" class="link"> Cont&aacute;ctanos</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="container-2">
        <table class="tbl-final">
            <tr>
                <td align="center">
                    <p class="text">Para seguir recibiendo mensajes de correo electr&oacute;nico de nuestra parte, debe agregar comunicacion@ecovalle.pe a tu lista de direcciones.</p>
                </td>
            </tr>
        </table>
    </div>

    <div>
        <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #666; margin: 20px 0; padding: 0;">
    </div>
</body>
</html>