<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KalenderController extends Controller
{
    /**
     * Data hari libur nasional Indonesia (hardcoded per tahun).
     * Update array ini setiap awal tahun baru.
     * Format: ['YYYY-MM-DD' => 'Nama Hari Libur']
     */
    private function getHariLiburNasional(int $year): array
    {
        $data = [
            2025 => [
                '2025-01-01' => 'Tahun Baru Masehi',
                '2025-01-27' => 'Isra Miraj Nabi Muhammad SAW',
                '2025-01-28' => 'Cuti Bersama Isra Miraj',
                '2025-01-29' => 'Tahun Baru Imlek 2576',
                '2025-03-29' => 'Hari Suci Nyepi (Tahun Baru Saka 1947)',
                '2025-03-31' => 'Idul Fitri 1446 H',
                '2025-04-01' => 'Idul Fitri 1446 H',
                '2025-04-02' => 'Cuti Bersama Idul Fitri',
                '2025-04-03' => 'Cuti Bersama Idul Fitri',
                '2025-04-04' => 'Cuti Bersama Idul Fitri',
                '2025-04-07' => 'Cuti Bersama Idul Fitri',
                '2025-04-18' => 'Wafat Isa Al Masih',
                '2025-05-01' => 'Hari Buruh Internasional',
                '2025-05-12' => 'Hari Raya Waisak 2569',
                '2025-05-29' => 'Kenaikan Isa Al Masih',
                '2025-06-01' => 'Hari Lahir Pancasila',
                '2025-06-06' => 'Idul Adha 1446 H',
                '2025-06-09' => 'Cuti Bersama Idul Adha',
                '2025-06-27' => 'Tahun Baru Islam 1447 H',
                '2025-08-17' => 'Hari Kemerdekaan Republik Indonesia',
                '2025-09-05' => 'Maulid Nabi Muhammad SAW',
                '2025-12-25' => 'Hari Raya Natal',
                '2025-12-26' => 'Cuti Bersama Natal',
            ],
            2026 => [
                '2026-01-01' => 'Tahun Baru Masehi',
                '2026-02-17' => 'Tahun Baru Imlek 2577',
                '2026-03-03' => 'Isra Miraj Nabi Muhammad SAW',
                '2026-03-19' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
                '2026-04-03' => 'Wafat Isa Al Masih',
                '2026-04-20' => 'Idul Fitri 1447 H',
                '2026-04-21' => 'Idul Fitri 1447 H',
                '2026-05-01' => 'Hari Buruh Internasional',
                '2026-05-14' => 'Kenaikan Isa Al Masih',
                '2026-05-31' => 'Hari Raya Waisak 2570',
                '2026-06-01' => 'Hari Lahir Pancasila',
                '2026-06-27' => 'Idul Adha 1447 H',
                '2026-07-16' => 'Tahun Baru Islam 1448 H',
                '2026-08-17' => 'Hari Kemerdekaan Republik Indonesia',
                '2026-09-24' => 'Maulid Nabi Muhammad SAW',
                '2026-12-25' => 'Hari Raya Natal',
            ],
        ];

        return $data[$year] ?? [];
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
        $firstDay     = mktime(0, 0, 0, $month, 1, $year);
        $numberOfDays = (int) date('t', $firstDay);
        $dayOfWeek    = (int) date('w', $firstDay);
        $monthNameEn  = date('F', $firstDay);

        $bulanId = [
            'January' => 'Januari', 'February' => 'Februari', 'March'    => 'Maret',
            'April'   => 'April',   'May'       => 'Mei',      'June'     => 'Juni',
            'July'    => 'Juli',    'August'    => 'Agustus',  'September'=> 'September',
            'October' => 'Oktober', 'November'  => 'November', 'December' => 'Desember',
        ];
        $monthNameId = $bulanId[$monthNameEn] ?? $monthNameEn;

        // Ambil agenda bulan ini dari DB
        $agendaList = DB::table('kalender')
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->get()
            ->map(fn($a) => (array) $a)
            ->toArray();

        // Kelompokkan per tanggal
        $agendaByDate = [];
        foreach ($agendaList as $agenda) {
            $key = date('Y-m-d', strtotime($agenda['tanggal']));
            $agendaByDate[$key][] = $agenda;
        }

        // Fetch hari libur nasional Indonesia
        $hariLiburNasional = $this->getHariLiburNasional($year);

        // Filter hanya bulan yang sedang ditampilkan
        $hariLiburBulanIni = [];
        foreach ($hariLiburNasional as $tgl => $nama) {
            if (date('n', strtotime($tgl)) == $month && date('Y', strtotime($tgl)) == $year) {
                $hariLiburBulanIni[$tgl] = $nama;
            }
        }

        // Tanggal yang dipilih
        $defaultDay       = (date('Y') == $year && date('n') == $month) ? (int) date('j') : 1;
        $selectedDay      = (int) $request->query('day', $defaultDay);
        $selectedDateFull = date('Y-m-d', mktime(0, 0, 0, $month, $selectedDay, $year));
        $selectedAgenda   = $agendaByDate[$selectedDateFull] ?? [];

        // Hari libur nasional untuk tanggal yang dipilih
        $selectedHariLibur = $hariLiburNasional[$selectedDateFull] ?? null;

        return view('admin.kalender.index', compact(
            'month', 'year', 'monthNameId',
            'numberOfDays', 'dayOfWeek',
            'agendaByDate', 'selectedDay', 'selectedDateFull', 'selectedAgenda',
            'hariLiburBulanIni', 'selectedHariLibur'
        ));
    }

    /** AJAX: tambah agenda */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|min:2|max:150',
            'tanggal'       => 'required|date',
            'deskripsi'     => 'nullable|string',
        ]);

        DB::table('kalender')->insert($validated);

        return response()->json(['status' => 'success', 'message' => 'Agenda berhasil ditambahkan']);
    }

    /** AJAX: update agenda */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id'            => 'required|integer|exists:kalender,id_kegiatan',
            'nama_kegiatan' => 'required|string|min:2|max:150',
            'tanggal'       => 'required|date',
            'deskripsi'     => 'nullable|string',
        ]);

        $id = $validated['id'];
        unset($validated['id']);

        DB::table('kalender')->where('id_kegiatan', $id)->update($validated);

        return response()->json(['status' => 'success', 'message' => 'Agenda berhasil diperbarui']);
    }

    /** AJAX: hapus agenda */
    public function destroy(Request $request)
    {
        $request->validate(['id' => 'required|integer|exists:kalender,id_kegiatan']);

        DB::table('kalender')->where('id_kegiatan', $request->id)->delete();

        return response()->json(['status' => 'success', 'message' => 'Agenda berhasil dihapus']);
    }

    /** AJAX: detail satu agenda */
    public function show(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $agenda = DB::table('kalender')->where('id_kegiatan', $request->id)->first();

        if (!$agenda) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => (array) $agenda]);
    }
}
