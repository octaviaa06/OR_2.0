document.addEventListener('DOMContentLoaded', function () {
    const routes = window.siswaRoutes || {};
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': routes.csrf || '',
    };

    let idHapus = null;

    const modalSiswa = new bootstrap.Modal(document.getElementById('modalSiswa'));
    const modalAkun  = new bootstrap.Modal(document.getElementById('modalAkun'));
    const modalHapus = new bootstrap.Modal(document.getElementById('modalHapus'));

    // ===== TOAST =====
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `show ${type}`;
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    // ===== SEARCH =====
    document.getElementById('searchInput')?.addEventListener('input', function () {
        const kw = this.value.toLowerCase();
        document.querySelectorAll('.siswa-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(kw) ? '' : 'none';
        });
    });

    // ===== FILTER KELAS =====
    document.getElementById('filterKelas')?.addEventListener('change', function () {
        const val = this.value;
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set('kelas_filter', val);
        } else {
            url.searchParams.delete('kelas_filter');
        }
        window.location.href = url.toString();
    });

    // ===== VALIDATION =====
    function setError(fieldId, errId, msg) {
        const f = document.getElementById(fieldId);
        const e = document.getElementById(errId);
        if (!f || !e) return false;
        f.classList.add('is-invalid'); f.classList.remove('is-valid');
        e.textContent = msg; e.classList.add('show');
        return false;
    }
    function clearError(fieldId, errId) {
        const f = document.getElementById(fieldId);
        const e = document.getElementById(errId);
        if (!f || !e) return;
        f.classList.remove('is-invalid'); f.classList.add('is-valid');
        e.classList.remove('show');
    }
    function validate() {
        let ok = true;
        const today = new Date().toISOString().split('T')[0];

        const nama      = document.getElementById('nama_siswa')?.value.trim();
        const kelas     = document.getElementById('kelas')?.value;
        const tgl       = document.getElementById('tanggal_lahir')?.value;
        const gender    = document.getElementById('gender')?.value;
        const namaOrtu  = document.getElementById('nama_ortu')?.value.trim();
        const telpOrtu  = document.getElementById('no_telp_ortu')?.value.trim();
        const alamat    = document.getElementById('alamat')?.value.trim();

        if (!nama || nama.length < 3)
            { setError('nama_siswa','namaSiswaError','Nama minimal 3 karakter'); ok = false; }
        else clearError('nama_siswa','namaSiswaError');

        if (!kelas)
            { setError('kelas','kelasError','Pilih kelas terlebih dahulu'); ok = false; }
        else clearError('kelas','kelasError');

        if (!tgl)
            { setError('tanggal_lahir','tglError','Tanggal lahir wajib diisi'); ok = false; }
        else if (tgl > today)
            { setError('tanggal_lahir','tglError','Tanggal lahir tidak boleh melebihi hari ini'); ok = false; }
        else clearError('tanggal_lahir','tglError');

        if (!gender)
            { setError('gender','genderError','Pilih jenis kelamin'); ok = false; }
        else clearError('gender','genderError');

        if (!namaOrtu || namaOrtu.length < 3)
            { setError('nama_ortu','namaOrtuError','Nama orang tua minimal 3 karakter'); ok = false; }
        else clearError('nama_ortu','namaOrtuError');

        if (!telpOrtu || !/^\d{10,15}$/.test(telpOrtu))
            { setError('no_telp_ortu','telpOrtuError','Nomor telepon 10–15 digit angka'); ok = false; }
        else clearError('no_telp_ortu','telpOrtuError');

        // alamat is nullable — no error needed
        if (alamat !== undefined) clearError('alamat','alamatError');

        return ok;
    }

    // Real-time validation
    [
        ['nama_siswa','namaSiswaError'],
        ['kelas','kelasError'],
        ['tanggal_lahir','tglError'],
        ['gender','genderError'],
        ['nama_ortu','namaOrtuError'],
        ['no_telp_ortu','telpOrtuError'],
        ['alamat','alamatError'],
    ].forEach(([fid, eid]) => {
        document.getElementById(fid)?.addEventListener('input', () => {
            if (document.getElementById(fid)?.value.trim()) clearError(fid, eid);
        });
        document.getElementById(fid)?.addEventListener('change', () => {
            if (document.getElementById(fid)?.value) clearError(fid, eid);
        });
    });

    // no_telp_ortu: angka only
    document.getElementById('no_telp_ortu')?.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    // ===== RESET MODAL =====
    document.getElementById('modalSiswa')?.addEventListener('hidden.bs.modal', () => {
        document.getElementById('formSiswa')?.reset();
        document.getElementById('id_siswa').value = '';
        document.querySelectorAll('.field-input').forEach(el => el.classList.remove('is-invalid','is-valid'));
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
    });

    // ===== TAMBAH =====
    document.getElementById('btnTambahSiswa')?.addEventListener('click', () => {
        document.getElementById('judulModalSiswa').textContent = 'Tambah Murid Baru';
        modalSiswa.show();
    });

    // ===== EDIT =====
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-siswa');
        if (!btn) return;
        const siswa = JSON.parse(btn.dataset.siswa || '{}');
        document.getElementById('judulModalSiswa').textContent = 'Edit Data Murid';
        document.getElementById('id_siswa').value        = siswa.id_siswa || '';
        document.getElementById('nama_siswa').value      = siswa.nama_siswa || '';
        document.getElementById('kelas').value           = siswa.kelas || '';
        document.getElementById('tanggal_lahir').value   = siswa.tanggal_lahir || '';
        document.getElementById('gender').value          = siswa.gender || '';
        document.getElementById('nama_ortu').value       = siswa.nama_ortu || '';
        document.getElementById('no_telp_ortu').value    = siswa.no_telp_ortu || '';
        document.getElementById('alamat').value          = siswa.alamat || '';
        modalSiswa.show();
    });

    // ===== SUBMIT FORM =====
    document.getElementById('formSiswa')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!validate()) return;

        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        const id = document.getElementById('id_siswa').value.trim();
        const kelasRaw = document.getElementById('kelas').value;
        // Normalisasi kelas: hapus prefix "Kelas " jika ada
        const kelas = kelasRaw.replace(/^kelas\s+/i, '').toUpperCase();

        const payload = {
            id_siswa:      id ? parseInt(id) : undefined,
            nama_siswa:    document.getElementById('nama_siswa').value.trim(),
            kelas:         kelas,
            tanggal_lahir: document.getElementById('tanggal_lahir').value,
            gender:        document.getElementById('gender').value,
            nama_ortu:     document.getElementById('nama_ortu').value.trim(),
            no_telp_ortu:  document.getElementById('no_telp_ortu').value.trim(),
            alamat:        document.getElementById('alamat').value.trim(),
        };
        if (!payload.id_siswa) delete payload.id_siswa;

        try {
            const res  = await fetch(id ? routes.update : routes.store, {
                method:  id ? 'PUT' : 'POST',
                headers, body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                modalSiswa.hide();
                showToast(data.message || 'Berhasil disimpan', 'success');
                // Reload ke filter kelas yang baru agar siswa langsung tampil di kelasnya
                setTimeout(() => {
                    const url = new URL(window.location.href);
                    if (payload.kelas) {
                        url.searchParams.set('kelas_filter', payload.kelas);
                    }
                    window.location.href = url.toString();
                }, 1200);
            } else {
                showToast(data.message || 'Gagal menyimpan', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Simpan';
        }
    });

    // ===== HAPUS =====
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-hapus-siswa');
        if (!btn) return;
        idHapus = btn.dataset.id;
        document.getElementById('namaHapus').textContent = btn.dataset.nama || '';
        modalHapus.show();
    });

    document.getElementById('btnKonfirmasiHapus')?.addEventListener('click', async () => {
        if (!idHapus) return;
        try {
            const res  = await fetch(routes.destroy, {
                method: 'DELETE', headers,
                body: JSON.stringify({ id_siswa: parseInt(idHapus) }),
            });
            const data = await res.json();
            modalHapus.hide();
            if (data.success) {
                showToast(data.message || 'Berhasil dihapus', 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.message || 'Gagal menghapus', 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        } finally {
            idHapus = null;
        }
    });

    // ===== LIHAT AKUN =====
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-akun-siswa');
        if (!btn) return;
        const id = btn.dataset.id;
        btn.disabled = true;
        try {
            const res  = await fetch(`${routes.akun}?id_siswa=${id}`, { headers });
            const data = await res.json();
            if (data.success) {
                const d = data.data;
                document.getElementById('akunNama').textContent     = d.nama     || '—';
                document.getElementById('akunUsername').textContent = d.username || '—';
                document.getElementById('akunPassword').textContent = d.password || '—';
                document.getElementById('akunRole').textContent     = d.role     || '—';
                modalAkun.show();
            } else {
                showToast(data.message || 'Gagal memuat akun', 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
        }
    });
});

// ===== QUICK STATS CALCULATION ✨ =====
function updateQuickStats() {
    const items = document.querySelectorAll('.siswa-item');
    let total = 0, laki = 0, perempuan = 0;
    
    items.forEach(item => {
        // Hanya hitung yang visible (tidak di-filter)
        if (item.style.display !== 'none') {
            total++;
            const gender = item.dataset.gender?.toLowerCase();
            if (gender === 'laki-laki') laki++;
            else if (gender === 'perempuan') perempuan++;
        }
    });
    
    // Update DOM dengan animasi counting
    animateCount('statTotal', total);
    animateCount('statLaki', laki);
    animateCount('statPerempuan', perempuan);
}

// Helper: Animate number counting
function animateCount(elementId, target) {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    const current = parseInt(el.textContent) || 0;
    if (current === target) return;
    
    const duration = 400;
    const start = performance.now();
    
    function step(now) {
        const progress = Math.min((now - start) / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(current + (target - current) * easeOut);
        el.textContent = value;
        
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// Update stats when search/filter changes
document.getElementById('searchInput')?.addEventListener('input', updateQuickStats);
document.getElementById('filterKelas')?.addEventListener('change', () => {
    setTimeout(updateQuickStats, 100); // Delay untuk DOM update
});

// Initial call
document.addEventListener('DOMContentLoaded', updateQuickStats);