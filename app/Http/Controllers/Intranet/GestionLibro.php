<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\LibroReclamaciones;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
class GestionLibro extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 45;
        $this->iMenuId = 46;
        $this->sPermisoListar = 'GINLIBROLISTAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.gestion_libro.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstLibro = [];
        if ($permiso) {
            $lstLibro = LibroReclamaciones::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstLibro' => $lstLibro];

        return response()->json($respuesta);
    }

    public function ajaxDownload($id)
    {
        $libro = LibroReclamaciones::find($id);
        $ruta = public_path().'/storage/reclamos/'.$libro->codigo.'.pdf';
        //Storage::disk('public')->delete('informesti/'.$id);
        return response()->download($ruta);
    }
}
