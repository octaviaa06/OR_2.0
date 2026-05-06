<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    protected string $apiBase;
    protected string $apiSiswa;
    protected string $apiAkun;

    public function __construct()
    {
        $this->apiBase  = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
        $this->apiSiswa = $this->apiBase . '/admin/data_siswa.php';
        $this->apiAkun  = $this->apiBase . '/admin/generate_akun.php';
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
            Log::error('Siswa API Error: ' . $e->getMessage());
            return null;
        }
    }

    /** Halaman daftar siswa */
    public function index(Request $request)
    {
        $selectedKelas = $request->query('kelas_filter', '');

        // Ambil semua data tanpa filter kelas ke API
        // (normalisasi dan filter dilakukan di sisi client/blade)
        $data      = $this->callApi($this->apiSiswa);
        $rawList   = $data['data'] ?? [];

        // Normalisasi kelas: "Kelas A" → "A", "kelas b" → "B", dst.
        $siswaList = array_map(function ($siswa) {
            if (isset($siswa['kelas'])) {
                $kelas = trim($siswa['kelas']);
                $kelas = preg_replace('/^kelas\s+/i', '', $kelas);
                $siswa['kelas'] = strtoupper($kelas);
            }
            return $siswa;
        }, $rawList);

        // Filter di PHP setelah normalisasi
        if ($selectedKelas !== '') {
            $siswaList = array_values(array_filter($siswaList, fn($s) => ($s['kelas'] ?? '') === strtoupper($selectedKelas)));
        }

        $today = now()->toDateString();

        return view('admin.siswa.index', compact('siswaList', 'selectedKelas', 'today'));
    }

    /** AJAX: tambah siswa */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa'   => 'required|string|min:3',
            'kelas'        => 'required|string',
            'tanggal_lahir'=> 'required|date|before_or_equal:today',
            'gender'       => 'required|in:Laki-Laki,Perempuan',
            'nama_ortu'    => 'required|string|min:3',
            'no_telp_ortu' => 'required|digits_between:10,15',
            'alamat'       => 'nullable|string',
        ]);

        // Normalisasi kelas sebelum dikirim ke API
        $validated['kelas'] = strtoupper(preg_replace('/^kelas\s+/i', '', trim($validated['kelas'])));

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->apiSiswa, $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data siswa berhasil ditambahkan']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal menyimpan'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: update siswa */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_siswa'     => 'required|integer',
            'nama_siswa'   => 'required|string|min:3',
            'kelas'        => 'required|string',
            'tanggal_lahir'=> 'required|date|before_or_equal:today',
            'gender'       => 'required|in:Laki-Laki,Perempuan',
            'nama_ortu'    => 'required|string|min:3',
            'no_telp_ortu' => 'required|digits_between:10,15',
            'alamat'       => 'nullable|string',
        ]);

        // Normalisasi kelas sebelum dikirim ke API
        $validated['kelas'] = strtoupper(preg_replace('/^kelas\s+/i', '', trim($validated['kelas'])));

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put($this->apiSiswa, $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data siswa berhasil diperbarui']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal memperbarui'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: hapus siswa */
    public function destroy(Request $request)
    {
        $request->validate(['id_siswa' => 'required|integer']);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->delete($this->apiSiswa, ['id_siswa' => $request->id_siswa]);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['success' => true, 'message' => 'Data siswa berhasil dihapus']);
            }
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Gagal menghapus'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: lihat akun siswa */
    public function akun(Request $request)
    {
        $request->validate(['id_siswa' => 'required|integer']);

        try {
            $data = $this->callApi($this->apiAkun, [
                'tipe' => 'siswa',
                'id'   => $request->id_siswa,
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
