<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PerizinanController extends Controller
{
    /** Halaman daftar perizinan */
    public function index()
    {
        $perizinanList = DB::table('izin')
            ->join('siswa', 'izin.id_siswa', '=', 'siswa.id_siswa')
            ->orderBy('izin.tanggal_pengajuan', 'desc')
            ->get([
                'izin.id_izin',
                'izin.tanggal_pengajuan',
                'izin.tanggal_mulai',
                'izin.tanggal_selesai',
                'izin.jenis_izin',
                'izin.keterangan',
                'izin.status',
                'izin.alasan_penolakan',
                'izin.tanggal_verifikasi',
                'siswa.nama_siswa',
                'siswa.kelas',
            ])
            ->map(function ($i) {
                $arr = (array) $i;
                // Generate tanggal_range dari tanggal_mulai + tanggal_selesai
                $mulai   = $arr['tanggal_mulai'] ?? '';
                $selesai = $arr['tanggal_selesai'] ?? '';
                if ($mulai && $selesai && $mulai !== $selesai) {
                    $arr['tanggal_range'] = date('d/m/Y', strtotime($mulai))
                        . ' - ' . date('d/m/Y', strtotime($selesai));
                } elseif ($mulai) {
                    $arr['tanggal_range'] = date('d/m/Y', strtotime($mulai));
                } else {
                    $arr['tanggal_range'] = '-';
                }
                return $arr;
            })
            ->toArray();

        return view('guru.perizinan.index', compact('perizinanList'));
    }

    /** AJAX: setujui atau tolak izin */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id_izin'          => 'required|integer|exists:izin,id_izin',
            'status'           => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $update = [
            'status'             => $validated['status'],
            'tanggal_verifikasi' => now(),
        ];

        if ($validated['status'] === 'Ditolak' && !empty($validated['alasan_penolakan'])) {
            $update['alasan_penolakan'] = $validated['alasan_penolakan'];
        }

        DB::table('izin')->where('id_izin', $validated['id_izin'])->update($update);

        return response()->json(['success' => true, 'message' => 'Status izin berhasil diperbarui']);
    }
}
