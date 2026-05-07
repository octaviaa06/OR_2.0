<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Halaman data siswa untuk guru
     * Guru hanya bisa melihat, tidak bisa edit/hapus
     */
    public function index(Request $request)
    {
        $selectedKelas = $request->query('kelas_filter', '');

        // Ambil data siswa dari database lokal
        $query = DB::table('siswa')->orderBy('kelas')->orderBy('nama_siswa');

        if ($selectedKelas !== '') {
            $query->where('kelas', $selectedKelas);
        }

        $siswaList = $query->get()->map(function ($siswa) {
            return (array) $siswa;
        })->toArray();

        // Ambil daftar kelas unik untuk filter dropdown
        $kelasList = DB::table('siswa')
            ->select('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();

        return view('guru.siswa.index', compact('siswaList', 'selectedKelas', 'kelasList'));
    }
}
