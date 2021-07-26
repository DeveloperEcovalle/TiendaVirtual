<html>
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1FHPDW986R"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-1FHPDW986R');
    </script>
    <!-- Google Analytics -->
    <link rel="preconnect dns-prefetch" href="https://www.google-analytics.com">
    <script>
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga('create', 'UA-199871946-1', 'auto');
    ga('send', 'pageview');
    </script>
    <script async src='https://www.google-analytics.com/analytics.js'></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ecovalle | @yield('title')</title>
    <link rel="icon" href="{{ asset('img/ecologo.ico') }}" />
    <meta name="description" content="Somos una empresa agroindustrial con más de 10 años de experiencia en el rubro de alimentos funcionales. 
          Todos nuestros productos pasan por un estricto proceso de producción y control de calidad, 
          utilizando los más altos estándares de calidad con certificación internacional.">
    @include('website._keywords')

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/plugins/bootstrapSocial/bootstrap-social.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/07952e76a1.js" crossorigin="anonymous"></script>
    <link href="/css/plugins/iCheck/custom.css" rel="stylesheet">
    <!--link href="/css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css" rel="stylesheet"-->
    <link href="/css/plugins/jQueryUI/jquery-ui.css" rel="stylesheet">
    <link href="/js/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="/css/popup/magnific-popup.css" rel="stylesheet">
    <link href="/css/ecovalle/ecovalle-font.css" rel="stylesheet">
    <link href="/css/website.css?n=1" rel="stylesheet">
    <link rel="stylesheet" href="/css/plugins/mklb/mklb.css" />
    @yield('izipay')
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '160356612792497'); 
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" 
        src="https://www.facebook.com/tr?id=160356612792497&ev=PageView
        &noscript=1"/>
    </noscript>
</head>
<body>
    <div id="content">
        <header class="bg-ecovalle-2 position-fixed">
            <div class="container-xl px-0" style="background: url('/img/bg_header.svg'); background-position: top right; background-repeat: no-repeat; background-size: 75%">
                <div class="row p-2">
                    <div class="col-12 col-md-3 d-none d-lg-block col-lg-3">
                        <a href="/index">
                            <img src="/img/logo_ecovalle_fondo_blanco.svg" class="img-fluid" alt="Logo Ecovalle">
                        </a>
                    </div>
                    <div class="col-12 col-md-12 col-xs-12 col-lg-9">
                        <nav class="navbar navbar-dark justify-content-center justify-content-lg-end p-0 pt-1">
                            <div class="col-md-6">
                                <form class="mb-0" action="{{route('tienda.buscarProducto')}}" method="POST" id="search-form">
                                    @csrf
                                    <div class="autocompletar">
                                        <input type="text" id="inputSearch" name="keyword" placeholder="{{ session('locale') === 'es' ? 'Buscar ...' : 'Search ...' }}" autocomplete="off">
                                        <button class="icon" type="button" onclick="$('#search-form').submit()"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav align-items-center justify-content-between" id="navUsuario">
                                    <li class="nav-item dropdown">
                                        @if(session()->has('cliente'))
                                        <a class="nav-link nav-ecovalle-blanco dropdown py-0 d-flex align-items-center text-right" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <!-- /mi-cuenta -->
                                            <span class="mr-13" style="line-height: 1.2">{{ session('cliente')->persona->nombres }}</span>
                                            <i class="ecovalle-usuario fa-2x"></i>
                                        </a>
                                        <div class="dropdown-menu rounded py-0" style="min-width: auto" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item py-2 px-3" href="/mi-cuenta">
                                                <i class="fa fa-user"></i> {{ $lstLocales['My account'] }}
                                            </a>
                                            <a class="dropdown-item py-2 px-3" href="#" v-on:click.prevent="ajaxSalir()">
                                                <i class="fa fa-sign-out"></i> {{ $lstLocales['Logout'] }}
                                            </a>
                                        </div>
                                        @else
                                        <a class="nav-link nav-ecovalle-blanco py-0 d-flex align-items-center text-right" href="#" data-toggle="modal" data-target="#modalInicioSesion">
                                            <span class="mr-1">{{ $lstLocales['Sign In'] }}</span>
                                            <i class="ecovalle-usuario fa-2x"></i>
                                        </a>
                                        @endif
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link nav-ecovalle-blanco position-relative py-0" href="/carrito-compras">
                                            <i class="ecovalle-carrito-compras mr-2 fa-2x"></i>
                                            <span class="badge badge-nuevo bg-amarillo position-absolute" style="top: 25%" v-if="lstCarritoCompras.length > 0" v-cloak>
                                                @{{ lstCarritoCompras.length }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link nav-ecovalle-blanco dropdown-toggle text-uppercase" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                            {{ session('locale') }}
                                        </a>
                                        <div class="dropdown-menu rounded py-0" style="min-width: auto">
                                            <a class="dropdown-item py-2 px-3" href="#" v-on:click.prevent="ajaxSetLocale('{{ session('locale') === 'es' ? 'en' : 'es' }}')">
                                                {{ session('locale') === 'es' ? 'EN' : 'ES' }}
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                        <nav class="navbar navbar-expand-md navbar-dark p-0">
                            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarEnlacesPrincipales" aria-controls="navbarEnlacesPrincipales" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>

                            <div class="collapse bg-ecovalle-2 p-1 navbar-collapse mt-lg-3" id="navbarEnlacesPrincipales">
                                <ul class="nav nav-fill flex-column flex-fill flex-md-row mt-2">
                                    <li class="nav-item {{ $iPagina === 1 ? 'active' : '' }} dropdown mt-1 mr-md-1">
                                        <a class="nav-link text-uppercase px-2 px-xl-3 mr-lg-1 rounded border dropdown-toggle" href="/nosotros" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ $lstLocales['About Us'] }}
                                        </a>
                                        <div class="dropdown-menu py-0 mt-1 rounded" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item py-2" href="/nosotros/quienes-somos">{{ $lstLocales['Who we are'] }}</a>
                                            <a class="dropdown-item py-2" href="/nosotros/certificaciones">{{ $lstLocales['Certifications'] }}</a>
                                            <a class="dropdown-item py-2" href="/servicios">{{ $lstLocales['Services'] }}</a>
                                            <a class="dropdown-item py-2" href="/nosotros/lineas-productos">{{ $lstLocales['Product lines'] }}</a>
                                        </div>
                                    </li>
                                    <li class="nav-item {{ $iPagina === 2 ? 'active' : '' }} mt-1 mr-md-1">
                                        <a class="nav-link text-uppercase px-2 mr-lg-1 rounded border" href="/tienda">{{ $lstLocales['Store'] }}</a>
                                    </li>
                                    <li class="nav-item {{ $iPagina === 3 ? 'active' : '' }} mt-1 mr-md-1 d-none">
                                        <a class="nav-link text-uppercase px-2 mr-lg-1 rounded border" href="/servicios">{{ $lstLocales['Services'] }}</a>
                                    </li>
                                    <li class="nav-item {{ $iPagina === 4 ? 'active' : '' }} dropdown mt-1 mr-md-1">
                                        <a class="nav-link text-uppercase px-2 mr-lg-1 rounded border" href="/se-ecovalle/socios">{{ $lstLocales['partners'] }}</a>
                                        <!--<a class="nav-link text-uppercase px-2 px-xl-3 mr-lg-1 rounded border dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ $lstLocales['Be Ecovalle'] }}
                                        </a>
                                        <div class="dropdown-menu py-0 mt-1 rounded" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item py-2" href="/se-ecovalle/socios">{{ $lstLocales['partners'] }}</a>
                                            <a class="dropdown-item py-2" href="/se-ecovalle/recursos-humanos">{{ $lstLocales['human_resources'] }}</a>
                                        </div>-->
                                    </li>
                                    <li class="nav-item {{ $iPagina === 5 ? 'active' : '' }} mt-1 mr-md-1">
                                        <a class="nav-link text-uppercase px-2 mr-lg-1 rounded border" href="/blog">Blog</a>
                                    </li>
                                    <li class="nav-item {{ $iPagina === 6 ? 'active' : '' }} mt-1 mr-md-1">
                                        <a class="nav-link text-uppercase px-2 rounded border" href="/contactanos">{{ $lstLocales['contact_us'] }}</a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="producto-modal" class="producto-modal position-relative"></div>
        </header>

        <div class="px-0 container-page">
            @section('content')
            <section class="bg-amarillo" v-if="iCargando === 0" v-cloak>
                <div class="container-xl">
                    <div class="row justify-content-center py-5">
                        <div class="col-md-6 col-11">
                            <h2 class="font-weight-bold text-uppercase text-center text-md-left text-white mt-md-4">{{ $lstLocales['contact_with_you'] }}</h2>
                            <p class="text-white text-center text-md-left">{{ $lstLocales['subscribe_to_newsletter'] }}</p>
                        </div>
                        <div class="col-md-6 col-11">
                            <div class="row justify-content-center justify-content-md-end mt-3">
                                <div class="col-lg-9 col-11">
                                    <form role="form" id="frmContactoContigo" v-on:submit.prevent="ajaxEnviarCorreoContactoContigo()">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="apellidos_y_nombres" placeholder="{{ $lstLocales['last_name_and_name'] }}" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" placeholder="Email" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <input type="date" class="form-control" name="fecha_de_nacimiento" placeholder="{{ $lstLocales['birthday'] }}" autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="numero_de_contacto" placeholder="{{ $lstLocales['contact_number'] }}" autocomplete="off">
                                        </div>
                                        <div class="form-group" v-if="respuestaCorreo">
                                            <p class="m-b-none font-weight-bold" v-html="respuestaCorreo.mensaje" :class="respuestaCorreo.result === 'error' ? 'text-danger' : `text-${respuestaCorreo.result}`"></p>
                                        </div>
                                        <div class="form-group text-center mb-0">
                                            <button type="submit" :disabled="iSuscribiendo === 1" class="btn btn-ecovalle-2 px-5 text-uppercase">
                                                <span v-if="iSuscribiendo === 0">{{ $lstLocales['subscribe'] }}</span>
                                                <span v-else><i class="fas fa-circle-notch fa-spin"></i></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @show
        </div>

        <footer>
            <div style="background: url('/img/pie_pagina.svg') top center no-repeat; background-size: contain">
                <div class="container-xl">
                    <div class="row justify-content-center">
                        <div class="col-10 col-md-4 align-items-center">
                            <div class="row py-2">
                                <div class="col-4 col-lg-3">
                                    <img src="/img/contactos-1.svg" class="img-fluid">
                                </div>
                                <div class="col-8 col-lg-9 d-flex align-items-center font-weight-bold">
                                    Call center<br>
                                    (044) 23 8060
                                </div>
                            </div>
                        </div>
                        <div class="col-10 col-md-4 align-items-center">
                            <div class="row py-2">
                                <div class="col-4 col-lg-3">
                                    <img src="/img/contactos-2.svg" class="img-fluid">
                                </div>
                                <div class="col-8 col-lg-9 d-flex align-items-center font-weight-bold">
                                    {{ $lstLocales['call_or_write_to_us'] }}<br>
                                    957 819 664
                                </div>
                            </div>
                        </div>
                        <div class="col-10 col-md-4 align-items-center">
                            <div class="row py-2">
                                <div class="col-4 col-lg-3">
                                    <img src="/img/contactos-3.svg" class="img-fluid">
                                </div>
                                <div class="col-8 col-lg-9 d-flex align-items-center font-weight-bold">
                                    {{ $lstLocales['contact_us'] }}<br>ventas@ecovalle.pe
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-5">
                <div class="container-xl">
                    <div class="row">
                        <div class="col-md-3 pb-5 text-center">
                            <img src="/img/logo_isotipo.svg" class="img-fluid" style="max-width: 182px">
                        </div>
                        <div class="col-md-9">
                            <div class="row justify-content-center">
                                <div class="col-11 col-md-4 mb-4 mb-md-0">
                                    <h5 class="h6 font-weight-bold text-uppercase titulo-subrayado-amarillo">{{ $lstLocales['Information'] }}</h5>
                                    <ul>
                                        <li><a href="/nosotros">{{ $lstLocales['About Us'] }}</a></li>
                                        <li><a href="/tienda">{{ $lstLocales['Store'] }}</a></li>
                                        <li><a href="/servicios">{{ $lstLocales['Services'] }}</a></li>
                                        <li><a href="/se-ecovalle/socios">{{ $lstLocales['partners'] }}</a></li>
                                        <li><a href="/blog">Blog</a></li>
                                        <li class="d-none"><a href="/se-ecovalle/recursos-humanos">{{ $lstLocales['human_resources'] }}</a></li>
                                        <li><a href="/guia-compras">{{ $lstLocales['Shopping guide'] }}</a></li>
                                        <li><a href="/politica-privacidad">{{ $lstLocales['Policy and Privacy'] }}</a></li>
                                        <li><a href="/terminos-condiciones">{{ $lstLocales['Terms and Conditions'] }}</a></li>
                                    </ul>
                                </div>
                                <div class="col-11 col-md-4 mb-4 mb-md-0">
                                    <h5 class="h6 font-weight-bold text-uppercase titulo-subrayado-amarillo">{{ $lstLocales['user_area'] }}</h5>
                                    <ul>
                                        @if(session()->has('cliente'))
                                        <li><a href="/mi-cuenta">{{ $lstLocales['My account'] }}</a></li>
                                        <li><a href="/mi-cuenta?menu=1">{{ $lstLocales['Update profile'] }}</a></li>
                                        @endif
                                        <li><a href="/olvide-mi-contrasena">{{ $lstLocales['forgot_my_password'] }}</a></li>
                                        <li><a href="/carrito-compras">{{ $lstLocales['Shopping cart'] }}</a></li>
                                        <!--<li><a href="/mi-cuenta/lista-deseos">{{ $lstLocales['My wish list'] }}</a></li>-->
                                        <li><a class="d-inline" href="/libro-reclamaciones">{{ $lstLocales['complaints_book'] }}</a> <i class="fa fa-book-open"></i></li>
                                    </ul>

                                    <div style="width: 80%; height: auto;" class="d-none">
                                        <a href="/libro-reclamaciones"><img class="img-fluid" src="/img/libro.jpg" alt=""></a>
                                    </div>
                                </div>
                                <div class="col-11 col-md-4 mb-4 mb-md-0">
                                    <h5 class="h6 font-weight-bold text-uppercase titulo-subrayado-amarillo">{{ $lstLocales['payment_methods'] }}</h5>
                                    <img src="/img/visa.svg" style="width: 50px;" class="mr-1">
                                    <img src="/img/mastercard.svg" style="width: 50px;">
                                    <h5 class="h6 font-weight-bold text-uppercase titulo-subrayado-amarillo mt-3">{{ $lstLocales['follow_us'] }}</h5>
                                    @if($empresa->enlace_facebook)
                                    <a href="{{ $empresa->enlace_facebook }}" target="_blank" class="btn btn-lg btn-social-icon mr-1">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    @endif
                                    @if($empresa->enlace_instagram)
                                    <a href="{{ $empresa->enlace_instagram }}" target="_blank" class="btn btn-lg btn-social-icon mr-1">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    @endif
                                    @if($empresa->enlace_youtube)
                                    <a href="{{ $empresa->enlace_youtube }}" target="_blank" class="btn btn-lg btn-social-icon mr-1">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                    @endif
                                    @if($empresa->enlace_tiktok)
                                    <a href="{{ $empresa->enlace_tiktok }}" target="_blank" class="btn btn-lg btn-social-icon mr-1">
                                        <i class="fab fa-tiktok"></i>
                                    </a>
                                    @endif
                                    @if($empresa->enlace_twitter)
                                    <a href="{{ $empresa->enlace_twitter }}" target="_blank" class="btn btn-lg btn-social-icon mr-1">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    @endif

                                    @if($empresa->enlace_linkedin)
                                    <a href="{{ $empresa->enlace_linkedin }}" target="_blank" class="btn btn-lg btn-social-icon">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    @endif

                                    <img src="/img/certificacion_pie_pagina.svg" class="img-fluid mt-4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-2 text-center bg-ecovalle-5">
                <small class="font-weight-bold">COPYRIGHT ECOVALLE. ALL RIGHTS RESERVED.</small>
            </div>
        </footer>

        <div class="modal" id="modalInicioSesion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0">
                    <div class="modal-body p-0">
                        <div class="row" id="inicioSesion">
                            {{-- <div class="col-12 col-md-5 d-none d-md-block" style="background-color: var(--verde-ecovalle-2); background-image: url('/img/logo_isotipo.svg'); background-size: cover; background-position-x: right; background-position-y: bottom">
                            </div> --}}
                            <div class="col-12 py-5">
                                <div class="row justify-content-center">
                                    <div class="col-10 col-lg-7">
                                        <img src="/img/logo_ecovalle.svg" class="img-fluid">
                                    </div>
                                    <div class="col-10 col-lg-8" v-cloak>
                                        <h5 class="text-center mt-4 mb-3">Iniciar sesi&oacute;n</h5>
                                        <form role="form" id="frmIniciarSesion">
                                            <div class="form-group">
                                                <input class="form-control input-inicio" type="email" name="email" placeholder="{{ $lstLocales['Email'] }}" required="required" autocomplete="off">
                                            </div>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input class="form-control input-inicio" type="password" style="border-right: 0px !important;" name="contrasena" placeholder="{{ $lstLocales['Password'] }}" id="password" required="required" autocomplete="off">
                                                    <span class="input-group-append"><button class="btn btn-password" value="0" onclick="clickactionPassword(this)" type="button"><i id="pass" class="fa fa-eye"></i> </button></span>
                                                </div>
                                            </div>
                                            <div class="alert text-center p-2 d-none" id="sMensaje">
                                                
                                            </div>
                                            <div class="form-group mb-1">
                                                <button type="submit" class="btn btn-block btn-ecovalle" id="iSubmit">
                                                    <span id="iComprobando" class="d-none"><i class="fas fa-circle-notch fa-spin"></i> Comprobando</span>
                                                    <span class="signIn">Iniciar sesi&oacute;n</span>
                                                </button>
                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-12 pb-2 text-center">
                                                <a class="nav-ecovalle-amarillo small" href="/olvide-mi-contrasena">&iquest;Olvid&oacute; su
                                                    contrase&ntilde;a?</a>
                                            </div>
                                            <div class="col-12 text-center">
                                                <p class="small mt-1 mb-2">&iquest;No tiene una cuenta?</p>
                                                <button class="btn btn-block btn-outline-ecovalle" onclick="locationRegister()">Reg&iacute;strese
                                                    aqu&iacute;</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!--<div class="container-all" id="popup" v-on:click="cerrar"> 
        
        <div class="popup">
            <div class="img">
                <a href="#">
                    <img class="img-fluid h-100 w-100" id="img-popup" src="/storage/empresa/Vy8QtpbijN949Rci2drGhUvnDxWDfIvxJM1ep9Rn.png" alt="ECO_VALLE">
                </a>
            </div>
            
            <span class="btn-close-popup" id="close" style="cursor: pointer">X</span>
        </div>
        
    </div>-->
    <!--https://www.ecovalle.pe/img/logo_ecovalle.png-->

    <div id="popup"></div>

    

    @if($telefono_whatsapp)
    <div class="position-fixed" style="bottom:50px; top:auto; right:30px; left:auto; -webkit-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75); -moz-box-shadow: 10px 10px 5px 0px rgba(0,0,0,0.75); box-shadow: 8px 8px 6px 0px rgba(0,0,0,0.75); border-radius: 50%;">
        <a class="d-sm-block" href="https://api.whatsapp.com/send?phone={{ $telefono_whatsapp->numero }}&text=Hola%20Ecovalle%2c%20necesito%20ayuda%20con%20la%20página%20web" target="_blank">
            <img src="/img/whatsapp_oficial.svg" style="width: 64px">
        </a>
    </div>
    @endif

    <!-- Mainly scripts -->
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.js"></script>
    <script src="/js/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    
    <script src="/js/plugins/toastr/toastr.min.js"></script>
    <script src="/js/plugins/iCheck/icheck.min.js"></script>
    <script src="/js/plugins/starrr/starrr.js"></script>
    <script src="/js/plugins/axios/axios.js"></script>
    <script src="/js/plugins/vue/vue.js"></script>
    <script src="/js/popup/jquery.magnific-popup.js"></script>
    <script src="/js/popup/jquery.magnific-popup.min.js"></script>
    <script src="/js/website/website.js?cvcn=14"></script>

    <script>
        $(document).ready(function() {

            /*$("#popup").hide().fadeIn(1000);

            //close the POPUP if the button with id="close" is clicked
            $("#close").on("click", function (e) {
                e.preventDefault();
                $("#popup").fadeOut(1000);
            });*/

            autocompletar();
            $('#frmIniciarSesion').submit(function(e){
                e.preventDefault();
                $('.signIn').addClass('d-none');
                $('#iComprobando').removeClass('d-none');
                let frmIniciarSesion = document.getElementById('frmIniciarSesion');
                let formData = new FormData(frmIniciarSesion);
                let lstCarritoCompras = $cookies.get('lstCarritoCompras');
                formData.append('sLstCarritoCompras', this.sLstCarritoCompras);

                axios.post('/iniciar-sesion/ajax/ingresar', formData)
                    .then(response => {
                        let respuesta = response.data;
                        console.log(respuesta);
                        if (respuesta.result === 'success') {
                            location.reload();
                        } else {
                            $('.signIn').removeClass('d-none');
                            $('#iComprobando').addClass('d-none');
                            let sClase = respuesta.result === 'error' ? 'alert-danger' : ('alert-' + respuesta.result);
                            $('#sMensaje').html(respuesta.mensaje)
                            $('#sMensaje').removeClass('d-none');
                            $('#sMensaje').addClass(sClase);
                        }
                    })
                    .catch(error => {
                        $('.signIn').removeClass('d-none');
                        $('#iComprobando').addClass('d-none');
                        $('#sMensaje').removeClass('d-none');
                        $('#sMensaje').addClass('alert-danger');
                        $('#sMensaje').html('Ocurrió un error inesperado. Intentar una vez más debería solucionar el problema; de no ser así, comuníquese con el administrador del sistema.');
                    });
            });

            // function password(b)
            // {
            //     let inputValue = b.value;
            //     alert(inputValue);
            // }

            /*posicionarFooter();

            $('.modal-producto').scroll(function() {    
                posicionarFooter();
            });
            
            function posicionarFooter() {
                var altura_del_footer = $('.total-carrito').outerHeight(true);
            
                if ($('.modal-producto').scrollTop() >= altura_del_footer){
                    $('.total-carrito').addClass('fixed-bottom');
                } else {
                    $('.modal-producto').removeClass('fixed-bottom');
                }
            }*/
        });

        function clickactionPassword(b)
        {
            var tipo = document.getElementById("password");
            if(tipo.type == "password"){
                $('#pass').removeClass('fa fa-eye');
                $('#pass').addClass('fa fa-eye-slash');
                tipo.type = "text";
            }else{
                $('#pass').removeClass('fa fa-eye-slash');
                $('#pass').addClass('fa fa-eye');
                tipo.type = "password";
            }
        }

        /*function window_mouseout( obj, evt, fn ) {
            if ( obj.addEventListener ) {

                obj.addEventListener( evt, fn, false );
            }
            else if ( obj.attachEvent ) {

                obj.attachEvent( 'on' + evt, fn );
            }
        }

        window_mouseout( document, 'mouseout', event => {

            event = event ? event : window.event;

            var from = event.relatedTarget || event.toElement;

            // Si quieres que solo salga una vez el mensaje borra lo comentado
            // y así se guarda en localStorage

            // let leftWindow   = localStorage.getItem( 'leftWindow' ) || false;

            if (!from || from.nodeName === 'HTML') {

                // Haz lo que quieras aquí
                //alert( '¿Quieres abandonar mi página?' );
                // localStorage.setItem( 'leftWindow', true );
                var inFormOrLink;
                $('a').on('click', function() { inFormOrLink = true; });
                $('form').on('submit', function() { inFormOrLink = true; });

                if(inFormOrLink) {
                    localStorage.removeItem('websitevisita');
                }
            }
        });*/

        /* !leftWindow  && */ 
    </script>
    @yield('js')
</body>
</html>
