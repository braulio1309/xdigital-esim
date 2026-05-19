@extends('auth-layouts.auth')

@section('title', 'Recarga eSIM')

@section('content')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0 py-5">
                <div class="row w-100 mx-0">
                    <div class="col-lg-6 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <h4 class="mb-1">Recargar eSIM</h4>
                            <p class="text-muted mb-4">Ingresa tu cédula, DNI o pasaporte para continuar.</p>

                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('recharge.document.lookup') }}" class="pt-2">
                                @csrf
                                <div class="form-group">
                                    <label for="identificador">Documento</label>
                                    <input
                                        id="identificador"
                                        type="text"
                                        name="identificador"
                                        value="{{ old('identificador', $cliente->identificador ?? '') }}"
                                        class="form-control @error('identificador') is-invalid @enderror"
                                        required
                                    >
                                    @error('identificador')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-block mt-3">Buscar eSIM</button>
                            </form>

                            @if($cliente && $transaction && $rechargeUrl)
                                <hr class="my-4">
                                <h5 class="mb-3">Datos encontrados</h5>
                                <p class="mb-1"><strong>Cliente:</strong> {{ $cliente->nombre }} {{ $cliente->apellido }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $cliente->email }}</p>
                                <p class="mb-3"><strong>ICCID:</strong> {{ $transaction->iccid }}</p>

                                <a href="{{ $rechargeUrl }}" class="btn btn-success btn-block">
                                    Continuar a compra de planes (3/5/10GB)
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
