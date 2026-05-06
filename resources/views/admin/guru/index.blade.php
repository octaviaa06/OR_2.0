@extends('layouts.admin')

@section('title', 'Data Guru | OrtuConnect')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/guru.css') }}">
@endsection

@section('content')
<div id="toast" role="alert" aria-live="polite"></div>

<div class="d-flex">
    @include('admin.partials.sidebar')

    <div class="flex-grow-1 main-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 header-fixed">
                <div class="d-flex align-items-center gap-3">
                    <div class="page-icon-wrapper">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="fw-bold m-0 page-title">Data Guru</h4>
                        <p class="page-subtitle m-0">Kelola data guru OrtuConnect</p>
                    </div>
                </div>
                @include('admin.partials.profile')
            </div>

            {{-- Toolbar --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="search-wrapper">
                    <svg class="search-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="searchInput" class="search-input"
                           placeholder="Cari nama, NIP, atau email...">
                </div>
                <button class="btn-add" id="btnTambahGuru">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Guru
                </button>
            </div>

            {{-- Grid Kartu Guru --}}
            <div class="row g-3" id="guruContainer">
                @if(empty($guruList))
                    <div class="col-12">
                        <div class="empty-state text-center">
                            <div class="empty-icon mb-4">
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <h5 class="mb-2">Tidak ada data guru</h5>
                            <p class="text-muted small">Silakan tambahkan data guru baru</p>
                        </div>
                    </div>
                @else
                    @foreach($guruList as $guru)
                        @php
                            $nama   = htmlspecialchars($guru['nama_guru'] ?? '');
                            $kata   = explode(' ', $nama);
                            $inisial = count($kata) >= 2
                                ? strtoupper(substr($kata[0],0,1) . substr($kata[1],0,1))
                                : strtoupper(substr($kata[0],0,2));
                        @endphp
                        <div class="col-md-4 guru-item" data-id="{{ $guru['id_guru'] }}">
                            <div class="guru-card">
                                {{-- Card Top --}}
                                <div class="guru-card-top">
                                    <div class="guru-avatar">{{ $inisial }}</div>
                                    <div class="guru-info-head">
                                        <h5 class="guru-name">{{ $nama }}</h5>
                                        <span class="guru-badge">NIP: {{ htmlspecialchars($guru['nip'] ?? '') }}</span>
                                    </div>
                                </div>

                                {{-- Card Body --}}
                                <div class="guru-card-body">
                                    <div class="info-row-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <span>{{ htmlspecialchars($guru['email'] ?? '') }}</span>
                                    </div>
                                    <div class="info-row-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.62 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span>{{ htmlspecialchars($guru['no_telp'] ?? '') }}</span>
                                    </div>
                                    <div class="info-row-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                        <span>Kelas {{ htmlspecialchars($guru['kelas'] ?? '') }}</span>
                                    </div>
                                    <div class="info-row-item">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span>{{ htmlspecialchars(Str::limit($guru['alamat'] ?? '', 40)) }}</span>
                                    </div>
                                </div>

                                {{-- Card Footer --}}
                                <div class="guru-card-footer">
                                    <button class="btn-akun btn-setujui-guru"
                                            data-id="{{ $guru['id_guru'] }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        Lihat Akun
                                    </button>
                                    <div class="d-flex gap-2">
                                        <button class="btn-icon btn-edit-guru" data-id="{{ $guru['id_guru'] }}"
                                                data-guru="{{ json_encode($guru) }}" title="Edit">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </button>
                                        <button class="btn-icon btn-hapus-guru" data-id="{{ $guru['id_guru'] }}"
                                                data-nama="{{ $nama }}" title="Hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>
    </div>
</div>

{{-- ===== MODAL TAMBAH / EDIT ===== --}}
<div class="modal fade" id="modalGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0" id="judulModalGuru">Tambah Guru Baru</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form id="formGuru" novalidate>
                @csrf
                <input type="hidden" id="id_guru" name="id_guru">
                <div class="modal-body glass-modal-body">
                    <div class="row g-4">
                        {{-- Nama --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="field-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" class="field-input" id="nama_guru" name="nama_guru"
                                       placeholder="Masukkan nama lengkap">
                                <span class="field-error" id="namaError"></span>
                            </div>
                        </div>
                        {{-- NIP --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="field-label">NIP <span class="required">*</span></label>
                                <input type="text" class="field-input" id="nip" name="nip"
                                       placeholder="8–18 digit angka" maxlength="18">
                                <span class="field-error" id="nipError"></span>
                                <span class="field-hint">Maksimal 18 digit</span>
                            </div>
                        </div>
                        {{-- Email --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="field-label">Email <span class="required">*</span></label>
                                <input type="email" class="field-input" id="email" name="email"
                                       placeholder="contoh@email.com">
                                <span class="field-error" id="emailError"></span>
                            </div>
                        </div>
                        {{-- Telepon --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="field-label">No. Telepon <span class="required">*</span></label>
                                <input type="text" class="field-input" id="no_telp" name="no_telp"
                                       placeholder="081234567890" maxlength="15">
                                <span class="field-error" id="telpError"></span>
                                <span class="field-hint">10–15 digit angka</span>
                            </div>
                        </div>
                        {{-- Kelas --}}
                        <div class="col-md-6">
                            <div class="form-field">
                                <label class="field-label">Kelas <span class="required">*</span></label>
                                <select class="field-input field-select" id="kelas" name="kelas">
                                    <option value="" disabled selected>-- Pilih Kelas --</option>
                                    <option value="A">Kelas A</option>
                                    <option value="B">Kelas B</option>
                                    <option value="C">Kelas C</option>
                                    <option value="D">Kelas D</option>
                                </select>
                                <span class="field-error" id="kelasError"></span>
                            </div>
                        </div>
                        {{-- Alamat --}}
                        <div class="col-12">
                            <div class="form-field">
                                <label class="field-label">Alamat <span class="required">*</span></label>
                                <textarea class="field-input" id="alamat" name="alamat" rows="3"
                                          placeholder="Masukkan alamat lengkap"></textarea>
                                <span class="field-error" id="alamatError"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer glass-modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-modal-save" id="btnSimpan">
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

{{-- ===== MODAL LIHAT AKUN ===== --}}
<div class="modal fade" id="modalAkun" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h5 class="modal-title m-0">Akun Guru</h5>
                </div>
                <button type="button" class="modal-close-btn" data-bs-dismiss="modal">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body glass-modal-body">
                <div class="akun-info">
                    <div class="akun-row"><span class="akun-label">Nama</span><span class="akun-value" id="akunNama">—</span></div>
                    <div class="akun-row"><span class="akun-label">Username</span><span class="akun-value" id="akunUsername">—</span></div>
                    <div class="akun-row"><span class="akun-label">Password</span><span class="akun-value" id="akunPassword">—</span></div>
                    <div class="akun-row"><span class="akun-label">Role</span><span class="akun-value" id="akunRole">—</span></div>
                </div>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button class="btn-modal-cancel" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL HAPUS ===== --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header glass-modal-header danger">
                <div class="d-flex align-items-center gap-2">
                    <div class="modal-icon danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
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
                <p class="mb-1">Apakah Anda yakin ingin menghapus data guru <strong id="namaHapus"></strong>?</p>
                <p class="text-muted small mb-0">Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
            </div>
            <div class="modal-footer glass-modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-modal-danger" id="btnKonfirmasiHapus">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    </svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Logout Form --}}
<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin/sidebar.js') }}"></script>
<script src="{{ asset('js/admin/guru.js') }}"></script>
<script>
window.guruRoutes = {
    store:   "{{ route('admin.guru.store') }}",
    update:  "{{ route('admin.guru.update') }}",
    destroy: "{{ route('admin.guru.destroy') }}",
    akun:    "{{ route('admin.guru.akun') }}",
    csrf:    "{{ csrf_token() }}"
};

// Auto-buka modal tambah guru jika dari akses cepat dashboard
if (new URLSearchParams(window.location.search).get('openModal') === 'true') {
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btnTambahGuru')?.click();
    });
}
</script>
@endsection
