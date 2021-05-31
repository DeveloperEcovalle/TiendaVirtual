<?php

namespace App\Http\Controllers\Intranet;

use App\Cliente;
use App\Documento;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Persona;
use App\Sunat06TipoDocumento;
use App\Ubigeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Clientes extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 16;
        $this->iMenuId = 17;
        $this->sPermisoListar = 'PERSCLIENTESLISTAR';
        $this->sPermisoInsertar = 'PERSCLIENTESINSERTAR';
        $this->sPermisoActualizar = 'PERSCLIENTESACTUALIZAR';
        $this->sPermisoEliminar = 'PERSCLIENTESELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.personas.clientes.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.personas.clientes.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.personas.clientes.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.personas.clientes.panel_editar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstClientes = [];
        if ($permiso) {
            // $lstClientes = Cliente::with(['persona', 'persona.documentos', 'persona.documentos.tipo_documento', 'ubigeo'])->get();
            $lstClientes = Cliente::with(['persona', 'persona.ubigeo'])->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstClientes' => $lstClientes];

        return response()->json($respuesta);
    }

    public function ajaxNuevoListarData(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $lstTiposDocumento = [];
        $lstUbigeo = [];
        if ($permiso) {
            //$lstTiposDocumento = Sunat06TipoDocumento::where('cliente', 1)->orderBy('orden')->get();
            $lstUbigeo = Ubigeo::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstTiposDocumento' => $lstTiposDocumento, 'lstUbigeo' => $lstUbigeo];

        return response()->json($respuesta);
    }

    public function ajaxNuevoConsultarDni(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        $numero_documento = $request->get('numero_de_documento');

        if ($permiso && strlen($numero_documento) == 8) {
            $sUrl = 'https://api.reniec.cloud/dni/' . $numero_documento;

            $httpResponse = Http::withOptions(['verify' => false])->get($sUrl);
            $respuesta->data = $httpResponse->json();
        }

        return response()->json($respuesta);
    }

    public function ajaxNuevoConsultarRuc(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        $numero_documento = $request->get('numero_de_documento');

        if ($permiso && strlen($numero_documento) == 11) {
            $sUrl = 'https://apiperu.dev/api/ruc/' . $numero_documento;

            $httpResponse = Http::withHeaders([
                'Authorization' => 'Bearer 9d88c56cc3ca7a6a1564c5ec85cc52c8eda23ebd5f6c334763cc7720d075fa46',
            ])->withOptions(['verify' => false])->get($sUrl);
            $respuesta->data = $httpResponse->json()['data'];
        }

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
            'nombres' => 'required|string|max:1000',
            'apellido_paterno' => 'nullable|string|max:200',
            'apellido_materno' => 'nullable|string|max:200',
            'correo' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:500',
        ]);

        $sLstDocumentos = $request->get('lista_documentos');
        $lstDocumentos = explode('|', $sLstDocumentos);

        foreach ($lstDocumentos as $doc) {
            $lstDocumento = explode(';', $doc);
            $documentoBuscado = Documento::where('sunat_06_codigo', $lstDocumento[0])->where('numero', $lstDocumento[1])->first();
            if ($documentoBuscado) {
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'El documento ' . $lstDocumento[1] . ' ya se encuentra registrado en otra persona.';
                return response()->json($respuesta);
            }
        }

        $fecha_reg = now()->toDateTimeString();

        $persona = new Persona;
        $persona->nombres = $request->get('nombres');
        $persona->apellido_1 = $request->get('apellido_paterno');
        $persona->apellido_2 = $request->get('apellido_materno');
        $persona->usuario_reg = $this->usuario->id;
        $persona->fecha_reg = $fecha_reg;
        $persona->save();

        $cliente = new Cliente;
        $cliente->id = $persona->id;
        $cliente->correo = $request->get('correo');
        $cliente->telefono = $request->get('telefono');
        $cliente->ubigeo_id = $request->get('distrito');
        $cliente->direccion = $request->get('direccion');
        $cliente->usuario_reg = $this->usuario->id;
        $cliente->fecha_reg = $fecha_reg;
        $cliente->save();

        $lstDocumentosInsertar = [];
        foreach ($lstDocumentos as $doc) {
            $lstDocumento = explode(';', $doc);

            array_push($lstDocumentosInsertar,
                array('persona_id' => $persona->id,
                    'sunat_06_codigo' => $lstDocumento[0],
                    'numero' => $lstDocumento[1],
                    'usuario_reg' => $this->usuario->id,
                    'fecha_reg' => $fecha_reg,
                )
            );
        }

        Documento::insert($lstDocumentosInsertar);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Cliente registrado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEditarListarData(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $lstTiposDocumento = [];
        $lstUbigeo = [];
        if ($permiso) {
            //$lstTiposDocumento = Sunat06TipoDocumento::where('cliente', 1)->orderBy('orden')->get();
            $lstUbigeo = Ubigeo::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstTiposDocumento' => $lstTiposDocumento, 'lstUbigeo' => $lstUbigeo];

        return response()->json($respuesta);
    }

    public function ajaxEditarConsultarDni(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        $numero_documento = $request->get('numero_de_documento');

        if ($permiso && strlen($numero_documento) == 8) {
            $sUrl = 'https://api.reniec.cloud/dni/' . $numero_documento;

            $httpResponse = Http::withOptions(['verify' => false])->get($sUrl);
            $respuesta->data = $httpResponse->json();
        }

        return response()->json($respuesta);
    }

    public function ajaxEditarConsultarRuc(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        $numero_documento = $request->get('numero_de_documento');

        if ($permiso && strlen($numero_documento) == 11) {
            $sUrl = 'https://apiperu.dev/api/ruc/' . $numero_documento;

            $httpResponse = Http::withHeaders([
                'Authorization' => 'Bearer 9d88c56cc3ca7a6a1564c5ec85cc52c8eda23ebd5f6c334763cc7720d075fa46',
            ])->withOptions(['verify' => false])->get($sUrl);
            $respuesta->data = $httpResponse->json()['data'];
        }

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
            'nombres' => 'required|string|max:1000',
            'apellido_paterno' => 'nullable|string|max:200',
            'apellido_materno' => 'nullable|string|max:200',
            'correo' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:500',
        ]);

        $sLstDocumentos = $request->get('lista_documentos');
        $lstDocumentos = explode('|', $sLstDocumentos);

        $id = $request->get('id');

        foreach ($lstDocumentos as $doc) {
            $lstDocumento = explode(';', $doc);
            $documentoBuscado = Documento::where('sunat_06_codigo', $lstDocumento[0])->where('numero', $lstDocumento[1])->where('persona_id', '<>', $id)->first();
            if ($documentoBuscado) {
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'El documento ' . $lstDocumento[1] . ' ya se encuentra registrado en otra persona.';
                return response()->json($respuesta);
            }
        }

        $fecha_act = now()->toDateTimeString();

        $persona = Persona::find($id);
        $persona->nombres = $request->get('nombres');
        $persona->apellido_1 = $request->get('apellido_paterno');
        $persona->apellido_2 = $request->get('apellido_materno');
        $persona->usuario_act = $this->usuario->id;
        $persona->fecha_act = $fecha_act;
        $persona->save();

        $cliente = Cliente::find($id);
        $cliente->correo = $request->get('correo');
        $cliente->telefono = $request->get('telefono');
        $cliente->ubigeo_id = $request->get('distrito');
        $cliente->direccion = $request->get('direccion');
        $cliente->usuario_act = $this->usuario->id;
        $cliente->fecha_act = $fecha_act;
        $cliente->save();

        Documento::where('persona_id', $id)->delete();

        $lstDocumentosInsertar = [];
        foreach ($lstDocumentos as $doc) {
            $lstDocumento = explode(';', $doc);

            array_push($lstDocumentosInsertar,
                array('persona_id' => $id,
                    'sunat_06_codigo' => $lstDocumento[0],
                    'numero' => $lstDocumento[1],
                    'usuario_reg' => $this->usuario->id,
                    'fecha_reg' => $fecha_act,
                )
            );
        }

        Documento::insert($lstDocumentosInsertar);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Cliente modificado correctamente.';

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

        $cliente = Cliente::find($request->get('id'));
        $cliente->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Cliente eliminado correctamente.';

        return response()->json($respuesta);
    }
}
