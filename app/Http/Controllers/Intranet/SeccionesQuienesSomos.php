<?php

namespace App\Http\Controllers\Intranet;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\SeccionQuienesSomos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeccionesQuienesSomos extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 34;
        $this->sPermisoListar = 'PWEBQUISOMLISTAR';
        $this->sPermisoInsertar = 'PWEBQUISOMINSERTAR';
        $this->sPermisoActualizar = 'PWEBQUISOMACTUALIZAR';
        $this->sPermisoEliminar = 'PWEBQUISOMELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.secciones_quienes_somos.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.paginaweb.secciones_quienes_somos.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.secciones_quienes_somos.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.secciones_quienes_somos.panel_editar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstSecciones = [];
        if ($permiso) {
            $empresa = Empresa::first();
            $lstSecciones = $empresa->secciones_quienes_somos()->orderBy('orden')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstSecciones' => $lstSecciones];

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
            'tipo' => 'required',
            'titulo_es' => 'required|string|max:255|unique:secciones_quienes_somos,titulo_es',
            'titulo_en' => 'required|string|max:255|unique:secciones_quienes_somos,titulo_en',
            'contenido_es' => 'required|string',
            'contenido_en' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png',
            'enlace_de_video' => 'nullable|url',
        ]);

        $imagen = $request->file('imagen');
        $ruta_imagen = $imagen ? $imagen->store('public/empresa') : null;

        $enlace_video = $request->get('enlace_de_video');
        $nuevo_enlace_video = $enlace_video ? str_replace('watch?v=', 'embed/', $enlace_video) : null;

        $orden = intval(SeccionQuienesSomos::max('orden')) + 1;

        $seccion_quienes_somos = new SeccionQuienesSomos;
        $seccion_quienes_somos->tipo = $request->get('tipo');
        $seccion_quienes_somos->orden = $request->get('orden');
        $seccion_quienes_somos->titulo_es = $request->get('titulo_es');
        $seccion_quienes_somos->titulo_en = $request->get('titulo_en');
        $seccion_quienes_somos->contenido_es = $request->get('contenido_es');
        $seccion_quienes_somos->contenido_en = $request->get('contenido_en');
        $seccion_quienes_somos->ruta_imagen = $ruta_imagen ? str_replace('public/', '/storage/', $ruta_imagen) : null;
        $seccion_quienes_somos->enlace_video = $nuevo_enlace_video;
        $seccion_quienes_somos->orden = $orden;
        $seccion_quienes_somos->usuario_reg = $this->usuario->id;
        $seccion_quienes_somos->fecha_reg = now()->toDateTimeString();
        Empresa::first()->secciones_quienes_somos()->save($seccion_quienes_somos);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Secci&oacute;n registrada correctamente.';

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

        $id = $request->get('id');

        $request->validate([
            'id' => 'required|numeric',
            'tipo' => 'required',
            'titulo_es' => 'required|string|max:255|unique:secciones_quienes_somos,titulo_es,' . $id,
            'titulo_en' => 'required|string|max:255|unique:secciones_quienes_somos,titulo_en,' . $id,
            'contenido_es' => 'required|string',
            'contenido_en' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png',
            'enlace_de_video' => 'nullable|url',
        ]);

        $seccion_quienes_somos = SeccionQuienesSomos::find($id);

        $imagen = $request->file('imagen');
        $sRutaImagenActual = $seccion_quienes_somos->ruta_imagen ? str_replace('/storage', 'public', $seccion_quienes_somos->ruta_imagen) : null;
        if ($imagen) {
            $sNombreImagenActual = $sRutaImagenActual ? str_replace('public/', '', $sRutaImagenActual) : null;
            if ($sNombreImagenActual) {
                Storage::disk('public')->delete($sNombreImagenActual);
            }
        }
        $ruta_imagen = $imagen ? $imagen->store('public/empresa') : null;

        $enlace_video = $request->get('enlace_de_video');
        $nuevo_enlace_video = $enlace_video ? str_replace('watch?v=', 'embed/', $enlace_video) : null;

        $seccion_quienes_somos->tipo = $request->get('tipo');
        $seccion_quienes_somos->titulo_es = $request->get('titulo_es');
        $seccion_quienes_somos->titulo_en = $request->get('titulo_en');
        $seccion_quienes_somos->contenido_es = $request->get('contenido_es');
        $seccion_quienes_somos->contenido_en = $request->get('contenido_en');
        $seccion_quienes_somos->ruta_imagen = $ruta_imagen ? str_replace('public/', '/storage/', $ruta_imagen) : null;
        $seccion_quienes_somos->enlace_video = $nuevo_enlace_video;
        $seccion_quienes_somos->usuario_act = $this->usuario->id;
        $seccion_quienes_somos->fecha_act = now()->toDateTimeString();
        $seccion_quienes_somos->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Secci&oacute;n modificada correctamente.';

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

        $seccion_quienes_somos = SeccionQuienesSomos::find($request->get('id'));
        $sRutaImagen = str_replace('/storage/', '', $seccion_quienes_somos->ruta_imagen);
        Storage::disk('public')->delete($sRutaImagen);
        $seccion_quienes_somos->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Secci&oacute;n eliminada correctamente.';

        return response()->json($respuesta);
    }
}
