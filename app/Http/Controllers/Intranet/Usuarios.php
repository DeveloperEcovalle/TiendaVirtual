<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Perfil;
use App\Persona;
use App\Usuario;
use Illuminate\Http\Request;

class Usuarios extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 30;
        $this->sPermisoListar = 'CONFUSUARIOSLISTAR';
        $this->sPermisoInsertar = 'CONFUSUARIOSINSERTAR';
        $this->sPermisoActualizar = 'CONFUSUARIOSACTUALIZAR';
        $this->sPermisoEliminar = 'CONFUSUARIOSELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.usuarios.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.configuracion.usuarios.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.usuarios.panel_nuevo');
    }

    public function ajaxNuevoListarPerfiles() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        $lstPerfiles = [];
        if ($permiso) {
            $lstPerfiles = Perfil::orderBy('id_interno')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPerfiles' => $lstPerfiles];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.usuarios.panel_editar');
    }

    public function ajaxEditarListarPerfiles() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        $lstPerfiles = [];
        if ($permiso) {
            $lstPerfiles = Perfil::orderBy('id_interno')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPerfiles' => $lstPerfiles];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstUsuarios = [];
        if ($permiso) {
            $lstUsuarios = Usuario::with(['persona'])->orderBy('id_interno')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUsuarios' => $lstUsuarios];

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
            'perfil' => 'required|numeric',
            'nombres' => 'required|string|max:200',
            'apellido_paterno' => 'required|string|max:200',
            'apellido_materno' => 'required|string|max:200',
            'username' => 'required|string|unique:usuarios,username',
            'contrasena' => 'required|string',
            'confirmar_contrasena' => 'required|string',
            'correo' => 'nullable|email',
            'telefono' => 'nullable',
        ]);

        $contrasena = $request->get('contrasena');
        $confirmar_contrasena = $request->get('confirmar_contrasena');

        if ($contrasena !== $confirmar_contrasena) {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Las contrase&ntilde;as no coinciden.';
            return response()->json($respuesta);
        }

        $fecha_reg = now()->toDateTimeString();

        $persona = new Persona;
        $persona->nombres = $request->get('nombres');
        $persona->apellido_1 = $request->get('apellido_paterno');
        $persona->apellido_2 = $request->get('apellido_materno');
        $persona->correo = $request->get('correo');
        $persona->telefono = $request->get('telefono');
        $persona->usuario_reg = $this->usuario->id;
        $persona->fecha_reg = $fecha_reg;
        $persona->save();

        $id_interno = intval(Usuario::max('id_interno')) + 1;

        $usuario = new Usuario;
        $usuario->perfil_id = $request->get('perfil');
        $usuario->persona_id = $persona->id;
        $usuario->id_interno = $id_interno;
        $usuario->username = $request->get('username');
        $usuario->contrasena = md5($contrasena);
        $usuario->usuario_reg = $this->usuario->id;
        $usuario->fecha_reg = $fecha_reg;
        $usuario->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Usuario registrado correctamente.';

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
            'perfil' => 'required|numeric',
            'nombres' => 'required|string|max:200',
            'apellido_paterno' => 'required|string|max:200',
            'apellido_materno' => 'required|string|max:200',
            'username' => 'required|string|unique:usuarios,username,' . $id,
            'correo' => 'nullable|email',
            'telefono' => 'nullable',
        ]);

        $fecha_act = now()->toDateTimeString();

        $persona = Persona::find($id);
        $persona->nombres = $request->get('nombres');
        $persona->apellido_1 = $request->get('apellido_paterno');
        $persona->apellido_2 = $request->get('apellido_materno');
        $persona->correo = $request->get('correo');
        $persona->telefono = $request->get('telefono');
        $persona->usuario_act = $this->usuario->id;
        $persona->fecha_act = $fecha_act;
        $persona->save();

        $usuario = Usuario::find($id);
        $usuario->perfil_id = $request->get('perfil');
        $usuario->username = $request->get('username');
        $usuario->usuario_act = $this->usuario->id;
        $usuario->fecha_act = $fecha_act;
        $usuario->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Usuario modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarContrasena(Request $request) {
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
            'contrasena' => 'required',
            'confirmar_contrasena' => 'required'
        ]);

        $contrasena = $request->get('contrasena');
        $confirmar_contrasena = $request->get('confirmar_contrasena');

        if ($contrasena !== $confirmar_contrasena) {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Las contrase&ntilde;as no coinciden.';
            return response()->json($respuesta);
        }

        $usuario = Usuario::find($request->get('id'));
        $usuario->contrasena = md5($contrasena);
        $usuario->usuario_act = $this->usuario->id;
        $usuario->fecha_act = now()->toDateTimeString();
        $usuario->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contrase&ntilde;a modificada correctamente.';

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

        $valor = Usuario::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Usuario eliminado correctamente.';

        return response()->json($respuesta);
    }
}
