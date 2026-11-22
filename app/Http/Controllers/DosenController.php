<?php

namespace App\Http\Controllers;

use App\Models\AsistenMatkul;
use App\Models\ForumPostingan;
use App\Models\LogAktivitasAsdos;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;
use App\Models\SupervisiDosen;
use App\Services\NilaiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    protected NilaiService $nilaiService;

    public function __construct(NilaiService $nilaiService)
    {
        $this->nilaiService = $nilaiService;
    }

    /**
     * Tampilan utama dashboard dosen: daftar mata kuliah dan permintaan asisten.
     */
    public function index()
    {
        $user = Auth::user();

        $matkulList = MataKuliah::where('id_dosen', $user->id_user)
            ->withCount([
                'peserta as total_praktikan',
                'asistenMatkul as total_asdos' => fn ($q) => $q->where('status', 'approved'),
                'asistenMatkul as pending_asdos' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingAsprakRequests = AsistenMatkul::whereHas('mataKuliah', fn ($q) => $q->where('id_dosen', $user->id_user))
            ->where('status', 'pending')
            ->with(['mataKuliah', 'asisten'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('dosen.dashboard', compact('user', 'matkulList', 'pendingAsprakRequests'));
    }

    /**
     * Pembuatan mata kuliah praktikum baru oleh dosen pengampu.
     */
    public function createMatkul(Request $request)
    {
        $request->validate([
            'kode_matkul' => 'required|string|max:20|unique:mata_kuliah,kode_matkul',
            'nama_matkul' => 'required|string|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'kode_matkul.unique' => 'Kode Mata Kuliah ini sudah digunakan oleh kelas lain.',
        ]);

        $user = Auth::user();
        $kodeMatkul = strtoupper(trim($request->kode_matkul));

        $matkul = MataKuliah::create([
            'kode_matkul' => $kodeMatkul,
            'nama_matkul' => trim($request->nama_matkul),
            'sks' => (int) $request->sks,
            'kode_gabung' => $kodeMatkul,
            'id_dosen' => $user->id_user,
            'deskripsi' => trim($request->deskripsi ?? ''),
            'created_at' => now(),
        ]);

        // Inisialisasi bobot standar via NilaiService
        $this->nilaiService->getBobotMatkul($matkul->id_matkul);

        return redirect()->route('dosen.dashboard')->with('success', "Mata kuliah '{$matkul->nama_matkul}' berhasil dibuat! Kode Gabung: {$matkul->kode_gabung}");
    }

    /**
     * Menyetujui permohonan asisten dosen (ACC).
     */
    public function accAsprak(int $idAsistenMatkul)
    {
        $user = Auth::user();
        $req = AsistenMatkul::where('id_asisten_matkul', $idAsistenMatkul)
            ->whereHas('mataKuliah', fn ($q) => $q->where('id_dosen', $user->id_user))
            ->first();

        if ($req) {
            $req->update(['status' => 'approved']);

            return redirect()->route('dosen.dashboard')->with('success', 'Asisten Dosen berhasil disetujui untuk kelas praktikum.');
        }

        return redirect()->route('dosen.dashboard')->with('error', 'Permintaan asisten dosen tidak ditemukan.');
    }

    /**
     * Menolak permohonan asisten dosen.
     */
    public function rejectAsprak(int $idAsistenMatkul)
    {
        $user = Auth::user();
        $req = AsistenMatkul::where('id_asisten_matkul', $idAsistenMatkul)
            ->whereHas('mataKuliah', fn ($q) => $q->where('id_dosen', $user->id_user))
            ->first();

        if ($req) {
            $req->update(['status' => 'rejected']);

            return redirect()->route('dosen.dashboard')->with('success', 'Permintaan asisten dosen telah ditolak.');
        }

        return redirect()->route('dosen.dashboard')->with('error', 'Permintaan asisten dosen tidak ditemukan.');
    }

    /**
     * Halaman pengawasan & monitoring: kinerja asdos, supervisi, dan rekapitulasi nilai praktikan.
     */
    public function monitoring(int $idMatkul)
    {
        $user = Auth::user();
        $matkul = MataKuliah::where('id_matkul', $idMatkul)
            ->where('id_dosen', $user->id_user)
            ->with(['dosen'])
            ->firstOrFail();

        $bobot = $this->nilaiService->getBobotMatkul($idMatkul);

        $asprakList = AsistenMatkul::where('id_matkul', $idMatkul)
            ->where('status', 'approved')
            ->with('asisten')
            ->get();

        $supervisiList = SupervisiDosen::where('id_matkul', $idMatkul)
            ->with('dosen')
            ->orderBy('created_at', 'desc')
            ->get();

        $logAktivitas = LogAktivitasAsdos::where('id_matkul', $idMatkul)
            ->with('asdos')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $rekapNilai = $this->nilaiService->getRekapNilai($idMatkul);

        // Perhitungan metrik evaluasi asisten dosen
        $totalModul = ForumPostingan::where('id_matkul', $idMatkul)->where('tipe_post', 'materi')->count();
        $totalTugas = ForumPostingan::where('id_matkul', $idMatkul)->where('tipe_post', 'tugas')->count();

        $totalDikumpulkan = PengumpulanTugas::whereHas('postingan', fn ($q) => $q->where('id_matkul', $idMatkul))->count();
        $totalDinilai = PengumpulanTugas::whereHas('postingan', fn ($q) => $q->where('id_matkul', $idMatkul))->whereNotNull('nilai')->count();
        $totalBelumDinilai = $totalDikumpulkan - $totalDinilai;
        $persenKoreksi = $totalDikumpulkan > 0 ? round(($totalDinilai / $totalDikumpulkan) * 100, 1) : 100.0;

        $tugasList = ForumPostingan::where('id_matkul', $idMatkul)
            ->where('tipe_post', 'tugas')
            ->withCount([
                'pengumpulan as jumlah_kumpul',
                'pengumpulan as jumlah_dinilai' => fn ($q) => $q->whereNotNull('nilai'),
                'pengumpulan as jumlah_belum' => fn ($q) => $q->whereNull('nilai'),
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $kinerjaAsdos = [
            'total_tugas' => $totalTugas,
            'total_modul' => $totalModul,
            'total_dikumpulkan' => $totalDikumpulkan,
            'total_dinilai' => $totalDinilai,
            'total_belum_dinilai' => $totalBelumDinilai,
            'persentase_selesai' => $persenKoreksi,
            'detail_tugas' => $tugasList,
        ];

        return view('dosen.monitoring', compact('matkul', 'bobot', 'asprakList', 'supervisiList', 'logAktivitas', 'rekapNilai', 'kinerjaAsdos'));
    }

    /**
     * Tambah catatan supervisi baru untuk asisten dosen.
     */
    public function addSupervisi(Request $request)
    {
        $request->validate([
            'id_matkul' => 'required|exists:mata_kuliah,id_matkul',
            'judul' => 'required|string|max:150',
            'catatan' => 'required|string',
        ]);

        $user = Auth::user();

        SupervisiDosen::create([
            'id_matkul' => $request->id_matkul,
            'id_dosen' => $user->id_user,
            'judul' => trim($request->judul),
            'catatan' => trim($request->catatan),
            'status' => 'terkirim',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dosen.monitoring', $request->id_matkul)->with('success', 'Catatan supervisi berhasil dikirimkan ke Asisten Dosen.');
    }

    /**
     * Hapus catatan supervisi dosen.
     */
    public function deleteSupervisi(int $idSupervisi, int $idMatkul)
    {
        $user = Auth::user();
        SupervisiDosen::where('id_supervisi', $idSupervisi)
            ->where('id_dosen', $user->id_user)
            ->delete();

        return redirect()->route('dosen.monitoring', $idMatkul)->with('success', 'Catatan supervisi berhasil dihapus.');
    }
}
