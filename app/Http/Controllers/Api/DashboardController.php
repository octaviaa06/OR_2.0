<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard?id_siswa=
     * Data dashboard: profil, agenda mendatang, kehadiran minggu ini, status izin terbaru
     */
    public function index(Request $request)
    {
        $idSiswa = $request->query('id_siswa') ?? $request->attributes->get('id_siswa');

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'id_siswa diperlukan.',
            ], 422);
        }

        // 1. Profil siswa
        $siswa = DB::table('siswa')->where('id_siswa', $idSiswa)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        // 2. Agenda mendatang H-1 s/d H+3
        $today     = now()->toDateString();
        $dateFrom  = now()->subDay()->toDateString();
        $dateTo    = now()->addDays(3)->toDateString();

        $agenda = DB::table('kalender')
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->orderBy('tanggal', 'asc')
            ->get(['id_kegiatan', 'nama_kegiatan', 'tanggal', 'deskripsi']);

        // 3. Kehadiran minggu ini (Senin s/d hari ini)
        $startOfWeek = now()->startOfWeek()->toDateString(); // Senin
        $endOfWeek   = now()->toDateString();

        $absensiMingguIni = DB::table('absensi')
            ->where('id_siswa', $idSiswa)
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->get(['tanggal', 'status']);

        $totalHari  = $absensiMingguIni->count();
        $jumlahHadir = $absensiMingguIni->where('status', 'Hadir')->count();

        // 4. Status izin terbaru
        $izinTerbaru = DB::table('izin')
            ->where('id_siswa', $idSiswa)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->first([
                'id_izin', 'jenis_izin', 'tanggal_mulai',
                'tanggal_selesai', 'status', 'tanggal_pengajuan',
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'profil' => [
                    'id_siswa'   => $siswa->id_siswa,
                    'nama_siswa' => $siswa->nama_siswa,
                    'kelas'      => $siswa->kelas,
                    'foto'       => $siswa->foto ?? null,
                ],
                'agenda_mendatang' => $agenda,
                'kehadiran_minggu_ini' => [
                    'jumlah_hadir' => $jumlahHadir,
                    'total_hari'   => $totalHari,
                    'detail'       => $absensiMingguIni,
                ],
                'izin_terbaru' => $izinTerbaru,
            ],
        ]);
    }
}
