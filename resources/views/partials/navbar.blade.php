<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark shadow-sm px-4">
    <!-- Sidebar toggle -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 text-white" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Spacer -->
    <div class="ml-auto"></div>

    <!-- Right navbar (User dropdown) -->
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" id="userDropdown" href="#" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg mr-1"></i>
                <span id="username" class="text-white small">Pengguna</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" class="dropdown-item" id="btnLogout">Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", async function() {
        const token = localStorage.getItem('token');
        try {
            const res = await fetch('/api/user', {
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            });
            const user = await res.json();
            document.getElementById('username').innerText = user.name;
        } catch {
            document.getElementById('username').innerText = 'Pengguna';
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar'); // Pastikan ini class sidebar kamu
        const mainContent = document.querySelector('.main-content');

        if (toggle && sidebar && mainContent) {
            toggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });
</script>
