@extends('layouts.admin')

@section('title', 'Kalender | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/kalender.css') }}">
@endsection

@section('content')
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('admin.partials.sidebar')

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
                @include('admin.partials.profile')
            </div>

            {{-- Tombol Tambah Agenda --}}
            <button class="btn-tambah-agenda w-100 mb-4" id="btnTambahAgenda">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Agenda
            </button>

            <div class="row g-4">

                {{-- Kalender --}}
                <div class="col-lg-6 col-md-12">
                    <div class="kalender-card">
                        {{-- Navigasi bulan --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="kalender-month-title">{{ $monthNameId }} {{ $year }}</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.kalender.index', ['month' => $month, 'year' => $year, 'nav' => 'prev', 'day' => $selectedDay]) }}"
                                   class="nav-arrow" title="Bulan Sebelumnya">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.kalender.index', ['month' => $month, 'year' => $year, 'nav' => 'next', 'day' => $selectedDay]) }}"
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

                                    $cls = 'tanggal-item';
                                    if ($isMinggu)   $cls .= ' minggu';
                                    if ($isToday)    $cls .= ' today';
                                    if ($isSelected) $cls .= ' selected-day';
                                    if ($hasAgenda)  $cls .= ' has-agenda';
                                @endphp
                                <a href="{{ route('admin.kalender.index', ['month' => $month, 'year' => $year, 'day' => $day]) }}"
                                   class="{{ $cls }}" data-date="{{ $dateStr }}">
                                    <span>{{ $day }}</span>
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
                                        <div class="kegiatan-actions" onclick="event.stopPropagation()">
                                            <button class="btn-kegiatan btn-edit-kegiatan"
                                                    onclick="editAgenda({{ $kegiatan['id'] ?? 0 }})">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                <span class="d-none d-md-inline">Edit</span>
                                            </button>
                                            <button class="btn-kegiatan btn-hapus-kegiatan"
                                                    onclick="hapusAgenda({{ $kegiatan['id'] ?? 0 }})">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                                <span class="d-none d-md-inline">Hapus</span>
                                            </button>
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

{{-- Modal Tambah / Edit Agenda --}}
<div class="modal fade" id="modalAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8"  y1="2" x2="8"  y2="6"/>
                            <line x1="3"  y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0" id="agendaModalLabel">Tambah Agenda</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form id="formAgenda" novalidate>
                @csrf
                <input type="hidden" id="agendaId">
                <div class="modal-body glass-modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-field">
                                <label class="field-label">Nama Kegiatan <span class="required">*</span></label>
                                <input type="text" class="field-input" id="agendaNama"
                                       placeholder="Masukkan nama kegiatan">
                                <span class="field-error" id="namaError"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-field">
                                <label class="field-label">Tanggal <span class="required">*</span></label>
                                <input type="date" class="field-input" id="agendaTanggal">
                                <span class="field-error" id="tanggalError"></span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-field">
                                <label class="field-label">Deskripsi <span class="field-optional">(Opsional)</span></label>
                                <textarea class="field-input" id="agendaDeskripsi" rows="3"
                                          placeholder="Tambahkan deskripsi kegiatan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer glass-modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal-save" id="btnSimpanAgenda">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
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

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header danger">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon modal-icon-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Konfirmasi Hapus</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <p class="mb-0" style="color:#e2e8f0;">Apakah Anda yakin ingin menghapus agenda ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-danger" id="btnKonfirmasiHapus">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    </svg>
                    Ya, Hapus
                </button>
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
<script src="{{ asset('js/admin/sidebar.js') }}"></script>
<script>
window.kalenderConfig = {
    storeUrl:   "{{ route('admin.kalender.store') }}",
    updateUrl:  "{{ route('admin.kalender.update') }}",
    destroyUrl: "{{ route('admin.kalender.destroy') }}",
    showUrl:    "{{ route('admin.kalender.show') }}",
    csrf:       "{{ csrf_token() }}",
    selectedDate: "{{ $selectedDateFull }}",
};
</script>
<script src="{{ asset('js/admin/kalender.js') }}"></script>
@endsection
