@extends('layouts.guru')

@section('title', 'Dashboard Guru | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/guru/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/guru/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<!-- Toast Notification -->
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('guru.partials.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content">

        <div class="admin-bg-grid"></div>
        <div class="admin-star-dust"></div>

        <div class="admin-mascot-peek">
            <svg viewBox="0 0 100 100" class="admin-mascot-svg">
                <circle cx="50" cy="15" r="8" fill="#8b5cf6"/>
                <rect x="48" y="15" width="4" height="10" fill="#a855f7"/>
                <rect x="20" y="25" width="60" height="50" rx="15" fill="#1e293b" stroke="#8b5cf6" stroke-width="2"/>
                <circle cx="35" cy="45" r="5" fill="#fff"/>
                <circle cx="65" cy="45" r="5" fill="#fff"/>
                <circle cx="35" cy="45" r="2" fill="#8b5cf6"/>
                <circle cx="65" cy="45" r="2" fill="#8b5cf6"/>
                <path d="M40 60 Q50 70 60 60" stroke="#fff" stroke-width="3" fill="none"/>
                <path d="M15 50 Q5 40 15 30" stroke="#64748b" stroke-width="4" fill="none" stroke-linecap="round"/>
                <path d="M85 50 Q95 40 85 30" stroke="#64748b" stroke-width="4" fill="none" stroke-linecap="round"/>
            </svg>
        </div>

        <div class="container-fluid" style="position: relative; z-index: 10;">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <div>
                    <h4 class="page-title m-0 d-none d-md-block">Dashboard Guru</h4>
                    <h5 class="page-title m-0 d-md-none">Dashboard Guru</h5>
                    <p class="page-subtitle m-0">Selamat datang, {{ session('username') ?? 'Guru' }}</p>
                </div>
                @include('guru.partials.profile')
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">

                <!-- Jumlah Siswa -->
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="stat-card-body p-4">
                            <p class="stat-label">Jumlah Siswa</p>
                            <p class="stat-value">{{ $siswa }}</p>
                            <div class="stat-change"><span>↑</span><span>Total</span></div>
                        </div>
                    </div>
                </div>

                <!-- Izin Menunggu -->
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="stat-card-body p-4">
                            <p class="stat-label">Izin Menunggu</p>
                            <p class="stat-value">{{ count($izin) }}</p>
                            <div class="stat-change"><span>⏳</span><span>Perlu ditinjau</span></div>
                        </div>
                    </div>
                </div>

                <!-- Siswa Masuk Hari Ini -->
                <div class="col-12 col-md-4">
                    <div class="stat-card">
                        <div class="stat-card-body p-3">
                            <p class="stat-label mb-2">Siswa Masuk Hari Ini</p>
                            <div class="circular-progress-wrapper">
                                <div class="circular-progress">
                                    <svg class="progress-ring" width="200" height="200" viewBox="0 0 200 200">
                                        <circle class="progress-ring__circle-bg"
                                                stroke="rgba(255,255,255,0.05)"
                                                stroke-width="12" fill="transparent" r="90" cx="100" cy="100"/>
                                        <defs>
                                            <linearGradient id="gradientColors" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#8b5cf6"/>
                                                <stop offset="50%" stop-color="#d946ef"/>
                                                <stop offset="100%" stop-color="#22c55e"/>
                                            </linearGradient>
                                        </defs>
                                        <circle class="progress-ring__circle"
                                                stroke="url(#gradientColors)"
                                                stroke-width="12" stroke-linecap="round"
                                                fill="transparent" r="90" cx="100" cy="100"
                                                stroke-dasharray="565.48"
                                                stroke-dashoffset="565.48"/>
                                    </svg>
                                    <div class="progress-content">
                                        <div class="progress-number">
                                            <span class="progress-current" id="siswaMasukCount">{{ $siswaMasukHariIni }}</span>
                                            <span class="progress-divider">/</span>
                                            <span class="progress-total" id="siswaTotalCount">{{ $siswa }}</span>
                                        </div>
                                        <div class="progress-label">present</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Akses Cepat -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Akses Cepat</h5>
            </div>
            <div class="row g-3 mb-4">
                @php
                $quickLinks = [
                    [
                        'url'   => route('guru.absensi.index'),
                        'svg'   => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/></svg>',
                        'text'  => 'Kelola Absensi',
                        'sub'   => 'Pantau kehadiran siswa',
                        'color' => 'purple',
                    ],
                    [
                        'url'   => route('guru.izin.index'),
                        'svg'   => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
                        'text'  => 'Proses Perizinan',
                        'sub'   => 'Tinjau izin masuk/keluar',
                        'color' => 'cyan',
                    ],
               [
    'url'   => '/guru/kalender',
    'svg'   => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="15" x2="12" y2="15" stroke-width="3"/></svg>',
    'text'  => 'Lihat Kalender',
    'sub'   => 'Jadwal & agenda sekolah',
    'color' => 'pink',
],
                ];
                @endphp

                @foreach($quickLinks as $link)
                <div class="col-12 col-md-4">
                    <a href="{{ $link['url'] }}" class="text-decoration-none">
                        <div class="quick-card quick-card--{{ $link['color'] }}">
                            <div class="quick-card__icon quick-card__icon--{{ $link['color'] }}">
                                {!! $link['svg'] !!}
                            </div>
                            <div class="quick-card__body">
                                <p class="quick-card__title">{{ $link['text'] }}</p>
                                <p class="quick-card__sub">{{ $link['sub'] }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Izin & Agenda -->
            <div class="row g-3">

                <!-- Izin Menunggu -->
                <div class="col-md-6">
                    <div class="dash-panel h-100">
                        <div class="dash-panel__header">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                <span class="dash-panel__title">Izin Menunggu</span>
                            </div>
                            <span class="pending-badge" id="izinCounter">{{ count($izin) }} PENDING</span>
                        </div>
                        <div class="dash-panel__body" id="izinContainer">
                            @if(empty($izin))
                                <p class="dash-empty">Tidak ada izin menunggu</p>
                            @else
                                @foreach($izin as $i)
                                    @php
                                        $idIzin    = $i['id_izin'] ?? $i['id'] ?? 0;
                                        $namaSiswa = $i['nama_siswa'] ?? $i['nama'] ?? 'N/A';
                                        $jenisIzin = $i['jenis_izin'] ?? 'Izin';
                                        $keterangan = $i['keterangan'] ?? '';
                                        $tglMulai  = $i['tanggal_mulai'] ?? '';
                                        $tglSelesai = $i['tanggal_selesai'] ?? '';
                                        $durasi = '';
                                        if (!empty($tglMulai) && !empty($tglSelesai) && $tglSelesai !== $tglMulai) {
                                            $diff = (strtotime($tglSelesai) - strtotime($tglMulai)) / 86400 + 1;
                                            $durasi = $diff . ' Hari';
                                        }
                                        $kata = explode(' ', $namaSiswa);
                                        $inisial = count($kata) >= 2
                                            ? strtoupper(substr($kata[0],0,1) . substr($kata[1],0,1))
                                            : strtoupper(substr($kata[0],0,2));
                                    @endphp
                                    <div class="izin-row" data-id="{{ (int)$idIzin }}">
                                        <div class="izin-avatar">{{ $inisial }}</div>
                                        <div class="izin-info">
                                            <p class="izin-nama">{{ htmlspecialchars($namaSiswa) }}</p>
                                            <p class="izin-detail">
                                                {{ htmlspecialchars($jenisIzin) }}
                                                @if($keterangan) - {{ htmlspecialchars(Str::limit($keterangan, 30)) }} @endif
                                                @if($durasi) · {{ $durasi }} @endif
                                            </p>
                                        </div>
                                        <div class="izin-actions">
                                            <button class="izin-btn izin-btn--approve btn-setujui"
                                                    data-id="{{ (int)$idIzin }}"
                                                    data-nama="{{ htmlspecialchars($namaSiswa) }}" title="Setujui">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </button>
                                            <button class="izin-btn izin-btn--reject btn-tolak"
                                                    data-id="{{ (int)$idIzin }}"
                                                    data-nama="{{ htmlspecialchars($namaSiswa) }}" title="Tolak">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Agenda Terdekat -->
                <div class="col-md-6">
                    <div class="dash-panel h-100">
                        <div class="dash-panel__header">
                            <div class="d-flex align-items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                                    <line x1="3"  y1="10" x2="21" y2="10"/>
                                </svg>
                                <span class="dash-panel__title">Agenda Terdekat</span>
                            </div>
                        </div>
                        <div class="dash-panel__body">
                            @if(empty($agenda))
                                <p class="dash-empty">Tidak ada agenda</p>
                            @else
                                <div class="agenda-timeline">
                                    @foreach($agenda as $idx => $a)
                                        @php
                                            $tgl = $a['tanggal'] ?? '';
                                            $waktu = $a['waktu_mulai'] ?? '';
                                            $dotColors = ['purple', 'cyan', 'gray'];
                                            $dot = $dotColors[$idx % 3];
                                            $labelTgl = '';
                                            if ($tgl) {
                                                $ts = strtotime($tgl);
                                                $today = strtotime(date('Y-m-d'));
                                                $diff = ($ts - $today) / 86400;
                                                if ($diff == 0)     $labelTgl = 'HARI INI';
                                                elseif ($diff == 1) $labelTgl = 'BESOK';
                                                else                $labelTgl = strtoupper(date('j M', $ts));
                                                if ($waktu) $labelTgl .= ', ' . date('H:i', strtotime($waktu));
                                            }
                                        @endphp
                                        <div class="timeline-item">
                                            <div class="timeline-dot timeline-dot--{{ $dot }}"></div>
                                            <div class="timeline-content">
                                                <p class="timeline-date">{{ $labelTgl }}</p>
                                                <p class="timeline-title">{{ htmlspecialchars($a['nama_kegiatan'] ?? '—') }}</p>
                                                @if(!empty($a['deskripsi']))
                                                    <p class="timeline-sub">{{ htmlspecialchars(Str::limit($a['deskripsi'], 40)) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Modals -->
@include('guru.modals.izin-approve')
@include('guru.modals.izin-reject')

<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/guru/sidebar.js') }}"></script>
<script src="{{ asset('js/admin/dashboard.js') }}"></script>
<script>
window.adminConfig = {
    apiUrl:     "{{ route('guru.izin.update') }}",
    refreshUrl: "{{ route('guru.izin.refresh') }}",
    userId:     {{ Session::get('id_akun', 0) }},
    siswaMasuk: {{ $siswaMasukHariIni }},
    siswaTidakMasuk: {{ $siswaTidakMasuk }},
    totalSiswa: {{ $siswa }}
};
</script>
@endsection
