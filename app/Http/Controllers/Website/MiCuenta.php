<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Persona;
use App\Cliente;
use App\Compra;
use App\Producto;
use App\Ubigeo;
use App\TelefonoEmpresa;
use Culqi\Culqi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;

class MiCuenta extends Website
{
    protected $lstTraduccionesMiCuenta;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesMiCuenta = [
            'en' => [
            ],
            'es' => [
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesMiCuenta' => $this->lstTraduccionesMiCuenta[$locale],
            'iPagina' => -1,
        ];

        $vista = $request->get('v');

        $vista = ($vista === null) ? 'index' : $vista;

        $view = 'website.micuenta.' . $vista;
        return view($view, $data);
    }

    public function ajaxListarPanelDesk(){
        return view('website.micuenta.panel_desk');
    }

    public function ajaxListarPanelAccount(){
        return view('website.micuenta.panel_account');
    }

    public function ajaxActualizarAccount(Request $request)
    {
        try {
            DB::beginTransaction();
            $respuesta = new Respuesta;
            $data = $request->all();
            $rules = [
                'nombres' => 'required',
                'tipo_documento' => 'required',
                'documento' => 'required|unique:personas,documento,'.session('cliente')->persona_id,
                'apellidos' => 'required_if:tipo_documento,DNI',
                'correo' => 'required|email|unique:personas,correo,'.session('cliente')->persona_id,

            ];
            $message = [
                'nombres.required' => 'El campo nombres es obligatorio.',
                'apellidos.required_if' => 'El campo apellidos es obligatorio.',
                'tipo_documento.required' => 'El campo tipo documento es obligatorio.',
                'documento.required' => 'El campo documento es obligatorio.',
                'documento.unique' => 'El campo documento debe ser ??nico',
                'correo.required' => 'El campo correo es obligatorio.',
                'correo.email' => 'El campo correo debe ser un email.',
                'correo.unique' => 'El correo electr??nico ingresado ya se encuentra registrado.',
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {

                DB::rollBack();
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'Ocurri?? un error de validaci??n.';
                $respuesta->data = array('errors' => $validator->getMessageBag()->toArray());
                return response()->json($respuesta);
            }

            $lstApellidos = explode(' ', $request->apellidos);
            $sApellido1 = $lstApellidos[0];
            $sApellido2 = count($lstApellidos) > 1 ? $lstApellidos[1] : null;

            $persona = Persona::find(session()->get('cliente')->persona_id);
            $persona->nombres = $request->nombres;
            $persona->apellido_1 = $sApellido1;
            $persona->apellido_2 = $sApellido2;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->documento = $request->documento;
            $persona->correo = $request->correo;
            $persona->update();

            $cliente = Cliente::find(session('cliente')->id);
            $cliente->email = $request->correo;
            if(!empty($request->password_actual))
            {
                if(md5($request->password_actual) != $cliente->password)
                {
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['No es tu contrase??a actual.']));
                    return response()->json($respuesta);
                }else{
                    if(!empty($request->password_nueva) && empty($request->password_confirm))
                    {
                        $cliente->update();
                        $request->session()->put('cliente', $cliente);
                        DB::commit();
                        $respuesta->result = Result::WARNING;
                        $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Confirmar contrase??a.']));
                        return response()->json($respuesta);
                    }

                    if(empty($request->password_nueva) && !empty($request->password_confirm))
                    {
                        $cliente->update();
                        $request->session()->put('cliente', $cliente);
                        DB::commit();
                        $respuesta->result = Result::WARNING;
                        $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contrase??a nueva']));
                        return response()->json($respuesta);
                    }

                    if(!empty($request->password_nueva) && !empty($request->password_confirm))
                    {
                        if($request->password_nueva != $request->password_confirm)
                        {
                            $cliente->update();
                            $request->session()->put('cliente', $cliente);
                            DB::commit();
                            $respuesta->result = Result::WARNING;
                            $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Contrase??as diferentes']));
                            return response()->json($respuesta);
                        }
                        else{
                            $cliente->password = md5($request->password_nueva);
                        }
                    }
                }
            }
            else
            {
                if(!empty($request->password_nueva) && empty($request->password_confirm))
                {
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Confirmar contrase??a.']));
                    return response()->json($respuesta);
                }

                if(empty($request->password_nueva) && !empty($request->password_confirm))
                {
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contrase??a nueva']));
                    return response()->json($respuesta);
                }

                if(!empty($request->password_nueva) && !empty($request->password_confirm))
                {
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contrase??a actual']));
                    return response()->json($respuesta);
                }

            }
            $cliente->update();
            session()->forget('cliente');
            session()->put('cliente', $cliente);

            DB::commit();
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Datos Actualizados.';
            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Ocurri?? un error de validaci??n.';
            $respuesta->data = array('errors' => array('error' => [$e->getMessage()]));
            return response()->json($respuesta);
        }
    }

    public function ajaxListarPanelAddress(){
        return view('website.micuenta.panel_address');
    }

    public function ajaxActualizarAddress(Request $request)
    {
        try {
            DB::beginTransaction();
            $respuesta = new Respuesta;
            $data = $request->all();
            $rules = [
                'departamento' => 'required',
                'provincia' => 'required',
                'distrito' => 'required',
                'direccion' => 'required',

            ];
            $message = [
                'departamento.required' => 'El campo departamento es obligatorio.',
                'provincia.required' => 'El campo provincia es obligatorio.',
                'distrito.required' => 'El campo distrito es obligatorio.',
                'direccion.required' => 'El direccion es obligatorio.',
            ];

            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {

                DB::rollBack();
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'Ocurri?? un error de validaci??n.';
                $respuesta->data = array('errors' => $validator->getMessageBag()->toArray());
                return response()->json($respuesta);
            }

            $persona = Persona::find(session()->get('cliente')->persona_id);
            $ubigeo = Ubigeo::where('departamento',$request->departamento)->where('provincia',$request->provincia)->where('distrito',$request->distrito)->first();
            $persona->ubigeo_id = $ubigeo ? $ubigeo->id : null;
            $persona->direccion = $request->direccion;
            $persona->update();

            $cliente = Cliente::find(session('cliente')->id);
            session()->forget('cliente');
            session()->put('cliente', $cliente);

            DB::commit();
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Direcci??n actualizada.';
            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Ocurri?? un error de validaci??n.';
            $respuesta->data = array('errors' => array('error' => [$e->getMessage()]));
            return response()->json($respuesta);
        }
    }

    public function ajaxListarPanelOrders(){
        return view('website.micuenta.panel_orders');
    }

    public function ajaxListarOrders(){
        $respuesta = new Respuesta();
        $lstOrders = [];
        $lstOrders = Compra::where('cliente_id',session('cliente')->id)->orderBy('id','desc')->get();
        foreach($lstOrders as $compra)
        {
            $compra->estado;
            foreach($compra->detalles as $detalle)
            {
                $detalle->producto;
            }
        }

        $respuesta->result = Result::SUCCESS;
        $respuesta->data = array('lstOrders' => $lstOrders);
        return response()->json($respuesta);


    }

    public function ajaxDownload($codigo)
    {
        $ruta = public_path().'/storage/pedidos/'.$codigo.'.pdf';
        return response()->download($ruta);
    }

    public function ajaxListarPanelShow(){
        return view('website.micuenta.panel_show');
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
        $f_actual = Carbon::now();
        $anio = date_format($f_actual,'Y');
        $codigo = substr($token,9,6).substr($venta->id,0,1).'-'.$anio;
        $venta->codigo = $codigo;
        $venta->token = $token;
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
        $respuesta->mensaje = 'El pago se ha realizado con exito muchas gracias por elegirnos.';
        return response()->json($respuesta);
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
