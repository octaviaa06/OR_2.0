document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.perizinanConfig || {};
    const headers = {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': cfg.csrf || '',
    };

    let currentIdIzin = null;

    const modalSetujui = new bootstrap.Modal(document.getElementById('modalSetujui'));
    const modalTolak   = new bootstrap.Modal(document.getElementById('modalTolak'));

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
        document.querySelectorAll('.izin-item').forEach(row => {
            const nama = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
            row.style.display = nama.includes(kw) ? '' : 'none';
        });
    });

    // ===== BUKA MODAL SETUJUI =====
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-setujui');
        if (!btn) return;

        currentIdIzin = btn.dataset.id;
        if (!currentIdIzin) { showToast('Error: ID izin tidak ditemukan', 'error'); return; }

        document.getElementById('setujuiNama').textContent    = btn.dataset.nama    || '—';
        document.getElementById('setujuiKelas').textContent   = btn.dataset.kelas   || '—';
        document.getElementById('setujuiJenis').textContent   = btn.dataset.jenis   || '—';
        document.getElementById('setujuiTanggal').textContent = btn.dataset.tanggal || '—';
        modalSetujui.show();
    });

    // ===== KONFIRMASI SETUJUI =====
    document.getElementById('btnKonfirmasiSetujui')?.addEventListener('click', function () {
        if (!currentIdIzin) return;
        modalSetujui.hide();
        updateStatus(currentIdIzin, 'Disetujui', null);
        currentIdIzin = null;
    });

    // ===== BUKA MODAL TOLAK =====
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-tolak');
        if (!btn) return;

        currentIdIzin = btn.dataset.id;
        if (!currentIdIzin) { showToast('Error: ID izin tidak ditemukan', 'error'); return; }

        document.getElementById('alasanTolak').value = '';
        modalTolak.show();
    });

    // ===== KONFIRMASI TOLAK =====
    document.getElementById('btnKonfirmasiTolak')?.addEventListener('click', function () {
        const alasan = document.getElementById('alasanTolak')?.value.trim();
        if (!alasan) { showToast('⚠ Alasan penolakan harus diisi!', 'error'); return; }
        if (!currentIdIzin) return;

        modalTolak.hide();
        updateStatus(currentIdIzin, 'Ditolak', alasan);
        currentIdIzin = null;
    });

    // ===== UPDATE STATUS =====
    async function updateStatus(idIzin, status, alasan) {
        // Disable tombol di baris yang bersangkutan
        const row     = document.querySelector(`.izin-item[data-id="${idIzin}"]`);
        const buttons = row?.querySelectorAll('.btn-aksi');
        buttons?.forEach(b => (b.disabled = true));

        const payload = {
            id_izin:           parseInt(idIzin),
            status:            status,
            id_guru_verifikasi: cfg.userId,
        };
        if (alasan) payload.alasan_penolakan = alasan;

        try {
            const res  = await fetch(cfg.updateUrl, {
                method: 'PUT', headers,
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (data.success) {
                showToast(status === 'Disetujui' ? '✓ Izin disetujui!' : '✗ Izin ditolak!', 'success');
                // Animasi fade out baris
                if (row) {
                    row.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    row.style.opacity    = '0';
                    row.style.transform  = 'translateX(-20px)';
                    setTimeout(() => location.reload(), 800);
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                showToast('❌ ' + (data.message || 'Gagal memperbarui status'), 'error');
                buttons?.forEach(b => (b.disabled = false));
            }
        } catch (err) {
            showToast('❌ Error: ' + err.message, 'error');
            buttons?.forEach(b => (b.disabled = false));
        }
    }
});
