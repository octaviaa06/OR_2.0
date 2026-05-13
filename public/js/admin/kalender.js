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

    let currentHapusId = null;

    // ===== INIT MODAL (Safe Check) =====
    const modalAgendaEl = document.getElementById('modalAgenda');
    const modalDetailEl = document.getElementById('modalDetail');
    const modalHapusEl  = document.getElementById('modalHapus');
    const modalSuksesEl = document.getElementById('modalSukses');

    const modalAgenda = modalAgendaEl ? new bootstrap.Modal(modalAgendaEl) : null;
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;
    const modalHapus  = modalHapusEl  ? new bootstrap.Modal(modalHapusEl)  : null;
    const modalSukses = modalSuksesEl ? new bootstrap.Modal(modalSuksesEl) : null;

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

    // ===== BUKA MODAL TAMBAH AGENDA =====
    document.getElementById('btnTambahAgenda')?.addEventListener('click', () => {
        resetForm();
        const label = document.getElementById('agendaModalLabel');
        if (label) label.textContent = 'Tambah Agenda';
        
        const btn = document.getElementById('btnSimpanAgenda');
        if (btn) {
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Simpan`;
        }
        modalAgenda?.show();
    });

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

    // ===== EDIT AGENDA =====
    window.editAgenda = async function (id) {
        console.log('✏️ Edit agenda ID:', id);
        
        if (!id || isNaN(parseInt(id))) { 
            showToast('ID Agenda tidak valid', 'error'); 
            return; 
        }
        
        if (!cfg.showUrl) {
            showToast('Konfigurasi URL tidak lengkap', 'error');
            return;
        }

        const btnSimpan = document.getElementById('btnSimpanAgenda');
        if (btnSimpan) {
            btnSimpan.disabled = true;
            btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memuat...';
        }

        try {
            const res = await fetch(`${cfg.showUrl}?id=${id}`, { headers });
            
            if (res.status === 419) {
                showToast('Sesi expired, silakan refresh halaman', 'error');
                return;
            }
            
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            
            const data = await res.json();
            
            if (data.status === 'success' && data.data) {
                const a = data.data;
                
                // Isi form
                const mapFields = {
                    'agendaId': a.id_kegiatan,
                    'agendaNama': a.nama_kegiatan || '',
                    'agendaTanggal': a.tanggal || '',
                    'agendaDeskripsi': a.deskripsi || ''
                };
                
                Object.entries(mapFields).forEach(([id, value]) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value;
                });
                
                // Update label & tombol
                const label = document.getElementById('agendaModalLabel');
                if (label) label.textContent = 'Edit Agenda';
                
                if (btnSimpan) {
                    btnSimpan.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Perbarui`;
                }
                
                // Tutup modal detail jika terbuka
                modalDetail?.hide();
                
                // Buka modal edit
                setTimeout(() => modalAgenda?.show(), 200);
            } else {
                showToast('❌ ' + (data.message || 'Gagal memuat data agenda'), 'error');
            }
        } catch (err) {
            console.error('✏️ Edit error:', err);
            showToast('Error: ' + err.message, 'error');
        } finally {
            if (btnSimpan) {
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg> Simpan`;
            }
        }
    };

    // ===== HAPUS AGENDA =====
    window.hapusAgenda = function (id) {
        console.log('🗑️ Hapus agenda ID:', id);
        
        if (!id || isNaN(parseInt(id))) { 
            showToast('ID Agenda tidak valid', 'error'); 
            return; 
        }
        
        if (!cfg.destroyUrl) {
            showToast('Konfigurasi URL hapus tidak ditemukan', 'error');
            return;
        }
        
        currentHapusId = id;
        
        // Update nama agenda di modal konfirmasi (opsional)
        const namaEl = document.getElementById('hapusNamaAgenda');
        if (namaEl) {
            // Cari nama dari data yang ada di halaman (jika tersedia)
            const item = document.querySelector(`[data-id="${id}"]`);
            if (item) {
                const nama = item.querySelector('.agenda-nama')?.textContent || 'agenda ini';
                namaEl.textContent = nama;
            }
        }
        
        modalHapus?.show();
    };

    // ===== KONFIRMASI HAPUS =====
    document.getElementById('btnKonfirmasiHapus')?.addEventListener('click', async () => {
        if (!currentHapusId) return;
        
        const btn = document.getElementById('btnKonfirmasiHapus');
        const originalContent = btn.innerHTML;
        
        try {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menghapus...';
            
            const res = await fetch(cfg.destroyUrl, {
                method: 'DELETE', 
                headers,
                body: JSON.stringify({ id: currentHapusId }),
            });
            
            if (res.status === 419) {
                showToast('Sesi expired, silakan refresh halaman', 'error');
                return;
            }
            
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }
            
            const data = await res.json();
            
            if (data.status === 'success') {
                modalHapus?.hide();
                showSukses('Berhasil!', data.message || 'Data agenda berhasil dihapus.');
            } else {
                showToast('❌ ' + (data.message || 'Gagal menghapus agenda'), 'error');
            }
        } catch (err) {
            console.error('🗑️ Hapus error:', err);
            showToast('Error: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            currentHapusId = null;
        }
    });

    // Reset currentHapusId saat modal hapus ditutup
    modalHapusEl?.addEventListener('hidden.bs.modal', () => {
        currentHapusId = null;
    });

    // ===== SUBMIT FORM AGENDA (Tambah/Edit) =====
    document.getElementById('formAgenda')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validasi input
        const namaEl = document.getElementById('agendaNama');
        const tanggalEl = document.getElementById('agendaTanggal');
        const nama = namaEl?.value.trim() || '';
        const tanggal = tanggalEl?.value || '';
        
        let valid = true;

        if (!nama) {
            const errEl = document.getElementById('namaError');
            if (errEl) {
                errEl.textContent = 'Nama kegiatan wajib diisi!';
                errEl.classList.add('show');
            }
            if (namaEl) namaEl.classList.add('is-invalid');
            valid = false;
        } else {
            const errEl = document.getElementById('namaError');
            if (errEl) errEl.classList.remove('show');
            if (namaEl) namaEl.classList.remove('is-invalid');
        }

        if (!tanggal) {
            const errEl = document.getElementById('tanggalError');
            if (errEl) {
                errEl.textContent = 'Tanggal wajib diisi!';
                errEl.classList.add('show');
            }
            if (tanggalEl) tanggalEl.classList.add('is-invalid');
            valid = false;
        } else {
            const errEl = document.getElementById('tanggalError');
            if (errEl) errEl.classList.remove('show');
            if (tanggalEl) tanggalEl.classList.remove('is-invalid');
        }

        if (!valid) return;

        // Siapkan data
        const idEl = document.getElementById('agendaId');
        const id = idEl?.value || '';
        const deskripsiEl = document.getElementById('agendaDeskripsi');
        
        const payload = {
            nama_kegiatan: nama,
            tanggal: tanggal,
            deskripsi: deskripsiEl?.value.trim() || ''
        };
        if (id) payload.id = id;

        // UI Loading
        const btn = document.getElementById('btnSimpanAgenda');
        const originalBtnContent = btn?.innerHTML || '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';
        }

        try {
            const isEdit = !!id;
            const url = isEdit ? cfg.updateUrl : cfg.storeUrl;
            const method = isEdit ? 'PUT' : 'POST';
            
            if (!url) {
                throw new Error(`URL ${isEdit ? 'update' : 'store'} tidak ditemukan di config`);
            }
            
            const res = await fetch(url, { 
                method, 
                headers, 
                body: JSON.stringify(payload) 
            });
            
            if (res.status === 419) {
                showToast('Sesi expired, silakan refresh halaman', 'error');
                return;
            }
            
            if (!res.ok) {
                const errData = await res.json().catch(() => ({}));
                throw new Error(errData.message || `HTTP ${res.status}`);
            }
            
            const data = await res.json();
            
            if (data.status === 'success') {
                modalAgenda?.hide();
                const actionText = isEdit ? 'mengedit' : 'menambah';
                showSukses(
                    `Berhasil ${actionText} data!`, 
                    data.message || `Agenda berhasil ${isEdit ? 'diperbarui' : 'ditambahkan'}.`
                );
            } else {
                showToast('❌ ' + (data.message || 'Gagal menyimpan agenda'), 'error');
            }
        } catch (err) {
            console.error('💾 Submit error:', err);
            showToast('Error: ' + err.message, 'error');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            }
        }
    });

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