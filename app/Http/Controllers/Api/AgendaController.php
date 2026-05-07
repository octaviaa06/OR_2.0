<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    /**
     * GET /api/agenda?month=&year=
     * Agenda/kegiatan sekolah per bulan
     */
    public function index(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year',  now()->year);

        // Validasi range
        if ($month < 1 || $month > 12) {
            return response()->json([
                'success' => false,
                'message' => 'Bulan tidak valid (1-12).',
            ], 422);
        }

        $agenda = DB::table('kalender')
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal', 'asc')
            ->get(['id_kegiatan', 'nama_kegiatan', 'tanggal', 'deskripsi']);

        return response()->json([
            'success' => true,
            'data'    => [
                'month'  => $month,
                'year'   => $year,
                'agenda' => $agenda->values(),
            ],
        ]);
    }

    /**
     * GET /api/agenda/mendatang
     * Agenda H-1 s/d H+3 untuk notifikasi
     */
    public function mendatang(Request $request)
    {
        $dateFrom = now()->subDay()->toDateString();
        $dateTo   = now()->addDays(3)->toDateString();

        $agenda = DB::table('kalender')
            ->whereBetween('tanggal', [$dateFrom, $dateTo])
            ->orderBy('tanggal', 'asc')
            ->get(['id_kegiatan', 'nama_kegiatan', 'tanggal', 'deskripsi']);

        return response()->json([
            'success' => true,
            'data'    => $agenda->values(),
        ]);
    }
}
