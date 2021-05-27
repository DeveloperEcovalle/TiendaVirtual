<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
    .bold,b,strong{font-weight:700}
    body{background-repeat:no-repeat;background-position:center center;text-align:center;margin:0}
    .tabla_borde{border:1px solid #666;
    border-radius:10px}
    tr.border_bottom td{border-bottom:1px solid #000}
    tr.border_top td{border-top:1px solid #666}
    .table-valores-totales tbody>tr>td{border:0}
    .table-valores-totales>tbody>tr>td:first-child{text-align:right}
    .table-valores-totales>tbody>tr>td:last-child{border-bottom:1px solid #666;text-align:right;width:30%}
    hr,img{border:0}
    table td{font-size:12px}
    html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;font-size:10px;-webkit-tap-highlight-color:transparent}a{&
    }
    a:active,a:hover{outline:0}
    img{vertical-align:middle}
    hr{height:0;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;margin-top:20px;margin-bottom:20px;border-top:1px solid #eee}
    table{border-spacing:0;border-collapse:collapse}
    @media print{blockquote,img,tr{page-break-inside:avoid}*,:after,:before{color:#000!important;text-shadow:none!important;background:0 0!important;-webkit-box-shadow:none!important;box-shadow:none!important}
    a,a:visited{text-decoration:underline}a[href]:after{content:" (" attr(href) ")"}
    blockquote{border:1px solid #999}
    img{max-width:100%!important}p{orphans:3;widows:3}
    .table{border-collapse:collapse!important}
    .table td{background-color:#fff!important}}
    a,a:focus,a:hover{text-decoration:none}*,:after,:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}a{color:#428bca;cursor:pointer}
    a:focus,a:hover{color:#2a6496}a:focus{outline:dotted thin;outline:-webkit-focus-ring-color auto 5px;outline-offset:-2px}
    h6{font-family:inherit;line-height:1.1;color:inherit;margin-top:10px;margin-bottom:10px}
    p{margin:0 0 10px}blockquote{padding:10px 20px;margin:0 0 20px;font-size:17.5px;border-left:5px solid #eee}
    table{background-color:transparent}
    .table{width:100%;max-width:100%;margin-bottom:20px}
    h6{font-weight:100;font-size:10px}
    body{line-height:1.42857143;font-family:"open sans","Helvetica Neue",Helvetica,Arial,sans-serif;
    background-color:#2f4050;font-size:13px;color:#676a6c;overflow-x:hidden}
    .table>tbody>tr>td{vertical-align:top;border-top:1px solid #e7eaec;line-height:1.42857;padding:8px}
    .white-bg{background-color:#fff}td{padding:6}
    .table-valores-totales tbody>tr>td{border-top:0 none!important}
    </style>
    </head>
<body class="white-bg" style="font-family:'Verdana'">
<table style="width: 100%">
<tbody><tr>
<td style="padding:30px;">
    <table width="100%" height="200px" border="0" aling="center" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td valign="bottom">
    
                    <table width="100%" height="100%" border="0" border-radius="" cellpadding="9" cellspacing="0">
                        <tbody>
                            <tr>
                                <td width="50%" height="90" align="center">
                                    <span>
                                        <img src="https://www.ecovalle.pe/img/logo_ecovalle.png" height="80" style="text-align:center"
                                            border="0">
                                    </span>
                                </td>
                            </tr>
                            <tr>
    
                                <td align="center" style="text-transform: uppercase;">
                                    <strong>
                                        <span style="font-size:15px">AGROENSANCHA S.R.L</span>
                                    </strong>
                                    <br>
                                    <strong>
                                        <span style="font-size:15px" text-align="center">R.U.C: 20482089594</span>
                                    </strong>
                                    <br>
                                    <strong>Dirección : </strong>Jr. José Martí 2184 La Esperanza 13007 Trujillo, Perú
                                    <br>    
    
                                    <span style="font-family:Tahoma, Geneva, sans-serif; font-size:20px" text-align="center">P E D I D O</span>
                                    <br>
                                    <span style="font-family:Tahoma, Geneva, sans-serif; font-size:19px" text-align="center">C O N F I R M A D O</span>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    COD:
                                    <span>{{ $venta->codigo }}</span>
                                </td>
                            </tr>
    
                            <tr>
                                <td width="60%" height="15" align="left">
                                    <strong>Tipo Compra:</strong>&nbsp; {{ $venta->tipo_compra }}
                                    <br>
                                    <strong>Tipo Comprobante:</strong>&nbsp; {{ $venta->tipo_comprobante }}
                                    <br>
                                    <strong>Razón Social:</strong>&nbsp; {{ $venta->cliente }}
                                    <br>
                                    <strong>{{ $venta->tipo_documento == 'RUC' ? 'R.U.C:' : 'DNI:' }}</strong>&nbsp; {{  $venta->documento }}
                                    <br>
                                    <strong>Dirección: </strong>&nbsp; {{  $venta->direccion }}
                                    <br>
                                    <strong>Fecha Emisión: </strong>&nbsp; {{ date_format($venta->created_at, 'Y/m/d')}}
                                    <br>
                                    @if ($venta->recoge)
                                    <strong>Encargado recojo:</strong>&nbsp; {{ $venta->recoge }}
                                    <br>
                                    <strong>DNI:</strong>&nbsp; {{ $venta->recoge_documento }}
                                    <br>
                                    <strong>Teléfono:</strong>&nbsp; {{ $venta->recoge_telefono }}
                                    <br>
                                    @endif
                                    <strong>Tipo Moneda: </strong>&nbsp; SOLES/PEN.
                                </td>
                            </tr>
                        </tbody>
                    </table>
    
                </td>
            </tr>
        </tbody>
    </table>
    
    <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tbody>
            <tr>
                <td align="center" class="bold">Cantidad</td>
                <!--<th align="center" class="bold">Unidad Medida</th>-->
                <!--<td align="center" class="bold">Código</td>-->
                <td align="center" class="bold">Descripción</td>
                <td align="center" class="bold">Valor Unitario</td>
                <td align="center" class="bold">Precio Total</td>
            </tr>
            @foreach ($carrito as $producto)
            <tr class="border_top">
                <td align="center">
                    {{ $producto->cantidad }} UND
                </td>
                <td align="center" width="300px">
                    <span>{{ $producto->nombre_es }}</span>
                    <br>
                </td>
                <td align="center">
                    S/. {{ number_format(round(($producto->pFinal * 10) / 10,1), 2) }}
                </td>
                <td align="center">
                    S/. {{ number_format(round((($producto->cantidad * $producto->pFinal) * 10) / 10, 1),2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td width="50%" valign="top">
                    <table width="100%" border="0" cellpadding="5" cellspacing="0">
                        <tbody>
                            <tr class="border_top">
                                <td align="left">
                                    <strong>SubTotal:</strong>
                                </td>
                                <td width="50" align="left">
                                    <span>S/. {{ number_format($venta->subtotal,2) }}</span>
                                </td>
                            </tr>
                            <tr class="border_top">
                                <td align="left">
                                    <strong>Delivery:</strong>
                                </td>
                                <td width="50" align="left">
                                    <span>S/. {{ number_format($venta->delivery,2) }}</span>
                                </td>
                            </tr>
                            <tr class="border_top">
                                <td align="left">
                                    <strong>Total a Pagar:</strong>
                                </td>
                                <td width="250" align="left">
                                    <span id="ride-importeTotal" class="ride-totalPagar">S/. {{ number_format($venta->subtotal + $venta->delivery,2) }}</span>
                                </td>
                            </tr>
                            <!--Anticipos-->
                            <!--End Anticipos-->
                            <tr>
                                <td colspan="4">
                                    <br>
                                    <br>
                                    <span style="font-family:Tahoma, Geneva, sans-serif; font-size:12px" text-align="center">
                                        <strong>-------------------------------------------------------</strong>
                                    </span>
                                    <br>
                                    <br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
    
                </td>
    
            </tr>
        </tbody>
    </table>
    
    <br>
    <br>
    
    <!-- Este div lo que hace es que si el contenido no entra completamente en la pagina te lo envia a una nueva pagina. Evitando que una imagen se divida por ejemplo-->
    <div>
        <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #666; margin: 20px 0; padding: 0;">
    </div>
    
    </td>
    </tr>
    </tbody>
    </table>
</body></html>