@extends('layouts.app', ['title' => 'Penilaian: ' . $matkul->nama_matkul . ' - SISPRAK'])

@section('content')
<!-- Header Navigasi -->
<div class="mb-6">
    <a href="{{ route('asprak.dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 mb-2 transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Dashboard Asdos</span>
    </a>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $matkul->kode_matkul }}
                </span>
                <span class="text-xs text-slate-500">&bull; {{ $matkul->sks }} SKS</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Lembar Penilaian: {{ $matkul->nama_matkul }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Dosen Pengampu: <strong class="text-slate-700">{{ $matkul->dosen->nama_lengkap ?? 'Dosen' }}</strong></p>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="openModal('modalBobot')" 
                    class="px-3.5 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition flex items-center space-x-1.5">
                <i class="fa-solid fa-sliders"></i>
                <span>Konfigurasi Bobot</span>
            </button>
            <a href="{{ route('forum.index', $matkul->id_matkul) }}" 
               class="px-3.5 py-2 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl shadow-xs transition flex items-center space-x-1.5">
                <i class="fa-solid fa-comments text-slate-400"></i>
                <span>Forum Kelas</span>
            </a>
        </div>
    </div>
</div>

<!-- Bobot Penilaian Saat Ini -->
<div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <p class="text-[11px] font-semibold text-slate-500 uppercase">Bobot Komponen Penilaian</p>
        <div class="flex flex-wrap gap-2 text-xs font-medium mt-1.5">
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">Absensi: {{ $bobot->persentase_absensi }}%</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">Tugas: {{ $bobot->persentase_tugas }}%</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">Quiz: {{ $bobot->persentase_quiz }}%</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">UTS: {{ $bobot->persentase_uts }}%</span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">UAS: {{ $bobot->persentase_uas }}%</span>
            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold">Total: 100%</span>
        </div>
    </div>
    <button onclick="openModal('modalBobot')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 self-start sm:self-auto">
        Ubah Bobot &rarr;
    </button>
</div>

<!-- Section: Catatan Supervisi dari Dosen -->
@if ($supervisiList->isNotEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs mb-6">
    <div class="flex items-center space-x-2 mb-3 pb-2 border-b border-slate-100">
        <i class="fa-solid fa-clipboard-user text-indigo-600"></i>
        <h2 class="text-sm font-bold text-slate-900">Catatan Supervisi dari Dosen Pengampu</h2>
    </div>

    <div class="space-y-3">
        @foreach ($supervisiList as $sup)
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="flex items-start justify-between gap-3 mb-1">
                    <h3 class="text-xs font-bold text-slate-900">{{ $sup->judul }}</h3>
                    <div class="flex items-center space-x-2 shrink-0">
                        @if ($sup->status === 'selesai')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Selesai</span>
                        @elseif ($sup->status === 'dibaca')
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">Dibaca</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Terkirim</span>
                        @endif

                        <button type="button" onclick="openModalTanggapi('{{ $sup->id_supervisi }}', '{{ addslashes($sup->judul) }}', '{{ addslashes($sup->tanggapan_asdos ?? '') }}')"
                                class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-slate-100 text-indigo-600 border border-slate-200 rounded-lg transition">
                            Tanggapi
                        </button>
                    </div>
                </div>
                <p class="text-xs text-slate-700 whitespace-pre-line">{{ $sup->catatan }}</p>

                @if ($sup->tanggapan_asdos)
                    <div class="mt-2.5 p-2.5 rounded-lg bg-white border border-slate-200 text-xs text-slate-600">
                        <span class="font-bold text-slate-800 block text-[11px]">Tanggapan Anda:</span>
                        <p class="whitespace-pre-line mt-0.5">{{ $sup->tanggapan_asdos }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Tabel Penilaian Praktikan -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs mb-6 overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Praktikan & Input Nilai</h2>
            <p class="text-xs text-slate-500 mt-0.5">Klik tombol "Input Nilai" pada baris mahasiswa untuk memasukkan nilai komponen.</p>
        </div>
        <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
            {{ $pesertaList->count() }} Praktikan
        </span>
    </div>

    @if ($pesertaList->isEmpty())
        <p class="text-xs text-slate-500 py-10 text-center">Belum ada mahasiswa yang bergabung ke kelas ini.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">NPM</th>
                        <th class="py-3 px-4">Nama Mahasiswa</th>
                        <th class="py-3 px-3 text-center">Hadir</th>
                        <th class="py-3 px-3 text-center">Absensi</th>
                        <th class="py-3 px-3 text-center">Tugas</th>
                        <th class="py-3 px-3 text-center">Quiz</th>
                        <th class="py-3 px-3 text-center">UTS</th>
                        <th class="py-3 px-3 text-center">UAS</th>
                        <th class="py-3 px-3 text-center">Nilai Akhir</th>
                        <th class="py-3 px-3 text-center">Mutu</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($pesertaList as $idx => $p)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-800">{{ $p->npm }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900">{{ $p->nama_lengkap }}</td>
                            <td class="py-3 px-3 text-center font-mono">{{ $p->jumlah_hadir }}/{{ $p->jumlah_pertemuan }}</td>
                            <td class="py-3 px-3 text-center text-slate-600">{{ number_format($p->nilai_absensi, 1) }}</td>
                            <td class="py-3 px-3 text-center text-slate-600">{{ number_format($p->nilai_tugas, 1) }}</td>
                            <td class="py-3 px-3 text-center text-slate-600">{{ number_format($p->nilai_quiz, 1) }}</td>
                            <td class="py-3 px-3 text-center text-slate-600">{{ number_format($p->nilai_uts, 1) }}</td>
                            <td class="py-3 px-3 text-center text-slate-600">{{ number_format($p->nilai_uas, 1) }}</td>
                            <td class="py-3 px-3 text-center font-bold text-slate-900">
                                {{ $p->nilai_akhir_total !== null ? number_format($p->nilai_akhir_total, 2) : '-' }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if ($p->huruf_matkul)
                                    @php
                                        $badgeColor = match(substr($p->huruf_matkul, 0, 1)) {
                                            'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'B' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'C' => 'bg-amber-50 text-amber-800 border-amber-200',
                                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold border {{ $badgeColor }}">
                                        {{ $p->huruf_matkul }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button" 
                                        onclick="openModalNilai('{{ $p->id_user }}', '{{ addslashes($p->nama_lengkap) }}', '{{ $p->jumlah_hadir }}', '{{ $p->jumlah_pertemuan }}', '{{ $p->nilai_tugas }}', '{{ $p->nilai_quiz }}', '{{ $p->nilai_uts }}', '{{ $p->nilai_uas }}')"
                                        class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg transition">
                                    Input Nilai
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Modal: Konfigurasi Bobot Penilaian -->
<div id="modalBobot" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Konfigurasi Bobot Penilaian</h3>
            <button onclick="closeModal('modalBobot')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('asprak.bobot.update') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Absensi (%)</label>
                    <input type="number" step="0.5" min="0" max="100" name="persentase_absensi" id="bobot_absen" value="{{ $bobot->persentase_absensi }}" required oninput="calcBobot()" 
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tugas (%)</label>
                    <input type="number" step="0.5" min="0" max="100" name="persentase_tugas" id="bobot_tugas" value="{{ $bobot->persentase_tugas }}" required oninput="calcBobot()"
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Quiz (%)</label>
                    <input type="number" step="0.5" min="0" max="100" name="persentase_quiz" id="bobot_quiz" value="{{ $bobot->persentase_quiz }}" required oninput="calcBobot()"
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">UTS (%)</label>
                    <input type="number" step="0.5" min="0" max="100" name="persentase_uts" id="bobot_uts" value="{{ $bobot->persentase_uts }}" required oninput="calcBobot()"
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">UAS (%)</label>
                    <input type="number" step="0.5" min="0" max="100" name="persentase_uas" id="bobot_uas" value="{{ $bobot->persentase_uas }}" required oninput="calcBobot()"
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Total Indicator -->
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-600 font-semibold">Total Bobot:</span>
                <span id="bobotTotalText" class="font-bold text-indigo-700">100%</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalBobot')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitBobot" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan Bobot
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Input Nilai Mahasiswa -->
<div id="modalNilai" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Input Nilai Mahasiswa</h3>
                <p id="modalNilaiNama" class="text-xs text-slate-500 mt-0.5 font-medium"></p>
            </div>
            <button onclick="closeModal('modalNilai')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('asprak.nilai.simpan') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">
            <input type="hidden" name="id_mahasiswa" id="inputNilaiIdMhs">

            <!-- Absensi -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <label class="block text-xs font-semibold text-slate-700 mb-2">Presensi / Kehadiran</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <span class="text-[11px] text-slate-500 block mb-1">Jumlah Hadir:</span>
                        <input type="number" min="0" name="jumlah_hadir" id="inputJumlahHadir" required 
                               class="w-full px-3 py-1.5 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 block mb-1">Total Pertemuan:</span>
                        <input type="number" min="1" name="jumlah_pertemuan" id="inputTotalPertemuan" required value="16" 
                               class="w-full px-3 py-1.5 text-sm bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Nilai Komponen -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rata-rata Tugas (0-100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="nilai_tugas" id="inputNilaiTugas" required 
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rata-rata Quiz (0-100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="nilai_quiz" id="inputNilaiQuiz" required 
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai UTS (0-100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="nilai_uts" id="inputNilaiUts" required 
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai UAS (0-100)</label>
                    <input type="number" step="0.1" min="0" max="100" name="nilai_uas" id="inputNilaiUas" required 
                           class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalNilai')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tanggapi Catatan Supervisi -->
<div id="modalTanggapiSupervisi" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Tanggapi Supervisi</h3>
            <button onclick="closeModal('modalTanggapiSupervisi')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('asprak.supervisi.tanggapi') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">
            <input type="hidden" name="id_supervisi" id="inputTanggapiIdSupervisi">

            <div>
                <p id="tanggapiJudulText" class="text-xs font-bold text-slate-800 mb-2"></p>
                <label for="inputTanggapan" class="block text-xs font-semibold text-slate-700 mb-1">Tanggapan / Laporan Asdos</label>
                <textarea name="tanggapan" id="inputTanggapan" rows="3" required placeholder="Tuliskan respon atau konfirmasi tindak lanjut..."
                          class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalTanggapiSupervisi')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Kirim Tanggapan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function calcBobot() {
        const a = parseFloat(document.getElementById('bobot_absen').value) || 0;
        const t = parseFloat(document.getElementById('bobot_tugas').value) || 0;
        const q = parseFloat(document.getElementById('bobot_quiz').value) || 0;
        const uts = parseFloat(document.getElementById('bobot_uts').value) || 0;
        const uas = parseFloat(document.getElementById('bobot_uas').value) || 0;
        const total = a + t + q + uts + uas;

        const txt = document.getElementById('bobotTotalText');
        const btn = document.getElementById('btnSubmitBobot');
        txt.innerText = total.toFixed(1) + '%';
        if (Math.abs(total - 100) < 0.01) {
            txt.className = 'font-bold text-emerald-600';
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            txt.className = 'font-bold text-rose-600';
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function openModalNilai(idMhs, nama, hadir, total, tugas, quiz, uts, uas) {
        document.getElementById('inputNilaiIdMhs').value = idMhs;
        document.getElementById('modalNilaiNama').innerText = nama;
        document.getElementById('inputJumlahHadir').value = hadir || 0;
        document.getElementById('inputTotalPertemuan').value = total || 16;
        document.getElementById('inputNilaiTugas').value = tugas || 0;
        document.getElementById('inputNilaiQuiz').value = quiz || 0;
        document.getElementById('inputNilaiUts').value = uts || 0;
        document.getElementById('inputNilaiUas').value = uas || 0;
        openModal('modalNilai');
    }

    function openModalTanggapi(idSup, judul, tanggapan) {
        document.getElementById('inputTanggapiIdSupervisi').value = idSup;
        document.getElementById('tanggapiJudulText').innerText = judul;
        document.getElementById('inputTanggapan').value = tanggapan || '';
        openModal('modalTanggapiSupervisi');
    }
</script>
@endsection
