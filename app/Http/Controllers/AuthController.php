<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }

        return view('auth.login', ['title' => 'Login - SISPRAK (Sistem Manajemen Praktikum)']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->username);

        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withInput()->with('error', 'NPM / NIP / Username atau Kata Sandi yang dimasukkan salah!');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('active_role', $user->getBaseRole());

        return redirect()->route($user->getDashboardRoute())->with('success', 'Selamat datang kembali, '.$user->nama_lengkap.'!');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->getDashboardRoute());
        }

        return view('auth.register', ['title' => 'Daftar Akun - SISPRAK']);
    }

    public function register(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'role' => 'required|in:dosen,mahasiswa',
            'password' => 'required|string|min:4',
        ];

        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi!',
            'username.required' => 'NPM / NIP wajib diisi!',
            'username.unique' => 'Username / NPM / NIP sudah terdaftar di sistem! Gunakan yang lain.',
            'password.required' => 'Kata sandi wajib diisi!',
            'password.min' => 'Password minimal terdiri dari 4 karakter!',
            'role.in' => 'Pendaftaran hanya terbuka untuk peran Dosen dan Mahasiswa!',
        ];

        // Validasi identitas sesuai role
        if ($request->role === 'dosen') {
            $rules['username'] = ['required', 'string', 'size:18', 'regex:/^\d{18}$/', 'unique:users,username'];
            $messages['username.size'] = 'Peringatan: NIP salah! NIP Dosen harus terdiri dari tepat 18 digit angka.';
            $messages['username.regex'] = 'Peringatan: NIP salah! NIP Dosen harus berupa 18 digit angka tanpa huruf atau simbol.';
        } elseif ($request->role === 'mahasiswa') {
            $rules['username'] = ['required', 'string', 'size:10', 'regex:/^\d{10}$/', 'unique:users,username'];
            $messages['username.size'] = 'Peringatan: NPM salah! NPM Mahasiswa harus terdiri dari tepat 10 digit angka.';
            $messages['username.regex'] = 'Peringatan: NPM salah! NPM Mahasiswa harus berupa 10 digit angka tanpa huruf atau simbol.';
            $rules['prodi'] = ['required', 'string', 'max:100'];
            $messages['prodi.required'] = 'Program Studi (Prodi) mahasiswa wajib diisi!';
        }

        $request->validate($rules, $messages);

        User::create([
            'nama_lengkap' => trim($request->nama_lengkap),
            'username' => trim($request->username),
            'prodi' => $request->filled('prodi') ? trim($request->prodi) : null,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'created_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan login.');
    }

    /**
     * Beralih peran (Role Switcher) via request POST.
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:asisten_dosen,mahasiswa',
        ]);

        return $this->performRoleSwitch($request->role);
    }

    /**
     * Beralih peran (Role Switcher) via rute GET langsung.
     */
    public function switchRoleDirect(string $role)
    {
        if (! in_array($role, ['asisten_dosen', 'mahasiswa'], true)) {
            return back()->with('error', 'Pilihan mode peran tidak valid!');
        }

        return $this->performRoleSwitch($role);
    }

    /**
     * Eksekusi perpindahan mode peran tanpa memodifikasi basis data.
     */
    protected function performRoleSwitch(string $targetRole)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        if (! $user->canSwitchRole()) {
            return back()->with('error', 'Akun Dosen tidak dapat berganti peran.');
        }

        session(['active_role' => $targetRole]);

        $roleName = $targetRole === 'asisten_dosen' ? 'Asisten Dosen (Asprak)' : 'Mahasiswa / Praktikan';
        $targetRoute = $targetRole === 'asisten_dosen' ? 'asprak.dashboard' : 'mahasiswa.dashboard';

        return redirect()->route($targetRoute)->with('success', "Mode berhasil diubah ke {$roleName}.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}