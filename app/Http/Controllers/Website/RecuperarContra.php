<?php

namespace App\Http\Controllers\Website;

use App\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Empresa;
use App\TelefonoEmpresa;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Illuminate\Support\Facades\Mail;

class RecuperarContra extends Website
{
    protected $lstTraduccionesRecuperarPassword;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesRecuperarPassword = [
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
            'lstTraduccionesRecuperarPassword' => $this->lstTraduccionesRecuperarPassword[$locale],
            'iPagina' => -1,
        ];
        return view('website.micuenta.recuperar.index', $data);
    }

    public function ajaxEnviar(Request $request)
    {
        try{
            DB::beginTransaction();
            $respuesta = new Respuesta;
            $data = $request->all();
            $rules = [
                'email' => 'required|email',
    
            ];
            $message = [
                'email.required' => 'El campo email es obligatorio.',
                'email.email' => 'El campo email debe ser un email.',
            ];
    
            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {

                DB::rollBack();
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'Ocurrió un error de validación.';
                $respuesta->data = array('errors' => $validator->getMessageBag()->toArray());
                return response()->json($respuesta);
            }
            $cliente = Cliente::where('email',$request->email)->first();
            if(empty($cliente)){
                DB::rollBack();
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'Correo de usuario no existe';
                $respuesta->data = array('errors' => array('error' => ['Correo de usuario no existe']));
                return response()->json($respuesta);
            }

            $password = generaPassword(8);
            $cliente->password = md5($password);
            $cliente->update();

            Mail::send('website.email.recuperacion',compact('cliente','password'), function ($mail) use ($cliente) {
                $mail->subject('TU CUENTA ECOVALLE HA SIDO RESTABLECIDA');
                $mail->to($cliente->email);
                $mail->from('website@ecovalle.pe','ECOVALLE');
            });

            DB::commit();
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Se envió la nueva contraseña a '.$cliente->email;
            return response()->json($respuesta);
        }catch(Exception $e)
        {
            DB::rollBack();
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Ocurrió un error';
            $respuesta->data = array('errors' => array('error' => [$e->getMessage()]));
            return response()->json($respuesta);
        }
    }
}
