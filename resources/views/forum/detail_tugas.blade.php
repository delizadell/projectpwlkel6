@extends('layouts.app', ['title' => 'Koreksi Tugas: ' . $post->judul . ' - SISPRAK'])

@section('content')
<div class="mb-6">
    <a href="{{ route('forum.index', $post->id_matkul) }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 mb-2 transition">
        <i class="fa-solid fa-arrow-left text-[11px]"></i>
        <span>Kembali ke Forum Praktikum</span>
    </a>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                    Tugas Praktikum
                </span>
                <span class="text-xs text-slate-500">&bull; {{ $post->mataKuliah->nama_matkul }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $post->judul }}</h1>
        </div>

        <div>
            <form action="{{ route('forum.lock.toggle', [$post->id_post, $post->id_matkul]) }}" method="POST" onsubmit="return confirm('Ubah status penguncian wadah tugas ini?');">
                @csrf
                <button type="submit" 
                        class="px-3.5 py-2 text-xs font-semibold rounded-xl border transition flex items-center space-x-1.5 shadow-xs {{ $post->is_closed ? 'bg-white hover:bg-slate-50 text-slate-700 border-slate-200' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border-rose-200' }}">
                    <i class="fa-solid {{ $post->is_closed ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    <span>{{ $post->is_closed ? 'Buka Kunci Pengumpulan' : 'Kunci Wadah Tugas' }}</span>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs mb-6 text-xs">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <span class="text-slate-500 font-semibold uppercase block mb-1">Tenggat Waktu (Deadline)</span>
            <span class="text-sm font-bold text-slate-900 font-mono">
                {{ $post->deadline ? $post->deadline->format('d M Y, H:i') : 'Tidak ada deadline' }}
            </span>
        </div>
        <div>
            <span class="text-slate-500 font-semibold uppercase block mb-1">Status Wadah</span>
            @if ($post->is_closed)
                <span class="inline-block px-2.5 py-0.5 rounded-md font-bold text-[11px] bg-rose-50 text-rose-700 border border-rose-200">
                    Pengumpulan Ditutup
                </span>
            @else
                <span class="inline-block px-2.5 py-0.5 rounded-md font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Pengumpulan Terbuka
                </span>
            @endif
        </div>
        <div>
            <span class="text-slate-500 font-semibold uppercase block mb-1">Total Mahasiswa Mengumpulkan</span>
            <span class="text-sm font-bold text-slate-900">{{ $submissions->count() }} Berkas</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="text-sm font-bold text-slate-900">
            Daftar Berkas Pengumpulan ({{ $submissions->count() }})
        </h3>
        @if ($submissions->isNotEmpty())
            <input type="text" id="searchSubmission" oninput="filterSubmissions()" placeholder="Cari nama atau NPM..." 
                   class="w-full sm:w-64 px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @endif
    </div>

    @if ($submissions->isEmpty())
        <p class="text-xs text-slate-500 py-10 text-center">Belum ada praktikan yang mengumpulkan tugas ini.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">NPM</th>
                        <th class="py-3 px-4">Nama Mahasiswa</th>
                        <th class="py-3 px-4">Waktu Submit</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-4">Berkas</th>
                        <th class="py-3 px-3 text-center">Nilai</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tBodySubmissions" class="divide-y divide-slate-100">
                    @foreach ($submissions as $idx => $s)
                        <tr class="hover:bg-slate-50 transition row-sub">
                            <td class="py-3 px-4 text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-800 col-npm">{{ $s->mahasiswa->username ?? '-' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-900 col-nama">{{ $s->mahasiswa->nama_lengkap ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">{{ $s->submitted_at ? $s->submitted_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="py-3 px-3 text-center">
                                @if ($s->status_submit === 'tepat_waktu')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Tepat Waktu</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Terlambat</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ asset('uploads/tugas/' . $s->file_path) }}" download 
                                   class="inline-flex items-center space-x-1.5 text-indigo-600 hover:text-indigo-800 font-medium">
                                    <i class="fa-solid fa-file-arrow-down text-xs"></i>
                                    <span>Unduh File</span>
                                </a>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-slate-900">
                                {{ $s->nilai !== null ? number_format($s->nilai, 1) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button" 
                                        onclick="openModalNilaiSubmission('{{ $s->id_pengumpulan }}', '{{ addslashes($s->mahasiswa->nama_lengkap ?? '') }}', '{{ $s->nilai }}', '{{ addslashes($s->catatan_asprak ?? '') }}')"
                                        class="px-2.5 py-1 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg transition">
                                    Beri Nilai
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div id="modalNilaiSubmission" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Koreksi & Nilai Tugas</h3>
                <p id="gradeStudentName" class="text-xs text-slate-500 mt-0.5 font-medium"></p>
            </div>
            <button onclick="closeModal('modalNilaiSubmission')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('forum.tugas.grade') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="id_post" value="{{ $post->id_post }}">
            <input type="hidden" name="id_pengumpulan" id="gradeIdPengumpulan">

            <div>
                <label for="gradeScore" class="block text-xs font-semibold text-slate-700 mb-1">Nilai Tugas (0 - 100)</label>
                <input type="number" step="0.5" min="0" max="100" id="gradeScore" name="nilai" required placeholder="Contoh: 85.5" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="gradeFeedback" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Evaluasi untuk Mahasiswa (Opsional)</label>
                <textarea id="gradeFeedback" name="catatan_asprak" rows="3" placeholder="Umpan balik atau perbaikan kode praktikan..." 
                          class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalNilaiSubmission')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalNilaiSubmission(idPengumpulan, namaMhs, nilai, catatan) {
        document.getElementById('gradeIdPengumpulan').value = idPengumpulan;
        document.getElementById('gradeStudentName').innerText = namaMhs;
        document.getElementById('gradeScore').value = (nilai !== '' && nilai !== 'null') ? nilai : '';
        document.getElementById('gradeFeedback').value = (catatan !== '' && catatan !== 'null') ? catatan : '';
        openModal('modalNilaiSubmission');
    }

    function filterSubmissions() {
        const query = document.getElementById('searchSubmission').value.toLowerCase();
        document.querySelectorAll('.row-sub').forEach(row => {
            const npm = row.querySelector('.col-npm').innerText.toLowerCase();
            const nama = row.querySelector('.col-nama').innerText.toLowerCase();
            if (npm.includes(query) || nama.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
