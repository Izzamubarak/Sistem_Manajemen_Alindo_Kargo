<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Alindo Cargo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0051d4;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            display: flex;
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }

        .login-left {
            background-color: #fff;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-left img {
            max-width: 100%;
            height: auto;
        }

        .login-right {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h4 {
            font-weight: bold;
        }

        .form-control {
            border-radius: 30px;
            padding-left: 20px;
        }

        .btn-login {
            border-radius: 30px;
            background-color: #007BFF;
            color: white;
            width: 100%;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-left">
            <img src="{{ asset('images/logo-alindo.png') }}" alt="Logo Alindo">
        </div>
        <div class="login-right">
            <h4 class="text-center">Selamat Datang</h4>
            <p class="text-center">Anda berada di Sistem Manajemen PT. Alindo</p>
            <form action="#" method="POST" id="loginForm">
                <div class="form-group">
                    <input type="text" class="form-control" name="username" placeholder="username">
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

            const email = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;

            const response = await fetch('http://localhost:8000/api/login', {
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
</body>

</html>
