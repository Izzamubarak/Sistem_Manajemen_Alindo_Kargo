@extends('layouts.guest')

@section('title', 'Lupa Password - Alindo Cargo')

@section('content')
    <div class="login-container">
        <div class="login-left">
            <h1>ALINDO</h1>
            <h2>PT ALIF LOGISTIK INDONESIA</h2>
            <p>
                Silakan masukkan email Anda untuk mereset password akun.
            </p>
        </div>

        <div class="login-right">
            <h4>Lupa Password</h4>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('forgot.submit') }}">
                @csrf
                <div class="form-group input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" class="form-control" name="email" placeholder="Email" required
                        autocomplete="email" />
                </div>
                <button type="submit" class="btn btn-login">Ajukan Reset</button>
            </form>
        </div>
    </div>
@endsection
