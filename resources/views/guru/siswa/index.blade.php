@extends('layouts.guru')

@section('title', 'Data Murid | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/guru/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/guru/siswa.css') }}">
@endsection

@section('content')

{{-- Toast Notification --}}
<div id="toast" role="alert" aria-live="polite"></div>

{{-- Sidebar Toggle Mobile --}}
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
    <span></span><span></span><span></span>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="d-flex">
    {{-- Sidebar --}}
    @include('guru.partials.sidebar')

    {{-- Main Content --}}
    <div class="flex-grow-1 main-content">
        <div class="container-fluid">

            {{-- ===== HEADER ===== --}}
            <div class="d-flex justify-content-between align-items-center mb-4 page-header">
                <div class="d-flex align-items-center">
                    <svg class="header-icon" viewBox="0 0 24 24" fill="none"
                         stroke="#0d6efd" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    <div>
                        <h4 class="fw-bold m-0 page-title">Data Murid</h4>
                        <p class="text-muted small m-0">
                            Total
                            <span class="stat-badge ms-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                {{ count($siswaList) }} Murid
                            </span>
                        </p>
                    </div>
                </div>
                <div class="profile-area">
                    @include('guru.partials.profile')
                </div>
            </div>

            {{-- ===== FILTER & SEARCH ===== --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

                {{-- Filter Kelas --}}
                <div class="filter-kelas-container">
                    <select id="filterKelas" class="filter-select" onchange="filterByKelas()">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas }}"
                                {{ $selectedKelas === $kelas ? 'selected' : '' }}>
                                {{ $kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search Bar --}}
                <div class="search-action-bar">
                    <div class="search-container">
                        <svg class="search-icon" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" id="searchInput" class="search-input"
                               placeholder="Cari murid berdasarkan nama atau kelas...">
                    </div>
                </div>

            </div>

            {{-- ===== GRID KARTU SISWA ===== --}}
            <div class="row g-3" id="siswaContainer">

                @if(empty($siswaList))
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none"
                                 stroke="#0dcaf0" stroke-width="1.2" stroke-linecap="round"
                                 class="mb-3 d-block mx-auto" style="opacity:0.5">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                            <p class="mb-0 fw-semibold">Tidak ada data murid.</p>
                        </div>
                    </div>
                @else
                    @foreach($siswaList as $siswa)
                        @php
                            $nama    = htmlspecialchars($siswa['nama_siswa'] ?? '');
                            $kata    = explode(' ', $nama);
                            $inisial = count($kata) >= 2
                                ? strtoupper(substr($kata[0], 0, 1) . substr($kata[1], 0, 1))
                                : strtoupper(substr($kata[0], 0, 2));
                        @endphp

                        <div class="col-lg-4 col-md-6 col-12 siswa-item">
                            <div class="card siswa-card shadow-sm">
                                <div class="card-body">

                                    {{-- Avatar + Nama + Kelas --}}
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-inisial">{{ $inisial }}</div>
                                        <div class="ms-3 flex-grow-1">
                                            <h5 class="siswa-name mb-1">{{ $nama }}</h5>
                                            <span class="siswa-class">
                                                {{ htmlspecialchars($siswa['kelas'] ?? '') }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Detail Info --}}
                                    <div class="siswa-details">
                                        <div class="detail-item">
                                            <span class="detail-label">Jenis Kelamin</span>
                                            <span class="detail-value">
                                                {{ htmlspecialchars($siswa['gender'] ?? '-') }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Orang Tua</span>
                                            <span class="detail-value">
                                                {{ htmlspecialchars($siswa['nama_ortu'] ?? '-') }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">No. Telp</span>
                                            <span class="detail-value">
                                                {{ htmlspecialchars($siswa['no_telp_ortu'] ?? '-') }}
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Alamat</span>
                                            <span class="detail-value">
                                                {{ htmlspecialchars(Str::limit($siswa['alamat'] ?? '-', 50)) }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    @endforeach
                @endif

            </div>
            {{-- end siswaContainer --}}

        </div>
    </div>
</div>

{{-- Logout Form --}}
<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ===== SIDEBAR MOBILE =====
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('open');
        sidebarOverlay?.classList.toggle('show');
    });
    sidebarOverlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        sidebarOverlay.classList.remove('show');
    });

    // ===== FILTER KELAS =====
    function filterByKelas() {
        const val = document.getElementById('filterKelas').value;
        window.location.href = val
            ? `?kelas_filter=${encodeURIComponent(val)}`
            : '?';
    }

    // ===== PENCARIAN REAL-TIME =====
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword  = this.value.toLowerCase();
        const items    = document.querySelectorAll('#siswaContainer .siswa-item');
        let   visible  = 0;

        items.forEach(item => {
            const match = item.textContent.toLowerCase().includes(keyword);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        // Pesan "tidak ditemukan"
        let noMsg = document.getElementById('noResultMessage');
        if (visible === 0 && keyword !== '') {
            if (!noMsg) {
                noMsg = document.createElement('div');
                noMsg.id        = 'noResultMessage';
                noMsg.className = 'col-12';
                noMsg.innerHTML = '<div class="alert alert-warning text-center fw-semibold">Tidak ada murid yang cocok dengan pencarian.</div>';
                document.getElementById('siswaContainer').appendChild(noMsg);
            }
        } else if (noMsg) {
            noMsg.remove();
        }
    });
</script>
@endsection
