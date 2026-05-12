<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->header('X-Auth-Token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.',
            ], 401);
        }

        // Cari akun berdasarkan token yang tersimpan di DB
        $akun = DB::table('akun')->where('token', $token)->first();

        if (!$akun) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], 401);
        }

        // Cek token expired
        if ($akun->token_expired_at && now()->gt($akun->token_expired_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah kadaluarsa. Silakan login ulang.',
            ], 401);
        }

        // Inject data akun ke request untuk dipakai controller
        $request->attributes->set('id_akun',  $akun->id_akun);
        $request->attributes->set('id_siswa', $akun->id_siswa);
        $request->attributes->set('username', $akun->username);
        $request->attributes->set('role',     $akun->role);

        return $next($request);
    }
}
