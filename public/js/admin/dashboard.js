document.addEventListener('DOMContentLoaded', function() {
    const config = window.adminConfig || {};
    let currentIdIzin = null;
    
    // Modal instances
    const modalApprove = new bootstrap.Modal(document.getElementById('modalKonfirmasiSetujui'));
    const modalReject = new bootstrap.Modal(document.getElementById('modalAlasanTolak'));

    // ===== TOAST NOTIFICATION =====
    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('toast');
        if (!toast) return;
        
        toast.textContent = message;
        toast.className = `show ${isSuccess ? 'success' : 'error'}`;
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // ===== CIRCULAR PROGRESS ANIMATION =====
    function updateCircularProgress() {
        const siswaMasukEl = document.getElementById('siswaMasukCount');
        const siswaTotalEl = document.getElementById('siswaTotalCount');
        
        if (!siswaMasukEl || !siswaTotalEl) return;
        
        const siswaMasuk = parseInt(siswaMasukEl.textContent) || 0;
        const totalSiswa = parseInt(siswaTotalEl.textContent) || 0;
        
        if (totalSiswa === 0) return;
        
        const percentage = (siswaMasuk / totalSiswa) * 100;
        const radius = 100;
        const circumference = 2 * Math.PI * radius; // 628.32
        const offset = circumference - (percentage / 100) * circumference;
        
        const circle = document.querySelector('.progress-ring__circle');
        if (circle) {
            // Set initial state
            circle.style.transition = 'none';
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = circumference;
            
            // Trigger reflow
            void circle.offsetWidth;
            
            // Animate to final state
            circle.style.transition = 'stroke-dashoffset 1.2s ease-out';
            circle.style.strokeDashoffset = offset;
        }
        
        // Animate counting numbers
        animateValue(siswaMasukEl, 0, siswaMasuk, 1000);
    }
    
    // Helper: Animate number counting
    function animateValue(element, start, end, duration) {
        if (start === end) return;
        
        const range = end - start;
        const startTime = performance.now();
        
        function step(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            // Easing function for smooth animation
            const easeOutQuart = 1 - Math.pow(1 - progress, 4);
            const current = Math.floor(start + (range * easeOutQuart));
            
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                element.textContent = end; // Ensure exact final value
            }
        }
        
        requestAnimationFrame(step);
    }

    // ===== APPROVE BUTTON CLICK =====
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-setujui')) {
            const id = e.target.dataset.id;
            const nama = e.target.dataset.nama;
            
            if (!id) { showToast('Error: ID izin tidak ditemukan', false); return; }
            
            currentIdIzin = id;
            document.getElementById('namaSiswaSetujui').textContent = `Menyetujui izin dari: ${nama}`;
            modalApprove.show();
        }
    });

    // ===== CONFIRM APPROVE =====
    document.getElementById('btnKonfirmasiSetujui')?.addEventListener('click', function() {
        if (!currentIdIzin) return;
        modalApprove.hide();
        updateIzinStatus(currentIdIzin, 'Disetujui', null);
        currentIdIzin = null;
    });

    // ===== REJECT BUTTON CLICK =====
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-tolak')) {
            const id = e.target.dataset.id;
            const nama = e.target.dataset.nama;
            
            if (!id) { showToast('Error: ID izin tidak ditemukan', false); return; }
            
            currentIdIzin = id;
            document.getElementById('alasanTolak').value = '';
            document.getElementById('namaSiswaTolak').textContent = `Menolak izin dari: ${nama}`;
            modalReject.show();
        }
    });

    // ===== CONFIRM REJECT =====
    document.getElementById('btnKonfirmasiTolak')?.addEventListener('click', function() {
        const alasan = document.getElementById('alasanTolak')?.value.trim();
        
        if (!alasan) { showToast('⚠ Alasan penolakan harus diisi!', false); return; }
        if (!currentIdIzin) return;
        
        modalReject.hide();
        updateIzinStatus(currentIdIzin, 'Ditolak', alasan);
        currentIdIzin = null;
    });

    // ===== AJAX UPDATE STATUS =====
    function updateIzinStatus(idIzin, status, alasan) {
        const item = document.querySelector(`.izin-item[data-id="${idIzin}"]`);
        if (!item) { showToast('Error: Item tidak ditemukan', false); return; }

        // Loading state
        const buttons = item.querySelectorAll('button');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        });

        const payload = {
            id_izin: parseInt(idIzin),
            status: status,
            id_admin_verifikasi: config.userId,
            _token: document.querySelector('meta[name="csrf-token"]')?.content || ''
        };

        if (alasan) payload.alasan_penolakan = alasan;

        fetch(config.apiUrl, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': payload._token
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(status === 'Disetujui' ? '✓ Izin disetujui!' : '✗ Izin ditolak!', true);
                
                // Animate remove
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    item.remove();
                    updateBadgeCount();
                    
                    // Check if empty
                    const container = document.getElementById('izinContainer');
                    if (!container.querySelector('.izin-item')) {
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
        buttons[0].innerHTML = '✔ Setujui';
        buttons[1].innerHTML = '✘ Tolak';
        buttons.forEach(btn => btn.disabled = false);
    }

    function updateBadgeCount() {
        const count = document.querySelectorAll('.izin-item').length;
        const badge = document.getElementById('izinCounter');
        if (badge) badge.textContent = count;
    }

    // ===== AUTO REFRESH IZIN =====
    function autoRefreshIzin() {
        setInterval(async () => {
            try {
                const res = await fetch(config.refreshUrl + '?t=' + Date.now());
                const data = await res.json();
                
                if (data.success) {
                    const currentCount = document.querySelectorAll('.izin-item').length;
                    if (data.count > currentCount) {
                        showToast('📬 Ada izin baru!', true);
                        // Optional: location.reload() untuk refresh penuh
                    }
                }
            } catch (e) {
                console.error('Auto refresh error:', e);
            }
        }, 30000);
    }

    // ===== CHART.JS ATTENDANCE =====
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const masuk = window.adminConfig.siswaMasuk || 0;
        const tidakMasuk = window.adminConfig.siswaTidakMasuk || 0;
        const total = window.adminConfig.totalSiswa || 0;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Masuk', 'Tidak Masuk'],
                datasets: [{
                    data: [masuk, tidakMasuk],
                    backgroundColor: ['#0d6efd', '#e9ecef'],
                    borderColor: ['#0d6efd', '#e9ecef'],
                    borderWidth: 2,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            },
            plugins: [{
                id: 'textCenter',
                beforeDatasetsDraw(chart) {
                    const {width, height, ctx} = chart;
                    ctx.restore();
                    ctx.font = `bold ${(height / 200).toFixed(2)}em sans-serif`;
                    ctx.textBaseline = "middle";
                    ctx.fillStyle = '#0d6efd';
                    
                    const text = `${masuk}/${total}`;
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });
    }

    // ===== INITIALIZE ALL ANIMATIONS =====
    // Delay slightly to ensure DOM is fully rendered
    setTimeout(() => {
        updateCircularProgress();
    }, 200);
    
    // Initialize auto refresh
    autoRefreshIzin();
});