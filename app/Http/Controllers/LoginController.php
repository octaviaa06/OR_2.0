<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login dan tidak ada error, redirect ke dashboard
        if (Session::has('role') && !session('error')) {
            return redirect(Session::get('role') === 'admin'
                ? route('admin.dashboard')
                : route('guru.dashboard'));
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username dan password wajib diisi',
            'password.required' => 'Username dan password wajib diisi',
        ]);

        $username = trim($request->username);
        $password = trim($request->password);

        $akun = DB::table('akun')->where('username', $username)->first();

        if (!$akun) {
            // Hapus session lama jika ada sebelum kembali ke login
            Session::flush();
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        $valid = ($akun->password === $password);

        if (!$valid) {
            $info = password_get_info($akun->password);
            if ($info['algoName'] !== 'unknown') {
                if (Hash::check($password, $akun->password)) {
                    $valid = true;
                }
            }
        }

        if (!$valid) {
            Session::flush();
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        if (!in_array($akun->role, ['admin', 'guru'])) {
            Session::flush();
            return back()->with('error', 'Akses ditolak. Gunakan aplikasi mobile untuk login sebagai orang tua.')->withInput();
        }

        Session::put('login',      true);
        Session::put('id_akun',    $akun->id_akun);
        Session::put('username',   $akun->username);
        Session::put('role',       $akun->role);
        Session::put('login_time', time());

        if ($akun->role === 'guru' && $akun->id_guru) {
            $guru = DB::table('guru')->where('id_guru', $akun->id_guru)->first();
            if ($guru) {
                Session::put('id_guru', $guru->id_guru);
                Session::put('kelas',   $guru->kelas);
            }
        }

        return redirect($akun->role === 'admin'
            ? route('admin.dashboard')
            : route('guru.dashboard'));
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}