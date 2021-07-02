<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Exception;

class Recepcion extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 49;
        $this->sPermisoListar = 'CONFRECEPCIONLISTAR';
        $this->sPermisoActualizar = 'CONFRECEPCIONACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.recepcion.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $empresa = null;
        if ($permiso) {
            $empresa = Empresa::first();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['empresa' => $empresa];

        return response()->json($respuesta);
    }

    public function ajaxActualizar(Request $request) {
        $this->init();

        try{
            $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

            $respuesta = new Respuesta;
            if ($permiso === null) {
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
                return response()->json($respuesta);
            }
    
            /*$request->validate([
                'correo_pedidos' => 'required',
                'telefono_pedidos' => 'required'
            ]);*/
    
            $empresa = Empresa::first();
            $empresa->correo_pedidos = $request->get('correo_pedidos');
            $empresa->telefono_pedidos = $request->get('telefono_pedidos');
            $empresa->correo_pedidos_1 = $request->get('correo_pedidos_1');
            $empresa->telefono_pedidos_1 = $request->get('telefono_pedidos_1');
            $empresa->update();
    
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Correo de recepcion de pedidos y teléfono modificados correctamente.';
    
            return response()->json($respuesta);
        }catch(Exception $e)
        {
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = $e->getMessage();
    
            return response()->json($respuesta);
        }
    }
}
