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

        // Decode token: base64(id_akun|timestamp|APP_KEY)
        $decoded = base64_decode($token);
        $parts   = explode('|', $decoded);

        if (count($parts) !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], 401);
        }

        [$idAkun, $timestamp, $key] = $parts;

        // Validasi APP_KEY
        if ($key !== env('APP_KEY')) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], 401);
        }

        // Token expired setelah 30 hari
        if ((time() - (int) $timestamp) > (60 * 60 * 24 * 30)) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah kadaluarsa. Silakan login ulang.',
            ], 401);
        }

        // Cek akun masih ada di DB
        $akun = DB::table('akun')->where('id_akun', (int) $idAkun)->first();

        if (!$akun) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
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
