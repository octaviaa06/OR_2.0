<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerizinanController extends Controller
{
    /**
     * GET /api/perizinan?username=
     * Riwayat izin siswa
     */
    public function index(Request $request)
    {
        $idSiswa = $request->attributes->get('id_siswa');

        // Bisa juga cari by username
        $username = $request->query('username');
        if ($username) {
            $akun = DB::table('akun')->where('username', $username)->first();
            if (!$akun || !$akun->id_siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan.',
                ], 404);
            }
            $idSiswa = $akun->id_siswa;
        }

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $perizinan = DB::table('izin')
            ->where('id_siswa', $idSiswa)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get([
                'id_izin', 'tanggal_pengajuan', 'tanggal_mulai',
                'tanggal_selesai', 'jenis_izin', 'keterangan',
                'status', 'alasan_penolakan', 'tanggal_verifikasi',
            ]);

        return response()->json([
            'success' => true,
            'data'    => $perizinan->values(),
        ]);
    }

    /**
     * POST /api/perizinan
     * Ajukan izin baru
     * Body: { tanggal_mulai, tanggal_selesai, jenis_izin, keterangan }
     */
    public function store(Request $request)
    {
        $idSiswa = $request->attributes->get('id_siswa');

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis_izin'      => 'required|in:Sakit,Izin',
            'keterangan'      => 'nullable|string|max:500',
        ]);

        // Cek apakah sudah ada izin yang overlap
        $overlap = DB::table('izin')
            ->where('id_siswa', $idSiswa)
            ->where('status', '!=', 'Ditolak')
            ->where(function ($q) use ($request) {
                $q->whereBetween('tanggal_mulai', [$request->tanggal_mulai, $request->tanggal_selesai])
                  ->orWhereBetween('tanggal_selesai', [$request->tanggal_mulai, $request->tanggal_selesai]);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada pengajuan izin pada tanggal tersebut.',
            ], 422);
        }

        $idIzin = DB::table('izin')->insertGetId([
            'id_siswa'          => $idSiswa,
            'tanggal_pengajuan' => now(),
            'tanggal_mulai'     => $request->tanggal_mulai,
            'tanggal_selesai'   => $request->tanggal_selesai,
            'jenis_izin'        => $request->jenis_izin,
            'keterangan'        => $request->keterangan,
            'status'            => 'Menunggu',
        ]);

        $izin = DB::table('izin')->where('id_izin', $idIzin)->first();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan izin berhasil dikirim.',
            'data'    => $izin,
        ], 201);
    }

    /**
     * GET /api/perizinan/status?id_izin=
     * Cek status izin tertentu (untuk polling notifikasi)
     */
    public function cekStatus(Request $request)
    {
        $idSiswa = $request->attributes->get('id_siswa');
        $idIzin  = $request->query('id_izin');

        $query = DB::table('izin')->where('id_siswa', $idSiswa);

        if ($idIzin) {
            $query->where('id_izin', $idIzin);
            $izin = $query->first(['id_izin', 'status', 'alasan_penolakan', 'tanggal_verifikasi']);
        } else {
            // Ambil izin terbaru
            $izin = $query->orderBy('tanggal_pengajuan', 'desc')
                ->first(['id_izin', 'status', 'alasan_penolakan', 'tanggal_verifikasi']);
        }

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Data izin tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $izin,
        ]);
    }
}
