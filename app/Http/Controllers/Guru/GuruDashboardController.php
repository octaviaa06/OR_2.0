<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class GuruDashboardController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
    }

    /**
     * Helper: call API eksternal
     */
    protected function callApi(string $endpoint, array $params = []): ?array
    {
        try {
            $url = "{$this->apiBaseUrl}{$endpoint}";
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->get($url);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Guru API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tampilkan Dashboard Guru
     */
    public function index()
    {
        // Data dashboard utama
        $dashboardData = $this->callApi('/admin/dashboard_admin.php', ['t' => time()]);
        $siswa = $dashboardData['siswa'] ?? 0;

        // Izin menunggu
        $izin = $this->getIzinMenunggu();

        // Hitung kehadiran hari ini
        $siswaMasukHariIni = $this->hitungSiswaMasukHariIni();
        $siswaTidakMasuk   = max(0, $siswa - $siswaMasukHariIni);

        // Agenda 7 hari ke depan
        $agenda = $this->getAgendaTerdekat();

        return view('guru.dashboard', compact(
            'siswa', 'izin', 'siswaMasukHariIni', 'siswaTidakMasuk', 'agenda'
        ));
    }

    /**
     * Ambil izin berstatus Menunggu/Pending
     */
    protected function getIzinMenunggu(): array
    {
        $response = $this->callApi('/perizinan.php', ['t' => time()]);
        $izin = [];

        if (!$response) return $izin;

        $dataList = $response['data'] ?? $response['izin_menunggu'] ?? [];

        if (\is_array($dataList)) {
            foreach ($dataList as $item) {
                $status = strtolower($item['status'] ?? '');
                if (\in_array($status, ['menunggu', 'pending', 'waiting'])) {
                    $izin[] = $item;
                }
            }
        }

        return $izin;
    }

    /**
     * Hitung siswa hadir hari ini
     */
    protected function hitungSiswaMasukHariIni(): int
    {
        $today     = date('Y-m-d');
        $kelasData = $this->callApi('/admin/absensi.php', ['mode' => 'kelas']);
        $kelasList = $kelasData['data'] ?? [];
        $total     = 0;

        foreach ($kelasList as $kelas) {
            $absensiData = $this->callApi('/admin/absensi.php', [
                'kelas'   => $kelas,
                'tanggal' => $today,
            ]);

            foreach ($absensiData['data'] ?? [] as $abs) {
                if (($abs['status_absensi'] ?? '') === 'Hadir') {
                    $total++;
                }
            }
        }

        return $total;
    }

    /**
     * Ambil agenda 7 hari ke depan (maks 5)
     */
    protected function getAgendaTerdekat(): array
    {
        $month      = date('m');
        $year       = date('Y');
        $agendaData = $this->callApi('/admin/agenda.php', ['month' => $month, 'year' => $year]);
        $allAgenda  = $agendaData['data'] ?? [];

        $today = new \DateTime();

        $upcoming = array_filter($allAgenda, function ($item) use ($today) {
            $tgl = $item['tanggal'] ?? '';
            if (!$tgl) return false;
            try {
                $date = new \DateTime($tgl);
                $diff = (int) $today->diff($date)->format('%r%a');
                return $diff >= 0 && $diff <= 7;
            } catch (\Exception $e) {
                return false;
            }
        });

        usort($upcoming, fn($a, $b) => strcmp($a['tanggal'] ?? '', $b['tanggal'] ?? ''));

        return array_slice(array_values($upcoming), 0, 5);
    }

    /**
     * Update status izin via AJAX
     */
    public function updateIzinStatus(Request $request)
    {
        $validated = $request->validate([
            'id_izin'          => 'required|integer',
            'status'           => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        try {
            $payload = [
                'id_izin'              => $validated['id_izin'],
                'status'               => $validated['status'],
                'id_guru_verifikasi'   => Session::get('id_akun'),
            ];

            if ($validated['status'] === 'Ditolak' && !empty($validated['alasan_penolakan'])) {
                $payload['alasan_penolakan'] = $validated['alasan_penolakan'];
            }

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withOptions(['verify' => false])
                ->put("{$this->apiBaseUrl}/perizinan.php", $payload);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['success'] ?? ($result['status'] ?? '') === 'success') {
                    return response()->json(['success' => true, 'message' => 'Status izin berhasil diperbarui']);
                }
            }

            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status'], 400);

        } catch (\Exception $e) {
            Log::error('Guru Update Izin Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Refresh data izin via AJAX
     */
    public function refreshIzin()
    {
        $izin = $this->getIzinMenunggu();
        return response()->json(['success' => true, 'data' => $izin, 'count' => \count($izin)]);
    }
}
