<?php

namespace App\Http\Controllers\Website;

use App\DetalleCarrito;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionContactoContigo;
use App\Persona;
use App\Producto;
use App\Publicidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Website extends Controller {

    protected $lstLocales;

    public function __construct() {
        $this->lstLocales = [
            'en' => [
                'Email' => 'Email',
                'Password' => 'Password',

                'categories' => 'Categories',
                'partners' => 'Partners',
                'human_resources' => 'Human resources',

                'new_revenues' => 'New revenues',

                'call_or_write_to_us' => 'Call or write to us',
                'contact_us' => 'Contact Us',

                'Information' => 'Information',
                'user_area' => 'User area',
                'payment_methods' => 'Payment Methods',
                'follow_us' => 'Follow Us',
                'forgot_my_password' => 'Forgot my password',
                'complaints_book' => 'Complaints book',

                'nationwide_shipments' => 'Nationwide shipments',
                'online_service' => 'Online service',
                'hours_from_monday_to_saturday' => 'Hours from Monday to Saturday',
                'secure_payment' => 'Secure payment',
                'guaranteed_payment_technology' => 'Guaranteed payment technology',
                'guaranteed_quality' => 'Guaranteed quality',
                'thinking_about_the_welfare_of_all' => 'Thinking about the welfare of all',

                'Home' => 'Home',
                'About Us' => 'About Us',
                'Sign In' => 'Sign In',
                'Who we are' => 'Who we are',
                'Product lines' => 'Product Lines',
                'Certifications' => 'Certifications',
                'Store' => 'Store',
                'Search Result' => 'Search Result',
                'Services' => 'Maquila',
                'Be Ecovalle' => 'Be Ecovalle',
                'Quick links' => 'Quick links',
                'Policy and Privacy' => 'Policy and Privacy',
                'Terms and Conditions' => 'Terms and Conditions',
                'Articles' => 'Articles',
                'FAQ' => 'FAQ',
                'Shopping guide' => 'Shopping guide',
                'Returning orders' => 'Returning orders',
                'My account' => 'My account',
                'Logout' => 'Logout',
                'Shopping cart' => 'Shopping cart',
                'Update profile' => 'Update my profile',
                'My wish list' => 'My wish list',
                'Customer service' => 'Customer service',
                'Make your purchases easily with' => 'Make your purchases easily with',

                'Add to cart' => 'Add to cart',
                'contact_with_you' => 'We would to keep in touch with you',
                'birthday' => 'Birthday',
                'subscribe_to_newsletter' => 'Subscribe to our Newsletter and receive all our news and promotions.',
                'last_name_and_name' => 'Last Name and Name',
                'contact_number' => 'Contact number',
                'subscribe' => 'Subscribe',
                'view_more' => 'View more',

                'related_products' => 'Related products',
            ],
            'es' => [
                'Email' => 'Email',
                'Password' => 'Contrase??a',

                'categories' => 'Categor??as',
                'partners' => 'Socios',
                'human_resources' => 'Recursos humanos',

                'new_revenues' => 'Nuevos ingresos',

                'call_or_write_to_us' => 'Ll??manos o escr??benos',
                'contact_us' => 'Cont??ctanos',

                'Information' => 'Informaci??n',
                'user_area' => '??rea de usuario',
                'payment_methods' => 'Medios de Pago',
                'follow_us' => 'S??guenos',
                'forgot_my_password' => 'Olvid?? mi contrase??a',
                'complaints_book' => 'Libro de reclamaciones',

                'nationwide_shipments' => 'Env??os a nivel nacional',
                'online_service' => 'Atenci??n online',
                'hours_from_monday_to_saturday' => 'Horarios de lunes a s??bado',
                'secure_payment' => 'Pago seguro',
                'guaranteed_payment_technology' => 'Tecnolog??a de pago garantizada',
                'guaranteed_quality' => 'Calidad garantizada',
                'thinking_about_the_welfare_of_all' => 'Pensando en el bienestar de todos',

                'Home' => 'Inicio',
                'About Us' => 'Nosotros',
                'Sign In' => 'Iniciar sesi??n',
                'Who we are' => 'Qui??nes somos',
                'Product lines' => 'L??neas de productos',
                'Certifications' => 'Certificaciones',
                'Store' => 'Tienda',
                'Search Result' => 'Resultado de b??squeda',
                'Services' => 'Maquila',
                'Be Ecovalle' => 'S?? Ecovalle',
                'Quick links' => 'Enlaces r??pidos',
                'Policy and Privacy' => 'Pol??tica de Privacidad',
                'Terms and Conditions' => 'T??rminos y Condiciones',
                'Articles' => 'Art??culos',
                'FAQ' => 'Preguntas frecuentes',
                'Shopping guide' => 'Gu??a de compra',
                'Returning orders' => 'Devoluciones de pedidos',
                'My account' => 'Mi cuenta',
                'Logout' => 'Salir',
                'Shopping cart' => 'Carrito de compras',
                'Update profile' => 'Actualizar mis datos',
                'My wish list' => 'Mi lista de deseos',
                'Customer service' => 'Atenci??n al cliente',
                'Make your purchases easily with' => 'Realiza tus compras f??cilmente con',

                'Add to cart' => 'Agregar al carrito',
                'contact_with_you' => 'Nos encantar??a mantener contacto contigo',
                'birthday' => 'Cumplea??os',
                'subscribe_to_newsletter' => 'Suscr??bete a nuestro Newsletter y recibe todas nuestras novedades y promociones.',
                'last_name_and_name' => 'Apellidos y Nombres',
                'contact_number' => 'N??mero de contacto',
                'subscribe' => 'Suscr??bete',
                'view_more' => 'Ver m??s',

                'related_products' => 'Productos relacionados',
            ]
        ];
    }

    public function language(Request $request, $language) {
        $session = $request->session();
        $session->put('locale', $language);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function ajaxLocale(Request $request) {
        $locale = $request->session()->get('locale');
        $data = [
            'locale' => $locale,
        ];

        $respuesta = new Respuesta();
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }

    public function ajaxListarCarrito(Request $request) {
        $lstCarrito = [];

        $session = $request->session();
        $bClienteEnSesion = $session->has('cliente');

        if ($bClienteEnSesion) {
            $cliente = $session->get('cliente');
            $persona = Persona::find($cliente->persona_id);
            $persona->ubigeo;
            $lstCarrito = DetalleCarrito::where('cliente_id', $cliente->id)
                ->with(['producto'])->get();
            foreach($lstCarrito as $carrito)
            {
                $carrito->producto->oferta_vigente;
                $carrito->producto->promocion_vigente;
                $carrito->producto->imagenes;
                $carrito->producto->precio_actual;
                $carrito->producto['cantidad'] = $carrito->cantidad;
            }
        }
        else{
            $persona = null;
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['bClienteEnSesion' => $persona, 'lstCarrito' => $lstCarrito];
        return response()->json($respuesta);
    }

    public function ajaxAgregarAlCarrito(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $producto = DetalleCarrito::where('producto_id',$producto_id)->first();

            if(empty($producto))
            {
                $detalle_carrito = new DetalleCarrito;
                $detalle_carrito->cliente_id = $cliente->id;
                $detalle_carrito->cantidad = 1;
                $detalle_carrito->producto_id = $producto_id;
                $detalle_carrito->fecha_reg = now()->toDateTimeString();
                $detalle_carrito->save();
            }
            else
            {
                $producto_find = DetalleCarrito::where('producto_id',$producto_id)->first();
                $producto_find->cantidad = 1;
                $producto_find->update();
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function ajaxEliminarDelCarrito(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
           $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $lstDetalleCarrito = DetalleCarrito::where('cliente_id', $cliente->id)->where('producto_id', $producto_id)->get();
            $detalle_carrito = $lstDetalleCarrito->get(0);

            if($detalle_carrito)
            {
                $detalle_carrito->delete();
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function ajaxDisminuirCantidadProductoCarrito(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $lstDetalleCarrito = DetalleCarrito::where('cliente_id', $cliente->id)->where('producto_id', $producto_id)->get();
            $detalle_carrito = $lstDetalleCarrito->get(0);

            if($detalle_carrito)
            {
                $detalle_carrito->cantidad = $detalle_carrito->cantidad - 1;
                if ($detalle_carrito->cantidad === 0) {
                    $detalle_carrito->delete();
                } else {
                    $detalle_carrito->save();
                }
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function ajaxAumentarCantidadProductoCarrito(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $lstDetalleCarrito = DetalleCarrito::where('cliente_id', $cliente->id)->where('producto_id', $producto_id)->get();
            $detalle_carrito = $lstDetalleCarrito->get(0);

            if($detalle_carrito)
            {
                $detalle_carrito->cantidad = $detalle_carrito->cantidad + 1;
                $detalle_carrito->save();
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function ajaxAumentarCantidadProductoCarritoCantidad(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');
            $cantidad = $request->get('iCantidad');

            $lstDetalleCarrito = DetalleCarrito::where('cliente_id', $cliente->id)->where('producto_id', $producto_id)->get();
            $detalle_carrito = $lstDetalleCarrito->get(0);

            if($detalle_carrito)
            {
                $detalle_carrito->cantidad = $cantidad;
                $detalle_carrito->save();
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;

        return response()->json($respuesta);
    }

    public function findproducto(Request $request) {
        $session = $request->session();
        $cliente = $session->get('cliente');
        
        /*$session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $lstDetalleCarrito = DetalleCarrito::where('cliente_id', $cliente->id)->where('producto_id', $producto_id)->get();
            $detalle_carrito = $lstDetalleCarrito->get(0);
        }*/

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $cliente;

        return response()->json($respuesta);
    }

    public function ajaxEnviarCorreoContactoContigo(Request $request) {
        $request->validate([
            'apellidos_y_nombres' => 'required',
            'email' => 'required',
            'fecha_de_nacimiento' => 'required',
            'numero_de_contacto' => 'required',
        ]);

        $apellidos_nombres = $request->get('apellidos_y_nombres');
        $email = $request->get('email');
        $fecha_nacimiento = $request->get('fecha_de_nacimiento');
        $numero_contacto = $request->get('numero_de_contacto');

        $notificacionContactoContigo = new NotificacionContactoContigo($apellidos_nombres, $email, $fecha_nacimiento, $numero_contacto);
        Mail::to('comunity.rrss@ecovalle.pe')->send($notificacionContactoContigo);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Suscripci??n registrada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxPublicidad()
    {
        $actual = now();
        $publicidad = Publicidad::where('f_inicio', '<=', date_format($actual, 'Y-m-d'))
        ->where('f_fin', '>=', date_format($actual, 'Y-m-d'))
        ->where('estado', 1)
        ->first();
        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = array('publicidad' => $publicidad);

        return response()->json($respuesta);
    }
}
