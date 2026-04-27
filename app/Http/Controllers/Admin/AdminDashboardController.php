<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
    }

    /**
     * Helper function untuk call API eksternal
     */
    protected function callApi($endpoint, $params = [])
    {
        try {
            $url = $this->apiBaseUrl . $endpoint;
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }

            $response = Http::timeout(10)
                ->withOptions(['verify' => false]) // Hanya untuk development, production gunakan SSL valid
                ->get($url);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tampilkan Dashboard Admin
     */
    public function index()
    {
        // Ambil data dashboard utama
        $dashboardData = $this->callApi('/admin/dashboard_admin.php', ['t' => time()]);
        
        $guru = $dashboardData['guru'] ?? 0;
        $siswa = $dashboardData['siswa'] ?? 0;
        $agenda = $dashboardData['agenda_terdekat'] ?? [];

        // Ambil data izin dengan filter "Menunggu"
        $izin = $this->getIzinMenunggu();

        // Hitung siswa masuk hari ini
        $siswaMasukHariIni = $this->hitungSiswaMasukHariIni();
        $siswaTidakMasuk = max(0, $siswa - $siswaMasukHariIni);

        return view('admin.dashboard', compact(
            'guru', 'siswa', 'agenda', 'izin', 
            'siswaMasukHariIni', 'siswaTidakMasuk'
        ));
    }

    /**
     * Ambil izin dengan status Menunggu/Pending
     */
    protected function getIzinMenunggu()
    {
        $response = $this->callApi('/perizinan.php', ['t' => time()]);
        $izin = [];

        if (!$response) return $izin;

        // Handle berbagai kemungkinan struktur response
        $dataList = $response['data'] ?? $response['izin_menunggu'] ?? [];

        if (is_array($dataList)) {
            foreach ($dataList as $item) {
                $status = strtolower($item['status'] ?? '');
                if (in_array($status, ['menunggu', 'pending', 'waiting'])) {
                    $izin[] = $item;
                }
            }
        }

        return $izin;
    }

    /**
     * Hitung siswa yang hadir hari ini
     */
    protected function hitungSiswaMasukHariIni()
    {
        $today = date('Y-m-d');
        $kelasData = $this->callApi('/admin/absensi.php', ['mode' => 'kelas']);
        $kelasList = $kelasData['data'] ?? [];
        $totalMasuk = 0;

        foreach ($kelasList as $kelas) {
            $absensiData = $this->callApi('/admin/absensi.php', [
                'kelas' => $kelas,
                'tanggal' => $today
            ]);

            foreach ($absensiData['data'] ?? [] as $abs) {
                if (($abs['status_absensi'] ?? '') === 'Hadir') {
                    $totalMasuk++;
                }
            }
        }

        return $totalMasuk;
    }

    /**
     * Update status izin (Approve/Reject) via AJAX
     */
    public function updateIzinStatus(Request $request)
    {
        $validated = $request->validate([
            'id_izin' => 'required|integer',
            'status' => 'required|in:Disetujui,Ditolak',
            'alasan_penolakan' => 'nullable|string|max:500',
            'id_admin_verifikasi' => 'nullable|integer'
        ]);

        try {
            $payload = [
                'id_izin' => $validated['id_izin'],
                'status' => $validated['status'],
                'id_admin_verifikasi' => $validated['id_admin_verifikasi'] ?? Session::get('id_akun')
            ];

            if ($validated['status'] === 'Ditolak' && !empty($validated['alasan_penolakan'])) {
                $payload['alasan_penolakan'] = $validated['alasan_penolakan'];
            }

            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withOptions(['verify' => false])
                ->put($this->apiBaseUrl . '/perizinan.php', $payload);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['success'] ?? $result['status'] === 'success') {
                    return response()->json(['success' => true, 'message' => 'Status izin berhasil diperbarui']);
                }
            }

            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status'], 400);

        } catch (\Exception $e) {
            Log::error('Update Izin Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Refresh data izin untuk auto-update via AJAX
     */
    public function refreshIzin()
    {
        $izin = $this->getIzinMenunggu();
        return response()->json(['success' => true, 'data' => $izin, 'count' => count($izin)]);
    }
}