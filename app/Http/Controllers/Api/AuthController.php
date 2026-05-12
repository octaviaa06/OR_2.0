<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Body: { username, password }
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $akun = DB::table('akun')
            ->where('username', $request->username)
            ->first();

        if (!$akun) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Support plain text password (legacy) dan hashed password
        $passwordValid = ($akun->password === $request->password)
            || Hash::check($request->password, $akun->password);

        if (!$passwordValid) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        // Hanya role 'ortu' yang boleh login via mobile
        if ($akun->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Aplikasi ini hanya untuk orang tua siswa.',
            ], 403);
        }

        // Ambil data siswa terkait
        $siswa = null;
        if ($akun->id_siswa) {
            $siswa = DB::table('siswa')->where('id_siswa', $akun->id_siswa)->first();
        }

        // Generate token: base64(id_akun|timestamp|random)
        $token     = base64_encode($akun->id_akun . '|' . time() . '|' . bin2hex(random_bytes(16)));
        $expiredAt = now()->addDays(30);

        // Simpan token ke DB
        DB::table('akun')->where('id_akun', $akun->id_akun)->update([
            'token'            => $token,
            'token_expired_at' => $expiredAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token'    => $token,
                'id_akun'  => $akun->id_akun,
                'username' => $akun->username,
                'role'     => $akun->role,
                'id_siswa' => $akun->id_siswa,
                'siswa'    => $siswa ? [
                    'id_siswa'   => $siswa->id_siswa,
                    'nama_siswa' => $siswa->nama_siswa,
                    'kelas'      => $siswa->kelas,
                ] : null,
            ],
        ]);
    }

    /**
     * POST /api/logout
     * Hapus FCM token saat logout
     */
    public function logout(Request $request)
    {
        $idAkun = $request->attributes->get('id_akun');

        if ($idAkun) {
            DB::table('akun')
                ->where('id_akun', $idAkun)
                ->update(['fcm_token' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}
