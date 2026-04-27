<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Jika sudah login, redirect sesuai role
        if (Session::has('role')) {
            $role = Session::get('role');
            $redirect = ($role === 'admin') 
                ? route('admin.dashboard') 
                : route('guru.dashboard');
            return redirect($redirect);
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'Username dan password wajib diisi',
            'password.required' => 'Username dan password wajib diisi'
        ]);

        $username = trim($request->username);
        $password = trim($request->password);

        // API Login
        $apiUrl = "https://ortuconnect.pbltifnganjuk.com/api/login.php";
        $payload = json_encode([
            "username" => $username,
            "password" => $password
        ]);

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->post($apiUrl, json_decode($payload, true));

            if ($response->status() !== 200) {
                return back()->with('error', 'Koneksi ke server gagal')->withInput();
            }

            $result = $response->json();

            if (!$result || empty($result['success'])) {
                $errorMessage = $result['message'] ?? "Username atau password salah";
                return back()->with('error', $errorMessage)->withInput();
            }

            $user = $result['user'] ?? [];
            if (empty($user['role'])) {
                return back()->with('error', 'Data akun tidak valid')->withInput();
            }

            // Set session
            Session::put('login', true);
            Session::put('id_akun', $user['id_akun']);
            Session::put('username', $user['username']);
            Session::put('role', $user['role']);
            Session::put('login_time', time());

            // Khusus guru
            if ($user['role'] === 'guru' && !empty($user['kelas'])) {
                Session::put('kelas', $user['kelas']);
            }

            // Redirect sesuai role
            $redirect = ($user['role'] === 'admin')
                ? route('admin.dashboard')
                : route('guru.dashboard');

            return redirect($redirect);

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}