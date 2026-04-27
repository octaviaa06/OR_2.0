document.addEventListener('DOMContentLoaded', function () {
    const routes = window.guruRoutes || {};
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': routes.csrf || '',
    };

    let idHapus = null;

    const modalGuru  = new bootstrap.Modal(document.getElementById('modalGuru'));
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
        document.querySelectorAll('.guru-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(kw) ? '' : 'none';
        });
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
        const nama  = document.getElementById('nama_guru')?.value.trim();
        const nip   = document.getElementById('nip')?.value.trim();
        const email = document.getElementById('email')?.value.trim();
        const telp  = document.getElementById('no_telp')?.value.trim();
        const kelas = document.getElementById('kelas')?.value;
        const alamat = document.getElementById('alamat')?.value.trim();

        if (!nama || nama.length < 3)          { setError('nama_guru','namaError','Nama minimal 3 karakter'); ok = false; } else clearError('nama_guru','namaError');
        if (!nip || !/^\d{8,18}$/.test(nip))   { setError('nip','nipError','NIP harus 8–18 digit angka'); ok = false; } else clearError('nip','nipError');
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('email','emailError','Format email tidak valid'); ok = false; } else clearError('email','emailError');
        if (!telp || !/^\d{10,15}$/.test(telp)) { setError('no_telp','telpError','Nomor telepon 10–15 digit'); ok = false; } else clearError('no_telp','telpError');
        if (!kelas)                              { setError('kelas','kelasError','Pilih kelas terlebih dahulu'); ok = false; } else clearError('kelas','kelasError');
        if (!alamat || alamat.length < 10)       { setError('alamat','alamatError','Alamat minimal 10 karakter'); ok = false; } else clearError('alamat','alamatError');

        return ok;
    }

    // Real-time validation
    [['nama_guru','namaError'], ['nip','nipError'], ['email','emailError'],
     ['no_telp','telpError'], ['kelas','kelasError'], ['alamat','alamatError']].forEach(([fid, eid]) => {
        document.getElementById(fid)?.addEventListener('input', () => {
            if (document.getElementById(fid)?.value.trim()) clearError(fid, eid);
        });
    });
    // NIP & telp: angka only
    ['nip','no_telp'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '');
        });
    });

    // ===== RESET MODAL =====
    document.getElementById('modalGuru')?.addEventListener('hidden.bs.modal', () => {
        document.getElementById('formGuru')?.reset();
        document.getElementById('id_guru').value = '';
        document.querySelectorAll('.field-input').forEach(el => el.classList.remove('is-invalid','is-valid'));
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
    });

    // ===== TAMBAH =====
    document.getElementById('btnTambahGuru')?.addEventListener('click', () => {
        document.getElementById('judulModalGuru').textContent = 'Tambah Guru Baru';
        modalGuru.show();
    });

    // ===== EDIT =====
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-guru');
        if (!btn) return;
        const guru = JSON.parse(btn.dataset.guru || '{}');
        document.getElementById('judulModalGuru').textContent = 'Edit Data Guru';
        document.getElementById('id_guru').value    = guru.id_guru || '';
        document.getElementById('nama_guru').value  = guru.nama_guru || '';
        document.getElementById('nip').value        = guru.nip || '';
        document.getElementById('email').value      = guru.email || '';
        document.getElementById('no_telp').value    = guru.no_telp || '';
        document.getElementById('kelas').value      = guru.kelas || '';
        document.getElementById('alamat').value     = guru.alamat || '';
        modalGuru.show();
    });

    // ===== SUBMIT FORM =====
    document.getElementById('formGuru')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!validate()) return;

        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

        const id = document.getElementById('id_guru').value.trim();
        const payload = {
            id_guru:   id || undefined,
            nama_guru: document.getElementById('nama_guru').value.trim(),
            nip:       document.getElementById('nip').value.trim(),
            email:     document.getElementById('email').value.trim(),
            no_telp:   document.getElementById('no_telp').value.trim(),
            kelas:     document.getElementById('kelas').value,
            alamat:    document.getElementById('alamat').value.trim(),
        };

        try {
            const res  = await fetch(id ? routes.update : routes.store, {
                method:  id ? 'PUT' : 'POST',
                headers, body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                modalGuru.hide();
                showToast(data.message || 'Berhasil disimpan', 'success');
                setTimeout(() => location.reload(), 1200);
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
        const btn = e.target.closest('.btn-hapus-guru');
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
                body: JSON.stringify({ id_guru: parseInt(idHapus) }),
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
        const btn = e.target.closest('.btn-setujui-guru');
        if (!btn) return;
        const id = btn.dataset.id;
        btn.disabled = true;
        try {
            const res  = await fetch(`${routes.akun}?id_guru=${id}`, { headers });
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
