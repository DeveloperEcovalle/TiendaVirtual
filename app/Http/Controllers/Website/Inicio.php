<?php

namespace App\Http\Controllers\Website;

use App\Banner;
use App\Blog;
use App\CategoriaProducto;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use App\Producto;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;

class Inicio extends Website {

    protected $lstTraduccionesInicio;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesInicio = [
            'en' => [
                'about_us' => 'About Us',
                'latest_content' => 'Latest content',
                'become_an_ecovalle_partner' => 'Become an Ecovalle Partner',
                'we_support_you' => 'We support you in starting your own business. We care about supplying physical and virtual businesses, large or small throughout Peru.',
                'join_now' => 'Join now',
            ],
            'es' => [
                'about_us' => 'Conócenos',
                'latest_content' => 'Contenido más reciente',
                'become_an_ecovalle_partner' => 'Sé un socio Ecovalle',
                'we_support_you' => 'Te acompañamos en el emprendimiento de tu propio negocio. Nos preocupamos por abastecer los negocios físicos y virtuales, grandes o pequeños en todo el Perú.',
                'join_now' => 'Únete ahora',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesInicio' => $this->lstTraduccionesInicio[$locale],

            'iPagina' => 0,
        ];

        return view('website.inicio', $data);
    }

    public function ajaxData(Request $request) {
        $lstBanners = Banner::where('activo', 1)->where('medio', 0)->orderBy('orden')->get();
        $pagina = Pagina::find(8);

        $fechaHace3Meses = now()->subMonth(3);
        $lstProductos = Producto::where('fecha_reg', '>=', $fechaHace3Meses->toDateTimeString())->whereHas('precio_actual')
            ->with(['precio_actual', 'oferta_vigente', 'imagenes', 'promocion_vigente'])->orderBy('fecha_reg', 'asc')->limit(20)->get();

        $bannerMedio = Banner::where('medio', 1)->first();
        $lstBlogs = Blog::orderBy('fecha_reg', 'desc')->limit(3)->get();

        $data = [
            'pagina' => $pagina,
            'lstBanners' => $lstBanners,
            'lstProductos' => $lstProductos,
            'bannerMedio' => $bannerMedio,
            'lstBlogs' => $lstBlogs
        ];

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }
}
