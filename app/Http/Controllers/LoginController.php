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
        if (Session::has('role')) {
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
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        // Support plain text (legacy) dan hashed password
        $valid = ($akun->password === $password)
            || Hash::check($password, $akun->password);

        if (!$valid) {
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        // Hanya admin dan guru yang bisa login di web
        if (!in_array($akun->role, ['admin', 'guru'])) {
            return back()->with('error', 'Akses ditolak. Gunakan aplikasi mobile untuk login sebagai orang tua.')->withInput();
        }

        // Set session
        Session::put('login',      true);
        Session::put('id_akun',    $akun->id_akun);
        Session::put('username',   $akun->username);
        Session::put('role',       $akun->role);
        Session::put('login_time', time());

        // Khusus guru — ambil data kelas dari tabel guru
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
