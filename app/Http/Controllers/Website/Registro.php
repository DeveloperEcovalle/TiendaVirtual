<?php

namespace App\Http\Controllers\Website;

use App\Cliente;
use App\Documento;
use App\Empresa;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Persona;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;

class Registro extends Website {

    protected $lstTraduccionesRegistro;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesRegistro = [
            'en' => [
                'Registration' => 'Registration',
                'required_fields' => 'Required fields',
                'Identification' => 'Identification',
                'ID Number' => 'ID Number',
                'Name' => 'Name',
                'Last Name' => 'Last Name',
                'address' => 'Address',
                'date_of_birth' => 'Date of birth',
                'landline_phone' => 'Landline phone',
                'cell_phone' => 'Cell phone',
                'gender' => 'Gender',
                'Access data' => 'Access data',
                'Email' => 'Email',
                'this_will_be_your_user' => 'This will be your user',
                'Password' => 'Password',
                'Confirm password' => 'Confirm password',
                'accept' => 'I wish that AGROENSANCHA S.R.L. (Ecovalle\'s corporate name) and its related companies to keep me informed of offers and promotions.',
                'register' => 'Register',
                'clicking_register' => 'By clicking on the Register button, you authorize the use of your personal data.',
            ],
            'es' => [
                'Registration' => 'Registro',
                'required_fields' => 'Campos obligatorios',
                'Identification' => 'Identificación',
                'ID Number' => 'Número de documento',
                'Name' => 'Nombres',
                'Last Name' => 'Apellidos',
                'address' => 'Dirección',
                'date_of_birth' => 'Fecha de nacimiento',
                'landline_phone' => 'Teléfono fijo',
                'cell_phone' => 'Teléfono celular',
                'gender' => 'Sexo',
                'Access data' => 'Datos de acceso',
                'Email' => 'Correo electrónico',
                'this_will_be_your_user' => 'Este será tu usuario',
                'Password' => 'Contraseña',
                'Confirm password' => 'Confirmar contraseña',
                'accept' => 'Deseo que AGROENSANCHA S.R.L. (razón social de Ecovalle) y sus empresas vinculadas me mantengan informado de ofertas y promociones.',
                'register' => 'Registrar',
                'clicking_register' => 'Pulsando el botón Registrar, usted autoriza el uso de sus datos personales.',
            ],
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesRegistro' => $this->lstTraduccionesRegistro[$locale],
            'iPagina' => -1,
        ];

        return view('website.registro', $data);
    }

    public function ajaxRegistrar(Request $request) {
        $request->validate([
            'tipo_de_documento' => 'required',
            'numero_de_documento' => 'required|unique:documentos,numero',
            'nombres' => 'required',
            'apellidos' => 'required_if:tipo_de_dcoumento,1',
            'fecha_de_nacimiento' => 'required|date_format:Y-m-d',
            'correo_electronico' => 'required|email',
            'contrasena' => 'required|confirmed',
            'acepto_terminos_y_condiciones_y_politica_de_privacidad' => 'required',
        ]);

        $iTipoDocumento = $request->get('tipo_de_documento');
        $sNumeroDocumento = $request->get('numero_de_documento');
        $sNombres = $request->get('nombres');
        $sApellidos = $request->get('apellidos');
        $sFechaNacimiento = $request->get('fecha_de_nacimiento');
        $sEmail = $request->get('correo_electronico');
        $sContrasena = $request->get('contrasena');

        $respuesta = new Respuesta;

        $cliente_correo_registrado = Cliente::where('correo', $sEmail)->first();
        if ($cliente_correo_registrado) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'El correo electrónico ingresado ya se encuentra registrado.';
            return response()->json($respuesta);
        }

        $documento_registrado = Documento::where('sunat_06_codigo', $iTipoDocumento)->where('numero', $sNumeroDocumento)->first();
        if ($documento_registrado) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'El documento ingresado ya se encuentra registrado.';
            return response()->json($respuesta);
        }

        $fecha_reg = now()->toDateTimeString();

        $lstApellidos = explode(' ', $sApellidos);
        $sApellido1 = $lstApellidos[0];
        $sApellido2 = count($lstApellidos) > 1 ? $lstApellidos[1] : null;

        $persona = new Persona;
        $persona->nombres = $sNombres;
        $persona->apellido_1 = $sApellido1;
        $persona->apellido_2 = $sApellido2;
        $persona->correo = $sEmail;
        $persona->usuario_reg = 1;
        $persona->fecha_reg = $fecha_reg;
        $persona->save();

        $cliente = new Cliente;
        $cliente->id = $persona->id;
        $cliente->clientes_varios = 0;
        $cliente->usuario_web = 1;
        $cliente->fecha_nacimiento = $sFechaNacimiento;
        $cliente->correo = $sEmail;
        $cliente->contrasena = md5($sContrasena);
        $cliente->usuario_reg = 1;
        $cliente->fecha_reg = $fecha_reg;
        $cliente->save();

        $documento = new Documento;
        $documento->sunat_06_codigo = $iTipoDocumento;
        $documento->numero = $sNumeroDocumento;
        $documento->usuario_reg = 1;
        $documento->fecha_reg = $fecha_reg;
        $persona->documentos()->save($documento);

        $clienteSesion = Cliente::with(['persona'])->find($persona->id);
        $request->session()->put('cliente', $clienteSesion);

        $respuesta->result = Result::SUCCESS;
        return response()->json($respuesta);
    }
}
