<nav class="navbar navbar-expand navbar-light topbar mb-4 shadow"
    style="background: linear-gradient(90deg, #ffffff 0%, #f0faf4 100%); border-bottom: 3px solid #1a6b3c;">

    <!-- Sidebar Toggler (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars" style="color: #1a6b3c;"></i>
    </button>

    <!-- Breadcrumb / Page Info -->
    <div class="d-none d-sm-flex align-items-center">
        <i class="fas fa-gavel mr-2" style="color: #1a6b3c;"></i>
        <span class="font-weight-bold" style="color: #1a6b3c; font-size: 0.85rem; letter-spacing: 1px;">
            LAPAU ANCAK
        </span>
        <span class="mx-2" style="color: #ccc;">|</span>
        <span class="text-muted small">
            {{ ucfirst(auth()->user()->role == 'admin_pusat' ? 'Admin Pusat' : 'Admin Satker') }}
        </span>
    </div>

    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Divider -->
        <div class="topbar-divider d-none d-sm-block"></div>

        @auth
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <!-- Info User -->
                <div class="d-none d-lg-flex flex-column text-right mr-3">
                    <span class="font-weight-bold small" style="color: #1a6b3c;">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-muted" style="font-size: 0.7rem;">
                        {{ auth()->user()->role == 'admin_pusat' ? '⚙️ Admin Pusat' : '🏢 Admin Satker' }}
                        @if(auth()->user()->role == 'admin_satker' && auth()->user()->satker)
                            — {{ auth()->user()->satker->nama_satker }}
                        @endif
                    </span>
                </div>

                <!-- Avatar -->
                <div style="
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #1a6b3c, #f6c90e);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    color: white;
                    font-size: 1rem;
                    box-shadow: 0 2px 8px rgba(26,107,60,0.3);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </a>

            <!-- Dropdown -->
            <div class="dropdown-menu dropdown-menu-right shadow"
                style="border: none; border-top: 3px solid #1a6b3c; min-width: 200px;">

                <!-- Info di dropdown -->
                <div class="px-3 py-2">
                    <div class="font-weight-bold text-dark small">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->email }}</div>
                </div>

                <div class="dropdown-divider"></div>

                <!-- Profile (opsional) -->
                <a class="dropdown-item small" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user fa-sm mr-2" style="color: #1a6b3c;"></i>Profil Saya
                </a>

                <div class="dropdown-divider"></div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item small text-danger">
                        <i class="fas fa-sign-out-alt fa-sm mr-2"></i>Logout
                    </button>
                </form>

            </div>
        </li>
        @endauth

    </ul>

</nav>