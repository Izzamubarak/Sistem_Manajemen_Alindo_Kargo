<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <a class="nav-link" href="{{ url('/home') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <a class="nav-link" href="{{ url('/paket') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                    Paket
                </a>
                <a id="karyawan" class="nav-link collapsed" href="#" data-toggle="collapse"
                    data-target="#collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Karyawan
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePages">
                    <nav class="sb-sidenav-menu-nested nav accordion">
                        <a class="nav-link" href="{{ url('/profile-admin') }}">Admin</a>
                        <a class="nav-link" href="{{ url('/profile-tim-operasional') }}">Tim Operasional</a>
                    </nav>
                </div>
                <a id="vendor" class="nav-link" href="{{ url('/vendor') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    Vendor
                </a>
                <a id="biaya" class="nav-link" href="{{ url('/biaya') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-coins"></i></div>
                    Biaya Operasional
                </a>
                <a id="laporan" class="nav-link" href="{{ url('/laporan') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                    Laporan
                </a>
            </div>
        </div>
    </nav>
</div>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const res = await fetch('/api/user', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Unauthorized');

            const user = await res.json();
            const role = user.role;
            if (role === 'tim-operasional') {
                document.getElementById('karyawan')?.style.setProperty('display', 'none');
                document.getElementById('biaya')?.style.setProperty('display', 'none');
                document.getElementById('laporan')?.style.setProperty('display', 'none');
                document.getElementById('vendor')?.style.setProperty('display', 'none');
            }

            if (role === 'admin') {
                document.getElementById('karyawan')?.style.setProperty('display', 'none');
                document.getElementById('laporan')?.style.setProperty('display', 'none');
            }

        } catch (err) {
            console.error(err);
            window.location.href = '/login';
        }
    });
</script>
