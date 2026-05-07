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
        
        <!-- ================= VISUAL EFFECTS START ================= -->
        <div class="admin-bg-grid"></div>
        <div class="admin-star-dust"></div>

        <div class="floating-icons-wrapper">
            <!-- Layer Belakang -->
            <div class="parallax-layer layer-back">
                <img src="{{ asset('images/icon/abc-block.png') }}" class="float-icon" style="--x: 15%; --y: 20%; --depth: 0.2;">
                <img src="{{ asset('images/icon/car.png') }}" class="float-icon" style="--x: 85%; --y: 30%; --depth: 0.3;">
                <img src="{{ asset('images/icon/toys.png') }}" class="float-icon" style="--x: 40%; --y: 80%; --depth: 0.2;">
            </div>
            <!-- Layer Tengah -->
            <div class="parallax-layer layer-mid">
                <img src="{{ asset('images/icon/animal.png') }}" class="float-icon" style="--x: 10%; --y: 45%; --depth: 0.5;">
                <img src="{{ asset('images/icon/lollipop.png') }}" class="float-icon" style="--x: 90%; --y: 60%; --depth: 0.6;">
                <img src="{{ asset('images/icon/rubber-duck.png') }}" class="float-icon" style="--x: 25%; --y: 75%; --depth: 0.5;">
            </div>
            <!-- Layer Depan -->
            <div class="parallax-layer layer-front">
                <img src="{{ asset('images/icon/car.png') }}" class="float-icon" style="--x: 5%; --y: 85%; --depth: 1.2;">
                <img src="{{ asset('images/icon/toys.png') }}" class="float-icon" style="--x: 95%; --y: 10%; --depth: 1.4;">
            </div>
        </div>

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
        <!-- ================= VISUAL EFFECTS END ================= -->

      <div class="container-fluid" style="position: relative; z-index: 10;">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
          <h4 class="fw-bold text-primary m-0 d-none d-md-block">Dashboard Admin</h4>
          <h5 class="fw-bold text-primary m-0 d-md-none">Dashboard Admin</h5>
          @include('admin.partials.profile')
        </div>

        <!-- ================= STATS ROW (3 COLUMNS) ================= -->
        <div class="row g-3 mb-4">
          
          <!-- 1. Jumlah Guru -->
          <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
              <div class="card-body stat-card-body p-4">
                <p class="stat-label">Jumlah Guru</p>
                <p class="stat-value">{{ $guru }}</p>
                <div class="stat-change"><span>↑</span><span>Aktif</span></div>
              </div>
            </div>
          </div>

          <!-- 2. Jumlah Siswa -->
          <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
              <div class="card-body stat-card-body p-4">
                <p class="stat-label">Jumlah Siswa</p>
                <p class="stat-value">{{ $siswa }}</p>
                <div class="stat-change"><span>↑</span><span>Total</span></div>
              </div>
            </div>
          </div>

          <!-- 3. Siswa Masuk Hari Ini (Circular Progress) -->
          <div class="col-12 col-md-4">
            <div class="card stat-card shadow-sm">
              <div class="card-body stat-card-body p-3">
                <p class="stat-label mb-2" style="font-size: 0.75rem; margin-bottom: 10px;">Siswa Masuk Hari Ini</p>
                
                <!-- Circular Progress Container -->
                <div class="circular-progress-wrapper">
                    <div class="circular-progress">
                        <svg class="progress-ring" width="200" height="200" viewBox="0 0 200 200">
                            <!-- Background Circle -->
                            <circle class="progress-ring__circle-bg" 
                                    stroke="rgba(255,255,255,0.05)" 
                                    stroke-width="12"
                                    fill="transparent"
                                    r="90" 
                                    cx="100" 
                                    cy="100"/>
                            
                            <!-- Gradient Definition -->
                            <defs>
                                <linearGradient id="gradientColors" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#8b5cf6" />
                                    <stop offset="50%" stop-color="#d946ef" />
                                    <stop offset="100%" stop-color="#22c55e" />
                                </linearGradient>
                            </defs>
                            
                            <!-- Progress Circle -->
                            <circle class="progress-ring__circle" 
                                    stroke="url(#gradientColors)" 
                                    stroke-width="12"
                                    stroke-linecap="round"
                                    fill="transparent"
                                    r="90" 
                                    cx="100" 
                                    cy="100"
                                    stroke-dasharray="565.48"
                                    stroke-dashoffset="565.48"/>
                        </svg>
                        
                        <!-- Center Content -->
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
        <!-- End Stats Row -->

        <!-- Quick Access -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold section-heading mb-0">Akses Cepat</h5>
        </div>
        <div class="row g-3 mb-4">
          @php
          $quickLinks = [
              [
                'url'      => route('admin.guru.index') . '?openModal=true',
                'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="17" y1="11" x2="23" y2="11"/></svg>',
                'text'     => 'Buat Akun Guru',
                'sub'      => 'Tambah pengajar baru',
                'color'    => 'purple',
              ],
              [
                'url'      => route('admin.siswa.index') . '?openModal=true',
                'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
                'text'     => 'Buat Akun Orang Tua',
                'sub'      => 'Hubungkan keluarga',
                'color'    => 'cyan',
              ],
              [
                'url'      => route('admin.kalender.index') . '?openModal=true',
                'svg'      => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="15" x2="12" y2="15" stroke-width="3"/></svg>',
                'text'     => 'Buat Agenda',
                'sub'      => 'Jadwalkan kegiatan',
                'color'    => 'pink',
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

        <!-- ================= IZIN & AGENDA (ORIGINAL LOGIC - UNCHANGED) ================= -->
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
                    <polyline points="10 9 9 9 8 9"/>
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
                      $idIzin     = $i['id_izin'] ?? $i['id'] ?? $i['id_perizinan'] ?? 0;
                      $namaSiswa  = $i['nama_siswa'] ?? $i['nama'] ?? $i['nama_lengkap'] ?? 'N/A';
                      $tglMulai   = $i['tanggal_mulai'] ?? $i['tanggal'] ?? $i['tanggal_izin'] ?? '';
                      $tglSelesai = $i['tanggal_selesai'] ?? $i['tanggal_akhir'] ?? '';
                      $jenisIzin  = $i['jenis_izin'] ?? 'Izin';
                      $keterangan = $i['keterangan'] ?? '';

                      $durasi = '';
                      if (!empty($tglMulai) && !empty($tglSelesai) && $tglSelesai !== $tglMulai) {
                          $diff = (strtotime($tglSelesai) - strtotime($tglMulai)) / 86400 + 1;
                          $durasi = $diff . ' Hari';
                      }

                      $kata    = explode(' ', $namaSiswa);
                      $inisial = count($kata) >= 2
                          ? strtoupper(substr($kata[0],0,1) . substr($kata[1],0,1))
                          : strtoupper(substr($kata[0],0,2));
                    @endphp
                    <div class="izin-row izin-item" data-id="{{ (int)$idIzin }}">
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
                        $tgl      = $a['tanggal'] ?? '';
                        $waktu    = $a['waktu_mulai'] ?? '';
                        $dotColors = ['purple', 'cyan', 'gray'];
                        $dot      = $dotColors[$idx % 3];

                        $labelTgl = '';
                        if ($tgl) {
                            $ts = strtotime($tgl);
                            $today = strtotime(date('Y-m-d'));
                            $diff  = ($ts - $today) / 86400;
                            if ($diff == 0)      $labelTgl = 'HARI INI';
                            elseif ($diff == 1)  $labelTgl = 'BESOK';
                            else                 $labelTgl = strtoupper(date('j M', $ts));
                            if ($waktu) $labelTgl .= ', ' . date('H:i', strtotime($waktu)) . ' AM';
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
        <!-- End Izin & Agenda -->

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