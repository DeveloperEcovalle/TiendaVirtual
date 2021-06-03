@extends('website.layout')

@section('title', 'Mi Cuenta')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/index">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['forgot_my_password'] }}</li>
            </ol>
        </nav>
    </div>

    <section v-cloak>
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-12 mb-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mx-0 border rounded shadow p-4 mb-5">
                                <div class="col-12">
                                    <p>¿Perdiste tu contraseña? Por favor, introduce tu correo electrónico. Recibirás una contraseña nueva por correo electrónico.</p>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <form id="frmRestablecer" v-on:submit.prevent="ajaxEnviar">
                                        <div class="form-group">
                                            <label> Correo elect&oacute;nico de la cuenta <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="email" placeholder="Email" autocomplete="off" required>
                                        </div>
                                        <div class="alert text-center p-2" :class="sClase" v-if="sMensaje != ''">
                                                @{{ sMensaje }}
                                        </div>
                                        <div class="form-group mb-1">
                                            <button type="submit" class="btn btn-block btn-ecovalle" :disabled="iComprobando === 1">
                                                <span v-if="iComprobando === 1"><i class="fas fa-circle-notch fa-spin"></i> Comprobando</span>
                                                <span v-else class="signIn">RESTABLECER CONTRASEÑA</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
<script src="/js/website/recuperar.js?cvcn=14"></script>
@endsection
