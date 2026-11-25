@extends('layouts.app', ['title' => 'Forum Praktikum: ' . $matkul->nama_matkul . ' - SISPRAK'])

@section('content')
@php
    $totalPengumuman = $posts->where('tipe_post', 'pengumuman')->count();
    $totalMateri = $posts->where('tipe_post', 'materi')->count();
    $totalTugas = $posts->where('tipe_post', 'tugas')->count();
@endphp

<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 text-xs font-mono font-bold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                    {{ $matkul->kode_matkul }}
                </span>
                <span class="text-xs text-slate-500 font-semibold">{{ $matkul->sks }} SKS</span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-600">Dosen: <strong class="text-slate-800">{{ $matkul->dosen->nama_lengkap ?? 'Dosen' }}</strong></span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $matkul->nama_matkul }}</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl">{{ $matkul->deskripsi ?: 'Forum diskusi materi dan pengumpulan tugas praktikum.' }}</p>
        </div>

        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center space-x-3 shrink-0">
            <div>
                <span class="text-[10px] font-semibold text-slate-500 uppercase block">Kode Gabung:</span>
                <span class="font-mono text-base font-bold text-slate-900">{{ $matkul->kode_gabung }}</span>
            </div>
            <button type="button" onclick="copyCode('{{ $matkul->kode_gabung }}', this)" 
                    class="p-2 bg-white hover:bg-slate-100 text-slate-600 rounded-lg border border-slate-200 transition" 
                    title="Salin Kode">
                <i class="fa-regular fa-copy text-xs"></i>
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
   
    <div class="lg:col-span-2 space-y-5">

        @if ($user->role === 'dosen' || $user->role === 'asisten_dosen')
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 flex items-center space-x-2">
                        <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                        <span>Publikasikan ke Forum Praktikum</span>
                    </h3>
                    <button type="button" onclick="toggleCreatePostBox()" id="btnToggleCreatePost" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center space-x-1">
                        <span id="textToggleCreatePost">Buka Form</span>
                        <i id="iconToggleCreatePost" class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                </div>

                <div id="boxCreatePost" class="p-5 hidden bg-white">
                    <form action="{{ route('forum.post.create') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Konten:</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_post" value="pengumuman" checked onchange="toggleFormType('pengumuman')" class="peer sr-only">
                                    <div class="p-2 text-center text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:border-indigo-300 transition">
                                        <i class="fa-solid fa-bullhorn mr-1 text-xs"></i> Pengumuman
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_post" value="materi" onchange="toggleFormType('materi')" class="peer sr-only">
                                    <div class="p-2 text-center text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:border-indigo-300 transition">
                                        <i class="fa-solid fa-book-bookmark mr-1 text-xs"></i> Modul Materi
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="tipe_post" value="tugas" onchange="toggleFormType('tugas')" class="peer sr-only">
                                    <div class="p-2 text-center text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 peer-checked:border-indigo-300 transition">
                                        <i class="fa-solid fa-list-check mr-1 text-xs"></i> Tugas Praktikum
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="judul" class="block text-xs font-semibold text-slate-700 mb-1">Judul Postingan</label>
                            <input type="text" id="judul" name="judul" required placeholder="Contoh: Modul Pertemuan 1 - Konsep OOP" 
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label for="isi" class="block text-xs font-semibold text-slate-700 mb-1">Isi / Petunjuk Praktikum</label>
                            <textarea id="isi" name="isi" rows="3" required placeholder="Tuliskan materi, instruksi tugas, atau pengumuman..." 
                                      class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        </div>

                        <div id="wrapperFileLampiran">
                            <label for="file_lampiran" class="block text-xs font-semibold text-slate-700 mb-1">Lampiran File / Modul (PDF / ZIP / Dokumen)</label>
                            <input type="file" id="file_lampiran" name="file_lampiran" 
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        </div>

                        <div id="wrapperDeadline" class="hidden">
                            <label for="deadline" class="block text-xs font-semibold text-slate-700 mb-1">Tenggat Waktu Pengumpulan (Deadline)</label>
                            <input type="datetime-local" id="deadline" name="deadline" 
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="pt-2 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                                Terbitkan ke Forum
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-comments text-xl"></i>
                </div>
                <h3 class="text-sm font-semibold text-slate-800">Forum Masih Kosong</h3>
                <p class="text-xs text-slate-500 mt-1">Belum ada pengumuman, modul materi, atau tugas praktikum yang diterbitkan.</p>
            </div>
        @else
            @foreach ($posts as $post)
                <div id="post-{{ $post->id_post }}" class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <!-- Post Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($post->author->nama_lengkap ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-slate-900">{{ $post->author->nama_lengkap ?? 'Pengguna' }}</span>
                                    @if ($post->author && $post->author->role === 'dosen')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Dosen</span>
                                    @elseif ($post->author && $post->author->role === 'asisten_dosen')
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Asdos</span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                         
                            @if ($post->tipe_post === 'tugas')
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-800 border border-amber-200">Tugas</span>
                            @elseif ($post->tipe_post === 'materi')
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Modul</span>
                            @else
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">Pengumuman</span>
                            @endif

                            @if ($user->role === 'dosen' || $user->role === 'asisten_dosen')
                                <div class="flex items-center space-x-1 pl-1.5 border-l border-slate-200">
                                
                                    <button type="button" 
                                            onclick="openModalEditPost('{{ $post->id_post }}', @js($post->judul), @js($post->isi), '{{ $post->tipe_post }}', '{{ $post->deadline ? date('Y-m-d\TH:i', strtotime($post->deadline)) : '' }}', '{{ $post->file_lampiran ?? '' }}')"
                                            class="p-1 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" 
                                            title="Edit Postingan">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>

                                    <form action="{{ route('forum.post.delete', $post->id_post) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan ini? Semua komentar dan berkas terkait akan ikut terhapus.')" 
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" 
                                                title="Hapus Postingan">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h2 class="text-base font-bold text-slate-900 mb-1.5">{{ $post->judul }}</h2>
                    <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">{{ $post->isi }}</p>

                    @if ($post->file_lampiran)
                        <div class="mt-3">
                            <a href="{{ asset('uploads/modul/' . $post->file_lampiran) }}" target="_blank" download 
                               class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-medium text-slate-700 transition">
                                <i class="fa-solid fa-paperclip text-slate-400"></i>
                                <span class="truncate max-w-xs">{{ $post->file_lampiran }}</span>
                                <i class="fa-solid fa-download text-[10px] text-slate-400"></i>
                            </a>
                        </div>
                    @endif

                    @if ($post->tipe_post === 'tugas')
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                            <!-- Baris Header Tugas: Deadline & Status & Aksi Dosen/Asdos / Tombol Submit Mhs -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200">
                                <div>
                                    <div class="flex items-center space-x-2 text-xs">
                                        <i class="fa-regular fa-clock text-slate-400"></i>
                                        <span class="text-slate-600">Deadline: 
                                            <strong>{{ $post->deadline ? date('d M Y, H:i', strtotime($post->deadline)) . ' WIB' : 'Tidak ada' }}</strong>
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        @if ($post->is_closed)
                                            <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-rose-600">
                                                <i class="fa-solid fa-lock text-[10px]"></i>
                                                <span>Pengumpulan Ditutup</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center space-x-1 text-[11px] font-semibold text-emerald-600">
                                                <i class="fa-solid fa-lock-open text-[10px]"></i>
                                                <span>Pengumpulan Terbuka</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if ($user->role === 'dosen' || $user->role === 'asisten_dosen')
                                     
                                        <form action="{{ route('forum.lock.toggle', [$post->id_post, $matkul->id_matkul]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white hover:bg-slate-50 text-slate-700 transition">
                                                {{ $post->is_closed ? 'Buka Kunci' : 'Kunci Tugas' }}
                                            </button>
                                        </form>

                                        <a href="{{ route('forum.detail_tugas', $post->id_post) }}" 
                                           class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition flex items-center space-x-1.5">
                                            <i class="fa-solid fa-list-check text-[10px]"></i>
                                            <span>Koreksi Tugas</span>
                                        </a>
                                    @elseif ($user->role === 'mahasiswa' && (!isset($post->my_submission) || !$post->my_submission))
                                        @if (!$post->is_closed)
                                            <button type="button" onclick="openModalSubmitTugas('{{ $post->id_post }}', @js($post->judul))" 
                                                    class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition flex items-center space-x-1.5 shadow-xs">
                                                <i class="fa-solid fa-cloud-arrow-up text-xs"></i>
                                                <span>Kumpulkan Tugas</span>
                                            </button>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">Wadah Ditutup</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if ($user->role === 'mahasiswa' && isset($post->my_submission) && $post->my_submission)
                                <div class="space-y-3 pt-1">
                                    <!-- 1. Box Berkas Tugas Mahasiswa -->
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center space-x-1">
                                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                    <span>Sudah Dikumpulkan</span>
                                                </span>
                                                @if ($post->my_submission->status_submit === 'terlambat')
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200">
                                                        Terlambat
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-50 text-blue-700 border border-blue-200">
                                                        Tepat Waktu
                                                    </span>
                                                @endif
                                            </div>

                                            <span class="text-[11px] text-slate-400">
                                                Dikumpulkan: {{ $post->my_submission->submitted_at ? \Carbon\Carbon::parse($post->my_submission->submitted_at)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                            <div class="flex items-center space-x-2 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-file-lines text-sm"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="text-xs font-semibold text-slate-800 block truncate">{{ $post->my_submission->file_path }}</span>
                                                    <span class="text-[10px] text-slate-400">Berkas tugas yang Anda kumpulkan</span>
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-1.5 shrink-0">
                                                <a href="{{ asset('uploads/tugas/' . $post->my_submission->file_path) }}" target="_blank" download
                                                   class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition flex items-center space-x-1">
                                                    <i class="fa-solid fa-download text-[11px]"></i>
                                                    <span>Unduh / Lihat File</span>
                                                </a>

                                                @if (!$post->is_closed)
                                                    <button type="button"
                                                            onclick="openModalEditTugas('{{ $post->my_submission->id_pengumpulan }}', @js($post->judul), '{{ $post->my_submission->file_path }}', @js($post->my_submission->catatan_mahasiswa ?? ''), '{{ $matkul->id_matkul }}')"
                                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 transition flex items-center space-x-1"
                                                            title="Ganti berkas atau perbarui catatan tugas">
                                                        <i class="fa-solid fa-pen-to-square text-[11px] text-indigo-600"></i>
                                                        <span>Edit</span>
                                                    </button>

                                                    <form action="{{ route('forum.tugas.delete', $post->my_submission->id_pengumpulan) }}" method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini? File berkas yang sudah diunggah akan dihapus permanen.')"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 transition flex items-center space-x-1"
                                                                title="Hapus berkas tugas">
                                                            <i class="fa-regular fa-trash-can text-[11px]"></i>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($post->my_submission->catatan_mahasiswa)
                                            <div class="mt-2 text-xs text-slate-600 bg-slate-50/70 p-2.5 rounded-lg border border-slate-100">
                                                <span class="text-[10px] font-bold text-slate-500 uppercase block mb-0.5">Catatan dari Anda:</span>
                                                <p class="whitespace-pre-line">{{ $post->my_submission->catatan_mahasiswa }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($post->my_submission->nilai !== null || !empty($post->my_submission->catatan_asprak))
                                        <div class="p-4 rounded-xl bg-gradient-to-r from-indigo-50/90 to-blue-50/90 border border-indigo-200 shadow-2xs">
                                            <div class="flex items-center justify-between mb-2 pb-2 border-b border-indigo-100">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs">
                                                        <i class="fa-solid fa-award"></i>
                                                    </div>
                                                    <span class="text-xs font-bold text-indigo-950">Hasil Penilaian & Evaluasi Asisten Dosen</span>
                                                </div>

                                                @if ($post->my_submission->nilai !== null)
                                                    <div class="flex items-center space-x-1 px-3 py-1 bg-white rounded-lg border border-indigo-200 shadow-2xs">
                                                        <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                                                        <span class="text-xs font-extrabold text-indigo-900">Nilai: {{ number_format($post->my_submission->nilai, 1) }} / 100</span>
                                                    </div>
                                                @else
                                                    <span class="text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">
                                                        Belum Diberi Nilai Angka
                                                    </span>
                                                @endif
                                            </div>

                                            @if (!empty($post->my_submission->catatan_asprak))
                                                <div class="mt-2 p-3 rounded-lg bg-white border border-indigo-100 shadow-2xs">
                                                    <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider block mb-1 flex items-center space-x-1.5">
                                                        <i class="fa-solid fa-comment-dots text-indigo-500"></i>
                                                        <span>Catatan dari Asisten Dosen:</span>
                                                    </span>
                                                    <p class="text-xs text-slate-800 whitespace-pre-line leading-relaxed font-medium bg-slate-50 p-2.5 rounded-md border border-slate-100">
                                                        {{ $post->my_submission->catatan_asprak }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <button type="button" onclick="toggleComments('comments-{{ $post->id_post }}')" 
                                class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1.5 mb-2">
                            <i class="fa-regular fa-comment text-slate-400"></i>
                            <span>{{ $post->komentar->count() }} Komentar</span>
                        </button>

                        <div id="comments-{{ $post->id_post }}" class="space-y-2 mt-2">
                            @foreach ($post->komentar as $kom)
                                <div class="p-2.5 rounded-lg bg-slate-50 text-xs text-slate-700 border border-slate-100 flex items-start justify-between group">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="font-bold text-slate-900">{{ $kom->user->nama_lengkap ?? 'Pengguna' }}</span>
                                            @if ($kom->user && $kom->user->role === 'dosen')
                                                <span class="px-1.5 py-0.2 rounded text-[8px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Dosen</span>
                                            @elseif ($kom->user && $kom->user->role === 'asisten_dosen')
                                                <span class="px-1.5 py-0.2 rounded text-[8px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Asdos</span>
                                            @endif
                                            <span class="text-[10px] text-slate-400">&bull; {{ $kom->created_at ? $kom->created_at->diffForHumans() : '' }}</span>
                                        </div>
                                        <p class="whitespace-pre-line">{{ $kom->isi_komentar }}</p>
                                    </div>
                                    @if ($user->role === 'dosen' || $user->role === 'asisten_dosen' || $kom->id_user === $user->id_user)
                                        <form action="{{ route('forum.comment.delete', $kom->id_komentar) }}" method="POST" 
                                              onsubmit="return confirm('Hapus komentar ini?')" 
                                              class="opacity-0 group-hover:opacity-100 transition shrink-0 ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-rose-600 p-1" title="Hapus Komentar">
                                                <i class="fa-regular fa-trash-can text-[11px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach

                            <form action="{{ route('forum.comment.add') }}" method="POST" class="flex gap-2 mt-2">
                                @csrf
                                <input type="hidden" name="id_post" value="{{ $post->id_post }}">
                                <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">
                                <input type="text" name="isi_komentar" required placeholder="Tulis komentar atau pertanyaan..." 
                                       class="flex-1 px-3 py-1.5 text-xs bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-slate-800 hover:bg-slate-900 text-white rounded-lg transition">
                                    Kirim
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <h3 class="text-xs font-bold text-slate-800 uppercase mb-3">Statistik Forum</h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Pengumuman</span>
                    <span class="font-bold text-slate-800">{{ $totalPengumuman }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Modul Materi</span>
                    <span class="font-bold text-slate-800">{{ $totalMateri }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tugas Praktikum</span>
                    <span class="font-bold text-slate-800">{{ $totalTugas }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
            <h3 class="text-xs font-bold text-slate-800 uppercase mb-3">Tim Pengajar</h3>
            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">Dosen Pengampu:</span>
                    <p class="font-bold text-slate-900">{{ $matkul->dosen->nama_lengkap ?? 'Dosen' }}</p>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">Asisten Dosen:</span>
                    @if ($matkul->asistenApproved->isNotEmpty())
                        @foreach ($matkul->asistenApproved as $asdos)
                            <p class="font-medium text-slate-700">&bull; {{ $asdos->nama_lengkap }}</p>
                        @endforeach
                    @else
                        <p class="text-slate-400">Belum ada asisten</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalSubmitTugas" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Kumpulkan Tugas</h3>
                <p id="submitJudulTugas" class="text-xs text-slate-500 mt-0.5 font-medium"></p>
            </div>
            <button onclick="closeModal('modalSubmitTugas')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form action="{{ route('forum.tugas.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="id_matkul" value="{{ $matkul->id_matkul }}">
            <input type="hidden" name="id_post" id="submitIdPost">

            <div>
                <label for="file_tugas" class="block text-xs font-semibold text-slate-700 mb-1">Unggah Berkas Tugas (ZIP / PDF / RAR, Max 50MB)</label>
                <input type="file" id="file_tugas" name="file_tugas" required 
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            </div>

            <div>
                <label for="catatan_mahasiswa" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea id="catatan_mahasiswa" name="catatan_mahasiswa" rows="2" placeholder="Catatan untuk Asisten Dosen..." 
                          class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalSubmitTugas')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Kumpulkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@if ($user->role === 'dosen' || $user->role === 'asisten_dosen')
<div id="modalEditPost" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                <span>Edit Postingan Forum</span>
            </h3>
            <button type="button" onclick="closeModal('modalEditPost')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="formEditPost" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="edit_judul" class="block text-xs font-semibold text-slate-700 mb-1">Judul Postingan</label>
                <input type="text" id="edit_judul" name="judul" required 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="edit_isi" class="block text-xs font-semibold text-slate-700 mb-1">Isi / Petunjuk Praktikum</label>
                <textarea id="edit_isi" name="isi" rows="4" required 
                          class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div id="wrapperEditDeadline" class="hidden">
                <label for="edit_deadline" class="block text-xs font-semibold text-slate-700 mb-1">Tenggat Waktu Pengumpulan (Deadline)</label>
                <input type="datetime-local" id="edit_deadline" name="deadline" 
                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="edit_file_lampiran" class="block text-xs font-semibold text-slate-700 mb-1">Ganti File Lampiran (Opsional, Max 20MB)</label>
                <div id="editCurrentFileInfo" class="text-xs text-slate-500 mb-2 p-2 bg-slate-50 rounded-lg border border-slate-200 hidden">
                    <span class="font-medium text-slate-600">File saat ini: </span>
                    <span id="editCurrentFileName" class="font-mono text-indigo-600 font-semibold truncate block"></span>
                </div>
                <input type="file" id="edit_file_lampiran" name="file_lampiran" 
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengubah file lampiran.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalEditPost')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if ($user->role === 'mahasiswa')
<div id="modalEditTugas" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                    <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
                    <span>Edit Tugas</span>
                </h3>
                <p id="editTugasJudul" class="text-xs text-slate-500 mt-0.5 font-medium"></p>
            </div>
            <button type="button" onclick="closeModal('modalEditTugas')" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <form id="formEditTugas" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id_matkul" id="editTugasIdMatkul">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">File Saat Ini</label>
                <div class="text-xs text-slate-600 p-2 bg-slate-50 rounded-lg border border-slate-200">
                    <span id="editTugasCurrentFile" class="font-mono text-indigo-600 font-semibold truncate block"></span>
                </div>
            </div>

            <div>
                <label for="edit_file_tugas" class="block text-xs font-semibold text-slate-700 mb-1">Ganti Berkas Tugas (Opsional, Max 50MB)</label>
                <input type="file" id="edit_file_tugas" name="file_tugas"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengganti file.</p>
            </div>

            <div>
                <label for="edit_catatan_mahasiswa" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea id="edit_catatan_mahasiswa" name="catatan_mahasiswa" rows="2" placeholder="Catatan untuk Asisten Dosen..."
                          class="w-full px-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('modalEditTugas')" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function toggleCreatePostBox() {
        const box = document.getElementById('boxCreatePost');
        const txt = document.getElementById('textToggleCreatePost');
        const icon = document.getElementById('iconToggleCreatePost');
        if (box.classList.contains('hidden')) {
            box.classList.remove('hidden');
            txt.innerText = 'Tutup Form';
            icon.classList.add('rotate-180');
        } else {
            box.classList.add('hidden');
            txt.innerText = 'Buka Form';
            icon.classList.remove('rotate-180');
        }
    }

    function toggleFormType(type) {
        const wrapDeadline = document.getElementById('wrapperDeadline');
        if (type === 'tugas') {
            wrapDeadline.classList.remove('hidden');
        } else {
            wrapDeadline.classList.add('hidden');
        }
    }

    function toggleComments(id) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden');
    }

    function openModalSubmitTugas(idPost, judul) {
        document.getElementById('submitIdPost').value = idPost;
        document.getElementById('submitJudulTugas').innerText = judul;
        openModal('modalSubmitTugas');
    }

    function openModalEditPost(idPost, judul, isi, tipePost, deadline, currentFile) {
        const form = document.getElementById('formEditPost');
        if (!form) return;

        form.action = "{{ url('forum/post') }}/" + idPost;
        document.getElementById('edit_judul').value = judul;
        document.getElementById('edit_isi').value = isi;

        const wrapDeadline = document.getElementById('wrapperEditDeadline');
        const deadlineInput = document.getElementById('edit_deadline');
        if (tipePost === 'tugas') {
            wrapDeadline.classList.remove('hidden');
            deadlineInput.value = deadline || '';
        } else {
            wrapDeadline.classList.add('hidden');
            deadlineInput.value = '';
        }

        const fileInfo = document.getElementById('editCurrentFileInfo');
        const fileName = document.getElementById('editCurrentFileName');
        if (currentFile && currentFile.trim() !== '') {
            fileInfo.classList.remove('hidden');
            fileName.innerText = currentFile;
        } else {
            fileInfo.classList.add('hidden');
            fileName.innerText = '';
        }

        openModal('modalEditPost');
    }

    function openModalEditTugas(idPengumpulan, judul, currentFile, catatan, idMatkul) {
        const form = document.getElementById('formEditTugas');
        if (!form) return;

        form.action = "{{ url('forum/submit') }}/" + idPengumpulan;
        document.getElementById('editTugasJudul').innerText = judul;
        document.getElementById('editTugasCurrentFile').innerText = currentFile;
        document.getElementById('editTugasIdMatkul').value = idMatkul;
        document.getElementById('edit_catatan_mahasiswa').value = catatan || '';

        openModal('modalEditTugas');
    }
</script>
@endsection
