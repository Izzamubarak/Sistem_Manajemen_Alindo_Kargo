<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>

    <!-- ✅ Bootstrap 4.6 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- ✅ Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <!-- ✅ Custom Styles -->
    <link href="{{ secure_asset('css/styles.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ secure_asset('favicon.png') }}">

    <!-- ✅ Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body class="sb-nav-fixed">

    {{-- Navbar & Sidebar --}}
    <div id="layoutSidenav">
        @include('partials.sidebar')

        <div id="layoutSidenav_content">
            <main class="p-4">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    <!-- ✅ Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ secure_asset('js/scripts.js') }}"></script>

    <!-- ✅ Session & Token Handling -->
    <script>
        const token = localStorage.getItem("token");
        const path = window.location.pathname;
        const publicPaths = ["/", "/login"];

        // 1. Belum login tapi akses private → redirect login
        if (!token && !publicPaths.includes(path)) {
            window.location.href = "/login";
        }

        // 2. Sudah login tapi masih di /login → redirect ke /home
        if (token && path === "/login") {
            window.location.href = "/home";
        }

        // 3. Reset token saat di halaman login
        if (path === "/login") {
            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }

        // 4. Cegah back ke halaman setelah logout
        window.addEventListener('pageshow', function(event) {
            if (!token && !publicPaths.includes(path) && event.persisted) {
                window.location.href = "/login";
            }
        });
    </script>

    <!-- ✅ Logout Handling -->
    <script>
        async function clearToken() {
            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {
                console.warn("Gagal logout dari API, lanjut hapus lokal");
            }

            localStorage.removeItem("token");
            localStorage.removeItem("user");
        }

        document.addEventListener("DOMContentLoaded", function() {
            const btnLogout = document.getElementById("btnLogout");
            if (btnLogout) {
                btnLogout.addEventListener("click", function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Keluar?',
                        text: 'Anda yakin ingin logout?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Logout',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            clearToken().then(() => {
                                window.location.href = "/login";
                            });
                        }
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
