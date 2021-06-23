<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PEDIDO DETALLE</title>
    <style>
        body{
            padding: 5px;
            font-family: "Gill Sans Extrabold", Helvetica, sans-serif;
        }

        header {
            padding-top: 1%;
            text-align: center;
            background-color: #02793C;
            padding-bottom: 0%;
        }

        header img{
            width: 10%;
            height: 10%;
        }

        header .line-header{
            width: 100%;
            margin-top: 1%;
            height: calc(0.4em + 0.4vw) !important;
        }

        .line-header-1{
            float: left;
            background-color: #0b965c;
            width: 80%;
            height: 100%;
        }

        .line-header-2{
            float: left;
            background-color: #3fa33f;
            width: 15%;
            height: 100%;
        }

        .line-header-3{
            float: left;
            background-color: #FF9D28;
            width: 5%;
            height: 100%;
        }

        footer {
            background-color: #02793C;
            align-items: center;
            height: 10%;
            margin-top: 2%;
        }

        .footer-container{
            background-color: #02793C;
            display: flex;
            padding: 1%;
            height: calc(0.4em + 0.4vw) !important;
            align-items: center;
        }

        .colum1{
            align-items: center;
            float: left;
            width: 50%;
        }
        
        .colum2{
            align-items: center;
            float: left;
            width: 50%;
            text-align: right;
        }

        .text-footer {
            font-weight: bolder;
            color: #fff;
            font-size: calc(0.4em + 0.4vw) !important;
            margin: 0%;
        }

        .btn-footer {
            text-decoration: none;
            outline: none;
            background-color: #FF9D28;
            color: #fff;
            padding: calc(0.3em + 0.3vw) !important;
            font-size: calc(0.4em + 0.4vw) !important;
            border-radius: calc(0.3em + 0.3vw) !important;
            font-weight: bold;
            margin: 0%;
        }

        .control-footer {
            border: 1px solid #02793C;
            height: 10px;
        }

        /*--------------------*/
        footer .line-footer{
            width: 100%;
            height: calc(0.4em + 0.4vw) !important;
        }

        .line-footer-1{
            float: left;
            background-color: #0b965c;
            width: 80%;
            height: 100%;
        }

        .line-footer-2{
            float: left;
            background-color: #3fa33f;
            width: 15%;
            height: 100%;
        }

        .line-footer-3{
            float: left;
            background-color: #FF9D28;
            width: 5%;
            height: 100%;
        }
        /*--------------------*/

        .container{
            border-right: 0.5px solid rgba(0, 0, 0, 0.2) !important;
            border-left: 0.5px solid rgba(0, 0, 0, 0.2) !important;
            padding: 5px !important;
        }

        .card-title {
            width: 100%;
            height: auto;
            text-align: center;
        }

        .title {
            font-size: calc(0.6em + 0.6vw) !important;
            font-weight: bolder;
        }

        .title-2 {
            font-size: calc(0.6em + 0.6vw) !important;
            font-weight: bolder;
            margin-bottom: 1px;
        }

        .card-text {
            width: 100%;
            height: auto;
            text-align: justify;
        }

        .text {
            font-size: calc(0.4em + 0.4vw) !important;
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
            width: calc(3em + 3vw) !important;
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
            font-size: calc(0.4em + 0.4vw) !important;
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
            font-size: calc(0.4em + 0.4vw) !important;
            font-weight: normal;
            margin: 2px;
        }

        .total {
            font-size: calc(0.5em + 0.5vw) !important;
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

        .subcontainer {
            display: flex;
        }

        .subcontainer-1{
            float: left;
            width: 50%;
            margin-right: 1%;
        }

        .subcontainer-2{
            float: left;
            width: 50%;
            margin-left: 1%;
        }

        .title-envio {
            font-size: calc(0.4em + 0.4vw) !important;
            font-weight: normal;
            margin-bottom: 0%;
        }

        .text-envio {
            font-size: calc(0.4em + 0.4vw) !important;
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

        .div-btn{
            width: 100%;
            text-align: center;
        }

        .btn-orden {
            text-decoration: none;
            outline: none;
            background-color: #FF9D28;
            color: #fff;
            padding: calc(0.3em + 0.3vw) !important;
            font-size: calc(0.4em + 0.4vw) !important;
            border-radius: calc(0.3em + 0.3vw) !important;
            width: 90%;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <img src="https://www.ecovalle.pe/img/logo_ecovalle.png" alt="">
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
        <p class="title-2">Compra {{ $compra->codigo }}</p>
        <div class="hr-divider"></div>
        <table class="tbl-detalles" cellpadding="0" cellspacing="0">
            @foreach ($compra->detalles as $detalle)
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
                    <p class="resum-total">S/. {{ number_format($compra->subtotal + $compra->descuento,2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Descuento</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. {{ number_format($compra->descuento,2) }}</p>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;"></td>
                <td class="contenido" style="width: 30%;" align="left">
                    <p class="resum-total">Env&iacute;o</p>
                </td>
                <td class="contenido" style="width: 20%;" align="right">
                    <p class="resum-total">S/. {{ number_format($compra->delivery,2) }}</p>
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
                    <p class="total">S/. {{ number_format($compra->subtotal + $compra->delivery,2) }}</p>
                </td>
            </tr>
        </table>
        <div class="subcontainer">
            <div class="subcontainer-1">
                <p class="title-2">Informaci&oacute;n de env&iacute;o</p>
                <div class="hr-divider"></div>
                <div class="informacion-envio">
                    <p class="title-envio">Enviado por:</p>
                    <p class="text-envio text-uppercase">{{ $compra->agencia }}</p>
                    <div class="br"></div>
                    <p class="title-envio">Enviado a:</p>
                    <p class="text-envio">{{ $compra->cliente }}</p>
                    <p class="text-envio">{{  $compra->direccion }}, {{  $compra->ubigeo->distrito }}, {{  $compra->ubigeo->provincia }}, {{  $compra->ubigeo->departamento }}</p>
                    <p class="text-envio">Cel. {{ $compra->telefono }}</p>
                </div>
            </div>
            <div class="subcontainer-2">
                <p class="title-2">Informaci&oacute;n de pago</p>
                <div class="hr-divider"></div>
                <div class="informacion-pago">
                    <p class="title-envio">Pagado con:</p>
                    <p class="text-envio">Transferencia / Dep&oacute;sito</p>
                    <div class="br"></div>
                    <p class="title-envio">Direcci&oacute;n de facturaci&oacute;n:</p>
                    <p class="text-envio">{{ $compra->cliente }}</p>
                    <p class="text-envio">{{  $compra->direccion }}, {{  $compra->ubigeo->distrito }}, {{  $compra->ubigeo->provincia }}, {{  $compra->ubigeo->departamento }}</p>
                    <p class="text-envio">Cel. {{ $compra->telefono }}</p>
                    <div class="br"></div>
                    <div class="div-btn">
                        <a href="#" class="btn-orden">Ver orden de compra</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="line-footer">            
            <div class="line-footer-3"></div>
            <div class="line-footer-2"></div>
            <div class="line-footer-1"></div>
        </div>
        <div class="control-footer"></div>
        <div class="footer-container">
            <div class="colum1">
                <p class="text-footer">No se pierda las &uacute;ltimas promociones</p>
            </div>
            <div class="colum2">
                <a href="https://www.ecovalle.pe/registro" class="btn-footer">Registrese ahora</a>
            </div>
        </div>
        <div class="control-footer"></div>
        <div class="line-footer">
            <div class="line-footer-1"></div>
            <div class="line-footer-2"></div>
            <div class="line-footer-3"></div>
        </div>
    </footer>

    <div>
        <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #666; margin: 20px 0; padding: 0;">
    </div>
</body>
</html>