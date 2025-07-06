<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="{{ url('/home') }}">Alindo Cargo</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" onclick="clearToken()">Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
