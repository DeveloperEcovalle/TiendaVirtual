<?php

namespace App\Http\Controllers\Website;

use App\Compra;
use App\DetalleCompra;
use Illuminate\Http\Request;
use App\Empresa;
use App\TelefonoEmpresa;
use Culqi\Culqi;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use App\Ubigeo;
use Exception;
use Illuminate\Support\Facades\DB;

class PagoEnvio extends Website
{
    protected $lstTraduccionesPagoEnvio;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesPagoEnvio = [
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
            'lstTraduccionesPagoEnvio' => $this->lstTraduccionesPagoEnvio[$locale],
            'iPagina' => -1,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.pago_envio', $data);
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

        $SECRET_KEY = "sk_test_d6a0afc0096d705a"; //sk_test_yE35C4w9LPOqh1qp
        $culqi = new Culqi(array('api_key' => $SECRET_KEY));

        $token = $request->get('token');
        $amount = $request->get('amount');
        $email = $request->get('email');

        $cargo = $culqi->Charges->create(
            array(
                'amount' => $amount, 
                'currency_code' => 'PEN', 
                'email' => $email, 
                'source_id' => $token
            )
        );
        if ($cargo === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No se pudo obtener el registro de pago.<br>Verifique su cuenta o línea de crédito a través de su banca por internet.';
            return response()->json($respuesta);
        }
        //$cargo = '';
        $respuesta->result = Result::SUCCESS;
        $dataRespuesta = ['cargo' => $cargo];
        $respuesta->data = $dataRespuesta;
        $respuesta->mensaje = 'Pago realizado con exito.';
        return response()->json($respuesta);
    }

    public function ajaxCrearVenta(Request $request)
    {
        try{

            DB::beginTransaction();
            $respuesta = new Respuesta;

            $token = $request->get('token');
            $email = $request->get('email');
    
            $tipo_compra = $request->get('tipo_compra');
            $tipo_comprobante = $request->get('tipo_comprobante');
            $cliente = $request->get('cliente');
            $tipo_documento = $request->get('tipo_documento');
            $documento = $request->get('documento');
            $telefono = $request->get('telefono');
            $direccion = $request->get('direccion');
            $subtotal = $request->get('subtotal');
            $delivery = $request->get('delivery');
            $departamento = $request->get('departamento');
            $provincia = $request->get('provincia');
            $distrito = $request->get('distrito');
            $detalles = $request->get('detalles');
            //$cliente = $request->session()->get('cliente');
            $created_at = now();
            $created_at = date_format($created_at, 'Y-m-d H:i');
    
            $venta = new Compra();
            $venta->tipo_compra = $tipo_compra;
            $venta->tipo_documento = $tipo_documento;
            $venta->documento = $documento;
            $venta->cliente = $cliente;
            $venta->telefono = $telefono;
            $venta->email = $email;
            $venta->subtotal = $subtotal;
            $venta->delivery = $delivery;
            $venta->tipo_comprobante = $tipo_comprobante;
            $venta->token = $token;
            $venta->direccion = $direccion;
            $ubigeo_id = null;
            if($departamento != '' && $provincia != '' && $distrito != '')
            {$ubigeo_id = Ubigeo::where('departamento',$departamento)->where('provincia',$provincia)->where('distrito',$distrito)->value('id');}
            $venta->ubigeo_id = $ubigeo_id;
            $venta->cliente_id = null;
            $venta->save();
            $detalles = json_decode($detalles,false);
            $cont = 0;
            while($cont < count($detalles))
            {
                $detalle = new DetalleCompra();
                $detalle->compra_id = $venta->id;
                $detalle->producto_id = $detalles[$cont]->id;
                $detalle->cantidad = $detalles[$cont]->cantidad;
                $detalle->save();
                $cont = $cont + 1;
            }
            DB::commit();
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Compra realizada exitosamente. ';
            return response()->json($respuesta);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }
}
