{{-- ===== HAMBURGER BUTTON (Mobile/Tablet) ===== --}}
<button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Sidebar">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="3" y1="6"  x2="21" y2="6"  class="ham-line ham-line-1"/>
        <line x1="3" y1="12" x2="21" y2="12" class="ham-line ham-line-2"/>
        <line x1="3" y1="18" x2="21" y2="18" class="ham-line ham-line-3"/>
    </svg>
</button>

{{-- ===== OVERLAY ===== --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ===== SIDEBAR ===== --}}
<div id="sidebar" class="sidebar expanded">

    {{-- Toggle Button Desktop --}}
    <div class="sidebar-header">
        <button class="slide-btn" id="toggleSidebarBtn" aria-label="Collapse Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>
    </div>

    {{-- Menu Navigation --}}
    <ul class="nav flex-column px-2">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                </span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>

        {{-- Data Guru --}}
        <li class="nav-item">
            <a href="{{ route('admin.guru.index') }}"
               class="nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>
                <span class="menu-text">Data Guru</span>
            </a>
        </li>

        {{-- Data Siswa --}}
        <li class="nav-item">
            <a href="{{ route('admin.siswa.index') }}"
               class="nav-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </span>
                <span class="menu-text">Data Murid</span>
            </a>
        </li>

        {{-- Absensi --}}
        <li class="nav-item">
            <a href="{{ route('admin.absensi.index') }}"
               class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </span>
                <span class="menu-text">Absensi</span>
            </a>
        </li>

        {{-- Perizinan --}}
        <li class="nav-item">
            <a href="{{ route('admin.perizinan.index') }}"
               class="nav-link {{ request()->routeIs('admin.perizinan.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </span>
                <span class="menu-text">Perizinan</span>
            </a>
        </li>

        {{-- Kalender --}}
        <li class="nav-item">
            <a href="{{ route('admin.kalender.index') }}"
               class="nav-link {{ request()->routeIs('admin.kalender.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8"  y1="2" x2="8"  y2="6"/>
                        <line x1="3"  y1="10" x2="21" y2="10"/>
                    </svg>
                </span>
                <span class="menu-text">Kalender</span>
            </a>
        </li>

    </ul>

    {{-- User Info Footer --}}
    <div class="sidebar-footer">
        <div class="user-mini">
            <div class="user-avatar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <div class="user-info">
                <span class="user-name">{{ session('username') ?? 'Admin' }}</span>
                <span class="user-role">Administrator</span>
            </div>
        </div>
    </div>

</div>
