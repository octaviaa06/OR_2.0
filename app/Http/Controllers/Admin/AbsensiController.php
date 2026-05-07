<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AbsensiController extends Controller
{
    /** Halaman utama absensi */
    public function index(Request $request)
    {
        $today   = now()->toDateString();
        $minDate = now()->subDays(5)->toDateString();
        $maxDate = $today;

        // Ambil semua kelas unik dari tabel siswa
        $allKelas = DB::table('siswa')
            ->select('kelas')->distinct()->orderBy('kelas')
            ->pluck('kelas')->toArray();

        // Kelas yang boleh diakses guru (dari session)
        $kelasSession = Session::get('kelas', '');
        $kelasGuru    = [];
        if ($kelasSession) {
            $kelasGuru = str_contains($kelasSession, ',')
                ? array_map('trim', explode(',', $kelasSession))
                : [$kelasSession];
        }

        $kelasList = !empty($kelasGuru)
            ? array_values(array_filter($allKelas, fn($k) => in_array($k, $kelasGuru)))
            : $allKelas;

        $kelasDefault  = $kelasList[0] ?? '';
        $selectedClass = $request->query('kelas', '');
        $selectedDate  = $request->query('tanggal', $today);

        // Validasi tanggal
        $selectedDate = max($minDate, min($maxDate, $selectedDate));

        // Validasi kelas
        if ($selectedClass && !empty($kelasGuru) && !in_array($selectedClass, $kelasGuru)) {
            $selectedClass = '';
        }

        $isDefaultView = $selectedClass === '';
        $absensiList   = [];
        $targetKelas   = $isDefaultView ? $kelasList : [$selectedClass];

        foreach ($targetKelas as $kelas) {
            $siswaList = DB::table('siswa')
                ->where('kelas', $kelas)
                ->orderBy('nama_siswa')
                ->get(['id_siswa', 'nama_siswa', 'kelas'])
                ->toArray();

            $absensiHariIni = DB::table('absensi')
                ->where('tanggal', $selectedDate)
                ->whereIn('id_siswa', array_column($siswaList, 'id_siswa'))
                ->pluck('status', 'id_siswa')
                ->toArray();

            foreach ($siswaList as $siswa) {
                $statusAbsensi = $absensiHariIni[$siswa->id_siswa] ?? null;
                $row = [
                    'id_siswa'       => $siswa->id_siswa,
                    'nama_siswa'     => $siswa->nama_siswa,
                    'kelas_nama'     => $siswa->kelas,
                    'status_absensi' => $statusAbsensi,
                    'is_recorded'    => !is_null($statusAbsensi),
                ];

                if ($isDefaultView && $row['is_recorded']) continue;

                $absensiList[] = $row;
            }
        }

        return view('admin.absensi.index', compact(
            'kelasList', 'kelasGuru', 'kelasDefault',
            'selectedClass', 'selectedDate', 'isDefaultView',
            'today', 'minDate', 'maxDate',
            'absensiList'
        ));
    }

    /** AJAX: simpan absensi */
    public function simpan(Request $request)
    {
        $request->validate([
            'tanggal'            => 'required|date',
            'kelas'              => 'required|string',
            'absensi'            => 'required|array',
            'absensi.*.id_murid' => 'required|integer|exists:siswa,id_siswa',
            'absensi.*.status'   => 'required|in:Hadir,Izin,Sakit,Alpa',
        ]);

        $tanggal = $request->tanggal;

        DB::transaction(function () use ($request, $tanggal) {
            foreach ($request->absensi as $item) {
                DB::table('absensi')->updateOrInsert(
                    ['id_siswa' => $item['id_murid'], 'tanggal' => $tanggal],
                    ['status'   => $item['status']]
                );
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Absensi berhasil disimpan']);
    }

    /** AJAX: export data absensi (return JSON, PDF dibuat di JS) */
    public function exportData(Request $request)
    {
        $request->validate([
            'kelas'       => 'required|string',
            'tanggal'     => 'required|date',
            'filter_type' => 'required|in:hari,minggu,bulan',
        ]);

        [$startDate, $endDate] = $this->getDateRange($request->filter_type, $request->tanggal);

        $rows = DB::table('absensi')
            ->join('siswa', 'absensi.id_siswa', '=', 'siswa.id_siswa')
            ->where('siswa.kelas', $request->kelas)
            ->whereBetween('absensi.tanggal', [$startDate, $endDate])
            ->get(['siswa.id_siswa', 'siswa.nama_siswa', 'absensi.status'])
            ->toArray();

        $statistics = [];
        foreach ($rows as $row) {
            $id = $row->id_siswa;
            if (!isset($statistics[$id])) {
                $statistics[$id] = [
                    'nama'  => $row->nama_siswa,
                    'Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0,
                ];
            }
            if (isset($statistics[$id][$row->status])) {
                $statistics[$id][$row->status]++;
            }
        }

        return response()->json([
            'status'     => 'success',
            'kelas'      => $request->kelas,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'data'       => array_values($statistics),
        ]);
    }

    private function getDateRange(string $type, string $date): array
    {
        $d = new \DateTime($date);
        if ($type === 'minggu') {
            return [
                (clone $d)->modify('monday this week')->format('Y-m-d'),
                (clone $d)->modify('sunday this week')->format('Y-m-d'),
            ];
        }
        if ($type === 'bulan') {
            return [$d->format('Y-m-01'), $d->format('Y-m-t')];
        }
        return [$date, $date];
    }
}
