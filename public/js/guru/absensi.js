document.addEventListener('DOMContentLoaded', function () {
    const cfg     = window.absensiConfig || {};
    const headers = {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
        'X-CSRF-TOKEN': cfg.csrf || '',
    };

    // ===== TOAST =====
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `show ${type}`;
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    // ===== STATUS SELECT COLOR =====
    window.updateStatusColor = function (sel) {
        sel.classList.remove('sel-hadir', 'sel-izin', 'sel-sakit', 'sel-alpa');
        sel.classList.add('sel-' + sel.value.toLowerCase());
    };

    // Init warna semua select
    document.querySelectorAll('.status-select').forEach(s => updateStatusColor(s));

    // ===== SIMPAN ABSENSI =====
    window.simpanAbsensi = async function () {
        const kelasEl = document.querySelector('input[name="kelas"]');
        const tanggalEl = document.querySelector('input[name="tanggal"]');
        const btn       = document.getElementById('btnSimpan');

        if (!kelasEl?.value)   { showToast('⚠ Pilih kelas terlebih dahulu!', 'error'); return; }
        if (!tanggalEl?.value) { showToast('⚠ Pilih tanggal terlebih dahulu!', 'error'); return; }

        // Validasi kelas guru
        if (cfg.kelasGuru?.length > 0 && !cfg.kelasGuru.includes(kelasEl.value)) {
            showToast('❌ Anda hanya bisa menyimpan absensi kelas yang Anda ajar!', 'error');
            return;
        }

        const form     = document.getElementById('formAbsensi');
        const formData = new FormData(form);
        const updates  = [];

        for (const [key, val] of formData.entries()) {
            if (key.includes('[status]')) {
                const match = key.match(/\[(\d+)\]/);
                if (match) updates.push({ id_murid: match[1], status: val });
            }
        }

        if (updates.length === 0) {
            showToast('Tidak ada perubahan untuk disimpan.', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;border-width:2px;"></span>Menyimpan...';

        try {
            const res  = await fetch(cfg.simpanUrl, {
                method: 'POST', headers,
                body: JSON.stringify({
                    tanggal: tanggalEl.value,
                    kelas:   kelasEl.value,
                    absensi: updates,
                }),
            });
            const data = await res.json();

            if (data.status === 'success') {
                showToast('✓ Absensi berhasil disimpan!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('❌ ' + (data.message || 'Gagal menyimpan'), 'error');
            }
        } catch (err) {
            showToast('❌ Koneksi error: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan`;
        }
    };

    // ===== BUKA MODAL EXPORT =====
    window.bukaModalExport = function () {
        const modal = new bootstrap.Modal(document.getElementById('modalExport'));
        updatePeriodeInfo();
        modal.show();
    };

    // ===== UPDATE INFO PERIODE =====
    window.updatePeriodeInfo = function () {
        const filter  = document.getElementById('exportFilter')?.value;
        const tanggal = document.getElementById('exportTanggal')?.value;
        const info    = document.getElementById('periodeInfo');
        const text    = document.getElementById('periodeText');
        if (!tanggal || !info || !text) return;

        const d = new Date(tanggal);
        let label = '';

        if (filter === 'minggu') {
            const start = new Date(d);
            start.setDate(d.getDate() - d.getDay() + 1); // Senin
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            label = `Minggu: ${fmt(start)} s/d ${fmt(end)}`;
        } else if (filter === 'bulan') {
            const first = new Date(d.getFullYear(), d.getMonth(), 1);
            const last  = new Date(d.getFullYear(), d.getMonth() + 1, 0);
            label = `Bulan: ${fmt(first)} s/d ${fmt(last)}`;
        } else {
            label = `Hari: ${fmt(d)}`;
        }

        text.textContent = label;
        info.style.display = 'block';
    };

    function fmt(date) {
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    // ===== EXPORT PDF =====
        window.exportPDF = async function () {

        const kelas   = document.getElementById('exportKelas')?.value?.trim();
        console.log('KELAS =', kelas);
        const tanggal = document.getElementById('exportTanggal')?.value;
        const filter  = document.getElementById('exportFilter')?.value;
        const btn     = document.getElementById('btnExport');

        if (!kelas) {
            showToast('❌ Kelas tidak ditemukan!', 'error');
            return;
        }

        if (!tanggal) {
            showToast('⚠ Pilih tanggal terlebih dahulu!', 'error');
            return;
        }

        if (cfg.kelasGuru?.length > 0 && !cfg.kelasGuru.includes(kelas)) {
            showToast('❌ Anda hanya bisa export kelas yang Anda ajar!', 'error');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" style="width:14px;height:14px;border-width:2px;"></span>Memproses...';

        try {
            const res  = await fetch(cfg.exportUrl, {
                method: 'POST', headers,
                body: JSON.stringify({ kelas, tanggal, filter_type: filter }),
            });
            const data = await res.json();

            if (!data.status || data.status !== 'success') {
                showToast('❌ ' + (data.message || 'Gagal mengambil data'), 'error');
                return;
            }

            // Generate PDF di browser menggunakan print
            generatePDF(data);
            showToast('✅ PDF berhasil dibuat!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalExport'))?.hide();

        } catch (err) {
            showToast('❌ Error: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF`;
        }
    };

    function generatePDF(data) {
        const isBulan = data.filter_type === 'bulan';
        const periode = data.start_date === data.end_date
            ? new Date(data.start_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
            : `${new Date(data.start_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })} — ${new Date(data.end_date).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}`;

        let rows = '';
        let totH = 0, totI = 0, totS = 0, totA = 0, totPersen = 0;
        data.data.forEach((s, i) => {
            totH += s.Hadir; totI += s.Izin; totS += s.Sakit; totA += s.Alpa;
            const persen = isBulan ? (s.persentase_kehadiran ?? 0) : null;
            if (isBulan) totPersen += persen;

            const persenCell = isBulan
                ? `<td style="font-weight:600;color:${persen >= 80 ? '#16a34a' : persen >= 60 ? '#d97706' : '#dc2626'}">${persen.toFixed(2)}%</td>`
                : '';
            rows += `<tr><td>${i + 1}</td><td style="text-align:left">${s.nama}</td><td>${s.Hadir}</td><td>${s.Izin}</td><td>${s.Sakit}</td><td>${s.Alpa}</td>${persenCell}</tr>`;
        });

        const jumlahSiswa = data.data.length;
        const rataRata    = jumlahSiswa > 0 ? (totPersen / jumlahSiswa).toFixed(2) : '0.00';

        const thPersen  = isBulan ? '<th>% Kehadiran</th>' : '';
        const totPersen2 = isBulan ? `<td style="color:#16a34a;font-weight:700">${rataRata}%</td>` : '';
        const infoHariEfektif = isBulan && data.hari_efektif
            ? `<div class="info-efektif">Hari Efektif Bulan Ini: <strong>${data.hari_efektif} hari</strong></div>`
            : '';

        const html = `<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Rekap Absensi</title>
        <style>
            body{font-family:Arial,sans-serif;margin:30px;color:#333}
            h1{text-align:center;color:#5b21b6;margin:0}
            .sub{text-align:center;font-size:13px;margin:6px 0 10px}
            .info-efektif{text-align:center;font-size:12px;margin:0 0 20px;color:#5b21b6;background:#ede9fe;padding:6px 12px;border-radius:6px;display:inline-block;width:auto;margin:0 auto 20px;display:block}
            table{width:100%;border-collapse:collapse;margin-top:10px}
            th{background:#5b21b6;color:white;padding:10px;text-align:center;font-size:12px}
            td{padding:8px;border:1px solid #ddd;text-align:center;font-size:11px}
            .total{font-weight:bold;background:#ede9fe}
            .footer{margin-top:24px;font-size:10px;text-align:right;color:#888}
        </style></head><body>
        <h1>REKAP ABSENSI</h1>
        <div class="sub"><strong>Kelas:</strong> ${data.kelas} &nbsp;|&nbsp; <strong>Periode:</strong> ${periode}</div>
        ${infoHariEfektif}
        <table><thead><tr><th>No</th><th>Nama Murid</th><th>Hadir</th><th>Izin</th><th>Sakit</th><th>Alpa</th>${thPersen}</tr></thead>
        <tbody>${rows}<tr class="total"><td colspan="2">TOTAL / RATA-RATA</td><td>${totH}</td><td>${totI}</td><td>${totS}</td><td>${totA}</td>${totPersen2}</tr></tbody></table>
        <div class="footer">Dicetak: ${new Date().toLocaleString('id-ID')}</div>
        </body></html>`;

        const win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(() => { win.print(); }, 500);
    }

    // Init periode info
    updatePeriodeInfo();
});
