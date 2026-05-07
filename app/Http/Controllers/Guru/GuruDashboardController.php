<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GuruDashboardController extends Controller
{
    /** Tampilkan Dashboard Guru */
    public function index()
    {
        // Hitung siswa sesuai kelas guru (atau semua jika admin)
        $kelasGuru = $this->getKelasGuru();

        $siswaQuery = DB::table('siswa');
        if (!empty($kelasGuru)) {
            $siswaQuery->whereIn('kelas', $kelasGuru);
        }
        $siswa = $siswaQuery->count();

        $izin              = $this->getIzinMenunggu();
        $siswaMasukHariIni = $this->hitungSiswaMasukHariIni($kelasGuru);
        $siswaTidakMasuk   = max(0, $siswa - $siswaMasukHariIni);
        $agenda            = $this->getAgendaTerdekat();

        return view('guru.dashboard', compact(
            'siswa', 'izin', 'siswaMasukHariIni', 'siswaTidakMasuk', 'agenda'
        ));
    }

    /** Ambil kelas yang dipegang guru dari session */
    protected function getKelasGuru(): array
    {
        $kelasSession = Session::get('kelas', '');
        if (!$kelasSession) return [];

        return str_contains($kelasSession, ',')
            ? array_map('trim', explode(',', $kelasSession))
            : [$kelasSession];
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
    protected function hitungSiswaMasukHariIni(array $kelasGuru): int
    {
        $query = DB::table('absensi')
            ->join('siswa', 'absensi.id_siswa', '=', 'siswa.id_siswa')
            ->where('absensi.tanggal', now()->toDateString())
            ->where('absensi.status', 'Hadir');

        if (!empty($kelasGuru)) {
            $query->whereIn('siswa.kelas', $kelasGuru);
        }

        return $query->count();
    }

    /** Ambil agenda 7 hari ke depan (maks 5) */
    protected function getAgendaTerdekat(): array
    {
        return DB::table('kalender')
            ->whereBetween('tanggal', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('tanggal')
            ->limit(5)
            ->get()
            ->map(fn($a) => (array) $a)
            ->toArray();
    }

    /** AJAX: approve / reject izin */
    public function updateIzinStatus(Request $request)
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

    /** AJAX: refresh data izin */
    public function refreshIzin()
    {
        $izin = $this->getIzinMenunggu();
        return response()->json(['success' => true, 'data' => $izin, 'count' => count($izin)]);
    }
}
