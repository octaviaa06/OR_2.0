@php
    $username = Session::get('username', 'User');
    $role     = Session::get('role', 'user');
    $kelas    = Session::get('kelas', null);
    $initial  = strtoupper(substr($username, 0, 1));

    $roleLabel = match($role) {
        'admin' => 'Administrator',
        'guru'  => 'Guru' . ($kelas ? ' — Kelas ' . $kelas : ''),
        default => ucfirst($role),
    };
@endphp

<div class="profile-wrapper" id="profileWrapper">

    {{-- Toggle Button --}}
    <button class="profile-btn" id="profileBtn" aria-label="Profile Menu">
        <div class="profile-avatar-btn">{{ $initial }}</div>
        <span class="profile-username d-none d-md-inline">{{ $username }}</span>
        <svg class="profile-chevron" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    {{-- Dropdown Card --}}
    <div class="profile-card" id="profileCard" role="menu">

        {{-- Header --}}
        <div class="profile-card-header">
            <div class="profile-card-avatar">{{ $initial }}</div>
            <div class="profile-card-info">
                <p class="profile-card-name">{{ $username }}</p>
                <p class="profile-card-role">{{ $roleLabel }}</p>
            </div>
        </div>

        <div class="profile-card-divider"></div>

        {{-- Logout --}}
        <a href="#" class="profile-logout-btn" role="menuitem"
           onclick="event.preventDefault(); document.getElementById('modalLogoutConfirm').classList.add('show-modal');">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Keluar
        </a>
    </div>
</div>

{{-- Modal Konfirmasi Logout --}}
<div class="logout-modal-overlay" id="modalLogoutConfirm" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="logout-modal-box">
        <div class="logout-modal-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </div>
        <h5 class="logout-modal-title" id="logoutModalTitle">Keluar dari Akun?</h5>
        <p class="logout-modal-desc">Kamu akan keluar dari sesi ini. Pastikan semua pekerjaan sudah tersimpan.</p>
        <div class="logout-modal-actions">
            <button type="button" class="logout-btn-cancel"
                    onclick="document.getElementById('modalLogoutConfirm').classList.remove('show-modal')">
                Batal
            </button>
            <button type="button" class="logout-btn-confirm"
                    onclick="document.getElementById('logoutForm').submit()">
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

@once
<script>
(function () {
    'use strict';
    const btn     = document.getElementById('profileBtn');
    const card    = document.getElementById('profileCard');
    const wrapper = document.getElementById('profileWrapper');
    if (!btn || !card || !wrapper) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = card.classList.toggle('show');
        btn.classList.toggle('active', isOpen);
        btn.setAttribute('aria-expanded', isOpen);
    });

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) {
            card.classList.remove('show');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && card.classList.contains('show')) {
            card.classList.remove('show');
            btn.classList.remove('active');
            btn.focus();
        }
    });
})();
</script>
@endonce
