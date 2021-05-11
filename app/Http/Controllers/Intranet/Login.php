<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Usuario;
use Illuminate\Http\Request;

class Login extends Controller {

    public function index() {
        return view('intranet.login');
    }

    public function ajaxIngresar(Request $request) {
        $request->validate([
            'usuario' => 'required',
            'contrasena' => 'required',
        ]);

        $sUsuario = $request->get('usuario');
        $sContrasena = $request->get('contrasena');

        $usuario = Usuario::where('username', $sUsuario)->where('contrasena', md5($sContrasena))->first();

        if ($usuario) {
            $request->session()->put('usuario', $usuario);
        }

        $respuesta = new Respuesta;
        $respuesta->result = $usuario ? Result::SUCCESS : Result::WARNING;
        $respuesta->data = $usuario;

        return response()->json($respuesta);
    }

    public function salir(Request $request) {
        $request->session()->forget('usuario');
        return redirect('/intranet');
    }
}
