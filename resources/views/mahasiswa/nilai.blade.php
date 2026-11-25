@extends('layouts.app', ['title' => 'Transparansi Nilai - SISPRAK'])

@section('content')
<!-- Header Navigasi -->
<div class="mb-6">
    <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 mb-2 transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Dashboard Mahasiswa</span>
    </a>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $matkul->kode_matkul }}
                </span>
                <span class="text-xs text-slate-500">&bull; {{ $matkul->sks }} SKS</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Transparansi Nilai Praktikum</h1>
            <p class="text-xs text-slate-500 mt-0.5">Mata Kuliah: <strong class="text-slate-800">{{ $matkul->nama_matkul }}</strong> &bull; Dosen: {{ $matkul->dosen->nama_lengkap ?? 'Dosen' }}</p>
        </div>

        <a href="{{ route('forum.index', $matkul->id_matkul) }}" 
           class="px-3.5 py-2 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl shadow-xs transition flex items-center space-x-1.5 self-start md:self-auto">
            <i class="fa-solid fa-comments text-slate-400"></i>
            <span>Buka Forum Kelas</span>
        </a>
    </div>
</div>

<!-- Kartu Ringkasan Nilai Akhir & Mutu -->
<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase">Akumulasi Nilai Akhir</span>
            <div class="flex items-baseline space-x-3 mt-1">
                <span class="text-4xl font-extrabold text-slate-900">
                    {{ $nilai && $nilai->nilai_akhir_total !== null ? number_format($nilai->nilai_akhir_total, 2) : '0.00' }}
                </span>
                <span class="text-xs text-slate-500">Skala 100</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Dihitung otomatis berdasarkan 5 komponen penilaian sesuai bobot resmi mata kuliah.</p>
        </div>

        <div class="flex items-center space-x-4 shrink-0 sm:border-l sm:border-slate-100 sm:pl-6">
            <div class="text-center">
                <span class="text-xs font-semibold text-slate-500 block mb-1">Huruf Mutu</span>
                @if ($nilai && $nilai->huruf_matkul)
                    @php
                        $badgeBg = match(substr($nilai->huruf_matkul, 0, 1)) {
                            'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'B' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'C' => 'bg-amber-50 text-amber-800 border-amber-200',
                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                        };
                    @endphp
                    <span class="inline-block px-4 py-1.5 rounded-xl text-xl font-bold border {{ $badgeBg }}">
                        {{ $nilai->huruf_matkul }}
                    </span>
                @else
                    <span class="inline-block px-4 py-1.5 rounded-xl text-xl font-bold border bg-slate-100 text-slate-400 border-slate-200">
                        -
                    </span>
                @endif
            </div>

            <div class="text-center">
                <span class="text-xs font-semibold text-slate-500 block mb-1">Angka Mutu</span>
                <span class="inline-block px-4 py-1.5 rounded-xl text-xl font-bold border bg-slate-50 text-slate-800 border-slate-200 font-mono">
                    {{ $nilai && $nilai->bobot_matkul !== null ? number_format($nilai->bobot_matkul, 2) : '0.00' }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Rincian 5 Komponen Penilaian -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h2 class="text-base font-bold text-slate-900">Rincian Komponen Penilaian</h2>
        <p class="text-xs text-slate-500 mt-0.5">Penjelasan bobot dan kontribusi nilai setiap aspek praktikum.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4">Komponen</th>
                    <th class="py-3 px-4 text-center">Status / Parameter</th>
                    <th class="py-3 px-4 text-center">Nilai Murni (0-100)</th>
                    <th class="py-3 px-4 text-center">Bobot (%)</th>
                    <th class="py-3 px-4 text-right">Kontribusi ke Nilai Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php
                    $absenVal = $nilai ? $nilai->nilai_absensi : 0;
                    $tugasVal = $nilai ? $nilai->nilai_tugas : 0;
                    $quizVal  = $nilai ? $nilai->nilai_quiz : 0;
                    $utsVal   = $nilai ? $nilai->nilai_uts : 0;
                    $uasVal   = $nilai ? $nilai->nilai_uas : 0;

                    $absenKon = $absenVal * ($bobot->persentase_absensi / 100);
                    $tugasKon = $tugasVal * ($bobot->persentase_tugas / 100);
                    $quizKon  = $quizVal * ($bobot->persentase_quiz / 100);
                    $utsKon   = $utsVal * ($bobot->persentase_uts / 100);
                    $uasKon   = $uasVal * ($bobot->persentase_uas / 100);
                @endphp

                <!-- 1. Absensi -->
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                        <i class="fa-solid fa-user-check text-slate-400 mr-1.5"></i> Absensi & Kehadiran
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                        {{ $nilai ? $nilai->jumlah_hadir : 0 }} / {{ $nilai ? $nilai->jumlah_pertemuan : 16 }} Hadir
                    </td>
                    <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                        {{ number_format($absenVal, 1) }}
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                        {{ $bobot->persentase_absensi }}%
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                        {{ number_format($absenKon, 2) }}
                    </td>
                </tr>

                <!-- 2. Tugas Praktikum -->
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                        <i class="fa-solid fa-file-lines text-slate-400 mr-1.5"></i> Rata-rata Tugas Praktikum
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-500">
                        Tugas Forum & Modul
                    </td>
                    <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                        {{ number_format($tugasVal, 1) }}
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                        {{ $bobot->persentase_tugas }}%
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                        {{ number_format($tugasKon, 2) }}
                    </td>
                </tr>

                <!-- 3. Quiz -->
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                        <i class="fa-solid fa-circle-question text-slate-400 mr-1.5"></i> Rata-rata Quiz
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-500">
                        Evaluasi Rutin
                    </td>
                    <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                        {{ number_format($quizVal, 1) }}
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                        {{ $bobot->persentase_quiz }}%
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                        {{ number_format($quizKon, 2) }}
                    </td>
                </tr>

                <!-- 4. UTS -->
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                        <i class="fa-solid fa-pen-ruler text-slate-400 mr-1.5"></i> Ujian Tengah Semester (UTS)
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-500">
                        Ujian Praktik Tengah
                    </td>
                    <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                        {{ number_format($utsVal, 1) }}
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                        {{ $bobot->persentase_uts }}%
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                        {{ number_format($utsKon, 2) }}
                    </td>
                </tr>

                <!-- 5. UAS -->
                <tr class="hover:bg-slate-50 transition">
                    <td class="py-3.5 px-4 font-semibold text-slate-900">
                        <i class="fa-solid fa-graduation-cap text-slate-400 mr-1.5"></i> Ujian Akhir Semester (UAS)
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-500">
                        Responsi / Project Akhir
                    </td>
                    <td class="py-3.5 px-4 text-center font-semibold text-slate-800">
                        {{ number_format($uasVal, 1) }}
                    </td>
                    <td class="py-3.5 px-4 text-center text-slate-600 font-medium">
                        {{ $bobot->persentase_uas }}%
                    </td>
                    <td class="py-3.5 px-4 text-right font-bold text-slate-900">
                        {{ number_format($uasKon, 2) }}
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-slate-900">
                <tr>
                    <td colspan="3" class="py-3.5 px-4">Total Akumulasi Nilai</td>
                    <td class="py-3.5 px-4 text-center">100%</td>
                    <td class="py-3.5 px-4 text-right text-indigo-700 text-sm">
                        {{ $nilai && $nilai->nilai_akhir_total !== null ? number_format($nilai->nilai_akhir_total, 2) : '0.00' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
