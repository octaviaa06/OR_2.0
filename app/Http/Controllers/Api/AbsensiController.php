<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * GET /api/absensi?id_siswa=&bulan=
     * Riwayat absensi per bulan
     * bulan format: YYYY-MM (contoh: 2025-11)
     */
    public function index(Request $request)
    {
        $idSiswa = $request->query('id_siswa') ?? $request->attributes->get('id_siswa');
        $bulan   = $request->query('bulan', now()->format('Y-m'));

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'id_siswa diperlukan.',
            ], 422);
        }

        // Validasi format bulan YYYY-MM
        if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            return response()->json([
                'success' => false,
                'message' => 'Format bulan tidak valid. Gunakan format YYYY-MM.',
            ], 422);
        }

        [$tahun, $bln] = explode('-', $bulan);

        $absensi = DB::table('absensi')
            ->where('id_siswa', $idSiswa)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->orderBy('tanggal', 'asc')
            ->get(['id_absensi', 'tanggal', 'status']);

        // Hitung rekap
        $rekap = [
            'Hadir' => $absensi->where('status', 'Hadir')->count(),
            'Izin'  => $absensi->where('status', 'Izin')->count(),
            'Sakit' => $absensi->where('status', 'Sakit')->count(),
            'Alpa'  => $absensi->where('status', 'Alpa')->count(),
            'total' => $absensi->count(),
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'id_siswa' => (int) $idSiswa,
                'bulan'    => $bulan,
                'rekap'    => $rekap,
                'absensi'  => $absensi->values(),
            ],
        ]);
    }
}
