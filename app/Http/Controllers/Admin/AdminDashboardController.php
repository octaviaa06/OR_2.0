<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminDashboardController extends Controller
{
    /** Tampilkan Dashboard Admin */
    public function index()
    {
        $guru  = DB::table('guru')->count();
        $siswa = DB::table('siswa')->count();

        // Agenda 7 hari ke depan (maks 5)
        $agenda = DB::table('kalender')
            ->whereBetween('tanggal', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('tanggal')
            ->limit(5)
            ->get()
            ->map(fn($a) => (array) $a)
            ->toArray();

        $izin = $this->getIzinMenunggu();

        $siswaMasukHariIni = $this->hitungSiswaMasukHariIni();
        $siswaTidakMasuk   = max(0, $siswa - $siswaMasukHariIni);

        return view('admin.dashboard', compact(
            'guru', 'siswa', 'agenda', 'izin',
            'siswaMasukHariIni', 'siswaTidakMasuk'
        ));
    }

    /** Ambil izin berstatus Menunggu */
    protected function getIzinMenunggu(): array
    {
        return DB::table('izin')
            ->join('siswa', 'izin.id_siswa', '=', 'siswa.id_siswa')
            ->where('izin.status', 'Menunggu')
            ->orderBy('izin.tanggal_pengajuan', 'desc')
            ->get([
                'izin.id_izin',
                'izin.jenis_izin',
                'izin.tanggal_mulai',
                'izin.tanggal_selesai',
                'izin.keterangan',
                'izin.status',
                'siswa.nama_siswa',
                'siswa.kelas',
            ])
            ->map(fn($i) => (array) $i)
            ->toArray();
    }

    /** Hitung siswa hadir hari ini */
    protected function hitungSiswaMasukHariIni(): int
    {
        return DB::table('absensi')
            ->where('tanggal', now()->toDateString())
            ->where('status', 'Hadir')
            ->count();
    }

    /** AJAX: approve / reject izin */
    public function updateIzinStatus(Request $request)
    {
        $validated = $request->validate([
            'id_izin'          => 'required|integer',
            'status'           => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $update = [
            'status'              => $validated['status'],
            'tanggal_verifikasi'  => now(),
        ];

        if ($validated['status'] === 'Ditolak' && !empty($validated['alasan_penolakan'])) {
            $update['alasan_penolakan'] = $validated['alasan_penolakan'];
        }

        $affected = DB::table('izin')
            ->where('id_izin', $validated['id_izin'])
            ->update($update);

        if ($affected) {
            return response()->json(['success' => true, 'message' => 'Status izin berhasil diperbarui']);
        }

        return response()->json(['success' => false, 'message' => 'Data izin tidak ditemukan'], 404);
    }

    /** AJAX: refresh data izin */
    public function refreshIzin()
    {
        $izin = $this->getIzinMenunggu();
        return response()->json(['success' => true, 'data' => $izin, 'count' => count($izin)]);
    }
}
