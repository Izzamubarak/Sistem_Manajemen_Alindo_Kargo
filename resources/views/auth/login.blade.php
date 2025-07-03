@extends('layouts.app')
@section('content')
    <div class="login-container">
        <div class="login-left">
            <img src="{{ asset('images/logo1.png') }}" alt="Logo Alindo">
        </div>
        <div class="login-right">
            <h4 class="text-center">Selamat Datang</h4>
            <p class="text-center">Anda berada di Sistem Manajemen PT. Alindo</p>
            <form action="#" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="text" class="form-control" name="email" placeholder="email">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="password">
                </div>
                <div class="form-group text-right">
                    <a href="#">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-login">Masuk</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById("loginForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;

            const response = await fetch(
                'https://sistemmanajemenperusahaan-production-affb.up.railway.app/api/login', {
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

            if (response.ok) {
                localStorage.setItem("token", result.token);
                localStorage.setItem("user", JSON.stringify(result.user));
                window.location.href = "/home";
            }
        });
    </script>
@endsection
