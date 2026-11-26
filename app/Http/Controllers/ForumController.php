<?php

namespace App\Http\Controllers;

use App\Models\ForumKomentar;
use App\Models\ForumPostingan;
use App\Models\LogAktivitasAsdos;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index($idMatkul)
    {
        $user = Auth::user();
        $matkul = MataKuliah::with('dosen')->findOrFail($idMatkul);

        $posts = ForumPostingan::where('id_matkul', $idMatkul)
            ->with(['author', 'komentar.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika mahasiswa, sertakan info pengumpulan tugas milik mereka
        if ($user->role === 'mahasiswa') {
            foreach ($posts as $p) {
                if ($p->tipe_post === 'tugas') {
                    $p->my_submission = PengumpulanTugas::where('id_post', $p->id_post)
                        ->where('id_mahasiswa', $user->id_user)
                        ->first();
                }
            }
        }

        return view('forum.index', compact('matkul', 'posts', 'user'));
    }

    public function createPost(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Hanya Dosen dan Asisten Dosen yang dapat menerbitkan postingan!');
        }

        $request->validate([
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'tipe_post' => 'required|in:pengumuman,materi,tugas',
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'file_lampiran' => 'nullable|file|max:20480', // max 20MB
            'deadline' => 'nullable|date',
        ]);

        $fileName = null;
        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $fileName = time().'_'.$safeName;
            $file->move(public_path('uploads/modul'), $fileName);
        }

        $post = ForumPostingan::create([
            'id_matkul' => $request->id_matkul,
            'id_author' => $user->id_user,
            'tipe_post' => $request->tipe_post,
            'judul' => trim($request->judul),
            'isi' => trim($request->isi),
            'file_lampiran' => $fileName,
            'deadline' => $request->deadline ? date('Y-m-d H:i:s', strtotime($request->deadline)) : null,
            'is_closed' => false,
            'created_at' => now(),
        ]);

        if ($user->role === 'asisten_dosen') {
            LogAktivitasAsdos::log(
                $request->id_matkul,
                $user->id_user,
                'Publikasi Konten Forum',
                'Menerbitkan '.ucfirst($request->tipe_post).': '.$request->judul
            );
        }

        return redirect()->route('forum.index', $request->id_matkul)->with('success', 'Postingan baru berhasil diterbitkan di forum praktikum!');
    }

    public function updatePost(Request $request, $idPost)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Hanya Dosen dan Asisten Dosen yang dapat mengedit postingan forum!');
        }

        $post = ForumPostingan::with('mataKuliah')->findOrFail($idPost);

        if ($user->role === 'dosen' && $post->mataKuliah->id_dosen !== $user->id_user) {
            return back()->with('error', 'Anda tidak memiliki hak akses mengedit postingan di kelas ini!');
        }

        $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'file_lampiran' => 'nullable|file|max:20480', // max 20MB
            'deadline' => 'nullable|date',
        ]);

        if ($request->hasFile('file_lampiran')) {
            if ($post->file_lampiran && file_exists(public_path('uploads/modul/'.$post->file_lampiran))) {
                @unlink(public_path('uploads/modul/'.$post->file_lampiran));
            }

            $file = $request->file('file_lampiran');
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $fileName = time().'_'.$safeName;
            $file->move(public_path('uploads/modul'), $fileName);
            $post->file_lampiran = $fileName;
        }

        $post->judul = trim($request->judul);
        $post->isi = trim($request->isi);

        if ($post->tipe_post === 'tugas') {
            $post->deadline = $request->deadline ? date('Y-m-d H:i:s', strtotime($request->deadline)) : null;
        }

        $post->save();

        if ($user->role === 'asisten_dosen') {
            LogAktivitasAsdos::log(
                $post->id_matkul,
                $user->id_user,
                'Pembaruan Konten Forum',
                'Memperbarui '.ucfirst($post->tipe_post).': '.$post->judul
            );
        }

        return redirect()->to(route('forum.index', $post->id_matkul).'#post-'.$post->id_post)->with('success', 'Postingan forum berhasil diperbarui!');
    }

    public function deletePost($idPost)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Hanya Dosen dan Asisten Dosen yang dapat menghapus postingan forum!');
        }

        $post = ForumPostingan::with(['mataKuliah', 'pengumpulan'])->findOrFail($idPost);

        if ($user->role === 'dosen' && $post->mataKuliah->id_dosen !== $user->id_user) {
            return back()->with('error', 'Anda tidak memiliki hak akses menghapus postingan di kelas ini!');
        }

        $idMatkul = $post->id_matkul;
        $judulPost = $post->judul;

        if ($post->file_lampiran && file_exists(public_path('uploads/modul/'.$post->file_lampiran))) {
            @unlink(public_path('uploads/modul/'.$post->file_lampiran));
        }

        foreach ($post->pengumpulan as $sub) {
            if ($sub->file_path && file_exists(public_path('uploads/tugas/'.$sub->file_path))) {
                @unlink(public_path('uploads/tugas/'.$sub->file_path));
            }
        }

        $post->delete();

        if ($user->role === 'asisten_dosen') {
            LogAktivitasAsdos::log(
                $idMatkul,
                $user->id_user,
                'Penghapusan Konten Forum',
                'Menghapus postingan: '.$judulPost
            );
        }

        return redirect()->route('forum.index', $idMatkul)->with('success', 'Postingan forum berhasil dihapus!');
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'id_post' => 'required|exists:forum_postingan,id_post',
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'isi_komentar' => 'required|string',
        ]);

        $user = Auth::user();

        ForumKomentar::create([
            'id_post' => $request->id_post,
            'id_user' => $user->id_user,
            'isi_komentar' => trim($request->isi_komentar),
            'created_at' => now(),
        ]);

        return redirect()->to(route('forum.index', $request->id_matkul).'#post-'.$request->id_post)->with('success', 'Komentar berhasil dikirimkan!');
    }

    public function deleteComment($idKomentar)
    {
        $user = Auth::user();
        $komentar = ForumKomentar::with('postingan.mataKuliah')->findOrFail($idKomentar);

        $isAuthor = $komentar->id_user === $user->id_user;
        $isDosen = $user->role === 'dosen' && $komentar->postingan->mataKuliah->id_dosen === $user->id_user;
        $isAsdos = $user->role === 'asisten_dosen';

        if (! $isAuthor && ! $isDosen && ! $isAsdos) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus komentar ini!');
        }

        $idMatkul = $komentar->postingan->id_matkul;
        $idPost = $komentar->id_post;
        $komentar->delete();

        return redirect()->to(route('forum.index', $idMatkul).'#post-'.$idPost)->with('success', 'Komentar berhasil dihapus!');
    }

    public function toggleLock($idPost, $idMatkul)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengunci tugas!');
        }

        $post = ForumPostingan::findOrFail($idPost);
        $post->is_closed = ! $post->is_closed;
        $post->save();

        if ($user->role === 'asisten_dosen') {
            $statusTxt = $post->is_closed ? 'Mengunci' : 'Membuka kunci';
            LogAktivitasAsdos::log(
                $idMatkul,
                $user->id_user,
                'Penguncian Tugas',
                "{$statusTxt} pengumpulan tugas: {$post->judul}"
            );
        }

        return redirect()->route('forum.index', $idMatkul)->with('success', 'Status pengumpulan tugas berhasil diubah!');
    }

    public function submitTugas(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            return back()->with('error', 'Hanya mahasiswa yang dapat mengumpulkan tugas!');
        }

        $request->validate([
            'id_post' => 'required|exists:forum_postingan,id_post',
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'file_tugas' => 'required|file|max:51200', // max 50MB
            'catatan_mahasiswa' => 'nullable|string',
        ]);

        $post = ForumPostingan::findOrFail($request->id_post);

        if ($post->is_closed) {
            return back()->with('error', 'Maaf, wadah pengumpulan tugas ini telah dikunci oleh Asisten Dosen!');
        }

        // Tentukan status submit (tepat waktu vs terlambat)
        $statusSubmit = 'tepat_waktu';
        if ($post->deadline && now()->greaterThan($post->deadline)) {
            $statusSubmit = 'terlambat';
        }

        $file = $request->file('file_tugas');
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
        $uniqueName = 'TUGAS_'.$post->id_post.'_'.$user->username.'_'.time().'_'.$safeName;
        $file->move(public_path('uploads/tugas'), $uniqueName);

        PengumpulanTugas::updateOrCreate(
            ['id_post' => $post->id_post, 'id_mahasiswa' => $user->id_user],
            [
                'file_path' => $uniqueName,
                'catatan_mahasiswa' => trim($request->catatan_mahasiswa ?? ''),
                'submitted_at' => now(),
                'status_submit' => $statusSubmit,
            ]
        );

        return redirect()->route('forum.index', $request->id_matkul)->with('success', 'File tugas praktikum Anda berhasil dikumpulkan!');
    }

    /**
     * Mahasiswa memperbarui berkas tugas yang sudah dikumpulkan.
     */
    public function updateTugas(Request $request, int $idPengumpulan)
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            return back()->with('error', 'Hanya mahasiswa yang dapat mengedit tugas!');
        }

        $submission = PengumpulanTugas::with('postingan')->findOrFail($idPengumpulan);

        if ($submission->id_mahasiswa !== $user->id_user) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengedit tugas ini!');
        }

        if ($submission->postingan->is_closed) {
            return back()->with('error', 'Wadah pengumpulan tugas ini telah dikunci!');
        }

        $request->validate([
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'file_tugas' => 'nullable|file|max:51200',
            'catatan_mahasiswa' => 'nullable|string',
        ]);

        if ($request->hasFile('file_tugas')) {
            // Hapus file lama
            if ($submission->file_path && file_exists(public_path('uploads/tugas/'.$submission->file_path))) {
                @unlink(public_path('uploads/tugas/'.$submission->file_path));
            }

            $file = $request->file('file_tugas');
            $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
            $uniqueName = 'TUGAS_'.$submission->id_post.'_'.$user->username.'_'.time().'_'.$safeName;
            $file->move(public_path('uploads/tugas'), $uniqueName);
            $submission->file_path = $uniqueName;

            // Perbarui status submit
            $statusSubmit = 'tepat_waktu';
            if ($submission->postingan->deadline && now()->greaterThan($submission->postingan->deadline)) {
                $statusSubmit = 'terlambat';
            }
            $submission->status_submit = $statusSubmit;
            $submission->submitted_at = now();
        }

        $submission->catatan_mahasiswa = trim($request->catatan_mahasiswa ?? '');
        $submission->save();

        return redirect()->route('forum.index', $request->id_matkul)->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Mahasiswa menghapus tugas yang sudah dikumpulkan.
     */
    public function deleteTugas(int $idPengumpulan)
    {
        $user = Auth::user();
        if ($user->role !== 'mahasiswa') {
            return back()->with('error', 'Hanya mahasiswa yang dapat menghapus tugas!');
        }

        $submission = PengumpulanTugas::with('postingan')->findOrFail($idPengumpulan);

        if ($submission->id_mahasiswa !== $user->id_user) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus tugas ini!');
        }

        if ($submission->postingan->is_closed) {
            return back()->with('error', 'Wadah pengumpulan tugas ini telah dikunci, tidak dapat menghapus tugas!');
        }

        $idMatkul = $submission->postingan->id_matkul;

        if ($submission->file_path && file_exists(public_path('uploads/tugas/'.$submission->file_path))) {
            @unlink(public_path('uploads/tugas/'.$submission->file_path));
        }

        $submission->delete();

        return redirect()->route('forum.index', $idMatkul)->with('success', 'Tugas berhasil dihapus dari sistem!');
    }

    public function detailTugas($idPost)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Akses terbatas untuk Dosen dan Asisten Dosen!');
        }

        $post = ForumPostingan::with(['mataKuliah'])->findOrFail($idPost);
        $submissions = PengumpulanTugas::where('id_post', $idPost)
            ->with('mahasiswa')
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('forum.detail_tugas', compact('post', 'submissions', 'user'));
    }

    public function gradeSubmission(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'dosen' && $user->role !== 'asisten_dosen') {
            return back()->with('error', 'Akses ditolak!');
        }

        $request->validate([
            'id_pengumpulan' => 'required|exists:pengumpulan_tugas,id_pengumpulan',
            'id_post' => 'required|exists:forum_postingan,id_post',
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan_asprak' => 'nullable|string',
        ]);

        $submission = PengumpulanTugas::findOrFail($request->id_pengumpulan);
        $submission->update([
            'nilai' => (float) $request->nilai,
            'catatan_asprak' => trim($request->catatan_asprak ?? ''),
        ]);

        if ($user->role === 'asisten_dosen') {
            $post = ForumPostingan::find($request->id_post);
            if ($post) {
                LogAktivitasAsdos::log(
                    $post->id_matkul,
                    $user->id_user,
                    'Penilaian Tugas Forum',
                    "Menilai tugas '{$post->judul}' (ID Pengumpulan #{$submission->id_pengumpulan}) dengan nilai {$request->nilai}"
                );
            }
        }

        return redirect()->route('forum.detail_tugas', $request->id_post)->with('success', 'Nilai tugas praktikan berhasil disimpan!');
    }
}
