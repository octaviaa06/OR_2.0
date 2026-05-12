public function index(Request $request, AttendanceService $attendance)
{
    // CAST ke integer di sini
    $siswaId = (int) $request->input('siswa_id', 1);
    $bulan = (int) $request->input('bulan', date('n'));
    $tahun = (int) $request->input('tahun', date('Y'));

    $persentase = $attendance->calculatePercentage($siswaId, $bulan, $tahun);

    return view('attendance.report', [
        'persentase' => $persentase,
        'siswaId' => $siswaId,
        'bulan' => $bulan,
        'tahun' => $tahun
    ]);
}