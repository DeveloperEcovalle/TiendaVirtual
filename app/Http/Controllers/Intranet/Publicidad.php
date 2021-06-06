<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Publicidad as AppPublicidad;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Publicidad extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 50;
        $this->sPermisoListar = 'CONFPUBLISTAR';
        $this->sPermisoInsertar = 'CONFPUBINSERTAR';
        $this->sPermisoActualizar = 'CONFPUBACTUALIZAR';
        $this->sPermisoEliminar = 'CONFPUBELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.publicidad.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.configuracion.publicidad.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.publicidad.panel_nuevo');
    }

    public function ajaxPanelEditar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.publicidad.panel_editar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstPublicidads = [];
        if ($permiso) {
            $lstPublicidads = AppPublicidad::orderBy('id', 'asc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPublicidads' => $lstPublicidads];

        return response()->json($respuesta);
    }

    public function ajaxInsertar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png',
            'f_inicio'  => 'required',
            'f_fin'  => 'required',
            'enlace' => 'nullable|url',
            'descripcion' => 'nullable|string|max:400'
        ]);

        $imagen = $request->file('imagen');
        $ruta = $imagen->store('public/publicidades');
        $nueva_ruta = str_replace('public/', '/storage/', $ruta);

        $banner = new AppPublicidad();
        $banner->ruta = $nueva_ruta;
        $banner->enlace = $request->get('enlace');
        $banner->descripcion = $request->get('descripcion');
        $banner->estado = 1;
        $banner->f_inicio = $request->get('f_inicio');
        $banner->f_fin = $request->get('f_fin');
        $banner->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Publicidad registrada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric',
            'enlace' => 'nullable|url',
            'descripcion' => 'nullable|string|max:400',
            'f_inicio'  => 'required',
            'f_fin'  => 'required',
            'estado' => 'nullable'
        ]);

        $banner = AppPublicidad::find($request->get('id'));
        $banner->enlace = $request->get('enlace');
        $banner->descripcion = $request->get('descripcion');
        $banner->f_inicio = $request->get('f_inicio');
        $banner->f_fin = $request->get('f_fin');
        $banner->estado = $request->get('estado') ? 1 : 0;
        $banner->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Publicidad modificada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminar(Request $request) {
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

        $publicidad = AppPublicidad::find($request->get('id'));
        $sRutaImagen = str_replace('/storage/', '', $publicidad->ruta);
        Storage::disk('public')->delete($sRutaImagen);
        $publicidad->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Banner eliminado correctamente.';

        return response()->json($respuesta);
    }
}
