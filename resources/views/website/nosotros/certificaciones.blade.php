@extends('website.layout')

@section('title', 'Nosotros | Certificaciones')

@section('content')
    <section>
        <img src="/img/certificaciones.jpg" class="img-fluid">
    </section>

    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">{{ $lstLocales['Home'] }}</a></li>
                <li class="breadcrumb-item"><a href="/nosotros">{{ $lstLocales['About Us'] }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $lstLocales['Certifications'] }}</li>
            </ol>
        </nav>
    </div>

    <section class="py-4">
        <div class="container">
            @foreach($lstCertificaciones as $certificacion)
                <div class="row mb-5 d-lg-block justify-content-center justify-content-lg-between overflow-hidden">
                    <div class="col-8 col-xl-5 col-lg-6 col-md-12 px-5 {{ $loop->index % 2 === 0 ? 'float-lg-right' : 'float-lg-left' }}">
                        <div class="px-md-5 px-lg-3 pt-lg-4 text-center">
                            <img class="img-fluid" src="{{ $certificacion->ruta_imagen }}">
                        </div>
                    </div>
                    <div class="col-12 col-xl-5 col-lg-6 col-md-12 {{ $loop->index % 2 === 0 ? 'float-lg-left' : 'float-lg-right' }}">
                        <h3>{{ session('locale') === 'en' ? $certificacion->nombre_en : $certificacion->nombre_es }}</h3>
                        {!! session('locale') === 'en' ? $certificacion->descripcion_en : $certificacion->descripcion_es !!}
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
