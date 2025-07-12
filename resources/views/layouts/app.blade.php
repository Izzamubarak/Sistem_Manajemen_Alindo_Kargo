<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>

    <!-- ✅ Bootstrap 4.6 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- ✅ Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />

    <!-- ✅ Local Style -->
    <link href="{{ secure_asset('css/styles.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>

<body class="sb-nav-fixed">

    @include('partials.navbar')

    <div id="layoutSidenav">
        @include('partials.sidebar')

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            @include('partials.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ secure_asset('js/scripts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <script>
        const token = localStorage.getItem("token");
        const path = window.location.pathname;
        const publicPaths = ["/", "/login"];

        // Kalau belum login dan akses halaman privat → tendang ke login
        if (!token && !publicPaths.includes(path)) {
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
    <script>
        async function clearToken() {
            const token = localStorage.getItem("token");

            try {
                await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {
                console.warn("Logout API gagal, tapi token tetap dihapus lokal.");
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
</body>

</html>
