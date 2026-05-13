document.addEventListener('DOMContentLoaded', function () {
    // ===== KONFIGURASI =====
    const cfg = window.kalenderConfig || {};
    
    // Validasi config wajib
    if (!cfg.csrf) {
        console.warn('⚠️ CSRF token tidak ditemukan, mencoba ambil dari meta tag...');
    }
    
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': cfg.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '',
    };


    // ===== TOAST NOTIFICATION =====
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) {
            console.log(`[${type.toUpperCase()}] ${msg}`);
            return;
        }
        t.textContent = msg;
        t.className = `toast show ${type}`;
        t.style.display = 'block';
        setTimeout(() => {
            t.classList.remove('show');
            t.style.display = 'none';
        }, 3500);
    }

    // ===== MODAL SUKSES + AUTO REFRESH =====
    function showSukses(title, message) {
        const titleEl = document.getElementById('suksesTitle');
        const msgEl = document.getElementById('suksesMessage');
        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
        
        if (modalSukses) {
            modalSukses.show();
            setTimeout(() => {
                modalSukses.hide();
                setTimeout(() => location.reload(), 300);
            }, 2000);
        } else {
            showToast(message, 'success');
            setTimeout(() => location.reload(), 1500);
        }
    }

    // ===== RESET FORM AGENDA =====
    function resetForm() {
        const fields = ['agendaId', 'agendaNama', 'agendaTanggal', 'agendaDeskripsi'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                if (id === 'agendaId') {
                    el.value = '';
                } else if (id === 'agendaTanggal') {
                    el.value = cfg.selectedDate || '';
                } else {
                    el.value = '';
                }
                el.classList.remove('is-invalid');
            }
        });
        
        ['namaError', 'tanggalError'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = '';
                el.classList.remove('show');
            }
        });
    }


    // ===== RESET FORM SAAT MODAL DITUTUP =====
    modalAgendaEl?.addEventListener('hidden.bs.modal', resetForm);

    // ===== LIHAT DETAIL AGENDA =====
    window.lihatDetailAgenda = function (agenda) {
        if (!agenda) return;
        
        const fields = {
            'detailNama': agenda.nama_kegiatan || '—',
            'detailTanggal': formatTanggal(agenda.tanggal),
            'detailDeskripsi': agenda.deskripsi || 'Tidak ada deskripsi'
        };
        
        Object.entries(fields).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });
        
        modalDetail?.show();
    };



    // ===== FORMAT TANGGAL INDONESIA =====
    function formatTanggal(tanggal) {
        if (!tanggal) return '—';
        const bulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];
        const d = new Date(tanggal + 'T00:00:00'); // Fix timezone issue
        if (isNaN(d.getTime())) return tanggal;
        return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
    }

    // ===== LOGGING DEBUG (bisa dihapus di production) =====
    console.log('✅ Kalender JS loaded', {
        csrf: cfg.csrf ? '✓' : '✗',
        urls: {
            show: !!cfg.showUrl,
            store: !!cfg.storeUrl,
            update: !!cfg.updateUrl,
            destroy: !!cfg.destroyUrl
        }
    });
});