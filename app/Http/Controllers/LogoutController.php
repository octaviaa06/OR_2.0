<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function show(Request $request)
    {
        $from = $request->get('from', '');
        return view('auth.logout_confirm', compact('from'));
    }

    public function process(Request $request)
    {
        if ($request->has('confirm_logout')) {

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login');
        }

        if ($request->has('cancel_logout')) {

            $redirect_map = [
                // ADMIN
                'dashboard admin' => '/dashboard_admin/home_admin',
                'DataGuru'        => '/admin/data-guru',
                'data'            => '/admin/data-siswa',
                'absensi'         => '/admin/absensi',
                'perizinan_admin' => '/admin/perizinan',
                'kalender'        => '/admin/kalender',

                // GURU
                'dashboard guru'  => '/dashboard_guru/home_guru',
                'data_siswa'      => '/guru/data-siswa',
                'absensi_siswa'   => '/guru/absensi',
                'perizinan_siswa' => '/guru/perizinan',
                'kalender guru'   => '/guru/kalender',
            ];

            $role = session('role');
            $default = $role === 'guru'
                ? '/dashboard_guru/home_guru'
                : '/dashboard_admin/home_admin';

            $target = $redirect_map[$request->from] ?? $default;

            return redirect($target);
        }
    }
}