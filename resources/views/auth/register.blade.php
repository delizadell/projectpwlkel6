@extends('layouts.app', ['title' => 'Daftar Akun - SISPRAK'])

@section('content')
<div class="max-w-md mx-auto my-8 sm:my-12">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <!-- Logo & Header -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center mx-auto mb-3 shadow-xs">
                <i class="fa-solid fa-user-plus text-lg"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi data berikut untuk bergabung ke SISPRAK</p>
        </div>

        <!-- Form Register -->
        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- 1. Nama Lengkap -->
            <div>
                <label for="nama_lengkap" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap & Gelar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap') }}" 
                           placeholder="Contoh: Budi Santoso"
                           class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
            </div>

            <!-- 2. Role -->
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-700 mb-1.5">Mendaftar Sebagai (Role)</label>
                <select id="role" name="role" required onchange="updateRolePlaceholder(this.value)"
                        class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-medium">
                    <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa / Praktikan</option>
                    <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen Pengampu</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">
                    <i class="fa-solid fa-circle-info mr-0.5"></i> Mahasiswa yang ditugaskan menjadi Asisten Dosen dapat beralih ke Mode Asprak setelah memiliki akun.
                </p>
            </div>

            <!-- 3. NPM / NIP / Username -->
            <div>
                <label for="username" id="lblUsername" class="block text-xs font-semibold text-slate-700 mb-1.5">NPM Mahasiswa</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-id-card text-sm"></i>
                    </div>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}" 
                           placeholder="Masukkan NPM 10 Digit (Contoh: 2301010001)"
                           class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition font-mono">
                </div>
                <p id="identifierWarning" class="text-[11px] text-rose-600 font-semibold mt-1 hidden"></p>
                <p id="identifierHint" class="text-[10px] text-slate-400 mt-1">
                    <i class="fa-solid fa-circle-info mr-0.5"></i> <span id="hintText">NPM terdiri dari 10 digit angka.</span>
                </p>
                @error('username')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1 flex items-center space-x-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- 4. Program Studi (Prodi) untuk Mahasiswa -->
            <div id="containerProdi">
                <label for="prodi" class="block text-xs font-semibold text-slate-700 mb-1.5">Program Studi (Prodi)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-graduation-cap text-sm"></i>
                    </div>
                    <input type="text" id="prodi" name="prodi" list="prodiSuggestions" value="{{ old('prodi') }}"
                           placeholder="Contoh: S1 Sistem Informasi"
                           class="w-full pl-10 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <datalist id="prodiSuggestions">
                        <option value="S1 Sistem Informasi">
                        <option value="S1 Teknik Informatika">
                        <option value="S1 Ilmu Komputer">
                        <option value="S1 Teknologi Informasi">
                        <option value="S1 Rekayasa Perangkat Lunak">
                        <option value="S1 Teknik Elektro">
                        <option value="S1 Sains Data">
                        <option value="D3 Sistem Informasi">
                        <option value="D3 Manajemen Informatika">
                    </datalist>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">
                    <i class="fa-solid fa-circle-info mr-0.5"></i> Pilih dari daftar rekomendasi atau ketik program studi Anda.
                </p>
                @error('prodi')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1 flex items-center space-x-1">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- 5. Password -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700">Kata Sandi</label>
                    <span class="text-[11px] text-slate-400">Minimal 4 karakter</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                           class="w-full pl-10 pr-10 py-2.5 text-sm bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <button type="button" onclick="togglePasswordVisibility('password', 'toggleRegisterEyeIcon')" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                        <i id="toggleRegisterEyeIcon" class="fa-regular fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full mt-2 py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-xs transition flex items-center justify-center space-x-2">
                <span>Daftar Akun</span>
                <i class="fa-solid fa-user-check text-xs"></i>
            </button>
        </form>

        <!-- Link ke Login -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800 ml-1">Masuk Sekarang</a>
        </div>
    </div>
</div>

<script>
    function updateRolePlaceholder(role) {
        const lbl = document.getElementById('lblUsername');
        const input = document.getElementById('username');
        const warning = document.getElementById('identifierWarning');
        const hintText = document.getElementById('hintText');

        warning.classList.add('hidden');
        input.classList.remove('border-rose-500');
        input.classList.add('border-slate-300');

        const containerProdi = document.getElementById('containerProdi');
        const inputProdi = document.getElementById('prodi');

        if (role === 'dosen') {
            lbl.innerText = 'NIP Dosen (18 Digit)';
            input.placeholder = 'Masukkan NIP 18 Digit (Contoh: 198501042012011002)';
            hintText.innerText = 'NIP Dosen harus terdiri dari 18 digit angka resmi.';
            input.setAttribute('maxlength', '18');
            if (containerProdi) containerProdi.classList.add('hidden');
            if (inputProdi) {
                inputProdi.removeAttribute('required');
                inputProdi.value = '';
            }
        } else {
            lbl.innerText = 'NPM Mahasiswa (10 Digit)';
            input.placeholder = 'Masukkan NPM 10 Digit (Contoh: 2301010001)';
            hintText.innerText = 'NPM Mahasiswa harus terdiri dari 10 digit angka.';
            input.setAttribute('maxlength', '10');
            if (containerProdi) containerProdi.classList.remove('hidden');
            if (inputProdi) inputProdi.setAttribute('required', 'required');
        }

        if (input.value.length > 0) {
            validateIdentifier(input.value);
        }
    }

    function validateIdentifier(value) {
        const role = document.getElementById('role').value;
        const warning = document.getElementById('identifierWarning');
        const input = document.getElementById('username');

        let targetLen = 10;
        let fieldName = 'NPM Mahasiswa';
        let errPrefix = 'NPM salah';

        if (role === 'dosen') {
            targetLen = 18;
            fieldName = 'NIP Dosen';
            errPrefix = 'NIP salah';
        }

        if (value.length === 0) {
            warning.classList.add('hidden');
            input.classList.remove('border-rose-500');
            input.classList.add('border-slate-300');
            return true;
        }

        // Cek jika mengandung selain angka
        if (!/^\d+$/.test(value)) {
            warning.classList.remove('hidden');
            input.classList.add('border-rose-500');
            input.classList.remove('border-slate-300');
            warning.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> <strong>Peringatan: ${errPrefix}!</strong> ${fieldName} hanya boleh berupa angka tanpa huruf atau simbol.`;
            return false;
        }

        // Cek jumlah digit
        if (value.length < targetLen) {
            warning.classList.remove('hidden');
            input.classList.add('border-rose-500');
            input.classList.remove('border-slate-300');
            warning.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1"></i> <strong>Peringatan: ${errPrefix}!</strong> ${fieldName} harus terdiri dari tepat ${targetLen} digit angka (saat ini: ${value.length} digit).`;
            return false;
        }

        if (value.length === targetLen) {
            warning.classList.add('hidden');
            input.classList.remove('border-rose-500');
            input.classList.add('border-slate-300');
            return true;
        }

        return true;
    }

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

    // Live validation saat mengetik
    document.getElementById('username').addEventListener('input', function() {
        validateIdentifier(this.value);
    });

    document.getElementById('username').addEventListener('blur', function() {
        validateIdentifier(this.value);
    });

    // Validasi sebelum submit form
    document.querySelector('form').addEventListener('submit', function(e) {
        const input = document.getElementById('username');
        if (!validateIdentifier(input.value)) {
            e.preventDefault();
            input.focus();
        }
    });

    // Inisialisasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        updateRolePlaceholder(document.getElementById('role').value);
    });
</script>
@endsection