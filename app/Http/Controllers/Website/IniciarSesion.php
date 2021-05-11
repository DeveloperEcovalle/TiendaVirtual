<?php

namespace App\Http\Controllers\Website;

use App\Cliente;
use App\DetalleCarrito;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
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
        $request->validate([
            'email' => 'required|email',
            'contrasena' => 'required',
        ]);

        $sEmail = $request->get('email');
        $sContrasena = $request->get('contrasena');

        $cliente = Cliente::where('correo', $sEmail)->where('contrasena', md5($sContrasena))->where('usuario_web', 1)->with(['persona'])->first();

        if ($cliente) {
            $request->session()->put('cliente', $cliente);

            $sLstCarritoCompras = $request->get('sLstCarritoCompras');
            if (strlen($sLstCarritoCompras) > 0) {
                DetalleCarrito::where('cliente_id', $cliente->id)->delete();

                $lstDetallesCarritoInsertar = array();
                $fecha_reg = now()->toDateTimeString();

                $lstCarritoCompras = explode('|', $sLstCarritoCompras);
                foreach ($lstCarritoCompras as $sDetalle) {
                    $detalle = explode(';', $sDetalle);

                    array_push($lstDetallesCarritoInsertar, array(
                        'cliente_id' => $cliente->id,
                        'producto_id' => $detalle[0],
                        'cantidad' => $detalle[1],
                        'fecha_reg' => $fecha_reg,
                    ));
                }

                DetalleCarrito::insert($lstDetallesCarritoInsertar);
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = $cliente !== null ? Result::SUCCESS : Result::WARNING;
        $respuesta->data = $cliente;

        return response()->json($respuesta);
    }
}
