<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="page-header-logo">
            <img src="{{ asset('images/logo2.png') }}" alt="Logo Header" style="height: 40px;">
            <span class="logo-header-text">Alindo Cargo</span>
        </div>
        <button id="sidebarToggle" class="toggle-btn">
            <i class="fas fa-bars toggle-icon"></i>
        </button>
    </div>

    <ul class="sidebar-menu">
        <li id="dashboard">
            <a href="/home"><i class="fa fa-tachometer-alt"></i> <span class="menu-text"> Dashboard</span></a>
        </li>
        <li id="paket">
            <a href="/paket"><i class="fa fa-box"></i> <span class="menu-text"> Paket</span></a>
        </li>
        <li class="dropdown" id="karyawan">
            <a href="#"><i class="fas fa-users"></i> <span class="menu-text">Karyawan</span>
                <i class="fas fa-chevron-down caret"></i>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{ url('/profile-admin') }}">
                        <i class="fas fa-user-shield"></i>
                        <span class="menu-text">Admin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/profile-tim-operasional') }}">
                        <i class="fas fa-user-cog"></i>
                        <span class="menu-text">Tim Operasional</span>
                    </a>
                </li>
            </ul>
        </li>
        <li id="vendor">
            <a href="/vendor"><i class="fa fa-truck"></i><span class="menu-text"> Vendor</span></a>
        </li>
        <li id="biaya">
            <a href="/biaya"><i class="fa fa-wallet"></i><span class="menu-text"> Biaya Operasional</span></a>
        </li>
        <li id="laporan">
            <a href="/laporan"><i class="fa fa-file-alt"></i><span class="menu-text"> Laporan</span></a>
        </li>
    </ul>

    <div class="sidebar-profile">
        <div class="avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="#" id="btnLogout" class="logout-link">Logout</a>
        </form>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const layout = document.getElementById('layoutSidenav');

        // Default: sidebar tidak collapsed
        layout.classList.remove('sidebar-collapsed');

        // Toggle sidebar collapse
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            layout.classList.toggle('sidebar-collapsed');
        });

        // Aktifkan dropdown karyawan
        const dropdownTriggers = document.querySelectorAll('.dropdown > a');
        dropdownTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.closest('.dropdown');
                parent.classList.toggle('open');
            });
        });

        // Cek token user untuk akses role
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
