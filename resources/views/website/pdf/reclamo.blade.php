<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        html{font-family:sans-serif;}

        body {
            margin: 1cm 1cm 1cm 1cm;
        }

        header {
            position: fixed;
            padding-top: 0.8cm;
            padding-left: 1.1cm;
            right: 0cm;
            height: 1.5cm;
            background-color:transparent;
            color: white;
            text-align: left;
            line-height: 1.3cm;
        }
        table td{border: 1px solid #000000; padding: 2px;}

        .padre {
            display: table;
            height:100px;
        }
        .hijo {
            display: table-cell;
            vertical-align: middle;
        }
    </style>
    <body>
        <header>
            <img src="https://www.ecovalle.pe/img/logo_ecovalle.png" style="width: 120px;height: 50px;">
        </header>
        <div style="padding-top: 1.3cm">
            <table style="width: 100%;margin:0px;" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="4" align="center">
                        <b style="font-size: 12px ;margin:2px !important;">LIBRO DE RECLAMACIONES</b>
                    </td>
                    <td rowspan="2" colspan="2" align="center">
                        <b style="font-size: 12px !important; margin-top: 2px !important;">HOJA DE RECLAMACIÓN</b><br>
                        <p style="font-size: 10px !important; margin: 1px !important;">N° {{$reclamo->codigo}}</p>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <b style="font-size: 10px !important; margin: 1px !important;">FECHA:</b>
                    </td>
                    <td align="center">
                        <p style="font-size: 10px; margin: 1px !important;">{{date("d", strtotime($reclamo->fecha_registro))}}</p>
                    </td>
                    <td align="center">
                        <p style="font-size: 10px; margin: 1px !important;">{{date("m", strtotime($reclamo->fecha_registro))}}</p>
                    </td>
                    <td align="center">
                        <p style="font-size: 10px; margin: 1px !important;">{{date("Y", strtotime($reclamo->fecha_registro))}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 15px;margin-top: 2px !important">{{$empresa->razon_social}} / R.U.C. {{$empresa->ruc_empresa}}</b>&emsp;
                        <p style="font-size: 10px;margin-top: 0px !important;margin-bottom: 2px !important;">{{$empresa->direccion}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 12px;">1. IDENTIFICACIÓN DEL CONSUMIDOR RECLAMANTE:</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">NOMBRES:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->nombres}} {{$reclamo->apellidos}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">DOMICILIO:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->direccion}} - No. {{$reclamo->lote}}  {{$reclamo->departamento}}/{{$reclamo->provincia}}/{{$reclamo->distrito}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">DNI / CE:</b>
                    </td>
                    <td colspan="2">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->numero_documento}}</p>
                    </td>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">TELÉFONO:</b>
                    </td>
                    <td colspan="2">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->telefono}} @if ($reclamo->otelefono) - {{$reclamo->otelefono}}@endif</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">E-MAIL:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->email}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <p style="font-size: 10px; margin:1px;">Padre o Madre / Representante (en el caso de que usted sea menor de edad):</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">NOMBRES:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;"></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">DOMICILIO:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;"></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">DNI / CE:</b>
                    </td>
                    <td colspan="2">
                        <p style="font-size: 12px;margin:1px;"></p>
                    </td>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">TELÉFONO:</b>
                    </td>
                    <td colspan="2">
                        <p style="font-size: 12px;margin:1px;"></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;margin:1px;">E-MAIL:</b>
                    </td>
                    <td colspan="5">
                        <p style="font-size: 12px;margin:1px;"></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 12px;">2. IDENTIFICACIÓN DEL BIEN CONTRATADO:</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;">PRODUCTO:</b>
                    </td>
                    <td colspan="1" align="center">
                        @if ($reclamo->bien_contratado == 'Producto')
                        <b style="font-size: 12px;">X</b> 
                        @endif
                    </td>
                    <td colspan="1">
                        <b style="font-size: 12px;">SERVICIO:</b>
                    </td>
                    <td colspan="1" align="center">
                        @if ($reclamo->bien_contratado == 'Servicio')
                        <b style="font-size: 12px;">X</b> 
                        @endif
                    </td>
                    <td colspan="1">
                        <b style="font-size: 12px;">MONTO RECLAMADO:</b>
                    </td>
                    <td colspan="1">
                        <p style="font-size: 12px;margin:1px;">{{$reclamo->monto_bien}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 12px;">DESCRIPCIÓN:</b><br>
                        <p style="font-size: 12px;margin:0px;">{{$reclamo->descripcion}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 12px;">3. DETALLE DE LA RECLAMACIÓN:</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;">RECLAMO:</b>
                    </td>
                    <td colspan="1" align="center">
                        @if ($reclamo->tipo_reclamo == 'Reclamo')
                        <b style="font-size: 12px;">X</b>
                        @endif
                    </td>
                    <td colspan="4">
                        <p style="font-size: 10px; margin:1px;">Disconformidad relacionada a los productos o servicios.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="1">
                        <b style="font-size: 12px;">QUEJA:</b>
                    </td>
                    <td colspan="1" align="center">
                        @if ($reclamo->tipo_reclamo == 'Queja')
                        <b style="font-size: 12px;">X</b>
                        @endif
                    </td>
                    <td colspan="4">
                        <p style="font-size: 10px; margin:1px;">Disconformidad no relacionada a los productos o servicios; malestar o descontento respecto a la atención al público.</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <b style="font-size: 12px;">DETALLE:</b>
                        <p style="font-size: 12px;margin:0px;">{{$reclamo->detalle}}</p>
                    </td>
                    <td colspan="2" rowspan="2">
                        <div style="float: top;">
                            <p style="font-size: 10px; margin:0px !important;">Firma del consumidor.</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <b style="font-size: 12px;">PEDIDO:</b>
                        <p style="font-size: 12px;margin:0px;">{{$reclamo->pedido}}</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <b style="font-size: 12px;">4. OBSERVACIONES Y ACCIONES ADOPTADAS POR EL PROVEEDOR:</b>
                    </td>
                </tr>
            </table>
            <table style="width: 100%;margin:0px;" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">
                        <b style="font-size: 12px;">FECHA DE COMUNICACIÓN DE RESPUESTA:</b>
                    </td>
                    <td colspan="1" align="center">
                        <p style="font-size: 12px;margin:0px;">[DÍA]</p>
                    </td>
                    <td colspan="1" align="center">
                        <p style="font-size: 12px;margin:0px;">[MES]</p>
                    </td>
                    <td colspan="1" align="center">
                        <p style="font-size: 12px;margin:0px;">[AÑO]</p>
                    </td>
                    <td colspan="1" rowspan="2">
                        <div style="float: top;">
                            <p style="font-size: 10px; margin:0px !important;">Firma del proveedor.</p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="padre">
                            <p style="font-size: 12px;margin:0px;" class="hijo">SU RECLAMO HA SIDO REGISTRADO Y SE PROCEDERA A SU EVALUACIÓN</p>
                        </div>
                    </td>
                </tr>
            </table>
            <p style="font-size: 10px; margin:0px;">* La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante INDECOPI.</p>
            <p style="font-size: 10px; margin:0px;">* El proveedor deberá dar respuesta al reclamo en un plazo no mayor de treinta(30) días calendario, pudiendo ampliar el plazo hasta por treinta(30) días más, previa
                comunicación al consumidor</p>
        </div>
    </body>
</html>