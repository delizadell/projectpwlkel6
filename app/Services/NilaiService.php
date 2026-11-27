<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\LogAktivitasAsdos;
use App\Models\NilaiAkhirMahasiswa;
use App\Models\NilaiQuiz;
use App\Models\NilaiTugas;
use App\Models\NilaiUas;
use App\Models\NilaiUts;
use App\Models\PersentaseMatkul;
use Illuminate\Support\Facades\DB;

class NilaiService
{
    /**
     * Dapatkan rekapitulasi nilai seluruh mahasiswa di suatu mata kuliah praktikum.
     */
    public function getRekapNilai(int $idMatkul)
    {
        return DB::table('peserta_praktikum as p')
            ->join('users as u', 'p.id_mahasiswa', '=', 'u.id_user')
            ->leftJoin('absensi as ab', function ($j) use ($idMatkul) {
                $j->on('ab.id_mahasiswa', '=', 'u.id_user')->where('ab.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_tugas as t', function ($j) use ($idMatkul) {
                $j->on('t.id_mahasiswa', '=', 'u.id_user')->where('t.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_quiz as q', function ($j) use ($idMatkul) {
                $j->on('q.id_mahasiswa', '=', 'u.id_user')->where('q.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_uts as uts', function ($j) use ($idMatkul) {
                $j->on('uts.id_mahasiswa', '=', 'u.id_user')->where('uts.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_uas as uas', function ($j) use ($idMatkul) {
                $j->on('uas.id_mahasiswa', '=', 'u.id_user')->where('uas.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_akhir_mahasiswa as nam', function ($j) use ($idMatkul) {
                $j->on('nam.id_mahasiswa', '=', 'u.id_user')->where('nam.id_matkul', '=', $idMatkul);
            })
            ->where('p.id_matkul', $idMatkul)
            ->select([
                'u.id_user',
                'u.username as npm',
                'u.nama_lengkap',
                DB::raw('COALESCE(ab.jumlah_hadir, 0) as jumlah_hadir'),
                DB::raw('COALESCE(ab.jumlah_pertemuan, 16) as jumlah_pertemuan'),
                DB::raw('COALESCE(ab.nilai_absensi, 0) as nilai_absensi'),
                DB::raw('COALESCE(t.rata_rata_tugas, 0) as nilai_tugas'),
                DB::raw('COALESCE(q.rata_rata_quiz, 0) as nilai_quiz'),
                DB::raw('COALESCE(uts.nilai_uts, 0) as nilai_uts'),
                DB::raw('COALESCE(uas.nilai_uas, 0) as nilai_uas'),
                'nam.nilai_akhir_total',
                'nam.huruf_matkul',
                'nam.bobot_matkul',
            ])
            ->orderBy('u.nama_lengkap', 'asc')
            ->get();
    }

    /**
     * Dapatkan nilai spesifik untuk seorang mahasiswa pada mata kuliah praktikum.
     */
    public function getNilaiMahasiswa(int $idMatkul, int $idMahasiswa)
    {
        return DB::table('users as u')
            ->leftJoin('absensi as ab', function ($j) use ($idMatkul) {
                $j->on('ab.id_mahasiswa', '=', 'u.id_user')->where('ab.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_tugas as t', function ($j) use ($idMatkul) {
                $j->on('t.id_mahasiswa', '=', 'u.id_user')->where('t.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_quiz as q', function ($j) use ($idMatkul) {
                $j->on('q.id_mahasiswa', '=', 'u.id_user')->where('q.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_uts as uts', function ($j) use ($idMatkul) {
                $j->on('uts.id_mahasiswa', '=', 'u.id_user')->where('uts.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_uas as uas', function ($j) use ($idMatkul) {
                $j->on('uas.id_mahasiswa', '=', 'u.id_user')->where('uas.id_matkul', '=', $idMatkul);
            })
            ->leftJoin('nilai_akhir_mahasiswa as nam', function ($j) use ($idMatkul) {
                $j->on('nam.id_mahasiswa', '=', 'u.id_user')->where('nam.id_matkul', '=', $idMatkul);
            })
            ->where('u.id_user', $idMahasiswa)
            ->select([
                'u.id_user',
                'u.username as npm',
                'u.nama_lengkap',
                DB::raw('COALESCE(ab.jumlah_hadir, 0) as jumlah_hadir'),
                DB::raw('COALESCE(ab.jumlah_pertemuan, 16) as jumlah_pertemuan'),
                DB::raw('COALESCE(ab.nilai_absensi, 0) as nilai_absensi'),
                DB::raw('COALESCE(t.rata_rata_tugas, 0) as nilai_tugas'),
                DB::raw('COALESCE(q.rata_rata_quiz, 0) as nilai_quiz'),
                DB::raw('COALESCE(uts.nilai_uts, 0) as nilai_uts'),
                DB::raw('COALESCE(uas.nilai_uas, 0) as nilai_uas'),
                'nam.nilai_akhir_total',
                'nam.huruf_matkul',
                'nam.bobot_matkul',
            ])
            ->first();
    }

    /**
     * Dapatkan konfigurasi bobot persentase mata kuliah (atau buat default jika belum ada).
     */
    public function getBobotMatkul(int $idMatkul): PersentaseMatkul
    {
        return PersentaseMatkul::firstOrCreate(
            ['id_matkul' => $idMatkul],
            [
                'persentase_absensi' => 10.00,
                'persentase_tugas' => 25.00,
                'persentase_quiz' => 15.00,
                'persentase_uts' => 25.00,
                'persentase_uas' => 25.00,
            ]
        );
    }

    /**
     * Update bobot persentase penilaian dengan validasi total 100%.
     */
    public function updateBobot(int $idMatkul, array $persen): PersentaseMatkul
    {
        return PersentaseMatkul::updateOrCreate(
            ['id_matkul' => $idMatkul],
            [
                'persentase_absensi' => $persen['absensi'],
                'persentase_tugas' => $persen['tugas'],
                'persentase_quiz' => $persen['quiz'],
                'persentase_uts' => $persen['uts'],
                'persentase_uas' => $persen['uas'],
            ]
        );
    }

    /**
     * Simpan nilai mahasiswa dan hitung nilai akhir secara atomik.
     */
    public function simpanNilaiMahasiswa(int $idMatkul, int $idMahasiswa, array $data, int $idOperator)
    {
        return DB::transaction(function () use ($idMatkul, $idMahasiswa, $data, $idOperator) {
            $bobot = $this->getBobotMatkul($idMatkul);

            $hadir = (int) $data['jumlah_hadir'];
            $totalPertemuan = (int) $data['jumlah_pertemuan'];
            $nilaiTugas = (float) $data['nilai_tugas'];
            $nilaiQuiz = (float) $data['nilai_quiz'];
            $nilaiUts = (float) $data['nilai_uts'];
            $nilaiUas = (float) $data['nilai_uas'];

            // 1. Absensi
            $nilaiAbsen = ($totalPertemuan > 0) ? ($hadir / $totalPertemuan) * 100 : 0;
            $nilaiAkhirAbsen = $nilaiAbsen * ($bobot->persentase_absensi / 100);

            Absensi::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                [
                    'jumlah_pertemuan' => $totalPertemuan,
                    'jumlah_hadir' => $hadir,
                    'nilai_absensi' => $nilaiAbsen,
                    'nilai_akhir_absensi' => $nilaiAkhirAbsen,
                ]
            );

            // 2. UTS
            NilaiUts::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                ['nilai_uts' => $nilaiUts]
            );

            // 3. UAS
            NilaiUas::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                ['nilai_uas' => $nilaiUas]
            );

            // 4. Tugas
            NilaiTugas::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                ['rata_rata_tugas' => $nilaiTugas]
            );

            // 5. Quiz
            NilaiQuiz::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                ['rata_rata_quiz' => $nilaiQuiz]
            );

            // 6. Hitung Total Nilai Akhir
            $nilaiAkhirTotal = 0;
            $nilaiAkhirTotal += $nilaiAbsen * ($bobot->persentase_absensi / 100);
            $nilaiAkhirTotal += $nilaiTugas * ($bobot->persentase_tugas / 100);
            $nilaiAkhirTotal += $nilaiQuiz * ($bobot->persentase_quiz / 100);
            $nilaiAkhirTotal += $nilaiUts * ($bobot->persentase_uts / 100);
            $nilaiAkhirTotal += $nilaiUas * ($bobot->persentase_uas / 100);

            $hurufMutu = NilaiAkhirMahasiswa::konversiHuruf($nilaiAkhirTotal);
            $bobotMutu = NilaiAkhirMahasiswa::konversiBobot($nilaiAkhirTotal);

            $nilaiAkhir = NilaiAkhirMahasiswa::updateOrCreate(
                ['id_matkul' => $idMatkul, 'id_mahasiswa' => $idMahasiswa],
                [
                    'nilai_akhir_total' => $nilaiAkhirTotal,
                    'huruf_matkul' => $hurufMutu,
                    'bobot_matkul' => $bobotMutu,
                    'updated_at' => now(),
                ]
            );

            // Log aktivitas asisten dosen
            LogAktivitasAsdos::log(
                $idMatkul,
                $idOperator,
                'Input Rekap Nilai',
                "Menyimpan nilai mahasiswa ID #{$idMahasiswa} (Hadir: {$hadir}/{$totalPertemuan}, Tugas: {$nilaiTugas}, Quiz: {$nilaiQuiz}, UTS: {$nilaiUts}, UAS: {$nilaiUas})"
            );

            return $nilaiAkhir;
        });
    }
}
