@extends('layouts.app', ['title' => 'Dashboard Asisten Dosen - SISPRAK'])

@section('content')
@php
    $totalMatkul = $matkulList->count();
    $totalMhs = $matkulList->sum('total_praktikan');
    $totalPending = $pendingRequests->count();
@endphp

<!-- Header Dashboard -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang, {{ $user->nama_lengkap }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola kelas praktikum binaan, input penilaian, dan koordinasi dengan dosen pengampu.</p>
    </div>
    <div>
        <button onclick="openModal('modalGabungPraktikumAsdos')" 
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-xs transition flex items-center space-x-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Gabung Praktikum</span>
        </button>
    </div>
</div>

<!-- Kartu Ringkasan Metrik (3 Kolom) -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Kelas Praktikum Aktif</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalMatkul }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Total Praktikan Binaan</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalMhs }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-slate-500">Permintaan Menunggu ACC</p>
            <p class="text-2xl font-bold {{ $totalPending > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">{{ $totalPending }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl {{ $totalPending > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center text-lg">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
    </div>
</div>

<!-- Status Pengajuan Menunggu ACC Dosen -->
@if ($pendingRequests->isNotEmpty())
<div class="mb-6 bg-white border border-amber-200 rounded-2xl p-5 shadow-xs">
    <div class="flex items-center space-x-2.5 mb-3 pb-2 border-b border-amber-100">
        <i class="fa-solid fa-clock text-amber-500"></i>
        <h2 class="text-sm font-bold text-slate-900">Pengajuan Menunggu Persetujuan Dosen ({{ $totalPending }})</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($pendingRequests as $p)
            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-800 uppercase">
                        {{ $p->mataKuliah->kode_matkul }}
                    </span>
                    <h3 class="text-xs font-bold text-slate-900 mt-1">{{ $p->mataKuliah->nama_matkul }}</h3>
                    <p class="text-[11px] text-slate-500">Dosen: {{ $p->mataKuliah->dosen->nama_lengkap ?? 'Dosen' }}</p>
                </div>
                <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-1 rounded border border-amber-200 shrink-0">
                    Menunggu ACC
                </span>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Daftar Kelas Praktikum Binaan -->
<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-slate-900">Kelas Praktikum Binaan</h2>
        <span class="text-xs text-slate-500">{{ $totalMatkul }} kelas aktif</span>
    </div>

    @if ($matkulList->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-chalkboard text-xl"></i>
            </div>
            <h3 class="text-sm font-semibold text-slate-800">Belum Bergabung ke Praktikum</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Minta Kode Gabung ke Dosen Pengampu lalu klik tombol di bawah untuk mengajukan diri sebagai asisten.</p>
            <button onclick="openModal('modalGabungPraktikumAsdos')" class="mt-4 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition">
                + Gabung Praktikum
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($matkulList as $matkul)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 transition">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $matkul->kode_matkul }}
                            </span>
                            <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded">
                                {{ $matkul->sks }} SKS
                            </span>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $matkul->nama_matkul }}</h3>
                        <p class="text-xs text-slate-500 mt-1">Dosen: <strong class="text-slate-700">{{ $matkul->dosen->nama_lengkap ?? 'Dosen' }}</strong></p>

                        <!-- Kode Gabung -->
                        <div class="mt-3 p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                            <span class="text-slate-500 text-[11px]">Kode: <strong class="font-mono text-slate-800">{{ $matkul->kode_gabung }}</strong></span>
                            <button type="button" onclick="copyCode('{{ $matkul->kode_gabung }}', this)" class="text-slate-400 hover:text-indigo-600 p-1" title="Salin Kode">
                                <i class="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>

                        <div class="mt-3 text-xs text-slate-500 flex items-center space-x-1.5">
                            <i class="fa-solid fa-users text-slate-400"></i>
                            <span>{{ $matkul->total_praktikan }} Mahasiswa Praktikan</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-3.5 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <a href="{{ route('asprak.penilaian', $matkul->id_matkul) }}" 
                           class="py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold text-center transition flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                            <span>Penilaian</span>
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

<!-- Modal: Gabung Praktikum Sebagai Asdos -->
<div id="modalGabungPraktikumAsdos" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Gabung Praktikum (Asisten)</h3>
            <button onclick="closeModal('modalGabungPraktikumAsdos')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('asprak.gabung') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="kode_gabung" class="block text-xs font-semibold text-slate-700 mb-1">Kode Gabung Praktikum</label>
                <input type="text" id="kode_gabung" name="kode_gabung" required placeholder="Contoh: PRAK-PBO-01" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase font-mono">
                <p class="text-[11px] text-slate-500 mt-1">Masukkan kode kelas praktikum yang diberikan oleh Dosen Pengampu.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalGabungPraktikumAsdos')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
