@extends('layouts.admin')

@section('title', 'Dashboard Admin | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/profil.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
@endsection

@section('content')
<!-- Toast Notification -->
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    <!-- Sidebar (includes hamburger, overlay, sidebar) -->
    @include('admin.partials.sidebar')

    <!-- Main Content -->
    <div class="flex-grow-1 main-content" 
         style="background-image: url('{{ asset('images/background/Dashboard Admin.png') }}'); 
                background-size: cover; 
                background-position: center;">
      <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
          <h4 class="fw-bold text-primary m-0 d-none d-md-block">Dashboard Admin</h4>
          <h5 class="fw-bold text-primary m-0 d-md-none">Dashboard Admin</h5>
          @include('admin.partials.profile')
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
          <div class="col-6 col-md-4">
            <div class="card stat-card shadow-sm">
              <div class="card-body stat-card-body p-4">
                <p class="stat-label">Jumlah Guru</p>
                <p class="stat-value">{{ $guru }}</p>
                <div class="stat-change"><span>↑</span><span>Aktif</span></div>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="card stat-card shadow-sm">
              <div class="card-body stat-card-body p-4">
                <p class="stat-label">Jumlah Siswa</p>
                <p class="stat-value">{{ $siswa }}</p>
                <div class="stat-change"><span>↑</span><span>Total</span></div>
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
              ['url' => '#', 'svg' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>', 'text' => 'Buat Akun Guru'],
              ['url' => '#', 'svg' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>', 'text' => 'Buat Akun Orang Tua'],
              ['url' => '#', 'svg' => '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>', 'text' => 'Buat Agenda'],
          ];
          @endphp
          
          @foreach($quickLinks as $link)
          <div class="col-6 col-md-4">
            <a href="{{ $link['url'] }}" class="text-decoration-none">
              <div class="card access-card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center w-100">
                  <div class="access-icon-svg">{!! $link['svg'] !!}</div>
                  <p class="access-text">{{ $link['text'] }}</p>
                </div>
              </div>
            </a>
          </div>
          @endforeach
        </div>

        <!-- Izin & Agenda Section -->
        <div class="row g-3">
          <!-- Izin Menunggu -->
          <div class="col-md-6">
            <div class="card border-primary shadow-sm h-100">
              <div class="card-body">
                <h6 class="text-primary d-flex align-items-center gap-2 mb-3">
                  <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                  </svg>
                  Izin Menunggu 
                  <span class="badge bg-primary" id="izinCounter">{{ count($izin) }}</span>
                </h6>
                <div class="border-top pt-2" id="izinContainer" style="max-height: 400px; overflow-y: auto;">
                  @if(empty($izin))
                    <p class="text-muted small mb-0">Tidak ada izin menunggu</p>
                  @else
                    @foreach($izin as $i)
                      @php
                        $idIzin = $i['id_izin'] ?? $i['id'] ?? $i['id_perizinan'] ?? 0;
                        $namaSiswa = $i['nama_siswa'] ?? $i['nama'] ?? $i['nama_lengkap'] ?? 'N/A';
                        $tglMulai = $i['tanggal_mulai'] ?? $i['tanggal'] ?? $i['tanggal_izin'] ?? '';
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
                  <svg class="section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                    <line x1="3"  y1="10" x2="21" y2="10"/>
                  </svg>
                  Agenda Terdekat
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

<!-- Modals -->
@include('admin.modals.izin-approve')
@include('admin.modals.izin-reject')

<!-- Logout Form (hidden) -->
<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="{{ asset('js/admin/sidebar.js') }}"></script>
<script src="{{ asset('js/admin/dashboard.js') }}"></script>
<script>
    // Pass data to JS
    window.adminConfig = {
        apiUrl: "{{ route('admin.izin.update') }}",
        refreshUrl: "{{ route('admin.izin.refresh') }}",
        userId: {{ Session::get('id_akun', 0) }},
        siswaMasuk: {{ $siswaMasukHariIni }},
        siswaTidakMasuk: {{ $siswaTidakMasuk }},
        totalSiswa: {{ $siswa }}
    };
</script>
@endsection