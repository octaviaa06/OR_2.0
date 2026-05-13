{{-- ===== SIDEBAR CONTAINER ===== --}}
<aside id="sidebar" class="sidebar expanded">

    {{-- ===== SIDEBAR HEADER ===== --}}
    <div class="sidebar-header">
        <div class="brand-area">
            <div class="logo-wrapper">
                <span class="logo-text">Ortu<span class="logo-highlight">Connect</span></span>
                {{-- Decorative sparkles --}}
                <svg class="logo-sparkle sparkle-1" width="8" height="8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0L14 10L24 12L14 14L12 24L10 14L0 12L10 10Z"/>
                </svg>
                <svg class="logo-sparkle sparkle-2" width="6" height="6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 0L14 10L24 12L14 14L12 24L10 14L0 12L10 10Z"/>
                </svg>
            </div>
        </div>
        {{-- Toggle Button Desktop --}}
        <button class="slide-btn" id="toggleSidebarBtn" aria-label="Collapse Sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
        </button>
    </div>

    {{-- ===== MENU NAVIGATION ===== --}}
    <ul class="nav flex-column px-2">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="3" width="7" height="7" rx="1" fill="url(#dash-primary)" opacity="0.3"/>
                        <rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                        <rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                        <rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="2"/>
                        <defs>
                            <linearGradient id="dash-primary" x1="0" y1="0" x2="24" y2="24">
                                <stop offset="0%" stop-color="#8B5CF6"/>
                                <stop offset="100%" stop-color="#6366F1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="menu-text">Dashboard</span>
                <span class="active-glow"></span>
            </a>
        </li>

        {{-- Divider --}}
        <li class="nav-divider"><span>Data Master</span></li>


        {{-- Data Siswa --}}
        <li class="nav-item">
            <a href="{{ route('guru.siswa.index') }}" class="nav-link {{ request()->routeIs('guru.siswa.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5" fill="url(#siswa-primary)" opacity="0.2" stroke="currentColor" stroke-width="2"/>
                        <defs>
                            <linearGradient id="siswa-primary" x1="2" y1="5" x2="22" y2="17">
                                <stop offset="0%" stop-color="#6366F1"/>
                                <stop offset="100%" stop-color="#818CF8"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="menu-text">Data Murid</span>
                <span class="active-glow"></span>
            </a>
        </li>

        {{-- Divider --}}
        <li class="nav-divider"><span>Aktivitas</span></li>

        {{-- Absensi --}}
        <li class="nav-item">
            <a href="{{ route('guru.absensi.index') }}" class="nav-link {{ request()->routeIs('guru.absensi.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M9 11l3 3L22 4" stroke="#8B5CF6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span class="menu-text">Absensi</span>
                <span class="active-glow"></span>
            </a>
        </li>

        {{-- Perizinan --}}
        <li class="nav-item">
            <a href="{{ route('guru.perizinan.index') }}" class="nav-link {{ request()->routeIs('guru.perizinan.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" fill="url(#izin-primary)" opacity="0.2" stroke="currentColor" stroke-width="2"/>
                        <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2"/>
                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <polyline points="10 9 9 9 8 9" stroke="#6366F1" stroke-width="2" stroke-linecap="round"/>
                        <defs>
                            <linearGradient id="izin-primary" x1="6" y1="2" x2="20" y2="20">
                                <stop offset="0%" stop-color="#8B5CF6"/>
                                <stop offset="100%" stop-color="#6366F1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </span>
                <span class="menu-text">Perizinan</span>
                <span class="active-glow"></span>
            </a>
        </li>

        {{-- Kalender --}}
    </ul>

    {{-- ===== FILLER ILLUSTRATION AREA ===== --}}
    <div class="sidebar-filler">
        <svg class="filler-illustration" viewBox="0 0 200 120" fill="none" aria-hidden="true">
            {{-- Rocket --}}
            <g transform="translate(60, 20) rotate(-15)">
                <path d="M30 0C30 0 45 20 45 50L35 60L25 60L15 50C15 20 30 0 30 0Z" fill="url(#rocket-body)" opacity="0.6"/>
                <circle cx="30" cy="30" r="5" fill="white" opacity="0.4"/>
                <path d="M15 50L10 70L25 60Z" fill="#F472B6" opacity="0.5"/>
                <path d="M45 50L50 70L35 60Z" fill="#F472B6" opacity="0.5"/>
                <ellipse cx="30" cy="75" rx="8" ry="12" fill="#FBBF24" opacity="0.6"/>
            </g>
            {{-- Books stack --}}
            <g transform="translate(20, 70)">
                <rect x="0" y="10" width="30" height="25" rx="2" fill="#8B5CF6" opacity="0.3"/>
                <rect x="5" y="5" width="30" height="25" rx="2" fill="#6366F1" opacity="0.35"/>
                <rect x="10" y="0" width="30" height="25" rx="2" fill="#A78BFA" opacity="0.4"/>
                <line x1="18" y1="5" x2="18" y2="25" stroke="white" stroke-width="1" opacity="0.3"/>
            </g>
            {{-- Decorative stars --}}
            <circle cx="150" cy="30" r="3" fill="#8B5CF6" opacity="0.4"/>
            <circle cx="170" cy="50" r="2" fill="#6366F1" opacity="0.3"/>
            <circle cx="140" cy="80" r="4" fill="#A78BFA" opacity="0.35"/>
            <circle cx="160" cy="100" r="2.5" fill="#818CF8" opacity="0.3"/>
            
            <defs>
                <linearGradient id="rocket-body" x1="15" y1="0" x2="45" y2="75">
                    <stop offset="0%" stop-color="#C4B5FD"/>
                    <stop offset="100%" stop-color="#8B5CF6"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    {{-- ===== SIDEBAR FOOTER (User Info) ===== --}}
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
                <span class="user-role">Guru</span>
            </div>
        </div>
    </div>

</aside>

{{-- ===== OVERLAY (Mobile) ===== --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ===== HAMBURGER BUTTON (Mobile/Tablet) ===== --}}
<button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Sidebar">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="3" y1="6"  x2="21" y2="6"  class="ham-line ham-line-1"/>
        <line x1="3" y1="12" x2="21" y2="12" class="ham-line ham-line-2"/>
        <line x1="3" y1="18" x2="21" y2="18" class="ham-line ham-line-3"/>
    </svg>
</button>