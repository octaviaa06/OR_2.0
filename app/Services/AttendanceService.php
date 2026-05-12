<?php

namespace App\Services;

use App\Models\Absensi; // Pastikan Model Absensi sudah dibuat
use Carbon\Carbon;

class AttendanceService
{
    // DAFTAR LIBUR (Hardcoded dulu buat Localhost)
    private array $holidays = [
        '2026-05-01', // Hari Buruh
        '2026-05-29', // Kenaikan Isa Almasih
        '2026-06-01', // Hari Lahir Pancasila
    ];

    /**
     * Hitung persentase kehadiran
     * @param int $siswaId
     * @param int $month
     * @param int $year
     * @return float
     */
    public function calculatePercentage(int $siswaId, int $month, int $year): float
    {
        $totalEffectiveDays = 0;
        $validDates = [];

        // 1. Loop cari total hari efektif (Senin-Jumat & Bukan Libur)
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            
            // Skip jika Weekend atau Libur Nasional
            if ($date->isWeekend() || in_array($date->toDateString(), $this->holidays)) {
                continue;
            }

            $totalEffectiveDays++;
            $validDates[] = $date->toDateString(); // Simpan tanggal valid untuk query DB
        }

        if ($totalEffectiveDays === 0) return 0.00;

        // 2. Hitung jumlah 'Hadir' berdasarkan tanggal-tanggal valid tadi
        // Query Eloquent: Cari data siswa di tanggal-tanggal efektif yang statusnya 'Hadir'
        $hadirCount = Absensi::where('id_siswa', $siswaId)
                             ->whereIn('tanggal', $validDates)
                             ->where('status', 'Hadir')
                             ->count();

        // 3. Rumus: (Jumlah Hadir / Total Hari Efektif) * 100%
        return round(($hadirCount / $totalEffectiveDays) * 100, 2);
    }
}