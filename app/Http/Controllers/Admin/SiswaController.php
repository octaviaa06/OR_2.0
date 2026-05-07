<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /** Halaman daftar siswa */
    public function index(Request $request)
    {
        $selectedKelas = $request->query('kelas_filter', '');

        $query = DB::table('siswa')->orderBy('kelas')->orderBy('nama_siswa');

        if ($selectedKelas !== '') {
            $query->where('kelas', $selectedKelas);
        }

        $siswaList = $query->get()
            ->map(fn($s) => (array) $s)
            ->toArray();

        $today = now()->toDateString();

        return view('admin.siswa.index', compact('siswaList', 'selectedKelas', 'today'));
    }

    /** AJAX: tambah siswa */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa'    => 'required|string|min:3|max:100',
            'kelas'         => 'required|string|max:50',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'gender'        => 'required|in:Laki-Laki,Perempuan',
            'nama_ortu'     => 'required|string|min:3|max:100',
            'no_telp_ortu'  => 'required|digits_between:10,15',
            'alamat'        => 'nullable|string|max:255',
        ]);

        $validated['kelas'] = strtoupper(preg_replace('/^kelas\s+/i', '', trim($validated['kelas'])));

        DB::table('siswa')->insert($validated);

        return response()->json(['success' => true, 'message' => 'Data siswa berhasil ditambahkan']);
    }

    /** AJAX: update siswa */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_siswa'      => 'required|integer|exists:siswa,id_siswa',
            'nama_siswa'    => 'required|string|min:3|max:100',
            'kelas'         => 'required|string|max:50',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'gender'        => 'required|in:Laki-Laki,Perempuan',
            'nama_ortu'     => 'required|string|min:3|max:100',
            'no_telp_ortu'  => 'required|digits_between:10,15',
            'alamat'        => 'nullable|string|max:255',
        ]);

        $id = $validated['id_siswa'];
        unset($validated['id_siswa']);
        $validated['kelas'] = strtoupper(preg_replace('/^kelas\s+/i', '', trim($validated['kelas'])));

        DB::table('siswa')->where('id_siswa', $id)->update($validated);

        return response()->json(['success' => true, 'message' => 'Data siswa berhasil diperbarui']);
    }

    /** AJAX: hapus siswa */
    public function destroy(Request $request)
    {
        $request->validate(['id_siswa' => 'required|integer|exists:siswa,id_siswa']);

        DB::table('siswa')->where('id_siswa', $request->id_siswa)->delete();

        return response()->json(['success' => true, 'message' => 'Data siswa berhasil dihapus']);
    }

    /** AJAX: lihat akun siswa */
    public function akun(Request $request)
    {
        $request->validate(['id_siswa' => 'required|integer']);

        $akun = DB::table('akun')
            ->join('siswa', 'akun.id_siswa', '=', 'siswa.id_siswa')
            ->where('akun.id_siswa', $request->id_siswa)
            ->first(['akun.id_akun', 'akun.username', 'akun.password', 'akun.role', 'siswa.nama_siswa']);

        if (!$akun) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => (array) $akun]);
    }
}
