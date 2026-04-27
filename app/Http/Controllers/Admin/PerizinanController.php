<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PerizinanController extends Controller
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
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Cache-Control' => 'no-cache'])
                ->get($url);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('Perizinan API Error: ' . $e->getMessage());
            return null;
        }
    }

    /** Halaman daftar perizinan */
    public function index()
    {
        $data          = $this->callApi("{$this->apiBase}/perizinan.php", ['t' => time()]);
        $perizinanList = $data['data'] ?? [];

        return view('admin.perizinan.index', compact('perizinanList'));
    }

    /** AJAX: setujui atau tolak izin */
    public function updateStatus(Request $request)
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
                'id_guru_verifikasi'   => Session::get('id_akun', 0),
            ];

            if ($validated['status'] === 'Ditolak' && !empty($validated['alasan_penolakan'])) {
                $payload['alasan_penolakan'] = $validated['alasan_penolakan'];
            }

            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put("{$this->apiBase}/perizinan.php", $payload);

            $result = $response->json();

            if ($result['success'] ?? false) {
                return response()->json(['success' => true, 'message' => 'Status izin berhasil diperbarui']);
            }

            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal memperbarui status'], 400);
        } catch (\Exception $e) {
            Log::error('Update Perizinan Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
