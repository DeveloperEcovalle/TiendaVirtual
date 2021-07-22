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
use App\Ubigeo;
use App\TelefonoEmpresa;
use Exception;
use Illuminate\Http\Request;

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
                'documento.unique' => 'El campo documento debe ser único',
                'correo.required' => 'El campo correo es obligatorio.',
                'correo.email' => 'El campo correo debe ser un email.',
                'correo.unique' => 'El correo electrónico ingresado ya se encuentra registrado.',
            ];
    
            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {

                DB::rollBack();
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'Ocurrió un error de validación.';
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
            
            //$persona->update();

            $cliente = Cliente::find(session('cliente')->id);
            $cliente->email = $request->correo;
            if(!empty($request->password_actual))
            {
                if(md5($request->password_actual) != $cliente->password)
                {
                    $persona->update();
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['No es tu contraseña actual.']));
                    return response()->json($respuesta);
                }else{
                    if(!empty($request->password_nueva) && empty($request->password_confirm))
                    {
                        $persona->update();
                        $cliente->update();
                        $request->session()->put('cliente', $cliente);
                        DB::commit();
                        $respuesta->result = Result::WARNING;
                        $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Confirmar contraseña.']));
                        return response()->json($respuesta);
                    }

                    if(empty($request->password_nueva) && !empty($request->password_confirm))
                    {
                        $persona->update();
                        $cliente->update();
                        $request->session()->put('cliente', $cliente);
                        DB::commit();
                        $respuesta->result = Result::WARNING;
                        $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contraseña nueva']));
                        return response()->json($respuesta);
                    }

                    if(!empty($request->password_nueva) && !empty($request->password_confirm))
                    {
                        if($request->password_nueva != $request->password_confirm)
                        {
                            $persona->update();
                            $cliente->update();
                            $request->session()->put('cliente', $cliente);
                            DB::commit();
                            $respuesta->result = Result::WARNING;
                            $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Contraseñas diferentes']));
                            return response()->json($respuesta);
                        }
                        else{
                            $cliente->password = md5($request->password_nueva);
                            $persona->password = $request->password_nueva;
                        }
                    }
                }
            }
            else
            {
                if(!empty($request->password_nueva) && empty($request->password_confirm))
                {
                    
                    $persona->update();
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Confirmar contraseña.']));
                    return response()->json($respuesta);
                }

                if(empty($request->password_nueva) && !empty($request->password_confirm))
                {
                    
                    $persona->update();
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contraseña nueva']));
                    return response()->json($respuesta);
                }

                if(!empty($request->password_nueva) && !empty($request->password_confirm))
                {
                    $persona->update();
                    $cliente->update();
                    $request->session()->put('cliente', $cliente);
                    DB::commit();
                    $respuesta->result = Result::WARNING;
                    $respuesta->data = array('errors' => array('Mensaje' => ['Datos actualizados.'],'Error' => ['Completar contraseña actual']));
                    return response()->json($respuesta);
                }

            }
            
            $persona->update();
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
            $respuesta->mensaje = 'Ocurrió un error de validación.';
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
                $respuesta->mensaje = 'Ocurrió un error de validación.';
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
            $respuesta->mensaje = 'Dirección actualizada.';
            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Ocurrió un error de validación.';
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
        $lstOrders = session('cliente')->compras;
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
}
