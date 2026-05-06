<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AbsensiController extends Controller
{
    protected string $apiBase;

    public function __construct()
    {
        $this->apiBase = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
    }

    protected function callApi(string $url, array $params = []): ?array
    {
        try {
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($url);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Absensi API Error: ' . $e->getMessage());
            return null;
        }
    }

    /** Halaman utama absensi */
    public function index(Request $request)
    {
        $today   = now()->toDateString();
        $minDate = now()->subDays(5)->toDateString();
        $maxDate = $today;

        // Ambil semua kelas dari API
        $kelasData = $this->callApi("{$this->apiBase}/admin/absensi.php", ['mode' => 'kelas']);
        $allKelas  = $kelasData['data'] ?? [];

        // Kelas dari session
        $kelasSession = Session::get('kelas', '');
        $kelasGuru    = [];
        if ($kelasSession) {
            $kelasGuru = str_contains($kelasSession, ',')
                ? array_map('trim', explode(',', $kelasSession))
                : [$kelasSession];
        }

        // Daftar kelas yang bisa diakses
        $kelasList = !empty($kelasGuru)
            ? array_values(array_unique(array_filter($allKelas, fn($k) => in_array($k, $kelasGuru))))
            : array_values(array_unique($allKelas));

        if (empty($kelasList) && !empty($kelasGuru)) {
            $kelasList = array_values(array_unique($kelasGuru));
        }

        $kelasDefault  = $kelasList[0] ?? '';
        $selectedClass = $request->query('kelas', '');   // kosong = semua kelas (default view)
        $selectedDate  = $request->query('tanggal', $today);

        // Validasi tanggal
        if ($selectedDate > $maxDate) $selectedDate = $maxDate;
        if ($selectedDate < $minDate) $selectedDate = $minDate;

        // Validasi kelas (jika dipilih)
        if ($selectedClass && !empty($kelasGuru) && !in_array($selectedClass, $kelasGuru)) {
            $selectedClass = '';
        }

        $absensiList  = [];
        $isDefaultView = $selectedClass === '';   // true = tampilkan semua belum absen

        if ($isDefaultView) {
            // Kumpulkan semua siswa yang BELUM diabsen dari semua kelas
            foreach ($kelasList as $kelas) {
                $data = $this->callApi("{$this->apiBase}/admin/absensi.php", [
                    'kelas'   => $kelas,
                    'tanggal' => $selectedDate,
                ]);
                foreach ($data['data'] ?? [] as $siswa) {
                    $siswa['is_recorded'] = !empty($siswa['status_absensi']);
                    $siswa['kelas_nama']  = $kelas;   // tambah info kelas untuk tampilan
                    if (!$siswa['is_recorded']) {
                        $absensiList[] = $siswa;
                    }
                }
            }
        } else {
            // Kelas spesifik dipilih — tampilkan semua siswa kelas itu
            $data = $this->callApi("{$this->apiBase}/admin/absensi.php", [
                'kelas'   => $selectedClass,
                'tanggal' => $selectedDate,
            ]);
            $absensiList = array_map(function ($a) use ($selectedClass) {
                $a['is_recorded'] = !empty($a['status_absensi']);
                $a['kelas_nama']  = $selectedClass;
                return $a;
            }, $data['data'] ?? []);
        }

        return view('guru.absensi.index', compact(
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
            'tanggal' => 'required|date',
            'kelas'   => 'required|string',
            'absensi' => 'required|array',
        ]);

        try {
            $payload = [
                'tanggal' => $request->tanggal,
                'kelas'   => $request->kelas,
                'absensi' => array_map(fn($item) => [
                    'id_murid' => $item['id_murid'],
                    'status'   => $item['status'],
                ], $request->absensi),
            ];

            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiBase}/admin/absensi.php", $payload);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'message' => $result['message'] ?? 'Absensi berhasil disimpan']);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Gagal menyimpan'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: export data untuk PDF (return JSON, PDF dibuat di JS) */
    public function exportData(Request $request)
    {
        $request->validate([
            'kelas'       => 'required|string',
            'tanggal'     => 'required|date',
            'filter_type' => 'required|in:hari,minggu,bulan',
        ]);

        $kelas      = $request->kelas;
        $tanggal    = $request->tanggal;
        $filterType = $request->filter_type;

        [$startDate, $endDate] = $this->getDateRange($filterType, $tanggal);

        // Kumpulkan data semua tanggal dalam rentang
        $allData    = [];
        $current    = new \DateTime($startDate);
        $end        = new \DateTime($endDate);

        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $data    = $this->callApi("{$this->apiBase}/admin/absensi.php", [
                'kelas'   => $kelas,
                'tanggal' => $dateStr,
            ]);
            foreach ($data['data'] ?? [] as $item) {
                $item['tanggal_absensi'] = $dateStr;
                $allData[] = $item;
            }
            $current->modify('+1 day');
        }

        // Hitung statistik per siswa
        $statistics = [];
        foreach ($allData as $item) {
            $id = $item['id_siswa'];
            if (!isset($statistics[$id])) {
                $statistics[$id] = ['nama' => $item['nama_siswa'], 'Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
            }
            $status = $item['status_absensi'] ?? '';
            if ($status && isset($statistics[$id][$status])) {
                $statistics[$id][$status]++;
            }
        }

        return response()->json([
            'status'     => 'success',
            'kelas'      => $kelas,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'data'       => array_values($statistics),
        ]);
    }

    private function getDateRange(string $type, string $date): array
    {
        $d = new \DateTime($date);
        return match ($type) {
            'minggu' => [
                (clone $d)->modify('monday this week')->format('Y-m-d'),
                (clone $d)->modify('sunday this week')->format('Y-m-d'),
            ],
            'bulan' => [
                $d->format('Y-m-01'),
                $d->format('Y-m-t'),
            ],
            default => [$date, $date],
        };
    }
}
