<?php

namespace App\Http\Controllers\Website;

use App\Cliente;
use App\DetalleCarrito;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Exception;
use Illuminate\Http\Request;

class IniciarSesion extends Controller {

    protected $lstLocales;

    public function __construct() {
        $this->lstLocales = [
            'en' => [
                'Email' => 'Email',
                'Password' => 'Password',
            ],
            'es' => [
                'Email' => 'Email',
                'Password' => 'Contraseña',
            ],
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'iPagina' => -1,
        ];

        return view('website.iniciar_sesion', $data);
    }

    public function ajaxIngresar(Request $request) {
       try{

            $data=request()->validate([
                'email'=>'required',
                'contrasena'=>'required'
            ],
            [
                'email.required'=>'Ingrese Usuario',
                'contrasena.required'=>'Ingrese Contraseña',
            ]);

            $sEmail = $request->get('email');
            $sContrasena = $request->get('contrasena');

            $cliente = Cliente::where('email', $sEmail)->where('password', md5($sContrasena))->first();
            $mensaje = '';
            $query = Cliente::where('email','=',$sEmail)->get();
            if($query->count()!=0)
            {
                // $hashp = $query[0]->password;
                // $password = $sContrasena;
                if($cliente)
                {
                    $request->session()->put('cliente', $cliente);
                }
                else
                {
                    $mensaje = 'Contraseña no válida';
                }
            }
            else{
                $mensaje = 'No tienes una cuenta';
            }
            /*if ($cliente) {
                $request->session()->put('cliente', $cliente);

                // $sLstCarritoCompras = $request->get('sLstCarritoCompras');
                // if (strlen($sLstCarritoCompras) > 0) {
                //     DetalleCarrito::where('cliente_id', $cliente->id)->delete();

                //     $lstDetallesCarritoInsertar = array();
                //     $fecha_reg = now()->toDateTimeString();

                //     $lstCarritoCompras = explode('|', $sLstCarritoCompras);
                //     foreach ($lstCarritoCompras as $sDetalle) {
                //         $detalle = explode(';', $sDetalle);

                //         array_push($lstDetallesCarritoInsertar, array(
                //             'cliente_id' => $cliente->id,
                //             'producto_id' => $detalle[0],
                //             'cantidad' => $detalle[1],
                //             'fecha_reg' => $fecha_reg,
                //         ));
                //     }

                //     DetalleCarrito::insert($lstDetallesCarritoInsertar);
                // }
            }else{
                $mensaje = 'No tienes una cuenta';
            }*/

            $respuesta = new Respuesta;
            $respuesta->result = $cliente != null ? Result::SUCCESS : Result::WARNING;
            $respuesta->data = $cliente;
            $respuesta->mensaje = $mensaje;

            return response()->json($respuesta);
       }
       catch(Exception $e)
       {
            $respuesta = new Respuesta;
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
       }
    }

    public function ajaxSalir()
    {
        session()->forget('cliente');
        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        return response()->json($respuesta);
    }
}
