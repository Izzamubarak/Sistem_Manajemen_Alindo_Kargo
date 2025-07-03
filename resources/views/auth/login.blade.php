<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Alindo Cargo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('{{ asset('images/logo3.png') }}') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            display: flex;
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            max-width: 850px;
            width: 100%;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }

        .login-left {
            background-color: #ffffff;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-left img {
            max-width: 250px;
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
            color: #333;
        }

        .form-control {
            border-radius: 30px;
            padding-left: 20px;
            height: 45px;
        }

        .btn-login {
            border-radius: 30px;
            background-color: #0051d4;
            color: white;
            width: 100%;
            height: 45px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #003f9a;
        }

        .form-group i {
            position: absolute;
            margin-top: 12px;
            margin-left: 15px;
            color: #888;
        }

        .input-icon {
            position: relative;
        }

        .input-icon input {
            padding-left: 40px;
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
            <img src="{{ asset('images/logo1.png') }}" alt="Logo Alindo" />
        </div>
        <div class="login-right">
            <h4 class="text-center mb-2">Selamat Datang</h4>
            <p class="text-center mb-4">Anda berada di Sistem Manajemen PT. Alindo</p>
            <form action="#" method="POST" id="loginForm">
                <div class="form-group input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" class="form-control" name="email" placeholder="Email" required />
                </div>
                <div class="form-group input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" class="form-control" name="password" placeholder="Password" required />
                </div>
                <div class="form-group text-right">
                    <a href="#">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-login">Masuk</button>
            </form>
        </div>
    </div>

    <!-- Font Awesome (untuk ikon input) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

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
</body>

</html>
