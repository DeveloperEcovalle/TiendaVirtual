<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GuiaCompras extends Intranet
{
    private $iPaginaId = 12;

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 51;
        $this->sPermisoListar = 'PWEBGUIALISTAR';
        $this->sPermisoActualizar = 'PWEBGUIAACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.guia_compras.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $pagina = null;
        if ($permiso) {
            $pagina = Pagina::find($this->iPaginaId);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

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
            'imagen_de_portada' => 'nullable|image|mimes:jpeg,png',
            'enlace_de_imagen_de_portada' => 'nullable',
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $imagen = $request->file('imagen_de_portada');

        /*if ($imagen) {
            if($pagina->ruta_imagen_portada)
            {
                $imag_aux = explode('/',$pagina->ruta_imagen_portada);
                Storage::disk('public')->delete('empresa/'.$imag_aux[3]);
            }
            $ruta = public_path().'/storage/empresa';
            $fileName = uniqid().$imagen->getClientOriginalName();
            $imagen->move($ruta,$fileName);
            $sNombreImagenActual = '/storage/empresa/'.$fileName;
            $nueva_ruta_imagen_portada = $sNombreImagenActual;
        } else {
            $nueva_ruta_imagen_portada = $pagina->ruta_imagen_portada;
        }*/
        $sRutaImagenActual = str_replace('/storage', 'public', $pagina->ruta_imagen_portada);
        $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
        Storage::disk('public')->delete($sNombreImagenActual);

        $ruta_imagen_portada = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;

        $nueva_ruta_imagen_portada = str_replace('public/', '/storage/', $ruta_imagen_portada);

        $pagina->ruta_imagen_portada = $nueva_ruta_imagen_portada;
        $pagina->enlace_imagen_portada = $request->get('enlace_de_imagen_de_portada');
        $pagina->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen de portada guardada correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen_portada];

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoEspanol(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_espanol' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_espanol = $request->get('contenido_en_espanol');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en español modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoIngles(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_ingles' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_ingles = $request->get('contenido_en_ingles');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en inglés modificado correctamente.';

        return response()->json($respuesta);
    }
}
