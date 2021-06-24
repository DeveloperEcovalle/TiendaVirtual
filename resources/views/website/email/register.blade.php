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
            width: 10%;
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
            <p class="title">!Bienvenid@ a Ecovalle!</p>
        </div>
        <div class="card-saludo">
            <p class="text">Hola Fulanita:</p>
            <p class="text">Gracias por formar parte de esta gran familia Ecovalle, aqui obtendr&aacute;s informaci&oacute;n de nuevos productos y promociones exclusivas.</p>
            <p class="text">¡Empecemos!</p>
        </div>
        <div class="contenido">
            <div class="card-imgs">
                <div class="img-1">
                    <img class="img-fluid" src="https://ecovalle.pe/storage/empresa/jn7uB7YOFTmgDnRsFya7Zrv4Fw3w4b8NH9h7tbgb.svg" alt="ImgBanner">
                </div>
                <div class="img-2">
                    <img class="img-fluid" style="margin-top: 40%;" src="https://ecovalle.pe/storage/empresa/AWDdpi0kNlUTomcQ8w7CopiosqjlIl93wYe5N9Ph.svg" alt="ImgBanner">
                </div>
                <div class="img-3">
                    <img class="img-fluid" style="margin-top: 20%;" src="https://ecovalle.pe/storage/empresa/Z54MZxw09BS7JmntZ8tS4zjVHoi5wpyulHaCvguK.svg" alt="ImgBanner">
                </div>
            </div>
            <div class="card-mensaje">
                <p class="text-mensaje">
                    ¿Est&aacute;s pensando en emprender? Hazlo con nosotros, te invitamos a ser Socio Ecovalle y recibir precios exclusivos en todos nuestros productos.
                </p>
                <a href="https://ecovalle.pe/se-ecovalle/socios" class="btn-socio">Si, quiero ser socio</a>
            </div>
        </div>
    </div>
</body>
</html>