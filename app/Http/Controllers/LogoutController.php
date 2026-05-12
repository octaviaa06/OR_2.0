<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    /**
     * Tampilkan halaman konfirmasi logout
     */
    public function show(Request $request)
    {
        $from = $request->query('from', '');
        return view('auth.logout_confirm', compact('from'));
    }

    /**
     * Proses logout atau batalkan
     */
    public function process(Request $request)
    {
        if ($request->has('confirm_logout')) {
            Session::flush();
            return redirect()->route('login');
        }

        if ($request->has('cancel_logout')) {
            $role = Session::get('role', '');
            $from = $request->input('from', '');

            // Map 'from' ke route name yang sesuai
            $redirectMap = [
                // Admin
                'dashboard_admin' => 'admin.dashboard',
                'DataGuru'        => 'admin.guru.index',
                'data_siswa'      => 'admin.siswa.index',
                'absensi'         => 'admin.absensi.index',
                'perizinan_admin' => 'admin.perizinan.index',
                'kalender'        => 'admin.kalender.index',
                // Guru
                'dashboard_guru'  => 'guru.dashboard',
                'perizinan_siswa' => 'guru.dashboard',
                'kalender_guru'   => 'guru.dashboard',
            ];

            if ($from && isset($redirectMap[$from])) {
                return redirect()->route($redirectMap[$from]);
            }

            // Default redirect berdasarkan role
            if ($role === 'guru') {
                return redirect()->route('guru.dashboard');
            }

            return redirect()->route('admin.dashboard');
        }

        // Fallback jika tidak ada tombol yang ditekan
        return redirect()->route('login');
    }
}
