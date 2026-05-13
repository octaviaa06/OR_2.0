<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    /** Halaman daftar guru */
    public function index()
    {
        $guruList = DB::table('guru')
            ->orderBy('nama_guru')
            ->get()
            ->map(fn($g) => (array) $g)
            ->toArray();

        return view('admin.guru.index', compact('guruList'));
    }

    /** AJAX: tambah guru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|min:3|max:100',
            'nip'       => 'required|digits_between:8,18',
            'alamat'    => 'required|string|min:3|max:255',
            'no_telp'   => 'required|digits_between:10,15',
            'email'     => 'required|email|max:100|unique:guru,email',
            'kelas'     => 'required|string|max:10',
        ]);

        $idGuru = DB::table('guru')->insertGetId($validated);

        // Generate akun otomatis:
        // username = email
        // password = kata pertama nama guru (lowercase) + 3 digit terakhir no_telp
        $namaAwal = strtolower(explode(' ', trim($validated['nama_guru']))[0]);
        $telpAkhir = substr($validated['no_telp'], -3);
        $password  = $namaAwal . $telpAkhir;

        DB::table('akun')->insert([
            'id_guru'  => $idGuru,
            'username' => $validated['email'],
            'password' => $password,
            'role'     => 'guru',
        ]);

        return response()->json(['success' => true, 'message' => 'Data guru berhasil ditambahkan']);
    }

    /** AJAX: update guru */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id_guru'   => 'required|integer|exists:guru,id_guru',
            'nama_guru' => 'required|string|min:3|max:100',
            'nip'       => 'required|digits_between:8,18',
            'alamat'    => 'required|string|min:3|max:255',
            'no_telp'   => 'required|digits_between:10,15',
            'email'     => 'required|email|max:100|unique:guru,email,' . $request->id_guru . ',id_guru',
            'kelas'     => 'required|string|max:10',
        ]);

        $id = $validated['id_guru'];
        unset($validated['id_guru']);

        DB::table('guru')->where('id_guru', $id)->update($validated);

        return response()->json(['success' => true, 'message' => 'Data guru berhasil diperbarui']);
    }

    /** AJAX: hapus guru */
    public function destroy(Request $request)
    {
        $request->validate(['id_guru' => 'required|integer|exists:guru,id_guru']);

        DB::table('guru')->where('id_guru', $request->id_guru)->delete();

        return response()->json(['success' => true, 'message' => 'Data guru berhasil dihapus']);
    }

    /** AJAX: lihat akun guru */
    public function akun(Request $request)
    {
        $request->validate(['id_guru' => 'required|integer']);

        $akun = DB::table('akun')
            ->join('guru', 'akun.id_guru', '=', 'guru.id_guru')
            ->where('akun.id_guru', $request->id_guru)
            ->first(['akun.id_akun', 'akun.username', 'akun.password', 'akun.role', 'guru.nama_guru']);

        if (!$akun) {
            return response()->json(['success' => false, 'message' => 'Akun tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => (array) $akun]);
    }
}
