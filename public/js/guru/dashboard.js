document.addEventListener('DOMContentLoaded', function () {
    const config = window.guruConfig || {};
    let currentIdIzin = null;

    // Modal instances
    const modalApprove = new bootstrap.Modal(document.getElementById('modalKonfirmasiSetujui'));
    const modalReject  = new bootstrap.Modal(document.getElementById('modalAlasanTolak'));

    // ===== SIDEBAR MOBILE =====
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    sidebarToggle?.addEventListener('click', function () {
        sidebar?.classList.toggle('open');
        sidebarOverlay?.classList.toggle('show');
    });
    sidebarOverlay?.addEventListener('click', function () {
        sidebar?.classList.remove('open');
        sidebarOverlay.classList.remove('show');
    });

    // ===== TOAST =====
    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = message;
        toast.className = `show ${isSuccess ? 'success' : 'error'}`;
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ===== APPROVE CLICK =====
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-setujui')) {
            const id   = e.target.dataset.id;
            const nama = e.target.dataset.nama;
            if (!id) { showToast('Error: ID izin tidak ditemukan', false); return; }
            currentIdIzin = id;
            document.getElementById('namaSiswaSetujui').textContent = `Menyetujui izin dari: ${nama}`;
            modalApprove.show();
        }
    });

    // ===== CONFIRM APPROVE =====
    document.getElementById('btnKonfirmasiSetujui')?.addEventListener('click', function () {
        if (!currentIdIzin) return;
        modalApprove.hide();
        updateIzinStatus(currentIdIzin, 'Disetujui', null);
        currentIdIzin = null;
    });

    // ===== REJECT CLICK =====
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-tolak')) {
            const id   = e.target.dataset.id;
            const nama = e.target.dataset.nama;
            if (!id) { showToast('Error: ID izin tidak ditemukan', false); return; }
            currentIdIzin = id;
            document.getElementById('alasanTolak').value = '';
            document.getElementById('namaSiswaTolak').textContent = `Menolak izin dari: ${nama}`;
            modalReject.show();
        }
    });

    // ===== CONFIRM REJECT =====
    document.getElementById('btnKonfirmasiTolak')?.addEventListener('click', function () {
        const alasan = document.getElementById('alasanTolak')?.value.trim();
        if (!alasan) { showToast('⚠ Alasan penolakan harus diisi!', false); return; }
        if (!currentIdIzin) return;
        modalReject.hide();
        updateIzinStatus(currentIdIzin, 'Ditolak', alasan);
        currentIdIzin = null;
    });

    // ===== AJAX UPDATE STATUS =====
    function updateIzinStatus(idIzin, status, alasan) {
        const item = document.querySelector(`.izin-row="${idIzin}"]`);
        if (!item) { showToast('Error: Item tidak ditemukan', false); return; }

        const buttons = item.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const payload = {
            id_izin:           parseInt(idIzin),
            status:            status,
            id_admin_verifikasi: config.userId,
        };
        if (alasan) payload.alasan_penolakan = alasan;

        fetch(config.apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(status === 'Disetujui' ? '✓ Izin disetujui!' : '✗ Izin ditolak!', true);

                item.style.transition = 'all 0.3s ease';
                item.style.opacity    = '0';
                item.style.transform  = 'translateX(-20px)';

                setTimeout(() => {
                    item.remove();
                    updateBadgeCount();
                    const container = document.getElementById('izinContainer');
                    if (container && !container.querySelector('.izin-row')) {
                        container.innerHTML = '<p class="text-muted small mb-0">Tidak ada izin menunggu</p>';
                    }
                }, 300);
            } else {
                showToast('❌ ' + (data.message || 'Gagal memperbarui'), false);
                resetButtons(buttons);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('❌ Gagal menghubungi server', false);
            resetButtons(buttons);
        });
    }

    function resetButtons(buttons) {
        if (buttons[0]) buttons[0].innerHTML = '✔ Setujui';
        if (buttons[1]) buttons[1].innerHTML = '✘ Tolak';
        buttons.forEach(btn => (btn.disabled = false));
    }

    function updateBadgeCount() {
        const currentCount = document.querySelectorAll('.izin-row').length;
        const badge = document.getElementById('izinCounter');
        if (badge) badge.textContent = count;
    }

    // ===== AUTO REFRESH =====
    setInterval(async () => {
        try {
            const res  = await fetch(config.refreshUrl + '?t=' + Date.now());
            const data = await res.json();
            if (data.success) {
                const item = document.querySelector(`.izin-row[data-id="${idIzin}"]`);
                if (data.count > currentCount) {
                    showToast('📬 Ada izin baru!', true);
                }
            }
        } catch (e) {
            console.error('Auto refresh error:', e);
        }
    }, 30000);

    // ===== CHART.JS =====
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const masuk      = config.siswaMasuk      || 0;
        const tidakMasuk = config.siswaTidakMasuk || 0;
        const total      = config.totalSiswa      || 0;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Masuk', 'Tidak Masuk'],
                datasets: [{
                    data: [masuk, tidakMasuk],
                    backgroundColor: ['#0d6efd', '#e9ecef'],
                    borderColor:     ['#0d6efd', '#e9ecef'],
                    borderWidth: 2,
                    cutout: '70%',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
            },
            plugins: [{
                id: 'textCenter',
                beforeDatasetsDraw(chart) {
                    const { width, height, ctx: c } = chart;
                    c.restore();
                    c.font = `bold ${(height / 200).toFixed(2)}em sans-serif`;
                    c.textBaseline = 'middle';
                    c.fillStyle    = '#0d6efd';
                    const text  = `${masuk}/${total}`;
                    const textX = Math.round((width - c.measureText(text).width) / 2);
                    c.fillText(text, textX, height / 2);
                    c.save();
                },
            }],
        });
    }
});
