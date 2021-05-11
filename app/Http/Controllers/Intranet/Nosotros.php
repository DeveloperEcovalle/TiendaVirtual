<?php

namespace App\Http\Controllers\Intranet;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Valor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Nosotros extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 22;
        $this->sPermisoListar = 'PWEBNOSOTRLISTAR';
        $this->sPermisoActualizar = 'PWEBNOSOTRACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.nosotros.index', $data);
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

    public function ajaxActualizarImagenPortada(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen_de_portada' => 'required|image|mimes:jpeg,png'
        ]);

        $empresa = Empresa::first();

        $imagen = $request->file('imagen_de_portada');
        $sRutaImagenActual = str_replace('/storage', 'public', $empresa->ruta_imagen_portada);
        $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
        Storage::disk('public')->delete($sNombreImagenActual);
        $ruta_imagen_portada = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;

        $nueva_ruta_imagen_portada = str_replace('public/', '/storage/', $ruta_imagen_portada);

        $empresa->ruta_imagen_portada = $nueva_ruta_imagen_portada;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen de portada reemplazada correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen_portada];

        return response()->json($respuesta);
    }
}
