<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GuruController extends Controller
{
    protected string $apiBase;
    protected string $apiGuru;
    protected string $apiAkun;

    public function __construct()
    {
        $this->apiBase = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
        $this->apiGuru = $this->apiBase . '/admin/data_guru.php';
        $this->apiAkun = $this->apiBase . '/admin/generate_akun.php';
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
            Log::error('Guru API Error: ' . $e->getMessage());
            return null;
        }
    }

    /** Halaman daftar guru */
    public function index()
    {
        $data     = $this->callApi($this->apiGuru);
        $guruList = $data['data'] ?? [];
        return view('admin.guru.index', compact('guruList'));
    }

    /** AJAX: simpan (tambah/edit) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|min:3',
            'nip'       => 'required|digits_between:8,18',
            'alamat'    => 'required|string|min:10',
            'no_telp'   => 'required|digits_between:10,15',
            'email'     => 'required|email',
            'kelas'     => 'required|string',
        ]);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiGuru, $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data guru berhasil ditambahkan']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal menyimpan'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: update */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_guru'   => 'required|integer',
            'nama_guru' => 'required|string|min:3',
            'nip'       => 'required|digits_between:8,18',
            'alamat'    => 'required|string|min:10',
            'no_telp'   => 'required|digits_between:10,15',
            'email'     => 'required|email',
            'kelas'     => 'required|string',
        ]);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put($this->apiGuru, $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data guru berhasil diperbarui']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal memperbarui'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: hapus */
    public function destroy(Request $request)
    {
        $request->validate(['id_guru' => 'required|integer']);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->delete($this->apiGuru, ['id_guru' => $request->id_guru]);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data guru berhasil dihapus']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal menghapus'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: lihat akun guru */
    public function akun(Request $request)
    {
        $request->validate(['id_guru' => 'required|integer']);

        try {
            $data = $this->callApi($this->apiAkun, [
                'tipe' => 'guru',
                'id'   => $request->id_guru,
            ]);

            if (($data['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'data' => $data['data']]);
            }
            return response()->json(['success' => false, 'message' => $data['message'] ?? 'Gagal memuat akun'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
