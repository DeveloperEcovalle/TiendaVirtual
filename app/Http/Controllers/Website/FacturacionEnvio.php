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
use App\Agencia;

class FacturacionEnvio extends Website {

    protected $lstTraduccionesFacturacionEnvio;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesFacturacionEnvio = [
            'en' => [
                'Billing and shipping' => 'Billing and shipping',
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
                'Billing and shipping' => 'Facturación y envío',
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
        $lstAgencias = Agencia::where('estado','ACTIVO')->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPreciosEnvio' => $lstPreciosEnvio,'lstAgencias' => $lstAgencias];

        return response()->json($respuesta);
    }

    public function ajaxListarDatosFacturacion(Request $request) {
        //TODO VERIFICAR EL STOCK DE LOS PRODUCTOS

        // $cliente = $request->session()->get('cliente');
        // if ($cliente) {
        //     $cliente->refresh();
        //     $cliente->load('persona', 'persona.documentos', 'ubigeo');
        // }

        $lstTiposComprobante = TipoComprobante::whereHas('tipo_comprobante_sunat', function ($sunat_tipo_comprobante) {
            $sunat_tipo_comprobante->where('ventas', 1);
        })->with('tipo_comprobante_sunat', 'tipo_comprobante_sunat.tipos_documento')->orderBy('id')->get();

        $lstUbigeo = Ubigeo::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstTiposComprobante' => $lstTiposComprobante, 'lstUbigeo' => $lstUbigeo];

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
