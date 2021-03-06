<?php

namespace App\Http\Controllers\Website;

use App\Cliente;
use App\Compra;
use App\DetalleCompra;
use Illuminate\Http\Request;
use App\Empresa;
use App\TelefonoEmpresa;
use Culqi\Culqi;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use App\Producto;
use App\Ubigeo;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Carbon;

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
                'empty_cart' => 'Tu carrito de compras est?? vac??o',
                'update_cart' => 'Actualizar carrito de compras',
                'order_summary' => 'Resumen del pedido',
                'price_summary' => 'Resumen de pago',

                'product' => 'Producto',
                'price' => 'Precio',
                'quantity' => 'Cantidad',
                'delete' => 'Eliminar',
                'proceed_to_purchase' => 'Proceder a la compra',
                'payment' => 'Pago',

                'delivery_address' => 'Direcci??n de env??o',
                'delivery_address_placeholder' => 'Ingrese aqu?? la direcci??n a donde llegar?? el producto',
                'subtotal_product_amount' => 'Subtotal de productos',

                'billing_and_delivery' => 'Facturaci??n y Env??o',
                'receipt_type' => 'Tipo de comprobante',
                'billing' => 'Facturaci??n',
                'identity_card' => 'Dcto. de identidad',
                'document_number' => 'N??mero de documento',
                'dni' => 'DNI',
                'name' => 'Nombres',
                'last_name' => 'Apellidos',
                'company_name' => 'Raz??n Social',
                'phone' => 'Tel??fono / Celular',
                'email' => 'Correo electr??nico',
                'additional_information' => 'Informaci??n adicional',
                'notes_order' => 'Notas para el pedido',

                'enter_your_data' => 'Ingresa todos tus datos o <a class="nav-ecovalle" href="#" data-toggle="modal" data-target="#modalInicioSesion">inicia sesi&oacute;n</a>',
                'your_order' => 'Tu Pedido',
                'delivery' => 'Env??o',
                'credit_or_debit_card' => 'Tarjeta de cr??dito o d??bito',
                'credit_or_debit_card_desc' => 'Paga con tu tarjeta de cr??dito o d??bito. Aceptamos todas las tarjetas.',
                'privacy_policy' => 'Tus datos personales se utilizar??n para procesar tu pedido, mejorar tu experiencia en esta web y otros prop??sitos descritos en nuestra <a class="nav-ecovalle" href="/politica-privacidad">pol??tica de privacidad</a>.',
                'terms_and_conditions' => 'He le??do y estoy de acuerdo con los <a class="nav-ecovalle" href="/terminos-condiciones">t??rminos y condiciones</a> de la web.',
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

    public function ajaxCrearCargo(Request $request, $id) {
        ini_set("max_execution_time", 60000);
        $respuesta = new Respuesta;

        $SECRET_KEY = "sk_live_c6a62e7d9661faea"; //sk_test_DDIXikjr5xQLViGo - sk_test_yE35C4w9LPOqh1qp - sk_live_c6a62e7d9661faea
        $culqi = new Culqi(array('api_key' => $SECRET_KEY));

        $token = $request->get('token');
        $amount = $request->get('amount');
        $email = $request->get('email');
        $cliente = $request->get('cliente');

        $venta = Compra::find($id);

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
            $respuesta->mensaje = 'No se pudo obtener el registro de pago.<br>Verifique su cuenta o l??nea de cr??dito a trav??s de su banca por internet.';
            return response()->json($respuesta);
        }

        //$cargo = '';
        $venta->estado_pago = '1';
        $venta->update();

        $carrito = array();
        $i = 0;
        while($i < count($venta->detalles))
        {
            $producto = Producto::find($venta->detalles[$i]->producto_id);
            $producto->cantidad = $venta->detalles[$i]->cantidad;
            $fPromocion = $producto->promocion_vigente === null ? 0.00 :
                ($producto->cantidad >= $producto->promocion_vigente->min && $producto->cantidad <= $producto->promocion_vigente->max ? ($producto->promocion_vigente->porcentaje ? (($producto->precio_actual->monto * $producto->promocion_vigente->porcentaje) / 100) : ($producto->promocion_vigente->monto)) : 0.00);
            $fPrecio = ($producto->oferta_vigente === null ? $producto->precio_actual->monto :
                ($producto->oferta_vigente->porcentaje ? ($producto->precio_actual->monto * (100 - $producto->oferta_vigente->porcentaje) / 100) : ($producto->precio_actual->monto - $producto->oferta_vigente->monto))) - $fPromocion;
            $producto->pFinal = $fPrecio;
            array_push($carrito,$producto);
            $i = $i + 1;
        }

        $enviar_mail = self::enviar_mail($venta->id, $carrito);
        $enviar_wsp = self::enviar_wsp($venta->id);

        $respuesta->result = Result::SUCCESS;
        $dataRespuesta = ['cargo' => $cargo];
        $respuesta->data = $dataRespuesta;
        $respuesta->mensaje = 'Compra registrada y pago realizado con exito.';
        return response()->json($respuesta);
    }

    public function ajaxCrearVenta(Request $request)
    {
        try{
            ini_set("max_execution_time", 60000);
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
            $recoge = $request->get('recoge');
            $recoge_documento = $request->get('recoge_documento');
            $recoge_telefono = $request->get('recoge_telefono');
            $subtotal = $request->get('subtotal');
            $delivery = $request->get('delivery');
            $departamento = $request->get('departamento');
            $provincia = $request->get('provincia');
            $distrito = $request->get('distrito');
            $agencia = $request->get('agencia');
            $detalles = $request->get('detalles');

            if (strlen($detalles) === 0) {
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'El carrito de compras est?? vac??o.';
                return response()->json($respuesta);
            }

            $cliente_id = session()->has('cliente') ? session()->get('cliente')->id : null;

            $fecha_reg = now();
            $fecha_reg = date_format($fecha_reg, 'Y-m-d');

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
            $venta->recoge = $recoge;
            $venta->recoge_documento = $recoge_documento;
            $venta->recoge_telefono = $recoge_telefono;
            $ubigeo_id = null;
            if($departamento != '' && $provincia != '' && $distrito != '')
            {$ubigeo_id = Ubigeo::where('departamento',$departamento)->where('provincia',$provincia)->where('distrito',$distrito)->value('id');}
            $venta->ubigeo_id = $ubigeo_id;
            $venta->cliente_id = $cliente_id;
            $venta->agencia = $agencia;
            $venta->estado_id = 1;
            $venta->fecha_reg = $fecha_reg;
            $venta->save();

            $f_actual = Carbon::now();
            $anio = date_format($f_actual,'Y');
            $codigo = substr($token,9,6).substr($venta->id,0,1).'-'.$anio;
            $venta->codigo = $codigo;
            $venta->update();

            $detalles = json_decode($detalles,false);
            $fDescuento = 0;
            $cont = 0;
            while($cont < count($detalles))
            {
                $detalle = new DetalleCompra();
                $detalle->compra_id = $venta->id;
                $detalle->producto_id = $detalles[$cont]->id;
                $detalle->cantidad = $detalles[$cont]->cantidad;
                /*----------------------------------*/
                $producto = Producto::find($detalles[$cont]->id);
                $fPromocion = $producto->promocion_vigente === null ? 0.00 :
                    ($detalles[$cont]->cantidad >= $producto->promocion_vigente->min && $detalles[$cont]->cantidad <= $producto->promocion_vigente->max ? ($producto->promocion_vigente->porcentaje ? (($producto->precio_actual->monto * $producto->promocion_vigente->porcentaje) / 100) : ($producto->promocion_vigente->monto)) : 0.00);
                $fPrecio = ($producto->oferta_vigente === null ? $producto->precio_actual->monto :
                    ($producto->oferta_vigente->porcentaje ? ($producto->precio_actual->monto * (100 - $producto->oferta_vigente->porcentaje) / 100) : ($producto->precio_actual->monto - $producto->oferta_vigente->monto)));
                $detalle->precio_actual = $producto->precio_actual->monto;
                $detalle->precio_venta = number_format(round(($fPrecio * 10) / 10, 1), 2);
                $detalle->promocion = number_format(round(($fPromocion * 10) / 10, 1), 2);
                $fOferta = 0;
                if($producto->precio_actual)
                {
                    $fOferta = $producto->precio_actual->monto - number_format(round(($fPrecio * 10) / 10, 1), 2);
                }
                 /*----------------------------------*/
                $detalle->save();
                $fDescuento = $fDescuento + (($fPromocion * $detalles[$cont]->cantidad) + ($fOferta * $detalles[$cont]->cantidad));
                $cont = $cont + 1;
            }

            $venta->descuento = number_format(round(($fDescuento * 10) / 10, 1), 2);
            $venta->update();

            //-------ENV??O DE CORREO PEDIDO---------
            $carrito = array();
            $i = 0;
            while($i < count($detalles))
            {
                /*-------------------*/
                $producto_update = Producto::find($detalles[$i]->id);
                $producto_update->stock_actual  = $producto_update->stock_actual - $detalles[$i]->cantidad;
                $producto_update->update();
                /*-------------------*/
                $producto = Producto::find($detalles[$i]->id);
                $producto->cantidad = $detalles[$i]->cantidad;
                $fPromocion = $producto->promocion_vigente === null ? 0.00 :
                    ($producto->cantidad >= $producto->promocion_vigente->min && $producto->cantidad <= $producto->promocion_vigente->max ? ($producto->promocion_vigente->porcentaje ? (($producto->precio_actual->monto * $producto->promocion_vigente->porcentaje) / 100) : ($producto->promocion_vigente->monto)) : 0.00);
                $fPrecio = ($producto->oferta_vigente === null ? $producto->precio_actual->monto :
                    ($producto->oferta_vigente->porcentaje ? ($producto->precio_actual->monto * (100 - $producto->oferta_vigente->porcentaje) / 100) : ($producto->precio_actual->monto - $producto->oferta_vigente->monto))) - $fPromocion;
                $producto->pFinal = $fPrecio;
                array_push($carrito,$producto);
                $i = $i + 1;
            }

            //-----ACTUALIZAR SESSION CLIENTE

            $cliente = Cliente::find(session('cliente')->id);
            session()->put('cliente', $cliente);

            foreach($cliente->detalles_carrito as $detalle)
            {
                $detalle->delete();
            }

            DB::commit();

            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Compra realizada exitosamente. ';
            $respuesta->data = array('id' => $venta->id);
            return response()->json($respuesta);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }

    public function enviar_mail($id, $carrito = array())
    {
        try
        {
            $venta = Compra::find($id);
            $pdf = PDF::loadview('website.pdf.pedido',['venta' => $venta, 'carrito' => $carrito])->setPaper('a4')->setWarnings(false);
            PDF::loadView('website.pdf.pedido',['venta' => $venta, 'carrito' => $carrito])
                ->save(public_path().'/storage/pedidos/' . $venta->codigo.'.pdf');

            Mail::send('website.email.pedido',compact("venta"), function ($mail) use ($pdf,$venta) {
                $mail->to($venta->email);
                $mail->subject('PEDIDO COD: '.$venta->codigo);
                $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
                $mail->from('website@ecovalle.pe','ECOVALLE');
            });

            $empresa = Empresa::first();

            if($empresa->correo_pedidos)
            {
                Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
                    $mail->to($empresa->correo_pedidos);
                    $mail->subject('PEDIDO COD: '.$venta->codigo);
                    $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
                    $mail->from('website@ecovalle.pe','ECOVALLE');
                });
            }

            if($empresa->correo_pedidos_1)
            {
                Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
                    $mail->to($empresa->correo_pedidos_1);
                    $mail->subject('PEDIDO COD: '.$venta->codigo);
                    $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
                    $mail->from('website@ecovalle.pe','ECOVALLE');
                });
            }

            Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
                $mail->to('ccubas@unitru.edu.pe');
                $mail->subject('PEDIDO COD: '.$venta->codigo);
                $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
                $mail->from('website@ecovalle.pe','ECOVALLE');
            });
            return array('success' => true);
        }
        catch(Exception $e)
        {

            return array('success' => true);
        }
    }

    public function enviar_wsp($id)
    {
        try{
            $venta = Compra::find($id);
            $empresa = Empresa::first();
            if($empresa->telefono_pedidos)
            {
                $result = enviapedido($venta, $empresa->telefono_pedidos);
            }

            if($empresa->telefono_pedidos_1)
            {
                $result = enviapedido($venta, $empresa->telefono_pedidos_1);
            }
            return array('success' => true);
        }
        catch(Exception $e)
        {
            return array('success' => false);
        }
    }
}
