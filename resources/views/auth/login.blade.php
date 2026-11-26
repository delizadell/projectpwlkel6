@extends('layouts.app', ['title' => 'Login - SISPRAK'])

@section('content')
<div class="max-w-md mx-auto my-10 sm:my-16">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <!-- Logo & Judul -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke SISPRAK</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Informasi Manajemen Praktikum Akademik</p>
        </div>

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Username / NPM / NIP -->
            <div>
                <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">NPM / NIP / Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}" 
                           placeholder="Masukkan NPM, NIP, atau Username"
                           class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                           class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('password', 'toggleEyeIcon')" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <i id="toggleEyeIcon" class="fa-regular fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" 
                    class="w-full mt-2 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-xs transition flex items-center justify-center space-x-2">
                <span>Masuk ke Akun</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Link Registrasi -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }
</script>
@endsection