<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Ini import Auth yang wajib ditambahkan

class AuthController extends Controller
{
    // Method untuk menampilkan halaman form login
    public function showLogin()
    {
        return view('auth.login'); // Sesuaikan dengan nama file blade kamu
    }

    // Method untuk memproses data dari form
    public function login(Request $request)
    {
        // 1. Validasi inputan tidak boleh kosong
        $credentials = $request->validate([
            'nim'      => 'required',
            'password' => 'required'
        ], [
            // Pesan error custom (opsional, agar bahasa Indonesia)
            'nim.required'      => 'NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.'
        ]);

        // 2. Coba proses login
        if (Auth::attempt($credentials)) {
            // Jika berhasil, buat sesi baru dan pindah ke dashboard
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // 3. Jika gagal, kembalikan ke halaman login dengan pesan error
        return back()->with('error', 'NIM atau Password salah!')->withInput();
    }

    // Method untuk logout (bonus sekalian)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
