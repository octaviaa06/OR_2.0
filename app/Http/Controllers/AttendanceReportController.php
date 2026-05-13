<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AttendanceService;

class AttendanceReportController extends Controller
{
    public function index(Request $request, AttendanceService $attendance)
    {
        $siswaId = (int) $request->input('siswa_id', 1);
        $bulan   = (int) $request->input('bulan', date('n'));
        $tahun   = (int) $request->input('tahun', date('Y'));

        $persentase = $attendance->calculatePercentage($siswaId, $bulan, $tahun);

        return view('attendance.report', [
            'persentase' => $persentase,
            'siswaId'    => $siswaId,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
        ]);
    }
}
