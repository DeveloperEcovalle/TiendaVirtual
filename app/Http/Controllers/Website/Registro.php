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

use App\Ubigeo;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        try{
            DB::beginTransaction();
            $respuesta = new Respuesta;
            $data = $request->all();
            $rules = [
                'nombres' => 'required',
                'tipo_documento' => 'required',
                'documento' => 'required|unique:personas,documento',
                'apellidos' => 'required_if:tipo_documento,DNI',
                'correo' => 'required|email|unique:personas,correo',
                'password' => 'required|',
                'cpassword' => 'required|same:password',
                'direccion' => 'required',
                'departamento' => 'required',
                'provincia' => 'required',
                'distrito' => 'required',
    
            ];
            $message = [
                'nombres.required' => 'El campo nombres es obligatorio.',
                'apellidos.required' => 'El campo apellidos es obligatorio.',
                'password.required' => 'El campo contraseña es obligatorio.',
                'cpassword.required' => 'El campo confirmar contraseña es obligatorio.',
                'cpassword.same' => 'Contraseñas diferentes.',
                'departamento.required' => 'El campo departamento es obligatorio.',
                'provincia.required' => 'El campo provincia es obligatorio.',
                'distrito.required' => 'El campo distrito es obligatorio.',
                'tipo_documento.required' => 'El campo tipo documento es obligatorio.',
                'direccion.required' => 'El campo direccion es obligatorio.',
                'documento.required' => 'El campo documento es obligatorio.',
                'documento.unique' => 'El campo documento debe ser único',
                'correo.required' => 'El campo correo es obligatorio.',
                'correo.email' => 'El campo correo debe ser un email.',
                'correo.unique' => 'El correo electrónico ingresado ya se encuentra registrado.',
            ];
    
            $validator =  Validator::make($data, $rules, $message);

            if ($validator->fails()) {

                DB::rollBack();
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'Ocurrió un error de validación.';
                $respuesta->data = array('errors' => $validator->getMessageBag()->toArray());
                return response()->json($respuesta);
    
            }
    
            $iTipoDocumento = $request->get('tipo_documento');
            $sDocumento = $request->get('documento');
            $sNombres = $request->get('nombres');
            $sApellidos = $request->get('apellidos');
            $sTelefono = $request->get('telefono');
            $sTelefono_fijo = $request->get('telefono_fijo');
            $sDireccion = $request->get('direccion');
            $sFechaNacimiento = $request->get('fecha_nacimiento');
            $sEmail = $request->get('correo');
            $sGenero = $request->get('genero');
            $sContrasena = $request->get('password');
    
            $cliente_correo_registrado = Cliente::where('email', $sEmail)->first();
            if ($cliente_correo_registrado) {
                DB::rollBack();
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'El correo electrónico ingresado ya se encuentra registrado.';
                return response()->json($respuesta);
            }
    
            $lstApellidos = explode(' ', $sApellidos);
            $sApellido1 = $lstApellidos[0];
            $sApellido2 = count($lstApellidos) > 1 ? $lstApellidos[1] : null;
    
            $persona = new Persona;
            $persona->nombres = $sNombres;
            $persona->apellido_1 = $sApellido1;
            $persona->apellido_2 = $sApellido2;
            $persona->tipo_documento = $iTipoDocumento;
            $persona->documento = $sDocumento;
            $persona->correo = $sEmail;
            $persona->password = $sContrasena;
            $persona->telefono = $sTelefono;
            $persona->genero = $sGenero;
            $persona->telefono_fijo = $sTelefono_fijo;
            $persona->direccion = $sDireccion;
            $persona->fecha_nacimiento = $sFechaNacimiento;
            $ubigeo = Ubigeo::where('departamento',$request->departamento)->where('provincia',$request->provincia)->where('distrito',$request->distrito)->first();
            $persona->ubigeo_id = $ubigeo ? $ubigeo->id : null;
            $persona->save();
    
            $cliente = new Cliente;
            $cliente->persona_id = $persona->id;
            $cliente->email = $sEmail;
            $cliente->password = md5($sContrasena);
            $cliente->save();
    
            $clienteSesion = Cliente::find($cliente->id);
            session()->forget('cliente');
            session()->put('cliente', $clienteSesion);

            Mail::send('website.email.register',compact("persona"), function ($mail) use ($persona) {
                $mail->subject('BIENVENID@ A ECOVALLE');
                $mail->to($persona->correo);
                $mail->from('website@ecovalle.pe','ECOVALLE');
            });
            
            DB::commit();
            $respuesta->result = Result::SUCCESS;
            return response()->json($respuesta);
        }catch(Exception $e)
        {
            DB::rollBack();
            $respuesta = new Respuesta();
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }

    public function ajaxListarDatos() {

        $lstUbigeo = Ubigeo::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUbigeo' => $lstUbigeo];

        return response()->json($respuesta);
    }
}
