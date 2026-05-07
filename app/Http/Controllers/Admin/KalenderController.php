<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KalenderController extends Controller
{
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
