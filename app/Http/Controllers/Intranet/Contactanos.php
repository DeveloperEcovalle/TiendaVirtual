<?php

namespace App\Http\Controllers\Intranet;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Contactanos extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 25;
        $this->sPermisoListar = 'PWEBCONTACLISTAR';
        $this->sPermisoActualizar = 'PWEBCONTACACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.contactanos.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $empresa = null;
        if ($permiso) {
            $empresa = Empresa::with(['telefonos'])->first();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['empresa' => $empresa];

        return response()->json($respuesta);
    }

    public function ajaxActualizarImagenContactanos(Request $request) {
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
        $sRutaImagenActual = str_replace('/storage', 'public', $empresa->ruta_imagen_contactanos);
        $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
        Storage::disk('public')->delete($sNombreImagenActual);
        $ruta_imagen_contactanos = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;

        $nueva_ruta_imagen_contactanos = str_replace('public/', '/storage/', $ruta_imagen_contactanos);

        $empresa->ruta_imagen_contactanos = $nueva_ruta_imagen_contactanos;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen de portada reemplazada correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen_contactanos];

        return response()->json($respuesta);
    }

    public function ajaxActualizarEnlaceMapa(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'nuevo_enlace_del_mapa' => 'required|url'
        ]);

        $empresa = Empresa::first();

        $enlace_mapa = $request->get('nuevo_enlace_del_mapa');
        $empresa->enlace_video = $enlace_mapa;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Enlace del mapa reemplazado correctamente.';
        $respuesta->data = ['sNuevoEnlaceMapa' => $enlace_mapa];

        return response()->json($respuesta);
    }

    public function ajaxActualizarDireccion(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'direccion_de_la_empresa' => 'required'
        ]);

        $empresa = Empresa::first();

        $direccion = $request->get('direccion_de_la_empresa');
        $empresa->direccion = $direccion;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Direcci&oacute;n modificada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarRedesSociales(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'enlace_de_facebook' => 'nullable|url',
            'enlace_de_instagram' => 'nullable|url',
            'enlace_de_youtube' => 'nullable|url',
            'enlace_de_linkedin' => 'nullable|url',
            'enlace_de_twitter' => 'nullable|url',
            'enlace_de_tiktok' => 'nullable|url',
        ]);

        $empresa = Empresa::first();
        $empresa->enlace_facebook = $request->get('enlace_de_facebook');
        $empresa->enlace_instagram = $request->get('enlace_de_instagram');
        $empresa->enlace_youtube = $request->get('enlace_de_youtube');
        $empresa->enlace_linkedin = $request->get('enlace_de_linkedin');
        $empresa->enlace_twitter = $request->get('enlace_de_twitter');
        $empresa->enlace_tiktok = $request->get('enlace_de_tiktok');
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Redes sociales modificadas correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarCorreo(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'correo_de_recepcion_para_contactanos' => 'required|email'
        ]);

        $empresa = Empresa::first();

        $correo_contactanos = $request->get('correo_de_recepcion_para_contactanos');
        $empresa->correo_contactanos = $correo_contactanos;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Correo modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxInsertarTelefono(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'icono' => 'required|string',
            'telefono' => 'required|string',
        ]);

        $whatsapp = $request->get('whatsapp_de_la_empresa') ? 1 : 0;
        if ($whatsapp === 1) {
            TelefonoEmpresa::where('whatsapp', 1)->update(['whatsapp' => 0]);
        }

        $telefonoEmpresa = new TelefonoEmpresa;
        $telefonoEmpresa->icono = $request->get('icono');
        $telefonoEmpresa->numero = $request->get('telefono');
        $telefonoEmpresa->whatsapp = $whatsapp;

        $empresa = Empresa::first();
        $empresa->telefonos()->save($telefonoEmpresa);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Tel&eacute;fono registrado correctamente.';
        $respuesta->data = ['id' => $telefonoEmpresa->id];

        return response()->json($respuesta);
    }

    public function ajaxEliminarTelefono(Request $request) {
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
        ]);

        $telefonoEmpresa = TelefonoEmpresa::find($request->get('id'));
        $telefonoEmpresa->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Tel&eacute;fono eliminado correctamente.';

        return response()->json($respuesta);
    }
}
