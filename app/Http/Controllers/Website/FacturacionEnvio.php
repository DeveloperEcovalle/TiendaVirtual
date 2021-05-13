<?php

namespace App\Http\Controllers\Website;

use App\DetalleCarrito;
use App\Http\Controllers\SunatComprobantesXml;
use App\IcbperAnual;
use App\Mail\NotificacionComprobanteCliente;
use App\Mail\VentaComprobanteCliente;
use App\PrecioEnvio;
use App\Producto;
use App\SerieComprobante;
use App\SunatCertificado;
use App\SunatFacturaBoleta;
use App\SunatFacturaBoletaDetalle;
use App\TipoComprobante;
use Culqi\Culqi;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\TelefonoEmpresa;
use App\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FacturacionEnvio extends Website {

    protected $lstTraduccionesFacturacionEnvio;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesFacturacionEnvio = [
            'en' => [
                'empty_cart' => 'Your shopping cart is empty',
                'update_cart' => 'Refresh shopping cart',
                'order_summary' => 'Order summary',
                'price_summary' => 'Price summary',

                'product' => 'Product',
                'price' => 'Price',
                'quantity' => 'Quantity',
                'delete' => 'Delete',
                'proceed_to_purchase' => 'Proceed to purchase',
                'payment' => 'Payment',

                'delivery_address' => 'Delivery address',
                'delivery_address_placeholder' => 'Enter here the address where the product will arrive',
                'subtotal_product_amount' => 'Subtotal product amount',

                'billing_and_delivery' => 'Billing and Delivery',
                'billing' => 'Billing',
                'receipt_type' => 'Receipt type',
                'identity_card' => 'Identity card',
                'document_number' => 'Document number',
                'dni' => 'ID',
                'name' => 'Name',
                'last_name' => 'Last Name',
                'company_name' => 'Company Name',
                'phone' => 'Phone',
                'email' => 'Email',
                'additional_information' => 'Additional information',
                'notes_order' => 'Notes for the order',

                'enter_your_data' => 'Enter all your data or <a class="nav-ecovalle" href="#" data-toggle="modal" data-target="#modalInicioSesion">sign in</a>',
                'your_order' => 'Your Order',
                'delivery' => 'Delivery',
                'credit_or_debit_card' => 'Credit or debit card',
                'credit_or_debit_card_desc' => 'Pay with your credit or debit card. We accept all cards.',
                'privacy_policy' => 'Your personal data will be used to process your order, improve your experience on this website and other purposes described in our <a class="nav-ecovalle" href="/politica-privacidad">privacy policy</a>.',
                'terms_and_conditions' => 'I have read and agree to the <a class="nav-ecovalle" href="/terminos-condiciones">terms and conditions</a> of the website.',
                'pay' => 'PAY',

                'finish' => 'FINISH',
            ],
            'es' => [
                'empty_cart' => 'Tu carrito de compras está vacío',
                'update_cart' => 'Actualizar carrito de compras',
                'order_summary' => 'Resumen del pedido',
                'price_summary' => 'Resumen de precio',

                'product' => 'Producto',
                'price' => 'Precio',
                'quantity' => 'Cantidad',
                'delete' => 'Eliminar',
                'proceed_to_purchase' => 'Proceder a la compra',
                'payment' => 'Pago',

                'delivery_address' => 'Dirección de envío',
                'delivery_address_placeholder' => 'Ingrese aquí la dirección a donde llegará el producto',
                'subtotal_product_amount' => 'Subtotal de productos',

                'billing_and_delivery' => 'Facturación y Envío',
                'receipt_type' => 'Tipo de comprobante',
                'billing' => 'Facturación',
                'identity_card' => 'Dcto. de identidad',
                'document_number' => 'Número de documento',
                'dni' => 'DNI',
                'name' => 'Nombres',
                'last_name' => 'Apellidos',
                'company_name' => 'Razón Social',
                'phone' => 'Teléfono / Celular',
                'email' => 'Correo electrónico',
                'additional_information' => 'Información adicional',
                'notes_order' => 'Notas para el pedido',

                'enter_your_data' => 'Ingresa todos tus datos o <a class="nav-ecovalle" href="#" data-toggle="modal" data-target="#modalInicioSesion">inicia sesi&oacute;n</a>',
                'your_order' => 'Tu Pedido',
                'delivery' => 'Envío',
                'credit_or_debit_card' => 'Tarjeta de crédito o débito',
                'credit_or_debit_card_desc' => 'Paga con tu tarjeta de crédito o débito. Aceptamos todas las tarjetas.',
                'privacy_policy' => 'Tus datos personales se utilizarán para procesar tu pedido, mejorar tu experiencia en esta web y otros propósitos descritos en nuestra <a class="nav-ecovalle" href="/politica-privacidad">política de privacidad</a>.',
                'terms_and_conditions' => 'He leído y estoy de acuerdo con los <a class="nav-ecovalle" href="/terminos-condiciones">términos y condiciones</a> de la web.',
                'pay' => 'PAGAR',

                'finish' => 'FINALIZAR',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $empresa = Empresa::with(['telefonos'])->first();
        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesFacturacionEnvio' => $this->lstTraduccionesFacturacionEnvio[$locale],
            'iPagina' => -1,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.facturacion_envio', $data);
    }

    public function ajaxListarPreciosEnvio() {
        // $lstPreciosEnvio = PrecioEnvio::all();

        // $respuesta = new Respuesta;
        // $respuesta->result = Result::SUCCESS;
        // $respuesta->data = ['lstPreciosEnvio' => $lstPreciosEnvio];

        // return response()->json($respuesta);
        $lstPreciosEnvio = Ubigeo::where('estado','ACTIVO')->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPreciosEnvio' => $lstPreciosEnvio];

        return response()->json($respuesta);
    }

    public function ajaxListarDatosFacturacion(Request $request) {
        //TODO VERIFICAR EL STOCK DE LOS PRODUCTOS

        $cliente = $request->session()->get('cliente');
        if ($cliente) {
            $cliente->refresh();
            $cliente->load('persona', 'persona.documentos', 'ubigeo');
        }

        $lstTiposComprobante = TipoComprobante::whereHas('tipo_comprobante_sunat', function ($sunat_tipo_comprobante) {
            $sunat_tipo_comprobante->where('ventas', 1);
        })->with('tipo_comprobante_sunat', 'tipo_comprobante_sunat.tipos_documento')->orderBy('id')->get();

        $lstUbigeo = Ubigeo::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['cliente' => $cliente, 'lstTiposComprobante' => $lstTiposComprobante, 'lstUbigeo' => $lstUbigeo];

        return response()->json($respuesta);
    }

    public function ajaxCrearCargo(Request $request) {
        $respuesta = new Respuesta;

        /*$sRutaComprobanteCityo = storage_path('app/public/comprobantes/10179123261-03-B001-614.xml');
        $sRutaComprobanteEcovalle = storage_path('app/public/comprobantes/20482089594-03-B001-13.xml');

        $xmlReader = new \XMLReader();

        $xmlReader->open($sRutaComprobanteCityo);
        $xmlReader->setParserProperty(\XMLReader::VALIDATE, true);
        $xmlCityoValido = $xmlReader->isValid();

        $xmlReader->open($sRutaComprobanteEcovalle);
        $xmlReader->setParserProperty(\XMLReader::VALIDATE, true);
        $xmlEcovalleValido = $xmlReader->isValid();

        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['xmlCityoValido' => $xmlCityoValido, 'xmlEcovalleValido' => $xmlEcovalleValido];

        return response()->json($respuesta);*/

        $detalles = $request->get('detalles');
        if (strlen($detalles) === 0) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'El carrito de compras está vacío.';
            return response()->json($respuesta);
        }

        $SECRET_KEY = "sk_test_yE35C4w9LPOqh1qp";
        $culqi = new Culqi(array('api_key' => $SECRET_KEY));

        $token = $request->get('token');
        $amount = $request->get('amount');
        $email = $request->get('email');

        $cargo = $culqi->Charges->create(array('amount' => $amount, 'currency_code' => 'PEN', 'email' => $email, 'source_id' => $token));

        if ($cargo === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No se pudo obtener el registro de pago.<br>Verifique su cuenta o línea de crédito a través de su banca por internet.';
            return response()->json($respuesta);
        }

        $dataRespuesta = ['cargo' => $cargo];

        $cliente = $request->session()->get('cliente');
        $fecha_emision = now();

        $iIdInterno = SunatFacturaBoleta::max('id_interno');
        $iNuevoIdInterno = $iIdInterno ? intval($iIdInterno) + 1 : 1;

        $iTipoComprobanteId = $request->get('tipo_de_comprobante');
        $tipoComprobante = TipoComprobante::find($iTipoComprobanteId);

        $serie = SerieComprobante::where('tipo_comprobante_id', $iTipoComprobanteId)
            ->where(function ($query) {
                $query->where('correlativo_actual', '<', 'correlativo_limite')
                    ->orWhere('correlativo_limite', 0);
            })->first();

        if ($serie === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Pago registrado correctamente.<br>
            Sin embargo, no se pudo generar el comprobante electrónico.<br>
            Por favor comuníquese con nosotros.';
            $respuesta->data = $dataRespuesta;

            return response()->json($respuesta);
        }

        $nombres = $request->get('nombres');
        $apellidos = $request->get('apellidos');
        $razon_social = $request->get('razon_social');
        $razon_social_cliente = $tipoComprobante->sunat_01_codigo === '01' ? $razon_social : ($nombres . ' ' . $apellidos);

        $fMontoTotalVenta = floatval($amount) / 100;
        $fMontoSinIgv = round($fMontoTotalVenta / 1.18, 8);
        $fMontoIgv = round($fMontoTotalVenta - $fMontoSinIgv, 8);

        $icbperAnual = IcbperAnual::where('anio', $fecha_emision->year)->first();

        $sRucEmisor = '20482089594';

        //region 1. INSERTAR LA BOLETA O FACTURA
        $sunatFacturaBoleta = new SunatFacturaBoleta;
        $sunatFacturaBoleta->id_interno = $iNuevoIdInterno;
        $sunatFacturaBoleta->tipo_comprobante_id = $iTipoComprobanteId;
        $sunatFacturaBoleta->cliente_id = $cliente ? $cliente->id : null;
        $sunatFacturaBoleta->serie_comprobante_id = $serie->id;
        $sunatFacturaBoleta->fecha_emision = $fecha_emision->toDateString();
        $sunatFacturaBoleta->hora_emision = $fecha_emision->toTimeString();
        $sunatFacturaBoleta->razon_social = 'AGROENSANCHA S.R.L.';
        $sunatFacturaBoleta->nombre_comercial = 'ECOVALLE';
        $sunatFacturaBoleta->domicilio_ubigeo = '13012';
        $sunatFacturaBoleta->domicilio_direccion_detallada = 'JR. JOSE MARTI NRO. 2184 OTR. LA ESPERANZA (A TRES CUADRAS DEL PUENTE CAPRICORNIO)';
        $sunatFacturaBoleta->domicilio_urbanizacion = 'LA ESPERANZA';
        $sunatFacturaBoleta->domicilio_provincia = 'TRUJILLO';
        $sunatFacturaBoleta->domicilio_departamento = 'LA LIBERTAD';
        $sunatFacturaBoleta->domicilio_distrito = 'TRUJILLO';
        $sunatFacturaBoleta->domicilio_codigo_pais = '51';
        $sunatFacturaBoleta->empresa_numero_ruc = $sRucEmisor;
        $sunatFacturaBoleta->empresa_tipo_documento = '6';
        $sunatFacturaBoleta->tipo_comprobante = $tipoComprobante->sunat_01_codigo;
        $sunatFacturaBoleta->serie_comprobante = $serie->valor;
        $sunatFacturaBoleta->nro_comprobante = $serie->correlativo_actual;
        $sunatFacturaBoleta->nro_documento_cliente = $request->get('numero_de_documento');
        $sunatFacturaBoleta->tipo_documento_cliente = $request->get('tipo_de_documento');
        $sunatFacturaBoleta->razon_social_cliente = $razon_social_cliente;
        $sunatFacturaBoleta->direccion_cliente = $request->get('direccion_de_envio');
        $sunatFacturaBoleta->total_valor_venta_bruto = $fMontoSinIgv;
        $sunatFacturaBoleta->total_valor_venta_neto = $fMontoSinIgv;
        $sunatFacturaBoleta->total_valor_venta_gravada_tipo_codigo = '1001';
        $sunatFacturaBoleta->total_valor_venta_gravada_monto = $fMontoSinIgv;
        $sunatFacturaBoleta->total_valor_venta_inafecta_tipo_codigo = '1002';
        $sunatFacturaBoleta->total_valor_venta_inafecta_monto = 0;
        $sunatFacturaBoleta->total_valor_venta_exonerada_tipo_codigo = '1003';
        $sunatFacturaBoleta->total_valor_venta_exonerada_monto = 0;
        $sunatFacturaBoleta->sumatoria_igv_monto_1 = $fMontoIgv;
        $sunatFacturaBoleta->sumatoria_igv_monto_2 = $fMontoIgv;
        $sunatFacturaBoleta->sumatoria_igv_codigo_tributo = '1000';
        $sunatFacturaBoleta->sumatoria_igv_nombre_tributo = 'IGV';
        $sunatFacturaBoleta->sumatoria_igv_codigo_tributo_internacional = 'VAT';
        $sunatFacturaBoleta->sumatoria_icbper_monto = 0;
        $sunatFacturaBoleta->icbper_anio = $icbperAnual->monto_icbper;
        $sunatFacturaBoleta->total_descuentos_monto = 0;
        $sunatFacturaBoleta->importe_total_venta = $fMontoTotalVenta;
        $sunatFacturaBoleta->tipo_moneda_comprobante = 'PEN';
        $sunatFacturaBoleta->leyenda_codigo = '1000';
        $sunatFacturaBoleta->leyenda_descripcion = '';
        $sunatFacturaBoleta->version_ubl = '2.1';
        $sunatFacturaBoleta->version_estructura_dcto = '1.0';
        $sunatFacturaBoleta->total_valor_venta_gratuitas_tipo_codigo = '1004';
        $sunatFacturaBoleta->descuentos_globales = 0;
        $sunatFacturaBoleta->porcentaje_descuentos_globales = 0;
        $sunatFacturaBoleta->descuentos_por_item_indicador = 'false';
        $sunatFacturaBoleta->descuentos_por_item_monto = 0;
        $sunatFacturaBoleta->sunat_igv = 18;
        $sunatFacturaBoleta->save();
        //endregion

        //region 2. INSERTAR LOS DETALLES DE LA BOLETA O FACTURA
        $lstSunatFacuraBoletaDetalles = [];
        $lstsDetalles = explode('|', $detalles);
        foreach ($lstsDetalles as $i => $sDetalle) {
            $detalle = explode(';', $sDetalle);

            $iProductoId = intval($detalle[0]);
            $iCantidad = intval($detalle[1]);
            $producto = Producto::find($iProductoId);

            $precioActual = $producto->precio_actual;
            $ofertaVigente = $producto->ofertaVigente;

            $fPrecioUnitario = $ofertaVigente === null ? $precioActual->monto :
                ($ofertaVigente->porcentaje ? ($precioActual->monto * (100 - $ofertaVigente->porcentaje) / 100) : ($precioActual->monto - $ofertaVigente->monto));
            $fValorVentaUnitario = round($fPrecioUnitario / 1.18, 8);

            $fPrecioTotal = $fPrecioUnitario * $iCantidad;
            $fValorVentaTotal = $fValorVentaUnitario * $iCantidad;
            $fMontoIgvDetalle = round($fPrecioTotal - $fValorVentaTotal, 8);

            $sunatFacturaBoletaDetalle = new SunatFacturaBoletaDetalle;
            $sunatFacturaBoletaDetalle->sunat_factura_boleta_id = $sunatFacturaBoleta->id;
            $sunatFacturaBoletaDetalle->producto_id = $iProductoId;
            $sunatFacturaBoletaDetalle->numero_orden = $i + 1;
            $sunatFacturaBoletaDetalle->unidad_medida = 'NIU';
            $sunatFacturaBoletaDetalle->cantidad = $iCantidad;
            $sunatFacturaBoletaDetalle->descripcion = $producto->nombre_es;
            $sunatFacturaBoletaDetalle->valor_unitario = $fValorVentaUnitario;
            $sunatFacturaBoletaDetalle->precio_venta_unitario_monto = $fPrecioUnitario;
            $sunatFacturaBoletaDetalle->precio_venta_unitario_codigo = '01';
            $sunatFacturaBoletaDetalle->igv_monto_1 = $fMontoIgvDetalle;
            $sunatFacturaBoletaDetalle->igv_monto_2 = $fMontoIgvDetalle;
            $sunatFacturaBoletaDetalle->igv_codigo_tipo = '10';
            $sunatFacturaBoletaDetalle->igv_codigo_tributo = '1000';
            $sunatFacturaBoletaDetalle->igv_nombre_tributo = 'IGV';
            $sunatFacturaBoletaDetalle->igv_codigo_tributo_internacional = 'VAT';
            $sunatFacturaBoletaDetalle->valor_venta = $fValorVentaTotal;
            $sunatFacturaBoletaDetalle->costo_promedio = 0;
            $sunatFacturaBoletaDetalle->precio_minimo = 0;
            $sunatFacturaBoletaDetalle->afectacion_igv = 'G';
            $sunatFacturaBoletaDetalle->valor_venta_bruto = $fValorVentaTotal;
            $sunatFacturaBoletaDetalle->save();

            $producto->stock_separado = $producto->stock_separado + $iCantidad;
            $producto->save();

            array_push($lstSunatFacuraBoletaDetalles, $sunatFacturaBoletaDetalle);
        }
        //endregion

        //region 3. ACTUALIZAR CORRELATIVO ACTUAL DE LA SERIE DEL COMPROBANTE
        $serie->correlativo_actual = $serie->correlativo_actual + 1;
        $serie->fecha_act = $fecha_emision->toDateTimeString();
        $serie->save();
        //endregion

        //region 4. OBTENER CERTIFICADO VIGENTE
        $sunatCertificado = SunatCertificado::where('fecha_inicio', '<=', $fecha_emision->toDateString())
            ->where('fecha_vencimiento', '>=', $fecha_emision->toDateString())
            ->first();

        if ($sunatCertificado === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Se registró el pago correctamente.<br>
            Se registró el comprobante electrónico correctamente; sin embargo, este no fue enviado a SUNAT.<br>
            Por favor, comuníquese con nosotros.';
            $respuesta->data = $dataRespuesta;
            return response()->json($respuesta);
        }
        //endregion

        //region 5. CREAR XML DEL COMPROBANTE
        $sNombreComprobante = SunatComprobantesXml::crearArchivoXmlFacturaBoleta($sunatFacturaBoleta);
        $sNombreComprobanteXml = $sNombreComprobante . '.xml';
        //endregion

        //region 6. FIRMAR COMPROBANTE Y ACTUALIZAR DIGEST VALUE
        $sDigestValue = SunatComprobantesXml::firmarComprobante($sNombreComprobanteXml, $sunatCertificado->nombre_archivo, $sunatCertificado->contrasena_certificado);
        $sunatFacturaBoleta->sunat_digest_value = $sDigestValue;
        $sunatFacturaBoleta->save();
        //endregion

        //region 7. ENVIAR COMPROBANTE A SUNAT Y ELIMINAR XML NO FIRMADO
        $respuestaEnvioSunat = SunatComprobantesXml::enviarSunat($sunatCertificado->usa_url_produccion, $sNombreComprobante, $sRucEmisor, $sunatCertificado->usuario_sol, $sunatCertificado->clave_sol);
        $dataRespuestaEnvioSunat = $respuestaEnvioSunat->data;
        $sunatFacturaBoleta->sunat_aceptado = $dataRespuestaEnvioSunat['sunat_aceptado'];
        $sunatFacturaBoleta->sunat_status_code_cdr = $dataRespuestaEnvioSunat['sunat_status_code_cdr'];
        $sunatFacturaBoleta->sunat_observaciones = $dataRespuestaEnvioSunat['sunat_observaciones'];
        $sunatFacturaBoleta->sunat_enviado = 1;
        $sunatFacturaBoleta->save();

        Storage::disk('public')->delete('comprobantes/' . $sNombreComprobanteXml);
        //endregion

        $sunatFacturaBoleta->refresh();

        //region 8. GENERAR PDF DEL COMPROBANTE
        SunatComprobantesXml::generarPdfFacturaBoleta($sunatFacturaBoleta, $lstSunatFacuraBoletaDetalles, $sNombreComprobante);
        $sNombreComprobantePdf = $sNombreComprobante . '.pdf';
        //endregion

        //region 9. ENVIAR XML Y PDF AL CORREO DEL CLIENTE
        $sRutaComprobanteXml = 'comprobantes_firmados/' . $sNombreComprobanteXml;
        $sRutaComprobantePdf = 'comprobantes/' . $sNombreComprobantePdf;
        $ventaComprobanteCliente = new VentaComprobanteCliente($sunatFacturaBoleta, $sRutaComprobanteXml, $sRutaComprobantePdf);
        Mail::to($email)->send($ventaComprobanteCliente);
        //endregion

        //region 10. ENVIAR XML Y PDF AL CORREO DE VENTAS@ECOVALLE.PE
        $notificacionComprobanteCliente = new NotificacionComprobanteCliente($sunatFacturaBoleta, $sRutaComprobanteXml, $sRutaComprobantePdf);
        Mail::to('facturacion@ecovalle.pe')->send($notificacionComprobanteCliente);
        //endregion

        $dataRespuesta = [
            'cargo' => $cargo,
            'sunatFacturaBoleta' => $sunatFacturaBoleta,
            'lstSunatFacuraBoletaDetalles' => $lstSunatFacuraBoletaDetalles
        ];

        if ($cliente) {
            DetalleCarrito::where('cliente_id', $cliente->id)->delete();
        }

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Pedido registrado correctamente.<br>
        Comprobante electrónico enviado a ' . $email . '.<br>
        Si el correo no está en su bandeja principal,
        por favor revise su carpeta de Spam o Correo no deseado.';

        $respuesta->data = $dataRespuesta;

        return response()->json($respuesta);
    }

    public function ajaxConsultaApi(Request $request)
    {
        $tipo_documento = $request->tipo_documento;
        $documento = $request->documento;
        if($tipo_documento == 'DNI')
        {
            $data = file_get_contents('https://dniruc.apisperu.com/api/v1/dni/'.$documento.'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFkbWluaXN0cmFjaW9uQGVjb3ZhbGxlLnBlIn0.AA_59jy9sKMMKAur43duHJLWRwqo5INB_n2rQb6I8iE');
        }
        else
        {
            $data = file_get_contents('https://dniruc.apisperu.com/api/v1/ruc/'.$documento.'?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFkbWluaXN0cmFjaW9uQGVjb3ZhbGxlLnBlIn0.AA_59jy9sKMMKAur43duHJLWRwqo5INB_n2rQb6I8iE');
        }
        
        $data = json_decode($data,false);
        if(isset($data->success))
        {
            $respuesta = new Respuesta;
            $respuesta->result = Result::ERROR;
        }else{
            $respuesta = new Respuesta;
            $respuesta->result = Result::SUCCESS;
            $respuesta->data = $data;
        }

        return response()->json($respuesta);
    }
}
