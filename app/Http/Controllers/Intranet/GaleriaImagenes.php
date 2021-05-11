<?php

namespace App\Http\Controllers\Intranet;

use App\Banner;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriaImagenes extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 39;
        $this->sPermisoListar = 'PWEBGALERIALISTAR';
        $this->sPermisoInsertar = 'PWEBGALERIAINSERTAR';
        $this->sPermisoEliminar = 'PWEBGALERIAELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.galeria_imagenes.index', $data);
    }

    public function ajaxListarImagenes(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstImagenes = [];
        if ($permiso) {
            $lstImagenes = Imagen::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstImagenes' => $lstImagenes];

        return response()->json($respuesta);
    }

    public function ajaxInsertarImagen(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen' => 'required|image|max:3000'
        ]);

        $archivo_imagen = $request->file('imagen');

        if (!$archivo_imagen) {
            $respuesta->result = Result::ERROR;
            return response()->json($respuesta, 400);
        }

        $ruta_imagen = $archivo_imagen->store('public/empresa');

        $imagen = new Imagen;
        $imagen->ruta = str_replace('public', '/storage', $ruta_imagen);
        $imagen->usuario_reg = $this->usuario->id;
        $imagen->fecha_reg = now()->toDateTimeString();
        $imagen->save();

        $respuesta->result = Result::SUCCESS;
        return response()->json($respuesta);
    }

    public function ajaxEliminarImagen(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoEliminar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $imagen = Imagen::find($request->get('id'));
        $sRutaImagen = str_replace('/storage/', '', $imagen->ruta);

        $eliminado = Storage::disk('public')->delete($sRutaImagen);
        if ($eliminado) $imagen->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen eliminada correctamente.';

        return response()->json($respuesta);
    }
}
