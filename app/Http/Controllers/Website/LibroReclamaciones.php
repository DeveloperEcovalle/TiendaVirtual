<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Empresa;
use App\TelefonoEmpresa;
use App\Http\Controllers\Result;
use App\Pagina;
use App\LibroReclamaciones as AppLibroReclamaciones;
use App\Http\Controllers\Respuesta;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade as PDF;
class LibroReclamaciones extends Website
{
    public function __construct() {
        parent::__construct();

        $this->lstLocalesLR = [
            'en' => [
                'Virtual Complaints Book' => 'VIRTUAL COMPLAINTS BOOK',
                'Identification of the complaining consumer' => 'Identification of the complaining consumer',
                'Another phone' => 'Another phone (Optional)',
                'Name' => 'Name',
                'Last Name' => 'Last Name',
                'Phone' => 'Phone',
                'Address' => 'Address',
                'Lot' => 'No. / Lot',
                'DeptInt' => 'Dept / Int (Optional)',
                'Urbanization' => 'Urbanization (Optional)',
                'Reference' => 'Reference (Optional)',
                'Departament' => 'Departament',
                'Province' => 'Province',
                'District' => 'District',
                'Document type' => 'Document type',
                'Document number' => 'Document number',
                'Email' => 'Email',
                'Identification of the contracted asset' => 'Identification of the contracted asset',
                'Description' => 'Description',
                'Actions taken by the provider' => 'Actions taken by the provider',
                'Send' => 'Send',
                'Claim detail' => 'Claim detail',
                'Claim type' => 'Claim type',
                'Detail' => 'Detail',
                'DetailO' => 'Detail (Optional)',
                'Order' => 'Order',
                'Amount of the good object of claim' => 'Amount of the good object of claim',
                'Order number' => 'Order number (Optional)',
            ],
            'es' => [
                'Virtual Complaints Book' => 'LIBRO DE RECLAMACIONES VIRTUAL',
                'Identification of the complaining consumer' => 'Identificación del consumidor reclamante',
                'Another phone' => 'Otro teléfono (Opcional)',
                'Name' => 'Nombres',
                'Last Name' => 'Apellidos',
                'Phone' => 'Teléfono/Celular',
                'Address' => 'Dirección',
                'Lot' => 'Nro. / Lote',
                'DeptInt' => 'Depto / Int (Opcional)',
                'Urbanization' => 'Urbanización (Opcional)',
                'Reference' => 'Referencia (Opcional)',
                'Departament' => 'Departamento',
                'Province' => 'Provincia',
                'District' => 'Distrito',
                'Document type' => 'Tipo de documento',
                'Document number' => 'Número de documento',
                'Email' => 'Correo electrónico',
                'Identification of the contracted asset' => 'Identificación del bien contratado',
                'Description' => 'Descripción',
                'Actions taken by the provider' => 'Acciones adoptadas por el proveedor',
                'Send' => 'Enviar',
                'Claim detail' => 'Detalle de reclamación',
                'Claim type' => 'Tipo de reclamo',
                'Detail' => 'Detalle',
                'DetailO' => 'Detalle (Opcional)',
                'Order' => 'Pedido',
                'Order number' => 'Número de pedido (Opcional)',
                'Amount of the good object of claim' => 'Monto del bien objeto de reclamo',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstLocalesLR' => $this->lstLocalesLR[$locale],
            'iPagina' => 11,
        ];

        return view('website.libro_reclamaciones', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(11);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }

    public function ajaxEnviar(Request $request) 
    {
        try
        {
            DB::beginTransaction();
            $reclamo = new AppLibroReclamaciones();
            $reclamo->nombres = $request->nombres;
            $reclamo->apellidos = $request->apellidos;
            $reclamo->telefono = $request->telefono;
            $reclamo->otelefono = $request->otelefono;
            $reclamo->direccion = $request->direccion;
            $reclamo->lote = $request->lote;
            $reclamo->dept_int = $request->dept_int;
            $reclamo->urbanizacion = $request->urbanizacion;
            $reclamo->referencia = $request->referencia;
            $reclamo->departamento = $request->departamento;
            $reclamo->provincia = $request->provincia;
            $reclamo->distrito = $request->distrito;
            $reclamo->tipo_documento = $request->tipo_documento;
            $reclamo->numero_documento = $request->numero_documento;
            $reclamo->email = $request->email;
            $reclamo->monto_bien = $request->monto_bien;
            $reclamo->bien_contratado = $request->bien_contratado;
            $reclamo->descripcion = $request->descripcion;
            $reclamo->numero_pedido = $request->numero_pedido;
            $reclamo->tipo_reclamo = $request->tipo_reclamo;
            $reclamo->detalle = $request->detalle;
            $reclamo->pedido = $request->pedido;
            $reclamo->detalleo = $request->detalleo;
            
            // $notificacionContactanosMail = new NotificacionContactanosMail($nombres, $apellidos, $asunto, $email, $telefono, $mensaje, $ruta_archivo);
            // Mail::to('comunity.rrss@ecovalle.pe')->send($notificacionContactanosMail);
        
            if($reclamo->save())
            {
                $respuesta = new Respuesta;
                $respuesta->result = Result::SUCCESS;
                $respuesta->mensaje = 'Reclamo enviado correctamente.';

                $codigo = generaCodigo((string)$reclamo->id);
                $reclamo->codigo = $codigo;
                $reclamo->update();

                $empresa = Empresa::find(1);
                Config::set('mail.mailers.smtp.username', 'ccubas.16.09@gmail.com');
                Config::set('mail.mailers.smtp.password', 'mdnewuttmqmofynj');

                $reclamo_pdf = AppLibroReclamaciones::find($reclamo->id);

                $pdf = PDF::loadview('website.pdf.reclamo',['reclamo' =>$reclamo_pdf, 'empresa' => $empresa])->setPaper('a4')->setWarnings(false);

                PDF::loadView('website.pdf.reclamo',['reclamo' =>$reclamo_pdf, 'empresa' => $empresa])
                ->save(public_path().'/storage/reclamos/' . $reclamo->codigo.'.pdf');
                
                Mail::send('website.email.reclamo',compact("reclamo"), function ($mail) use ($pdf,$reclamo) {
                    $mail->to('developer.ecovalle@gmail.com');
                    $mail->subject('RECLAMO N° '.$reclamo->codigo);
                    $mail->attachdata($pdf->output(), $reclamo->codigo.'.pdf');
                    $mail->from('ccubas.16.09@gmail.com','ECO VALLE');
                });
            }
            else
            {
                $respuesta = new Respuesta;
                $respuesta->result = Result::ERROR;
                $respuesta->mensaje = 'Error de envío.';
            }

            DB::commit();
            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $respuesta = new Respuesta;
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }
    
}
