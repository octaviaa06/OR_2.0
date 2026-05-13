@extends('layouts.guru')

@section('title', 'Kalender | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/guru/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/guru/kalender.css') }}">
@endsection

@section('content')
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('guru.partials.sidebar')

    <div class="flex-grow-1 main-content kalender-bg">
        <div class="container-fluid py-3 py-md-4">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <div class="d-flex align-items-center gap-3">
                    <div class="page-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8"  y1="2" x2="8"  y2="6"/>
                            <line x1="3"  y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="fw-bold m-0 page-title">Kalender</h4>
                        <p class="page-subtitle m-0">Kelola agenda kegiatan sekolah</p>
                    </div>
                </div>
                @include('guru.partials.profile')
            </div>


            <div class="row g-4">

                {{-- Kalender --}}
                <div class="col-lg-6 col-md-12">
                    <div class="kalender-card">
                        {{-- Navigasi bulan --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="kalender-month-title">{{ $monthNameId }} {{ $year }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('guru.kalender.index', ['month' => $month, 'year' => $year, 'nav' => 'prev', 'day' => $selectedDay]) }}"
                                   class="nav-arrow" title="Bulan Sebelumnya">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                </a>
                                <a href="{{ route('guru.kalender.index', ['month' => $month, 'year' => $year, 'nav' => 'next', 'day' => $selectedDay]) }}"
                                   class="nav-arrow" title="Bulan Berikutnya">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Grid kalender --}}
                        <div class="kalender-grid">
                            @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $i => $hari)
                                <div class="hari-header {{ $i === 0 ? 'minggu' : '' }}">{{ $hari }}</div>
                            @endforeach

                            {{-- Sel kosong awal --}}
                            @for($i = 0; $i < $dayOfWeek; $i++)
                                <div class="tanggal-kosong"></div>
                            @endfor

                            {{-- Tanggal --}}
                            @for($day = 1; $day <= $numberOfDays; $day++)
                                @php
                                    $dateStr   = date('Y-m-d', mktime(0,0,0,$month,$day,$year));
                                    $isToday   = $dateStr === date('Y-m-d');
                                    $isSelected = $day === $selectedDay;
                                    $hasAgenda  = isset($agendaByDate[$dateStr]);
                                    $isMinggu   = ($dayOfWeek + $day - 1) % 7 === 0;
                                    $isLibur    = isset($hariLiburBulanIni[$dateStr]);

                                    $cls = 'tanggal-item';
                                    if ($isMinggu)   $cls .= ' minggu';
                                    if ($isLibur)    $cls .= ' hari-libur';
                                    if ($isToday)    $cls .= ' today';
                                    if ($isSelected) $cls .= ' selected-day';
                                    if ($hasAgenda)  $cls .= ' has-agenda';
                                @endphp
                                <a href="{{ route('guru.kalender.index', ['month' => $month, 'year' => $year, 'day' => $day]) }}"
                                   class="{{ $cls }}" data-date="{{ $dateStr }}"
                                   @if($isLibur) title="{{ $hariLiburBulanIni[$dateStr] }}" @endif>
                                    <span>{{ $day }}</span>
                                    @if($isLibur)<span class="libur-dot"></span>@endif
                                </a>
                            @endfor

                            {{-- Sel kosong akhir --}}
                            @php $cellsAfter = ($dayOfWeek + $numberOfDays) % 7; @endphp
                            @if($cellsAfter > 0)
                                @for($i = $cellsAfter; $i < 7; $i++)
                                    <div class="tanggal-kosong"></div>
                                @endfor
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Daftar Agenda --}}
                <div class="col-lg-6 col-md-12">
                    <div class="agenda-card">
                        <h5 class="agenda-card-title">Agenda Kegiatan</h5>
                        <p class="agenda-card-date">
                            Tanggal:
                            <strong>{{ \Carbon\Carbon::parse($selectedDateFull)->translatedFormat('j F Y') }}</strong>
                        </p>

                        <div id="daftarAgendaContent">
                            @if($selectedHariLibur)
                                <div class="hari-libur-banner">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <span>Hari Libur Nasional: <strong>{{ $selectedHariLibur }}</strong></span>
                                </div>
                            @endif
                            @if(empty($selectedAgenda))
                                <div class="agenda-empty">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8"  y1="2" x2="8"  y2="6"/>
                                        <line x1="3"  y1="10" x2="21" y2="10"/>
                                        <line x1="10" y1="15" x2="14" y2="15"/>
                                    </svg>
                                    <p class="mb-0 mt-2">Tidak ada agenda pada tanggal ini.</p>
                                </div>
                            @else
                                @foreach($selectedAgenda as $kegiatan)
                                    <div class="kegiatan-item"
                                         onclick="lihatDetailAgenda({{ json_encode($kegiatan) }})">
                                        <div class="flex-grow-1">
                                            <p class="kegiatan-nama">{{ htmlspecialchars($kegiatan['nama_kegiatan'] ?? 'Kegiatan Tanpa Nama') }}</p>
                                            <small class="kegiatan-tanggal">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                                                    <line x1="3"  y1="10" x2="21" y2="10"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($kegiatan['tanggal'] ?? $selectedDateFull)->translatedFormat('j F Y') }}
                                            </small>
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

{{-- Modal Detail Agenda --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Detail Agenda</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <div class="detail-card">
                    <div class="detail-row">
                        <span class="detail-label">Nama Kegiatan</span>
                        <span class="detail-value" id="detailNama">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tanggal</span>
                        <span class="detail-value" id="detailTanggal">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Deskripsi</span>
                        <span class="detail-value" id="detailDeskripsi">—</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Sukses --}}
<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-modal">
            <div class="modal-body glass-modal-body text-center py-4">
                <div class="sukses-icon mb-3">
                    <svg width="60" height="60" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="38" fill="none" stroke="rgba(34,197,94,0.3)" stroke-width="3"/>
                        <path fill="none" stroke="#22c55e" stroke-width="4" stroke-linecap="round" d="M20,42 L32,54 L60,26"/>
                    </svg>
                </div>
                <h5 class="fw-bold mb-2" style="color:#f1f5f9;" id="suksesTitle">Berhasil!</h5>
                <p style="color:#94a3b8;" id="suksesMessage">Operasi berhasil dilakukan.</p>
                <button type="button" class="btn-modal-save" data-bs-dismiss="modal" style="margin:0 auto;">OK</button>
            </div>
        </div>
    </div>
</div>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/guru/sidebar.js') }}"></script>
<script>
    // ===== 1. KONFIGURASI WAJIB =====
window.kalenderConfig = {
    storeUrl: "{{ route('guru.kalender.store') }}",

    // URL update dengan placeholder ID
    updateUrl: "{{ route('guru.kalender.update', ['id' => '__ID__']) }}",

    destroyUrl: "{{ route('guru.kalender.destroy') }}",
    showUrl: "{{ route('guru.kalender.show') }}",

    csrf: "{{ csrf_token() }}",
    selectedDate: "{{ $selectedDateFull ?? date('Y-m-d') }}",
};

    // ===== 2. FUNGSI AUTO-OPEN MODAL (ROBUST) =====
    function checkAutoOpenModal() {
        const params = new URLSearchParams(window.location.search);
        
        // Cek jika parameter openModal=true ada
        if (params.get('openModal') === 'true') {
            // Bersihkan URL agar tidak reopen saat refresh (opsional tapi disarankan)
            params.delete('openModal');
            const newUrl = window.location.pathname + 
                          (params.toString() ? '?' + params.toString() : '') + 
                          window.location.hash;
            window.history.replaceState({}, document.title, newUrl);

            // Coba buka modal
            const btn = document.getElementById('btnTambahAgenda');
            if (btn) {
                console.log('🚀 Auto-opening modal tambah agenda...');
                btn.click();
            } else {
                console.warn('⚠️ Tombol btnTambahAgenda tidak ditemukan, mungkin script dimuat terlalu cepat.');
            }
        }
    }

    // ===== 3. EKSEKUSI: Cek status DOM saat ini =====
    if (document.readyState === 'loading') {
        // DOM belum siap → tunggu event
        document.addEventListener('DOMContentLoaded', checkAutoOpenModal);
    } else {
        // DOM sudah siap → jalankan langsung
        checkAutoOpenModal();
    }
</script>
<script src="{{ asset('js/guru/kalender.js}"></script>
@endsection
