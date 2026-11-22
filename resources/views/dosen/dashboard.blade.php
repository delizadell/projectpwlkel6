@extends('layouts.app', ['title' => 'Dashboard Dosen - SISPRAK'])

@section('content')
@php
    $totalMatkul = $matkulList->count();
    $totalMhs = $matkulList->sum('total_praktikan');
    $totalAsdos = $matkulList->sum('total_asdos');
    $totalPending = $pendingAsprakRequests->count();
@endphp

<!-- Header Dashboard -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang, {{ $user->nama_lengkap }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola mata kuliah praktikum, persetujuan asisten dosen, dan supervisi akademik.</p>
    </div>
    <div class="flex items-center space-x-3">
        <button onclick="openModal('modalCreateMatkul')" 
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-xs transition flex items-center space-x-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Buat Mata Kuliah</span>
        </button>
    </div>
</div>

<!-- Kartu Ringkasan Metrik (4 Kolom) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Total Mata Kuliah</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalMatkul }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-book-open"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Total Praktikan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalMhs }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Asisten Dosen Aktif</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalAsdos }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-user-gear"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Permintaan Pending</p>
            <p class="text-2xl font-bold {{ $totalPending > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                {{ $totalPending }}
            </p>
        </div>
        <div class="w-10 h-10 rounded-xl {{ $totalPending > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center text-lg">
            <i class="fa-solid fa-user-clock"></i>
        </div>
    </div>
</div>

<!-- Section: Pengajuan Asisten Dosen Menunggu Persetujuan -->
@if ($pendingAsprakRequests->isNotEmpty())
<div class="mb-6 bg-white border border-amber-200 rounded-2xl p-5 shadow-xs">
    <div class="flex items-center justify-between mb-4 pb-3 border-b border-amber-100">
        <div class="flex items-center space-x-2.5">
            <i class="fa-solid fa-bell text-amber-500"></i>
            <h2 class="text-sm font-bold text-slate-900">
                Pengajuan Asisten Dosen Menunggu Persetujuan ({{ $totalPending }})
            </h2>
        </div>
        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
            Perlu Tindakan
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($pendingAsprakRequests as $req)
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                        {{ $req->mataKuliah->kode_matkul }}
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 mt-2">{{ $req->asisten->nama_lengkap }}</h3>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">NPM: {{ $req->asisten->username }}</p>
                    <p class="text-xs text-slate-600 mt-1.5">Kelas: {{ $req->mataKuliah->nama_matkul }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-200 flex items-center gap-2">
                    <form action="{{ route('dosen.asprak.acc', $req->id_asisten_matkul) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-check text-[10px]"></i>
                            <span>Setujui (ACC)</span>
                        </button>
                    </form>
                    <form action="{{ route('dosen.asprak.reject', $req->id_asisten_matkul) }}" method="POST" class="flex-1" onsubmit="return confirm('Tolak pengajuan asisten ini?');">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-3 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-xs font-semibold transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                            <span>Tolak</span>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Section: Daftar Kelas Mata Kuliah -->
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-slate-900">Mata Kuliah Praktikum</h2>
        <span class="text-xs text-slate-500 font-medium">{{ $totalMatkul }} kelas terdaftar</span>
    </div>

    @if ($matkulList->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-book-open text-xl"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-800">Belum Ada Mata Kuliah</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Anda belum membuat kelas praktikum. Klik tombol di bawah untuk membuat kelas pertama Anda.</p>
            <button onclick="openModal('modalCreateMatkul')" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                + Buat Mata Kuliah Baru
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($matkulList as $matkul)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 transition">
                    <div>
                        <!-- Top Meta -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $matkul->kode_matkul }}
                            </span>
                            <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded">
                                {{ $matkul->sks }} SKS
                            </span>
                        </div>

                        <!-- Title & Description -->
                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $matkul->nama_matkul }}</h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $matkul->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}</p>

                        <!-- Kode Gabung Box -->
                        <div class="mt-4 p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-semibold text-slate-500 block">Kode Gabung Praktikan:</span>
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $matkul->kode_gabung }}</span>
                            </div>
                            <button type="button" onclick="copyCode('{{ $matkul->kode_gabung }}', this)" 
                                    class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-white rounded-lg border border-transparent hover:border-slate-200 transition" 
                                    title="Salin Kode Gabung">
                                <i class="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>

                        <!-- Stats Ringkas -->
                        <div class="mt-3 flex items-center space-x-4 text-xs text-slate-500">
                            <span class="flex items-center space-x-1.5">
                                <i class="fa-solid fa-users text-slate-400"></i>
                                <span>{{ $matkul->total_praktikan }} Mahasiswa</span>
                            </span>
                            <span class="flex items-center space-x-1.5">
                                <i class="fa-solid fa-user-gear text-slate-400"></i>
                                <span>{{ $matkul->total_asdos }} Asdos</span>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-5 pt-3.5 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <a href="{{ route('dosen.monitoring', $matkul->id_matkul) }}" 
                           class="py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold text-center transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-chart-line text-[11px]"></i>
                            <span>Monitoring</span>
                        </a>
                        <a href="{{ route('forum.index', $matkul->id_matkul) }}" 
                           class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold text-center transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-comments text-[11px]"></i>
                            <span>Forum</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal: Buat Mata Kuliah Baru -->
<div id="modalCreateMatkul" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Buat Mata Kuliah Praktikum</h3>
            <button onclick="closeModal('modalCreateMatkul')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('dosen.matkul.create') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="kode_matkul" class="block text-xs font-semibold text-slate-700 mb-1">Kode Mata Kuliah</label>
                <input type="text" id="kode_matkul" name="kode_matkul" required placeholder="Contoh: PRAK-PBO-01" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase font-mono">
                <p class="text-[11px] text-slate-500 mt-1">Kode ini akan digunakan praktikan & asdos untuk bergabung.</p>
            </div>

            <div>
                <label for="nama_matkul" class="block text-xs font-semibold text-slate-700 mb-1">Nama Mata Kuliah</label>
                <input type="text" id="nama_matkul" name="nama_matkul" required placeholder="Contoh: Pemrograman Berorientasi Objek" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="sks" class="block text-xs font-semibold text-slate-700 mb-1">Bobot SKS</label>
                <input type="number" id="sks" name="sks" required min="1" max="6" value="3" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Praktikum (Opsional)</label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Informasi singkat silabus praktikum..." 
                          class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalCreateMatkul')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan & Buat Kelas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
