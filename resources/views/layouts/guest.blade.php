<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Login - Sistem Manajemen')</title>

    <!-- Bootstrap & Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <link href="{{ secure_asset('css/login_style.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ secure_asset('favicon.png') }}">
    @stack('styles')
</head>

<body>

    @yield('content')

    <script>
        const token = localStorage.getItem("token");
        const path = window.location.pathname;

        const publicPaths = ["/", "/login", "/forgot-password"];
        const isResetPassword = path.startsWith("/reset-password");

        // Kalau belum login dan akses halaman privat → tendang ke login
        if (!token && !publicPaths.includes(path) && !isResetPassword) {
            window.location.href = "/login";
        }

        // Kalau sudah login tapi masih di halaman login → arahkan ke /home
        if (token && path === "/login") {
            window.location.href = "/home";
        }

        // Kalau sedang di halaman login → hapus token
        if (path === "/login") {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }
    </script>

    @stack('scripts')
</body>

</html>
