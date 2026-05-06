<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KalenderController extends Controller
{
    protected string $apiBase;

    public function __construct()
    {
        $this->apiBase = env('API_BASE_URL', 'https://ortuconnect.pbltifnganjuk.com/api');
    }

    protected function callApi(string $url, array $params = []): array
    {
        try {
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
            $response = Http::timeout(10)->withOptions(['verify' => false])->get($url);
            return $response->successful() ? ($response->json() ?? []) : [];
        } catch (\Exception $e) {
            Log::error('Kalender API Error: ' . $e->getMessage());
            return [];
        }
    }

    /** Halaman kalender */
    public function index(Request $request)
    {
        $month = (int) $request->query('month', date('n'));
        $year  = (int) $request->query('year',  date('Y'));

        // Navigasi bulan
        $nav = $request->query('nav');
        if ($nav === 'next') {
            $month++;
            if ($month > 12) { $month = 1; $year++; }
        } elseif ($nav === 'prev') {
            $month--;
            if ($month < 1) { $month = 12; $year--; }
        }

        // Komponen kalender
        $firstDay      = mktime(0, 0, 0, $month, 1, $year);
        $numberOfDays  = (int) date('t', $firstDay);
        $dayOfWeek     = (int) date('w', $firstDay);   // 0=Sun
        $monthNameEn   = date('F', $firstDay);

        $bulanId = [
            'January'=>'Januari','February'=>'Februari','March'=>'Maret',
            'April'=>'April','May'=>'Mei','June'=>'Juni',
            'July'=>'Juli','August'=>'Agustus','September'=>'September',
            'October'=>'Oktober','November'=>'November','December'=>'Desember',
        ];
        $monthNameId = $bulanId[$monthNameEn] ?? $monthNameEn;

        // Ambil agenda dari API
        $data      = $this->callApi("{$this->apiBase}/admin/agenda.php", ['month' => $month, 'year' => $year]);
        $agendaList = $data['data'] ?? [];

        // Kelompokkan per tanggal
        $agendaByDate = [];
        foreach ($agendaList as $agenda) {
            $key = date('Y-m-d', strtotime($agenda['tanggal']));
            $agendaByDate[$key][] = $agenda;
        }

        // Tanggal yang dipilih
        $defaultDay       = (date('Y') == $year && date('n') == $month) ? (int) date('j') : 1;
        $selectedDay      = (int) $request->query('day', $defaultDay);
        $selectedDateFull = date('Y-m-d', mktime(0, 0, 0, $month, $selectedDay, $year));
        $selectedAgenda   = $agendaByDate[$selectedDateFull] ?? [];

        return view('admin.kalender.index', compact(
            'month', 'year', 'monthNameId',
            'numberOfDays', 'dayOfWeek',
            'agendaByDate', 'selectedDay', 'selectedDateFull', 'selectedAgenda'
        ));
    }

    /** AJAX: simpan agenda (POST = tambah, PUT = edit) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|min:2',
            'tanggal'       => 'required|date',
            'deskripsi'     => 'nullable|string',
        ]);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiBase}/admin/agenda.php", $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'message' => $result['message'] ?? 'Agenda berhasil ditambahkan']);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Gagal menyimpan'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: update agenda */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'required',
            'nama_kegiatan' => 'required|string|min:2',
            'tanggal'       => 'required|date',
            'deskripsi'     => 'nullable|string',
        ]);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->put("{$this->apiBase}/admin/agenda.php", $validated);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'message' => $result['message'] ?? 'Agenda berhasil diperbarui']);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Gagal memperbarui'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: hapus agenda */
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required']);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->delete("{$this->apiBase}/admin/agenda.php", ['id' => $request->id]);

            $result = $response->json();
            if (($result['status'] ?? '') === 'success') {
                return response()->json(['status' => 'success', 'message' => $result['message'] ?? 'Agenda berhasil dihapus']);
            }
            return response()->json(['status' => 'error', 'message' => $result['message'] ?? 'Gagal menghapus'], 400);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** AJAX: ambil detail satu agenda */
    public function show(Request $request)
    {
        $request->validate(['id' => 'required']);

        try {
            $data = $this->callApi("{$this->apiBase}/admin/agenda.php", ['id' => $request->id]);
            if (($data['status'] ?? '') === 'success' && isset($data['data'])) {
                return response()->json(['status' => 'success', 'data' => $data['data']]);
            }
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
