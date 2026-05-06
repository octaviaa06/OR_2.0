@extends('layouts.guru')

@section('title', 'Absensi Siswa | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/absensi.css') }}">
@endsection

@section('content')
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('guru.partials.sidebar')

    <div class="flex-grow-1 main-content absensi-bg">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="page-icon-wrapper">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8"  y1="2" x2="8"  y2="6"/>
                                <line x1="3"  y1="10" x2="21" y2="10"/>
                                <polyline points="9 16 11 18 15 14"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="fw-bold m-0 page-title">Absensi Siswa</h4>
                            <p class="page-subtitle m-0">Kelola kehadiran siswa per kelas</p>
                        </div>
                    </div>
                    @if(!empty($kelasGuru))
                    <div class="mt-2 ms-1">
                        <span class="kelas-badge">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                            Kelas: {{ implode(', ', $kelasGuru) }}
                        </span>
                    </div>
                    @endif
                </div>
                @include('guru.partials.profile')
            </div>

            {{-- Filter Form --}}
            <form id="filterForm" method="GET" action="{{ route('guru.absensi.index') }}">
                <div class="filter-card mb-4">
                    <div class="d-flex gap-3 align-items-end flex-wrap">

                        {{-- Tanggal --}}
                        <div class="filter-group">
                            <label class="filter-label">Tanggal</label>
                            <input type="date" name="tanggal" class="filter-input"
                                   value="{{ $selectedDate }}"
                                   min="{{ $minDate }}" max="{{ $maxDate }}"
                                   onchange="this.form.submit()">
                        </div>

                        {{-- Kelas --}}
                        <div class="filter-group">
                            <label class="filter-label">Kelas</label>
                            @if(empty($kelasList))
                                <div class="alert-inline">Tidak ada kelas tersedia</div>
                            @else
                                <select name="kelas" class="filter-input filter-select" onchange="this.form.submit()">
                                    <option value="" {{ $selectedClass === '' ? 'selected' : '' }}>
                                        — Semua Kelas (Belum Absen) —
                                    </option>
                                    @foreach($kelasList as $k)
                                        <option value="{{ $k }}" {{ $selectedClass === $k ? 'selected' : '' }}>
                                            {{ $k }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex gap-2 filter-actions">
                            <button type="button" class="btn-action-primary" id="btnSimpan"
                                    onclick="simpanAbsensi()"
                                    {{ ($isDefaultView || empty($selectedClass)) ? 'disabled' : '' }}>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                    <polyline points="17 21 17 13 7 13 7 21"/>
                                    <polyline points="7 3 7 8 15 8"/>
                                </svg>
                                Simpan
                            </button>
                            <button type="button" class="btn-action-secondary"
                                    onclick="bukaModalExport()" {{ empty($kelasList) ? 'disabled' : '' }}>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="12" y1="18" x2="12" y2="12"/>
                                    <line x1="9" y1="15" x2="15" y2="15"/>
                                </svg>
                                Export PDF
                            </button>
                            <button type="button" class="btn-action-ghost" onclick="window.location.reload()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <polyline points="23 4 23 10 17 10"/>
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Info Bar --}}
            <div class="info-bar mb-4">
                <div class="d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    @if($isDefaultView)
                        <span>Menampilkan <strong>semua siswa belum diabsen</strong> pada
                              <strong>{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</strong></span>
                    @else
                        <span><strong>Kelas:</strong> {{ $selectedClass }} &nbsp;|&nbsp;
                              <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</span>
                    @endif
                </div>
                <span class="info-range">
                    Rentang: {{ \Carbon\Carbon::parse($minDate)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($maxDate)->format('d/m/Y') }}
                </span>
            </div>

            {{-- Tabel Absensi --}}
            <div class="absensi-card">

                @if(empty($absensiList))
                    <div class="empty-state text-center">
                        <div class="empty-icon mb-3">
                            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        @if($isDefaultView)
                            <p class="mb-1 fw-semibold">Semua siswa sudah diabsen hari ini!</p>
                            <p class="text-muted small mb-0">Tidak ada siswa yang belum memiliki status absensi.</p>
                        @else
                            <p class="mb-0">Tidak ada data siswa di kelas <strong>{{ $selectedClass }}</strong>.</p>
                        @endif
                    </div>

                @else
                    {{-- Header tabel --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="table-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <line x1="8" y1="6" x2="21" y2="6"/>
                                <line x1="8" y1="12" x2="21" y2="12"/>
                                <line x1="8" y1="18" x2="21" y2="18"/>
                                <line x1="3" y1="6" x2="3.01" y2="6"/>
                                <line x1="3" y1="12" x2="3.01" y2="12"/>
                                <line x1="3" y1="18" x2="3.01" y2="18"/>
                            </svg>
                            @if($isDefaultView)
                                Siswa Belum Diabsen — Semua Kelas
                            @else
                                Daftar Absensi — {{ $selectedClass }}
                            @endif
                        </h5>
                        <span class="total-badge">
                            @if($isDefaultView)
                                {{ count($absensiList) }} Belum Absen
                            @else
                                Total: {{ count($absensiList) }} Siswa
                            @endif
                        </span>
                    </div>

                    <form id="formAbsensi">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $selectedDate }}">
                        <input type="hidden" name="kelas"   value="{{ $selectedClass }}">

                        <div class="table-responsive">
                            <table class="absensi-table">
                                <thead>
                                    <tr>
                                        <th style="width:5%">No</th>
                                        @if($isDefaultView)
                                            <th style="width:18%">Kelas</th>
                                            <th style="width:37%">Nama Siswa</th>
                                        @else
                                            <th style="width:40%">Nama Siswa</th>
                                        @endif
                                        <th style="width:20%">Status Saat Ini</th>
                                        <th style="width:20%" class="text-center">Status Baru</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no    = 1;
                                        $stats = ['Hadir'=>0,'Izin'=>0,'Sakit'=>0,'Alpa'=>0,'belum'=>0];
                                        $badgeMap = [
                                            'Hadir' => 'badge-hadir',
                                            'Izin'  => 'badge-izin',
                                            'Sakit' => 'badge-sakit',
                                            'Alpa'  => 'badge-alpa',
                                        ];
                                    @endphp
                                    @foreach($absensiList as $a)
                                        @php
                                            if ($a['is_recorded']) {
                                                $s = $a['status_absensi'];
                                                if (isset($stats[$s])) $stats[$s]++;
                                            } else {
                                                $stats['belum']++;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            @if($isDefaultView)
                                                <td>
                                                    <span class="kelas-badge" style="font-size:11px;padding:3px 9px;">
                                                        {{ $a['kelas_nama'] ?? '' }}
                                                    </span>
                                                </td>
                                            @endif
                                            <td class="fw-semibold">{{ htmlspecialchars($a['nama_siswa'] ?? 'N/A') }}</td>
                                            <td>
                                                @if($a['is_recorded'])
                                                    <span class="status-badge {{ $badgeMap[$a['status_absensi']] ?? 'badge-secondary' }}">
                                                        {{ $a['status_absensi'] }}
                                                    </span>
                                                @else
                                                    <span class="status-badge badge-secondary">Belum diabsen</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden"
                                                       name="absensi[{{ $a['id_siswa'] }}][id_siswa]"
                                                       value="{{ $a['id_siswa'] }}">
                                                @if($a['is_recorded'])
                                                    <span class="locked-text">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                        </svg>
                                                        Terkunci
                                                    </span>
                                                @else
                                                    <select name="absensi[{{ $a['id_siswa'] }}][status]"
                                                            class="status-select"
                                                            onchange="updateStatusColor(this)">
                                                        <option value="Hadir">Hadir</option>
                                                        <option value="Izin">Izin</option>
                                                        <option value="Sakit">Sakit</option>
                                                        <option value="Alpa">Alpa</option>
                                                    </select>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Statistik (hanya saat kelas spesifik dipilih) --}}
                        @if(!$isDefaultView)
                        <div class="stats-grid mt-4">
                            <div class="stat-item stat-hadir">
                                <span class="stat-dot"></span>
                                <span class="stat-label">Hadir</span>
                                <span class="stat-count">{{ $stats['Hadir'] }}</span>
                            </div>
                            <div class="stat-item stat-izin">
                                <span class="stat-dot"></span>
                                <span class="stat-label">Izin</span>
                                <span class="stat-count">{{ $stats['Izin'] }}</span>
                            </div>
                            <div class="stat-item stat-sakit">
                                <span class="stat-dot"></span>
                                <span class="stat-label">Sakit</span>
                                <span class="stat-count">{{ $stats['Sakit'] }}</span>
                            </div>
                            <div class="stat-item stat-alpa">
                                <span class="stat-dot"></span>
                                <span class="stat-label">Alpa</span>
                                <span class="stat-count">{{ $stats['Alpa'] }}</span>
                            </div>
                        </div>
                        @endif

                        @if($stats['belum'] > 0 && !$isDefaultView)
                        <div class="warning-bar mt-3">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                             <strong>{{ $stats['belum'] }} </strong> 
                        </div>
                        @endif

                        {{-- Tombol simpan massal (default view) --}}
                        @if($isDefaultView)
                        <div class="warning-bar mt-3" style="background:rgba(139,92,246,0.08);border-color:rgba(139,92,246,0.2);color:#c4b5fd;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            Untuk menyimpan absensi, pilih kelas spesifik terlebih dahulu.
                        </div>
                        @endif
                    </form>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Modal Export PDF --}}
<div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Export PDF Absensi</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-field">
                            <label class="field-label">Kelas <span class="required">*</span></label>
                            <select id="exportKelas" class="field-input field-select">
                                <option value="">— Pilih Kelas —</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k }}" {{ $selectedClass === $k ? 'selected' : '' }}>{{ $k }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-field">
                            <label class="field-label">Filter Periode</label>
                            <select id="exportFilter" class="field-input field-select" onchange="updatePeriodeInfo()">
                                <option value="hari">Per Hari</option>
                                <option value="minggu">Per Minggu</option>
                                <option value="bulan">Per Bulan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-field">
                            <label class="field-label">Tanggal <span class="required">*</span></label>
                            <input type="date" id="exportTanggal" class="field-input"
                                   value="{{ $selectedDate }}"
                                   min="{{ $minDate }}" max="{{ $maxDate }}"
                                   onchange="updatePeriodeInfo()">
                        </div>
                    </div>
                    <div class="col-12" id="periodeInfo" style="display:none;">
                        <div class="info-bar">
                            <span id="periodeText"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-save" id="btnExport" onclick="exportPDF()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin/sidebar.js') }}"></script>
<script>
    window.absensiConfig = {
    simpanUrl: "{{ route('guru.absensi.simpan') }}",
    exportUrl: "{{ route('guru.absensi.export') }}",
    csrf: "{{ csrf_token() }}",
    kelasGuru: @json($kelasGuru),
    isDefaultView: @json($isDefaultView),
};
</script>
<script src="{{ asset('js/admin/absensi.js') }}"></script>
@endsection
