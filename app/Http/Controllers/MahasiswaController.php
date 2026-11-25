<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\PesertaPraktikum;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    protected NilaiService $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    /**
     * Dashboard mahasiswa: daftar kelas praktikum yang diikuti.
     */
    public function index()
    {
        $user = Auth::user();

        $matkulList = MataKuliah::whereHas('peserta', function ($q) use ($user) {
            $q->where('id_mahasiswa', $user->id_user);
        })
            ->with(['dosen'])
            ->withCount(['peserta as total_praktikan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.dashboard', compact('user', 'matkulList'));
    }

    /**
     * Mahasiswa bergabung ke kelas praktikum menggunakan kode gabung dari Dosen.
     */
    public function joinKelas(Request $request)
    {
        $request->validate([
            'kode_gabung' => 'required|string',
        ]);

        $user = Auth::user();
        $code = strtoupper(trim($request->kode_gabung));

        $matkul = MataKuliah::where('kode_gabung', $code)->orWhere('kode_matkul', $code)->first();
        if (! $matkul) {
            return back()->with('error', 'Mata kuliah dengan Kode Gabung tersebut tidak ditemukan.');
        }

        $existing = PesertaPraktikum::where('id_matkul', $matkul->id_matkul)
            ->where('id_mahasiswa', $user->id_user)
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah terdaftar di kelas praktikum ini.');
        }

        PesertaPraktikum::create([
            'id_matkul' => $matkul->id_matkul,
            'id_mahasiswa' => $user->id_user,
            'joined_at' => now(),
        ]);

        return back()->with('success', "Berhasil bergabung ke kelas praktikum '{$matkul->nama_matkul}'.");
    }

    /**
     * Lembar transparansi penilaian akademik untuk mahasiswa.
     */
    public function nilai(int $idMatkul)
    {
        $user = Auth::user();

        $matkul = MataKuliah::with('dosen')->findOrFail($idMatkul);

        $isEnrolled = PesertaPraktikum::where('id_matkul', $idMatkul)
            ->where('id_mahasiswa', $user->id_user)
            ->exists();

        if (! $isEnrolled) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Anda belum terdaftar di mata kuliah ini.');
        }

        $bobot = $this->nilaiService->getBobotMatkul($idMatkul);
        $nilai = $this->nilaiService->getNilaiMahasiswa($idMatkul, $user->id_user);

        return view('mahasiswa.nilai', compact('matkul', 'bobot', 'nilai'));
    }
}
