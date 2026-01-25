@extends('auth-layouts.auth')

@section('title', 'Registro Cliente eSIM')

@section('contents')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-6 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                        <div class="brand-logo text-center mb-4">
                            <img src="{{ filter_var(config('settings.application.company_logo'), FILTER_VALIDATE_URL) ? config('settings.application.company_logo') : request()->root().config('settings.application.company_logo') }}" alt="logo">
                        </div>
                        <h4 class="text-center mb-2">Registro de Cliente eSIM</h4>
                        @if($parametro)
                        <p class="text-center text-muted mb-4">Beneficiario: <strong>{{ $parametro }}</strong></p>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif

                        <form class="pt-3" method="POST" action="{{ route('registro.esim.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       placeholder="Ingrese su nombre"
                                       value="{{ old('nombre') }}"
                                       required>
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="apellido">Apellido</label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       placeholder="Ingrese su apellido"
                                       value="{{ old('apellido') }}"
                                       required>
                                @error('apellido')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Ingrese su email"
                                       value="{{ old('email') }}"
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                    Registrar Cliente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
