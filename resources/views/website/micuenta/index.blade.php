@extends('website.layout')

@section('title', 'Mi Cuenta')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/index">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['My account'] }}</li>
            </ol>
        </nav>
    </div>

    <div class="modal-pago active" v-if="iPagando == 1">
        <img src="/img/cargando-carrito.gif" alt="Shopping">
    </div>

    <section v-cloak>
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-4 mb-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mx-0 border rounded shadow">
                                <div class="col-12 bg-amarillo text-white text-uppercase py-2">
                                    {{ $lstLocales['My account'] }}
                                </div>
                                <div class="col-12 py-2">
                                    <div class="row">
                                        <div class="col-12 col-sm-4 col-lg-12 px-3">
                                            <div class="w-100 py-2 mi-cuenta" v-bind:class="iMenuSeleccionado === 0 ? 'bg-ecovalle' : ''" v-on:click="panelDesk()" style="cursor: pointer;" v-cloak>
                                                <div class="d-inline-block">
                                                    <label class="m-0">
                                                        &nbsp;@{{ locale === 'es' ? 'Escritorio' : 'Desk' }}
                                                    </label>

                                                </div>
                                                <div class="d-inline-block float-right pr-2 mt-1">
                                                    <i class="fa fa-tachometer"></i>
                                                </div>
                                            </div>
                                            <hr class="m-0">
                                        </div>
                                        <div class="col-12 col-sm-4 col-lg-12 px-3">
                                            <div class="w-100 py-2 mi-cuenta" :class="iMenuSeleccionado === 1 ? 'bg-ecovalle' : ''" v-on:click="panelAccount()" style="cursor: pointer;" v-cloak>
                                                <div class="d-inline-block">
                                                    <label class="m-0">
                                                        &nbsp;@{{ locale === 'es' ? 'Detalles de cuenta' : 'Account details' }}
                                                    </label>
                                                </div>
                                                <div class="d-inline-block float-right pr-2 mt-1">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                            <hr class="m-0">
                                        </div>
                                        <div class="col-12 col-sm-4 col-lg-12 px-3">
                                            <div class="w-100 py-2 mi-cuenta" :class="iMenuSeleccionado === 2 ? 'bg-ecovalle' : ''" v-on:click="panelAddress()" style="cursor: pointer;" v-cloak>
                                                <div class="d-inline-block">
                                                    <label class="m-0">
                                                        &nbsp;@{{ locale === 'es' ? 'Direcci&oacute;n' : 'Address' }}
                                                    </label>
                                                </div>
                                                <div class="d-inline-block float-right pr-2 mt-1">
                                                    <i class="fa fa-home"></i>
                                                </div>
                                            </div>
                                            <hr class="m-0">
                                        </div>
                                        <div class="col-12 col-sm-4 col-lg-12 px-3">
                                            <div class="w-100 py-2 mi-cuenta" :class="iMenuSeleccionado === 3 ? 'bg-ecovalle' : ''" v-on:click="panelOrders()" style="cursor: pointer;" v-cloak>
                                                <div class="d-inline-block">
                                                    <label class="m-0">
                                                        &nbsp;@{{ locale === 'es' ? 'Pedidos' : 'Orders' }}
                                                    </label>
                                                </div>
                                                <div class="d-inline-block float-right pr-2 mt-1">
                                                    <i class="fa fa-shopping-basket"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mx-0 border rounded shadow" id="panel">
                                <div class="col-12 text-center" v-if="iCargandoPanel === 1">
                                    <img src="/img/spinner.svg">
                                </div>
                            </div>
                            <div class="modal-pedido" id="pedido">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://checkout.culqi.com/js/v3"></script>
    <script src="/js/website/miCuenta.js?cvcn=14"></script>
@endsection
