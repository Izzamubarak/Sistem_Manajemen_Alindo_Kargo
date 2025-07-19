@extends('layouts.guest')
@section('title', 'Login - Alindo Cargo')

@section('content')
    <div class="login-container">
        <div class="login-left">
            <h1>ALINDO</h1>
            <h2>PT ALIF LOGISTIK INDONESIA</h2>
            <p>
                Selamat datang. Anda sedang berada di sistem manajemen internal perusahaan.<br>
                Verifikasi diri Anda jika Anda adalah salah satu staf yang bekerja di sini.
            </p>
        </div>

        <div class="login-right">
            <h4>Login</h4>
            <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>
            <form action="#" method="POST" id="loginForm">
                <div class="form-group input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" class="form-control" name="email" placeholder="Email" required />
                </div>
                <div class="form-group input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" placeholder="Password" required />
                </div>
                <div class="forgot-password">
                    <a href="/forgot-password">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-login">Masuk</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        document.getElementById("loginForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;

            const response = await fetch(
                'http://127.0.0.1:8000/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

            const result = await response.json();
            const errorDiv = document.getElementById("errorMessage");

            if (response.ok) {
                localStorage.setItem("token", result.token);
                localStorage.setItem("user", JSON.stringify(result.user));
                window.location.href = "/home";
            } else {
                errorDiv.classList.remove("d-none");
                errorDiv.textContent = result.message || "Terjadi kesalahan saat login.";
            }
        });
    </script>
@endpush
