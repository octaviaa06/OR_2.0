<div class="sidebar bg-white border-end shadow-sm" id="sidebar" style="min-width: 220px; min-height: 100vh;">
    <div class="p-3 border-bottom text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 40px;">
    </div>
    <nav class="p-3">
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a href="{{ route('guru.dashboard') }}"
                   class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    🏠 Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('guru.siswa.index') }}"
                   class="nav-link {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                    👨‍🎓 Data Murid
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('guru.absensi.index') }}"
                   class="nav-link {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
                    📋 Absensi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('guru.perizinan.index') }}"
                   class="nav-link {{ request()->routeIs('guru.perizinan.*') ? 'active' : '' }}">
                    📝 Perizinan
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-dark">
                    📅 Kalender
                </a>
            </li>
        </ul>
    </nav>
</div>
