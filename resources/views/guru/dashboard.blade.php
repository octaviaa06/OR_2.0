@extends('layouts.guru')

@section('title', 'Dashboard Guru | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/guru/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/guru/sidebar.css') }}">
@endsection

@section('content')
<!-- Toast Notification -->
<div id="toast" role="alert" aria-live="polite"></div>

<!-- Toggle Button Mobile -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
    <span></span><span></span><span></span>
</button>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="d-flex">
    <!-- Sidebar -->
    @include('guru.partials.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content"
         style="background-image: url('{{ asset('images/background/Dashboard Admin.png') }}');
                background-size: cover;
                background-position: center;">
        <div class="container-fluid">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <h4 class="fw-bold text-primary m-0 d-none d-md-block">Dashboard Guru</h4>
                <h5 class="fw-bold text-primary m-0 d-md-none">Dashboard Guru</h5>
                @include('guru.partials.profile')
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body stat-card-body p-4">
                            <p class="stat-label">Jumlah Siswa</p>
                            <p class="stat-value">{{ $siswa }}</p>
                            <div class="stat-change"><span>↑</span><span>Total</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body stat-card-body p-4">
                            <p class="stat-label">Izin Menunggu</p>
                            <p class="stat-value">{{ count($izin) }}</p>
                            <div class="stat-change"><span>↑</span><span>Proses</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body stat-card-body p-4">
                            <p class="stat-label">Siswa Masuk Hari Ini</p>
                            <div class="chart-container">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Access -->
            <h5 class="fw-bold text-primary mb-3">Akses Cepat</h5>
            <div class="row g-3 mb-4">
                @php
                $quickLinks = [
                    ['url' => route('guru.absensi.index'), 'icon' => asset('images/Absensi Biru.png'), 'text' => 'Kelola Absensi'],
                    ['url' => route('guru.perizinan.index'), 'icon' => asset('images/Perizinan Biru.png'), 'text' => 'Proses Perizinan'],
                    ['url' => '#', 'icon' => asset('images/Kalender_biru.png'), 'text' => 'Lihat Kalender'],
                ];
                @endphp

                @foreach($quickLinks as $link)
                <div class="col-6 col-md-4">
                    <a href="{{ $link['url'] }}" class="text-decoration-none">
                        <div class="card access-card shadow-sm h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center w-100">
                                <img src="{{ $link['icon'] }}" class="access-icon" alt="{{ $link['text'] }}">
                                <p class="access-text">{{ $link['text'] }}</p>
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
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-primary d-flex align-items-center gap-2 mb-3">
                                <img src="{{ asset('images/Pesan.png') }}" width="22" alt="Izin">
                                Izin Menunggu
                                <span class="badge bg-primary" id="izinCounter">{{ count($izin) }}</span>
                            </h6>
                            <div class="border-top pt-2" id="izinContainer" style="max-height: 400px; overflow-y: auto;">
                                @if(empty($izin))
                                    <p class="text-muted small mb-0">Tidak ada izin menunggu</p>
                                @else
                                    @foreach($izin as $i)
                                        @php
                                            $idIzin    = $i['id_izin'] ?? $i['id'] ?? $i['id_perizinan'] ?? 0;
                                            $namaSiswa = $i['nama_siswa'] ?? $i['nama'] ?? $i['nama_lengkap'] ?? 'N/A';
                                            $tglMulai  = $i['tanggal_mulai'] ?? $i['tanggal'] ?? $i['tanggal_izin'] ?? '';
                                            $tglSelesai = $i['tanggal_selesai'] ?? $i['tanggal_akhir'] ?? '';

                                            $tanggalDisplay = '';
                                            if (!empty($tglMulai)) {
                                                $tanggalDisplay = date('d/m/Y', strtotime($tglMulai));
                                                if (!empty($tglSelesai) && $tglSelesai !== $tglMulai) {
                                                    $tanggalDisplay .= ' - ' . date('d/m/Y', strtotime($tglSelesai));
                                                }
                                            }
                                        @endphp
                                        <div class="mb-3 pb-2 border-bottom izin-item" data-id="{{ (int)$idIzin }}">
                                            <p class="mb-1 small">
                                                <strong>{{ htmlspecialchars($namaSiswa) }}</strong>
                                                @if(!empty($i['kelas']))
                                                    <span class="badge bg-secondary ms-1">{{ htmlspecialchars($i['kelas']) }}</span>
                                                @endif
                                                <br>
                                                <span class="text-muted">
                                                    {{ htmlspecialchars($i['jenis_izin'] ?? 'Izin') }}
                                                    @if($tanggalDisplay) • {{ $tanggalDisplay }} @endif
                                                </span>
                                            </p>
                                            @if(!empty($i['keterangan']))
                                                <p class="mb-2 small text-muted fst-italic">
                                                    "{{ htmlspecialchars(substr($i['keterangan'], 0, 50)) }}{{ strlen($i['keterangan']) > 50 ? '...' : '' }}"
                                                </p>
                                            @endif
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-success btn-sm flex-fill btn-setujui"
                                                        data-id="{{ (int)$idIzin }}"
                                                        data-nama="{{ htmlspecialchars($namaSiswa) }}">
                                                    ✔ Setujui
                                                </button>
                                                <button class="btn btn-danger btn-sm flex-fill btn-tolak"
                                                        data-id="{{ (int)$idIzin }}"
                                                        data-nama="{{ htmlspecialchars($namaSiswa) }}">
                                                    ✘ Tolak
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agenda Terdekat -->
                <div class="col-md-6">
                    <div class="card border-primary shadow-sm h-100">
                        <div class="card-body">
                            <h6 class="text-primary d-flex align-items-center gap-2 mb-3">
                                <img src="{{ asset('images/Kalender_Biru.png') }}" width="22" alt="Agenda"> Agenda Terdekat
                            </h6>
                            <div class="agenda-simple">
                                @if(empty($agenda))
                                    <div class="text-center py-4">
                                        <p class="text-muted small mb-0">Tidak ada agenda</p>
                                    </div>
                                @else
                                    @foreach($agenda as $a)
                                        <div class="agenda-item-simple">
                                            <div class="agenda-date-simple">
                                                {{ !empty($a['tanggal']) ? date('d M', strtotime($a['tanggal'])) : '-- ---' }}
                                            </div>
                                            <div class="agenda-content-simple">
                                                <strong class="agenda-title-simple">{{ htmlspecialchars($a['nama_kegiatan'] ?? '—') }}</strong>
                                                @if(!empty($a['waktu_mulai']))
                                                    <span class="agenda-time-simple">• {{ date('H:i', strtotime($a['waktu_mulai'])) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Setujui -->
<div class="modal fade" id="modalKonfirmasiSetujui" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Konfirmasi Persetujuan Izin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning small mb-3">
                    <strong>Apakah Anda yakin ingin menyetujui izin ini?</strong>
                </div>
                <div class="alert alert-info small">
                    <strong id="namaSiswaSetujui"></strong>
                </div>
                <p class="small text-muted mb-0">Setelah disetujui, izin tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btnKonfirmasiSetujui">Ya, Setujui</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="modalAlasanTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Alasan Penolakan Izin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small mb-3">
                    <strong id="namaSiswaTolak"></strong>
                </div>
                <form id="formAlasanTolak">
                    <div class="mb-3">
                        <label for="alasanTolak" class="form-label">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="alasanTolak" rows="4"
                                  placeholder="Contoh: Dokumen tidak lengkap..." required></textarea>
                        <small class="text-muted d-block mt-2">Alasan ini akan dikirimkan ke orang tua siswa</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnKonfirmasiTolak">Tolak Izin</button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="{{ asset('js/guru/dashboard.js') }}"></script>
<script>
    window.guruConfig = {
        apiUrl:          "{{ route('guru.izin.update') }}",
        refreshUrl:      "{{ route('guru.izin.refresh') }}",
        userId:          {{ Session::get('id_akun', 0) }},
        siswaMasuk:      {{ $siswaMasukHariIni }},
        siswaTidakMasuk: {{ $siswaTidakMasuk }},
        totalSiswa:      {{ $siswa }}
    };
</script>
@endsection
