<?php

namespace App\Http\Controllers\Intranet;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturacionElectronica extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 32;
        $this->sPermisoListar = 'CONFFACELELISTAR';
        $this->sPermisoActualizar = 'CONFFACELEACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.facturacion_electronica.index', $data);
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

    public function ajaxActualizarUsuarioClaveSOL(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'usuario_sol' => 'required',
            'clave_sol' => 'required'
        ]);

        $empresa = Empresa::first();
        $empresa->usuario_sol = $request->get('usuario_sol');
        $empresa->clave_sol = $request->get('clave_sol');
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Usuario y Clave SOL modificados correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarCertificadoDigital(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $empresa = Empresa::first();
        $required_nullable = $empresa->ruta_certificado_digital ? 'nullable' : 'required';

        $request->validate([
            'certificado_digital' => $required_nullable . '|file',
            'contrasena_del_certificado_digital' => 'required|string',
            'fecha_de_inicio' => 'required|date_format:Y-m-d',
            'fecha_limite' => 'required|date_format:Y-m-d',
        ]);

        $certificado_digital = $request->file('certificado_digital');
        $sRutaCertificadoDigitalActual = $empresa->ruta_certificado_digital ? str_replace('/storage', 'public', $empresa->ruta_certificado_digital) : null;
        $sNombreCertificadoDigitalActual = $sRutaCertificadoDigitalActual ? str_replace('public/', '', $sRutaCertificadoDigitalActual) : null;

        if ($certificado_digital) {
            Storage::disk('public')->delete($sNombreCertificadoDigitalActual);
        }

        $ruta_certificado_digital = $certificado_digital ? $certificado_digital->store('public/empresa') : $sRutaCertificadoDigitalActual;
        $nueva_ruta_certificado_digital = str_replace('public/', '/storage/', $ruta_certificado_digital);

        $empresa->ruta_certificado_digital = $nueva_ruta_certificado_digital;
        $empresa->contrasena_certificado_digital = $request->get('contrasena_del_certificado_digital');
        $empresa->fecha_inicio_certificado_digital = $request->get('fecha_de_inicio');
        $empresa->fecha_limite_certificado_digital = $request->get('fecha_limite');
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Certificado digital actualizado correctamente.';

        return response()->json($respuesta);
    }
}
