@extends('layouts.guest')

@section('title', 'Reset Password - Alindo Cargo')

@section('content')
    <div
        style="max-width: 400px; margin: auto; background: rgba(255,255,255,0.9); padding: 2rem; border-radius: 20px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">Reset Password</h2>
        <form method="POST" action="{{ route('reset.submit') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div style="margin-bottom: 1rem;">
                <input type="password" name="password" placeholder="Password Baru" required
                    style="width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 30px;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <input type="password" name="password_confirmation" placeholder="Ulangi Password" required
                    style="width: 100%; padding: 10px 15px; border: 1px solid #ccc; border-radius: 30px;">
            </div>

            <button type="submit"
                style="width: 100%; background-color: #0051d4; color: white; border: none; padding: 10px; border-radius: 30px; font-weight: bold;">
                Reset
            </button>
        </form>
    </div>
@endsection
