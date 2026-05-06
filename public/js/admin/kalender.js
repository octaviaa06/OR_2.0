document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.kalenderConfig || {};
    const headers = {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': cfg.csrf || '',
    };

    let currentHapusId = null;

    const modalAgenda = new bootstrap.Modal(document.getElementById('modalAgenda'));
    const modalDetail = new bootstrap.Modal(document.getElementById('modalDetail'));
    const modalHapus  = new bootstrap.Modal(document.getElementById('modalHapus'));
    const modalSukses = new bootstrap.Modal(document.getElementById('modalSukses'));

    // ===== TOAST =====
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `show ${type}`;
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    // ===== MODAL SUKSES =====
    function showSukses(title, message, actionType = '') {
        document.getElementById('suksesTitle').textContent   = title;
        document.getElementById('suksesMessage').textContent = message;
        modalSukses.show();
        setTimeout(() => {
            modalSukses.hide();
            setTimeout(() => location.reload(), 300);
        }, 2500);
    }

    // ===== RESET FORM =====
    function resetForm() {
        document.getElementById('agendaId').value       = '';
        document.getElementById('agendaNama').value     = '';
        document.getElementById('agendaTanggal').value  = cfg.selectedDate || '';
        document.getElementById('agendaDeskripsi').value = '';
        document.getElementById('namaError').classList.remove('show');
        document.getElementById('tanggalError').classList.remove('show');
        document.getElementById('agendaNama').classList.remove('is-invalid');
        document.getElementById('agendaTanggal').classList.remove('is-invalid');
    }

    // ===== BUKA MODAL TAMBAH =====
    document.getElementById('btnTambahAgenda')?.addEventListener('click', () => {
        resetForm();
        document.getElementById('agendaModalLabel').textContent = 'Tambah Agenda';
        const btn = document.getElementById('btnSimpanAgenda');
        btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Simpan`;
        modalAgenda.show();
    });

    // ===== RESET MODAL SAAT DITUTUP =====
    document.getElementById('modalAgenda')?.addEventListener('hidden.bs.modal', resetForm);

    // ===== LIHAT DETAIL =====
    window.lihatDetailAgenda = function (agenda) {
        document.getElementById('detailNama').textContent     = agenda.nama_kegiatan || '—';
        document.getElementById('detailTanggal').textContent  = formatTanggal(agenda.tanggal);
        document.getElementById('detailDeskripsi').textContent = agenda.deskripsi || 'Tidak ada deskripsi';
        modalDetail.show();
    };

    // ===== EDIT AGENDA =====
    window.editAgenda = async function (id) {
        if (!id) { showToast('ID Agenda tidak valid', 'error'); return; }
        try {
            const res  = await fetch(`${cfg.showUrl}?id=${id}`, { headers });
            const data = await res.json();
            if (data.status === 'success' && data.data) {
                const a = data.data;
                document.getElementById('agendaId').value        = a.id;
                document.getElementById('agendaNama').value      = a.nama_kegiatan;
                document.getElementById('agendaTanggal').value   = a.tanggal;
                document.getElementById('agendaDeskripsi').value = a.deskripsi || '';
                document.getElementById('agendaModalLabel').textContent = 'Edit Agenda';
                const btn = document.getElementById('btnSimpanAgenda');
                btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Perbarui`;
                // Tutup modal detail jika terbuka
                bootstrap.Modal.getInstance(document.getElementById('modalDetail'))?.hide();
                setTimeout(() => modalAgenda.show(), 300);
            } else {
                showToast('Gagal memuat data agenda', 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        }
    };

    // ===== HAPUS AGENDA =====
    window.hapusAgenda = function (id) {
        if (!id) { showToast('ID Agenda tidak valid', 'error'); return; }
        currentHapusId = id;
        modalHapus.show();
    };

    document.getElementById('btnKonfirmasiHapus')?.addEventListener('click', async () => {
        if (!currentHapusId) return;
        try {
            const res  = await fetch(cfg.destroyUrl, {
                method: 'DELETE', headers,
                body: JSON.stringify({ id: currentHapusId }),
            });
            const data = await res.json();
            modalHapus.hide();
            if (data.status === 'success') {
                showSukses('Berhasil menghapus data!', data.message || 'Data agenda berhasil dihapus.');
            } else {
                showToast('❌ ' + (data.message || 'Gagal menghapus'), 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        } finally {
            currentHapusId = null;
        }
    });

    document.getElementById('modalHapus')?.addEventListener('hidden.bs.modal', () => {
        currentHapusId = null;
    });

    // ===== SUBMIT FORM =====
    document.getElementById('formAgenda')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const nama    = document.getElementById('agendaNama').value.trim();
        const tanggal = document.getElementById('agendaTanggal').value;
        let valid = true;

        if (!nama) {
            document.getElementById('namaError').textContent = 'Nama kegiatan wajib diisi!';
            document.getElementById('namaError').classList.add('show');
            document.getElementById('agendaNama').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('namaError').classList.remove('show');
            document.getElementById('agendaNama').classList.remove('is-invalid');
        }

        if (!tanggal) {
            document.getElementById('tanggalError').textContent = 'Tanggal wajib diisi!';
            document.getElementById('tanggalError').classList.add('show');
            document.getElementById('agendaTanggal').classList.add('is-invalid');
            valid = false;
        } else {
            document.getElementById('tanggalError').classList.remove('show');
            document.getElementById('agendaTanggal').classList.remove('is-invalid');
        }

        if (!valid) return;

        const id      = document.getElementById('agendaId').value;
        const btn     = document.getElementById('btnSimpanAgenda');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;border-width:2px;"></span>Menyimpan...';

        const payload = {
            id:             id || undefined,
            nama_kegiatan:  nama,
            tanggal:        tanggal,
            deskripsi:      document.getElementById('agendaDeskripsi').value.trim(),
        };
        if (!payload.id) delete payload.id;

        try {
            const url    = id ? cfg.updateUrl : cfg.storeUrl;
            const method = id ? 'PUT' : 'POST';
            const res    = await fetch(url, { method, headers, body: JSON.stringify(payload) });
            const data   = await res.json();

            if (data.status === 'success') {
                modalAgenda.hide();
                const actionType = id ? 'mengedit' : 'menambah';
                const title = id ? 'Berhasil mengedit data!' : 'Berhasil menambah data!';
                showSukses(title, data.message || 'Data agenda berhasil disimpan.');
            } else {
                showToast('❌ ' + (data.message || 'Gagal menyimpan'), 'error');
            }
        } catch (err) {
            showToast('Error: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Simpan`;
        }
    });

    // ===== FORMAT TANGGAL =====
    function formatTanggal(tanggal) {
        if (!tanggal) return '—';
        const bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                       'Juli','Agustus','September','Oktober','November','Desember'];
        const d = new Date(tanggal);
        if (isNaN(d.getTime())) return tanggal;
        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    }
});
