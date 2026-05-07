<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * GET /api/profile?username=
     * Ambil data profil siswa berdasarkan username akun
     */
    public function show(Request $request)
    {
        $username = $request->query('username');
        $idSiswa  = $request->attributes->get('id_siswa');

        // Bisa cari by username atau pakai id_siswa dari token
        if ($username) {
            $akun = DB::table('akun')->where('username', $username)->first();
            if (!$akun || !$akun->id_siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun tidak ditemukan.',
                ], 404);
            }
            $idSiswa = $akun->id_siswa;
        }

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $siswa = DB::table('siswa')->where('id_siswa', $idSiswa)->first();

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        // Ambil username dari tabel akun
        $akun = DB::table('akun')->where('id_siswa', $idSiswa)->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'id_siswa'     => $siswa->id_siswa,
                'nama_siswa'   => $siswa->nama_siswa,
                'kelas'        => $siswa->kelas,
                'tanggal_lahir'=> $siswa->tanggal_lahir,
                'gender'       => $siswa->gender,
                'alamat'       => $siswa->alamat,
                'nama_ortu'    => $siswa->nama_ortu,
                'no_telp_ortu' => $siswa->no_telp_ortu,
                'username'     => $akun->username ?? null,
                'foto'         => $siswa->foto ?? null,
            ],
        ]);
    }

    /**
     * POST /api/profile
     * Update data profil siswa
     * Body: { nama_siswa, alamat, tanggal_lahir, gender, nama_ortu, no_telp_ortu }
     */
    public function update(Request $request)
    {
        $idSiswa = $request->attributes->get('id_siswa');

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'nama_siswa'   => 'sometimes|string|min:3|max:100',
            'alamat'       => 'sometimes|string|max:255',
            'tanggal_lahir'=> 'sometimes|date',
            'gender'       => 'sometimes|in:Laki-Laki,Perempuan',
            'nama_ortu'    => 'sometimes|string|min:3|max:100',
            'no_telp_ortu' => 'sometimes|digits_between:10,15',
        ]);

        $updateData = array_filter([
            'nama_siswa'   => $request->nama_siswa,
            'alamat'       => $request->alamat,
            'tanggal_lahir'=> $request->tanggal_lahir,
            'gender'       => $request->gender,
            'nama_ortu'    => $request->nama_ortu,
            'no_telp_ortu' => $request->no_telp_ortu,
        ], fn($v) => !is_null($v));

        if (empty($updateData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang diubah.',
            ], 422);
        }

        DB::table('siswa')->where('id_siswa', $idSiswa)->update($updateData);

        $siswa = DB::table('siswa')->where('id_siswa', $idSiswa)->first();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data'    => [
                'id_siswa'     => $siswa->id_siswa,
                'nama_siswa'   => $siswa->nama_siswa,
                'kelas'        => $siswa->kelas,
                'tanggal_lahir'=> $siswa->tanggal_lahir,
                'gender'       => $siswa->gender,
                'alamat'       => $siswa->alamat,
                'nama_ortu'    => $siswa->nama_ortu,
                'no_telp_ortu' => $siswa->no_telp_ortu,
                'foto'         => $siswa->foto ?? null,
            ],
        ]);
    }

    /**
     * POST /api/upload-photo
     * Upload foto profil siswa
     * Body: multipart/form-data { foto: file }
     */
    public function uploadPhoto(Request $request)
    {
        $idSiswa = $request->attributes->get('id_siswa');

        if (!$idSiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus foto lama jika ada
        $siswa = DB::table('siswa')->where('id_siswa', $idSiswa)->first();
        if (!empty($siswa->foto)) {
            $oldPath = str_replace('/storage/', 'public/', $siswa->foto);
            Storage::delete($oldPath);
        }

        // Simpan foto baru
        $file     = $request->file('foto');
        $filename = 'siswa_' . $idSiswa . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs('public/foto_siswa', $filename);
        $url      = '/storage/foto_siswa/' . $filename;

        DB::table('siswa')->where('id_siswa', $idSiswa)->update(['foto' => $url]);

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diupload.',
            'data'    => [
                'foto' => $url,
            ],
        ]);
    }
}
