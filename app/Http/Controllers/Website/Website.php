<?php

namespace App\Http\Controllers\Website;

use App\DetalleCarrito;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionContactoContigo;
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
                'Services' => 'Services',
                'Be Ecovalle' => 'Be Ecovalle',
                'Quick links' => 'Quick links',
                'Policy and Privacy' => 'Policy and Privacy',
                'Terms and Conditions' => 'Terms and Conditions',
                'Articles' => 'Articles',
                'FAQ' => 'FAQ',
                'Shopping guide' => 'Shopping guide',
                'Returning orders' => 'Returning orders',
                'My account' => 'My account',
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
                'Password' => 'Contraseña',

                'categories' => 'Categorías',
                'partners' => 'Socios',
                'human_resources' => 'Recursos humanos',

                'new_revenues' => 'Nuevos ingresos',

                'call_or_write_to_us' => 'Llámanos o escríbenos',
                'contact_us' => 'Contáctanos',

                'Information' => 'Información',
                'user_area' => 'Área de usuario',
                'payment_methods' => 'Medios de Pago',
                'follow_us' => 'Síguenos',
                'forgot_my_password' => 'Olvidé mi contraseña',
                'complaints_book' => 'Libro de reclamaciones',

                'nationwide_shipments' => 'Envíos a nivel nacional',
                'online_service' => 'Atención online',
                'hours_from_monday_to_saturday' => 'Horarios de lunes a sábado',
                'secure_payment' => 'Pago seguro',
                'guaranteed_payment_technology' => 'Tecnología de pago garantizada',
                'guaranteed_quality' => 'Calidad garantizada',
                'thinking_about_the_welfare_of_all' => 'Pensando en el bienestar de todos',

                'Home' => 'Inicio',
                'About Us' => 'Nosotros',
                'Sign In' => 'Iniciar sesión',
                'Who we are' => 'Quiénes somos',
                'Product lines' => 'Líneas de productos',
                'Certifications' => 'Certificaciones',
                'Store' => 'Tienda',
                'Search Result' => 'Resultado de búsqueda',
                'Services' => 'Servicios',
                'Be Ecovalle' => 'Sé Ecovalle',
                'Quick links' => 'Enlaces rápidos',
                'Policy and Privacy' => 'Política de Privacidad',
                'Terms and Conditions' => 'Términos y Condiciones',
                'Articles' => 'Artículos',
                'FAQ' => 'Preguntas frecuentes',
                'Shopping guide' => 'Guía de compra',
                'Returning orders' => 'Devoluciones de pedidos',
                'My account' => 'Mi cuenta',
                'Shopping cart' => 'Carrito de compras',
                'Update profile' => 'Actualizar mis datos',
                'My wish list' => 'Mi lista de deseos',
                'Customer service' => 'Atención al cliente',
                'Make your purchases easily with' => 'Realiza tus compras fácilmente con',

                'Add to cart' => 'Agregar al carrito',
                'contact_with_you' => 'Nos encantaría mantener contacto contigo',
                'birthday' => 'Cumpleaños',
                'subscribe_to_newsletter' => 'Suscríbete a nuestro Newsletter y recibe todas nuestras novedades y promociones.',
                'last_name_and_name' => 'Apellidos y Nombres',
                'contact_number' => 'Número de contacto',
                'subscribe' => 'Suscríbete',
                'view_more' => 'Ver más',

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
            $lstCarrito = DetalleCarrito::where('cliente_id', $cliente->id)
                ->with(['producto', 'producto.imagenes', 'producto.precio_actual', 'producto.oferta_vigente'])->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['bClienteEnSesion' => $bClienteEnSesion, 'lstCarrito' => $lstCarrito];
        return response()->json($respuesta);
    }

    public function ajaxAgregarAlCarrito(Request $request) {
        $session = $request->session();
        if ($session->has('cliente')) {
            $cliente = $session->get('cliente');
            $producto_id = $request->get('iProductoId');

            $detalle_carrito = new DetalleCarrito;
            $detalle_carrito->cliente_id = $cliente->id;
            $detalle_carrito->cantidad = 1;
            $detalle_carrito->producto_id = $producto_id;
            $detalle_carrito->fecha_reg = now()->toDateTimeString();
            $detalle_carrito->save();
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

            $detalle_carrito->delete();
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

            $detalle_carrito->cantidad = $detalle_carrito->cantidad - 1;
            if ($detalle_carrito->cantidad === 0) {
                $detalle_carrito->delete();
            } else {
                $detalle_carrito->save();
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

            $detalle_carrito->cantidad = $detalle_carrito->cantidad + 1;
            $detalle_carrito->save();
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

            $detalle_carrito->cantidad = $detalle_carrito->cantidad + $cantidad;
            $detalle_carrito->save();
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
        $respuesta->mensaje = 'Suscripción registrada correctamente.';

        return response()->json($respuesta);
    }
}
