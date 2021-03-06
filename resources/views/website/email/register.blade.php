<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body{
            padding: 10vw;
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
            padding: 2vh;
            padding-bottom: 2vh;
            padding-left: 5vw;
            padding-right: 5vw;
            height: auto;
            background-color: #fff;
        }

        .card-title {
            width: 100%;
            height: auto;
            margin-bottom: 1vw;
            text-align: center;
        }

        .title {
            font-size: 2vw; /*2vw*/
            font-weight: bolder;
        }

        .card-saludo {
            width: 100%;
            height: auto;
            text-align: justify;
        }

        .text {
            font-size: 1.5vw; /*2vw*/
            font-weight: 500;
        }

        .contenido {
            background-color: #f5f9f4;
            border-radius: 1vw;
            padding: 5%;
        }

        .card-imgs {
            height: 15vw;
            padding: 1%;
        }

        .img-1 {
            margin-left: 27%;
            width: 15%;
            height: 100%;
            float: left;
        }

        .img-2 {
            width: 15%;
            height: 100%;
            float: left;
        }

        .img-3 {
            margin-right: 27%;
            width: 15%;
            height: 100%;
            float: left;
        }

        .card-mensaje {
            height: 13vw;
            padding: 1%;
            text-align: center;
        }

        .text-mensaje {
            font-size: 2vw; /*2vw*/
            font-weight: bolder;
            margin-top: 1%;
            margin-bottom: 2%;
        }

        .btn-socio {
            text-decoration: none;
            outline: none;
            background-color: #FF9D28;
            color: #fff;
            margin-top: 1%;
            padding: 1%;
            font-size: 1.5vw;
            border-radius: 0.5vw;
            font-weight: bolder;
        }

        .contenido-2 {
            height: 5vw;
            padding-top: 1%;
            padding-bottom: 1%;
        }

        .card-image {
            float: left;
            width: 6%;
            height: 100%;
            display: flex;
            align-items: center;
            align-content: center;
            margin-right: 1%;
            margin-left: 16%;
        }

        .card-image div {
            height: 80%;
            display: flex;
            align-content: center;
            align-items: start;
        }

        .card-text-com {
            float: left;
            width: 35%;
            height: 100%;
            display: flex;
            align-items: center;
            align-content: center;
        }

        .card-text-com div {
            height: 50%;
            display: flex;
            align-content: center;
            align-items: start;
        }

        .card-btn {
            float: left;
            width: 25%;
            height: 100%;
            display: flex;
            align-items: center;
            margin-right: 16%;
            align-content: center;
        }

        .card-btn div {
            height: 50%;
            display: flex;
            align-content: center;
            align-items: start;
        }
        
        .img-fluid {
            width: 100%;
            height: 70%;
        }

        .img-fluid-1 {
            width: 100%;
            height: 100%;
        }

        .link {
            outline: none;
            color: black;
            font-size: 2vw; /*2vw*/
            font-weight: bolder;
            margin: 0vw;
            padding: 0vw;
        }

        .container-2 {
            text-align: center;
        }
    </style>

</head>
<body>
    <header>
        <img src="https://www.ecovalle.pe/img/KJK56VJVdjsdjs2nvYYvkj.png" alt="Ecovalle">
        <div class="line-header">
            <div class="line-header-1"></div>
            <div class="line-header-2"></div>
            <div class="line-header-3"></div>
        </div>
    </header>

    <div class="container">
        <div class="card-title">
            <p class="title">!Bienvenid@ a Ecovalle!</p>
        </div>
        <div class="card-saludo">
            <p class="text">Hola {{$persona->nombres}}:</p>
            <p class="text">Gracias por formar parte de esta gran familia Ecovalle, aqui obtendr&aacute;s informaci&oacute;n de nuevos productos y promociones exclusivas.</p>
            <p class="text">??Empecemos!</p>
        </div>
        <div class="contenido">
            <div class="card-imgs">
                <div class="img-1">
                    <img class="img-fluid" src="https://ecovalle.pe/storage/empresa/dVdC3GsjwI98xiIAvnDlisLULepz2TzfRH1kE2jB.png" alt="ImgBanner">
                </div>
                <div class="img-2">
                    <img class="img-fluid" style="margin-top: 40%;" src="https://ecovalle.pe/storage/empresa/dVdC3GsjwI98xiIAvnDlisLULepz2TzfRH1kE2jB.png" alt="ImgBanner">
                </div>
                <div class="img-3">
                    <img class="img-fluid" style="margin-top: 20%;" src="https://ecovalle.pe/storage/empresa/dVdC3GsjwI98xiIAvnDlisLULepz2TzfRH1kE2jB.png" alt="ImgBanner">
                </div>
            </div>
            <div class="card-mensaje">
                <p class="text-mensaje">
                    ??Est&aacute;s pensando en emprender? Hazlo con nosotros, te invitamos a ser Socio Ecovalle y recibir precios exclusivos en todos nuestros productos.
                </p>
                <a href="https://ecovalle.pe/se-ecovalle/socios" class="btn-socio">Si, quiero ser socio</a>
            </div>
        </div>
        <div class="contenido-2">
            <div class="card-image">
                <div>
                    <img class="img-fluid-1" src="https://ecovalle.pe/img/5a4525f5546ddca7e1fcbc86.png" alt="Contactanos">
                </div>
            </div>
            <div class="card-text-com">
                <div>
                    <p class="title" style="margin: 0vw;">??A&uacute;n necesitas ayuda?</p>
                </div>
            </div>
            <div class="card-btn">
                <div>
                    <a href="https://ecovalle.pe/contactanos" class="link"> Cont&aacute;ctanos</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-2">
        <p class="text" style="padding-left: 2%; padding-right: 2%;">Para seguir recibiendo mensajes de correo electr&oacute;nico de nuestra parte, debe agregar comunicacion@ecovalle.pe a tu lista de direcciones.</p>
    </div>
</body>
</html>