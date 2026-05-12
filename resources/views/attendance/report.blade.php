<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .card { 
            border: 1px solid #ddd; 
            padding: 20px; 
            border-radius: 8px; 
            max-width: 500px;
            margin: 0 auto;
        }
        .percentage { 
            font-size: 48px; 
            font-weight: bold; 
            color: #4CAF50;
            text-align: center;
        }
        .info { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="card">
        <h2>📊 Rekap Absensi Siswa</h2>
        
        <div class="info">
            <strong>Siswa ID:</strong> {{ $siswaId }}
        </div>
        
        <div class="info">
            <strong>Periode:</strong> 
            @php
                $bulanName = \Carbon\Carbon::create()->month($bulan)->format('F');
            @endphp
            {{ ucfirst($bulanName) }} {{ $tahun }}
        </div>
        
        <div class="percentage">
            {{ $persentase }}%
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            @php
                $prevBulan = $bulan - 1;
                $nextBulan = $bulan + 1;
                $prevTahun = $tahun;
                $nextTahun = $tahun;
                
                if ($prevBulan < 1) {
                    $prevBulan = 12;
                    $prevTahun--;
                }
                if ($nextBulan > 12) {
                    $nextBulan = 1;
                    $nextTahun++;
                }
            @endphp
            
            <a href="/rekap-absensi?siswa_id={{ $siswaId }}&bulan={{ $prevBulan }}&tahun={{ $prevTahun }}">
                ← Bulan Sebelumnya
            </a>
            |
            <a href="/rekap-absensi?siswa_id={{ $siswaId }}&bulan={{ $nextBulan }}&tahun={{ $nextTahun }}">
                Bulan Selanjutnya →
            </a>
        </div>
    </div>
</body>
</html>