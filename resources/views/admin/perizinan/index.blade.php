@extends('layouts.admin')

@section('title', 'Perizinan | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/perizinan.css') }}">
@endsection

@section('content')
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('admin.partials.sidebar')

    <div class="flex-grow-1 main-content perizinan-bg">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <div class="d-flex align-items-center gap-3">
                    <div class="page-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="fw-bold m-0 page-title">Perizinan</h4>
                        <p class="page-subtitle m-0">Kelola pengajuan izin siswa</p>
                    </div>
                </div>
                @include('admin.partials.profile')
            </div>

            {{-- Card Tabel --}}
            <div class="perizinan-card">

                {{-- Toolbar --}}
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="table-title m-0">
                            Daftar Perizinan Murid
                        </h5>
                        <span class="total-badge">{{ count($perizinanList) }} Pengajuan</span>
                    </div>
                    <div class="search-wrapper">
                        <svg class="search-icon-svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" id="searchInput" class="search-input"
                               placeholder="Cari nama siswa...">
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="table-responsive">
                    <table class="perizinan-table" id="perizinanTable">
                        <thead>
                            <tr>
                                <th style="width:4%">No</th>
                                <th style="width:18%">Nama Murid</th>
                                <th style="width:8%">Kelas</th>
                                <th style="width:12%">Jenis Izin</th>
                                <th style="width:16%">Tanggal</th>
                                <th style="width:20%">Keterangan</th>
                                <th style="width:10%">Status</th>
                                <th style="width:12%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($perizinanList))
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon mb-3">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                            </div>
                                            <p class="mb-0">Tidak ada data perizinan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @php $no = 1; @endphp
                                @foreach($perizinanList as $izin)
                                    @php $status = $izin['status'] ?? 'Menunggu'; @endphp
                                    <tr class="izin-item" data-id="{{ $izin['id_izin'] ?? '' }}">
                                        <td>{{ $no++ }}</td>
                                        <td class="fw-semibold">{{ htmlspecialchars($izin['nama_siswa'] ?? 'N/A') }}</td>
                                        <td>
                                            <span class="kelas-chip">{{ htmlspecialchars($izin['kelas'] ?? 'N/A') }}</span>
                                        </td>
                                        <td>{{ htmlspecialchars($izin['jenis_izin'] ?? 'N/A') }}</td>
                                        <td>
                                            <span class="d-block">{{ htmlspecialchars($izin['tanggal_range'] ?? '-') }}</span>
                                            <span style="font-size:11.5px; color:#a78bfa;">
                                                Diajukan: {{ htmlspecialchars($izin['tanggal_pengajuan'] ?? '-') }}
                                            </span>
                                        </td>
                                        <td style="font-size:13px; color:#e2e8f0;">
                                            {{ htmlspecialchars($izin['keterangan'] ?? '-') }}
                                        </td>
                                        <td>
                                            @if($status === 'Disetujui')
                                                <span class="status-badge badge-disetujui">Disetujui</span>
                                            @elseif($status === 'Ditolak')
                                                <span class="status-badge badge-ditolak">Ditolak</span>
                                            @else
                                                <span class="status-badge badge-menunggu">Menunggu</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($status === 'Menunggu')
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <button class="btn-aksi btn-setujui"
                                                            data-id="{{ $izin['id_izin'] ?? '' }}"
                                                            data-nama="{{ htmlspecialchars($izin['nama_siswa'] ?? 'N/A') }}"
                                                            data-kelas="{{ htmlspecialchars($izin['kelas'] ?? 'N/A') }}"
                                                            data-jenis="{{ htmlspecialchars($izin['jenis_izin'] ?? 'N/A') }}"
                                                            data-tanggal="{{ htmlspecialchars($izin['tanggal_range'] ?? '-') }}">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                            <polyline points="20 6 9 17 4 12"/>
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                    <button class="btn-aksi btn-tolak"
                                                            data-id="{{ $izin['id_izin'] ?? '' }}">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted" style="font-size:12px;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Konfirmasi Setujui --}}
<div class="modal fade" id="modalSetujui" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon modal-icon-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Konfirmasi Persetujuan Izin</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <p class="mb-3" style="color:#94a3b8;">Apakah Anda yakin ingin menyetujui izin berikut?</p>
                <div class="detail-card">
                    <div class="detail-row">
                        <span class="detail-label">Nama Siswa</span>
                        <span class="detail-value" id="setujuiNama">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Kelas</span>
                        <span class="detail-value" id="setujuiKelas">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Jenis Izin</span>
                        <span class="detail-value" id="setujuiJenis">—</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tanggal</span>
                        <span class="detail-value" id="setujuiTanggal">—</span>
                    </div>
                </div>
                <div class="info-note mt-3">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Orang tua siswa akan menerima notifikasi. Aksi ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-approve" id="btnKonfirmasiSetujui">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Alasan Tolak --}}
<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header danger">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon modal-icon-danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Alasan Penolakan Izin</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <div class="form-field">
                    <label class="field-label">
                        Alasan Penolakan <span class="required">*</span>
                    </label>
                    <textarea class="field-input" id="alasanTolak" rows="4"
                              placeholder="Contoh: Dokumen tidak lengkap, format tidak sesuai..."></textarea>
                    <span class="field-hint">Alasan ini akan dikirimkan ke orang tua siswa.</span>
                </div>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-reject" id="btnKonfirmasiTolak">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Tolak Izin
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
window.perizinanConfig = {
    updateUrl: "{{ route('admin.perizinan.update') }}",
    csrf:      "{{ csrf_token() }}",
    userId:    {{ Session::get('id_akun', 0) }},
};
</script>
<script src="{{ asset('js/admin/perizinan.js') }}"></script>
@endsection
