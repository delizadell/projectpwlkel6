<?php

namespace App\Http\Controllers;

use App\Models\AsistenMatkul;
use App\Models\LogAktivitasAsdos;
use App\Models\MataKuliah;
use App\Models\SupervisiDosen;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsprakController extends Controller
{
    protected NilaiService $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    public function index()
    {
        $user = Auth::user();

        $matkulList = MataKuliah::whereHas('asistenMatkul', function ($q) use ($user) {
            $q->where('id_asisten', $user->id_user)->where('status', 'approved');
        })
            ->with(['dosen'])
            ->withCount(['peserta as total_praktikan'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingRequests = AsistenMatkul::where('id_asisten', $user->id_user)
            ->where('status', 'pending')
            ->with(['mataKuliah.dosen'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('asprak.dashboard', compact('user', 'matkulList', 'pendingRequests'));
    }

    public function gabungPraktikum(Request $request)
    {
        $request->validate([
            'kode_gabung' => 'required|string',
        ]);

        $user = Auth::user();
        $code = strtoupper(trim($request->kode_gabung));

        $matkul = MataKuliah::where('kode_gabung', $code)->orWhere('kode_matkul', $code)->first();
        if (! $matkul) {
            return back()->with('error', 'Mata kuliah praktikum dengan kode tersebut tidak ditemukan.');
        }

        $existing = AsistenMatkul::where('id_matkul', $matkul->id_matkul)
            ->where('id_asisten', $user->id_user)
            ->first();

        if ($existing) {
            if ($existing->status === 'approved') {
                return back()->with('error', 'Anda sudah terdaftar sebagai asisten di mata kuliah ini.');
            } elseif ($existing->status === 'pending') {
                return back()->with('error', 'Permohonan Anda sedang menunggu persetujuan (ACC) dari Dosen Pengampu.');
            } else {
                $existing->update(['status' => 'pending', 'requested_at' => now()]);

                return back()->with('success', 'Permohonan bergabung telah diajukan kembali ke Dosen Pengampu.');
            }
        }

        AsistenMatkul::create([
            'id_matkul' => $matkul->id_matkul,
            'id_asisten' => $user->id_user,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return back()->with('success', "Permohonan menjadi asisten kelas '{$matkul->nama_matkul}' berhasil dikirimkan ke Dosen Pengampu.");
    }

    public function penilaian(int $idMatkul)
    {
        $user = Auth::user();

        $isAsdos = AsistenMatkul::where('id_matkul', $idMatkul)
            ->where('id_asisten', $user->id_user)
            ->where('status', 'approved')
            ->exists();

        if (! $isAsdos) {
            return redirect()->route('asprak.dashboard')->with('error', 'Anda tidak terdaftar sebagai asisten di kelas ini.');
        }

        $matkul = MataKuliah::with('dosen')->findOrFail($idMatkul);
        $bobot = $this->nilaiService->getBobotMatkul($idMatkul);
        $pesertaList = $this->nilaiService->getRekapNilai($idMatkul);

        $supervisiList = SupervisiDosen::where('id_matkul', $idMatkul)
            ->with('dosen')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('asprak.penilaian', compact('matkul', 'bobot', 'pesertaList', 'supervisiList'));
    }

    public function updateBobot(Request $request)
    {
        $request->validate([
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'persentase_absensi' => 'required|numeric|min:0|max:100',
            'persentase_tugas' => 'required|numeric|min:0|max:100',
            'persentase_quiz' => 'required|numeric|min:0|max:100',
            'persentase_uts' => 'required|numeric|min:0|max:100',
            'persentase_uas' => 'required|numeric|min:0|max:100',
        ]);

        $absen = (float) $request->persentase_absensi;
        $tugas = (float) $request->persentase_tugas;
        $quiz = (float) $request->persentase_quiz;
        $uts = (float) $request->persentase_uts;
        $uas = (float) $request->persentase_uas;

        $total = $absen + $tugas + $quiz + $uts + $uas;
        if (abs($total - 100.0) > 0.01) {
            return back()->with('error', "Total persentase bobot harus tepat 100%. (Total saat ini: {$total}%)");
        }

        $this->nilaiService->updateBobot($request->id_matkul, [
            'absensi' => $absen,
            'tugas' => $tugas,
            'quiz' => $quiz,
            'uts' => $uts,
            'uas' => $uas,
        ]);

        $user = Auth::user();
        LogAktivitasAsdos::log(
            $request->id_matkul,
            $user->id_user,
            'Perubahan Bobot Nilai',
            "Memperbarui bobot penilaian: Absen {$absen}%, Tugas {$tugas}%, Quiz {$quiz}%, UTS {$uts}%, UAS {$uas}%"
        );

        return back()->with('success', 'Persentase bobot nilai berhasil diperbarui.');
    }

    public function simpanNilai(Request $request)
    {
        $request->validate([
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'id_mahasiswa' => 'required|exists:users,id_user',
            'jumlah_hadir' => 'required|integer|min:0',
            'jumlah_pertemuan' => 'required|integer|min:1',
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_quiz' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
        ]);

        if ((int) $request->jumlah_hadir > (int) $request->jumlah_pertemuan) {
            return back()->with('error', 'Jumlah hadir tidak boleh melebihi total pertemuan.');
        }

        try {
            $user = Auth::user();
            $this->nilaiService->simpanNilaiMahasiswa(
                (int) $request->id_matkul,
                (int) $request->id_mahasiswa,
                $request->all(),
                $user->id_user
            );

            return back()->with('success', 'Nilai mahasiswa berhasil dihitung dan disimpan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan nilai: '.$e->getMessage());
        }
    }
    public function tanggapiSupervisi(Request $request)
    {
        $request->validate([
            'id_supervisi' => 'required|exists:supervisi_dosen,id_supervisi',
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'tanggapan' => 'required|string',
            'status' => 'nullable|in:terkirim,dibaca,selesai',
        ]);

        $supervisi = SupervisiDosen::findOrFail($request->id_supervisi);
        $status = $request->input('status', 'selesai');

        $supervisi->update([
            'tanggapan_asdos' => trim($request->tanggapan),
            'status' => $status,
            'updated_at' => now(),
        ]);

        $user = Auth::user();
        LogAktivitasAsdos::log(
            $request->id_matkul,
            $user->id_user,
            'Tanggapan Supervisi',
            "Menanggapi catatan supervisi #{$request->id_supervisi}"
        );

        return back()->with('success', 'Tanggapan atas catatan supervisi berhasil dikirim.');
    }
}
