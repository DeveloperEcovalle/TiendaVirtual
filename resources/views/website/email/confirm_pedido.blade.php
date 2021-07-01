<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            background-color: #f5f9f4;
            font-family: "Gill Sans Extrabold", Helvetica, sans-serif;
        }

        header {
            background-color: #02793C;
            text-align: center;
            padding-bottom: 0%;
        }

        header img{
            padding-top: 1vw;
            width: 15%;
            height: 10%;
            padding-bottom: 1vw;
        }

        header .line-header {
            width: 100%;
            height: calc(0.2em + 0.2vw) !important;
        }

        .line-header-1 {
            width: 80%;
            height: 100%;
            background-color: #0b965c;
            float: left;
        }

        .line-header-2 {
            width: 15%;
            height: 100%;
            background-color: #3fa33f;
            float: left;
        }

        .line-header-3{
            width: 5%;
            height: 100%;
            background-color: #FF9D28;
            float: left;
        }

        /**========CONTAINER==========**/
        .container {
            padding-top: 2vh;
            padding-bottom: 2vh;
            padding-left: 5%;
            padding-right: 5%;
            height: auto;
            background-color: #fff;
        }

        .hr-divider {
            width: 100%;
            height: 0px;
            border-top: 3px solid #333;
            margin-top: 1%;
            margin-bottom: 1%;
        }

        .card-title {
            text-align: center;
        }

        .card {
            padding: 1%;
        }

        .tbl-detalle {
            width: 100%;
        }

        .tbl-detalle tr td {
            width: 50%;
        }

        .title {
            font-size: 2vw; /*2vw*/
            font-weight: bolder;
        }

        .text {
            font-size: 1.5vw; /*2vw*/
            font-weight: 500;
        }

        .total {
            font-size: 2vw; /*2vw*/
            font-weight: 550;
            color: #02793C;
        }

        .nota {
            padding: 5%;
            background-color: #f5f9f4;
            border-radius: 1vw;
            height: auto;
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
            <p class="title">{{ $estado->titulo }}</p>
        </div>
        <p class="title" style="margin-bottom: 1vw;">Compra {{ $venta->codigo }}</p>
        <div class="hr-divider"></div>
        <div class="card">
            <table class="tbl-detalle">
                <tr>
                    <td style="padding-right: 5%;">
                        <p class="text" style="margin-bottom: 1%;">Compra: </p>
                        <p class="text" style="margin-top: 0%; text-transform: uppercase !important;">{{ $venta->tipo_compra }}</p>
                        @if($venta->tipo_compra != 'RECOJO EN TIENDA' && !empty($agencia))
                        <p class="text" style="margin-bottom: 1%;">Enviado a trav&eacute;s de:</p>
                        <p class="text" style="margin-top: 0%; text-transform: uppercase !important; margin-bottom: 1%;">{{ $agencia->nombre }}</p>
                        <p class="text" style="margin-top: 0%; text-transform: uppercase !important;">{{ $agencia->descripcion }}</p>
                        @endif
                        @if($venta->tipo_compra != 'RECOJO EN TIENDA')
                        <p class="text" style="margin-bottom: 1%;">Enviado a:</p>
                        @else
                        <p class="text" style="margin-bottom: 1%;">Detalle:</p>
                        @endif
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;">{{ $venta->cliente }}</p>
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;">{{  $venta->direccion }}@if(!empty($venta->ubigeo)){{', '.$venta->ubigeo->distrito.', '. $venta->ubigeo->provincia.', '.$venta->ubigeo->departamento}}@endif</p>
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;">Per&uacute; (PE)</p>
                        <p class="text" style="margin-top: 0%;">{{ $venta->telefono }}</p>
                        @if($estado->descripcion)
                            <p class="text">{{  $estado->descripcion }}</p>
                        @endif
                        
                    </td>
                    <td style="padding-left: 5%;">
                        <p class="text" style="margin-bottom: 1%;">Punto de entrega:</p>
                        @if($venta->tipo_compra != 'RECOJO EN TIENDA')
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;">{{  $venta->direccion }}@if(!empty($venta->ubigeo)){{', '.$venta->ubigeo->distrito.', '. $venta->ubigeo->provincia.', '.$venta->ubigeo->departamento}}@endif</p>
                        @else
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;">{{  $empresa->direccion }}</p>
                        <p class="text" style="margin-top: 0%; margin-bottom: 1%;display: none;">Frente al estadio nacional</p>
                        @endif
                        <p class="text" style="margin-bottom: 1%;">Importe total de compra</p>
                        <p class="total" style="margin-top: 0%; margin-bottom: 1%;">S/. {{ number_format($venta->subtotal + $venta->delivery,2) }}</p>
                        @if($estado->nota)
                        <div class="nota">
                            <p class="text">
                                Nota: {{  $estado->nota }}
                            </p>
                        </div>
                        @endif
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
</body>
</html>