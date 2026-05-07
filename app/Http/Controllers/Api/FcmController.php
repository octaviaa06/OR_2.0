<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FcmController extends Controller
{
    /**
     * POST /api/fcm-token
     * Simpan FCM token device saat login
     * Body: { fcm_token }
     */
    public function store(Request $request)
    {
        $idAkun = $request->attributes->get('id_akun');

        $request->validate([
            'fcm_token' => 'required|string|max:255',
        ]);

        DB::table('akun')
            ->where('id_akun', $idAkun)
            ->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil disimpan.',
        ]);
    }
}
