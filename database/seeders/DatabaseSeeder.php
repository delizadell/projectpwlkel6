<?php

namespace Database\Seeders;

use App\Models\AsistenMatkul;
use App\Models\ForumPostingan;
use App\Models\LogAktivitasAsdos;
use App\Models\MataKuliah;
use App\Models\PengumpulanTugas;
use App\Models\PersentaseMatkul;
use App\Models\PesertaPraktikum;
use App\Models\SupervisiDosen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users
        $users = [
            [
                'id_user' => 1,
                'username' => 'dosen1',
                'password' => Hash::make('admin'),
                'nama_lengkap' => 'Dr. Hendra, M.Kom.',
                'prodi' => null,
                'role' => 'dosen',
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_user' => 2,
                'username' => 'asprak1',
                'password' => Hash::make('asprak'),
                'nama_lengkap' => 'Rian Hidayat (Asprak)',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'asisten_dosen',
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_user' => 3,
                'username' => '230101001',
                'password' => Hash::make('mhs'),
                'nama_lengkap' => 'Budi Santoso',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'mahasiswa',
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_user' => 4,
                'username' => '27278216781273',
                'password' => '$2y$10$VaIjg/Pw.pYQH9DfQk27A.lOtnTnNY1/lnWxo/jkRw27hob3YCqGm',
                'nama_lengkap' => 'budiono siregar',
                'prodi' => null,
                'role' => 'dosen',
                'created_at' => '2026-11-08 11:25:52',
            ],
            [
                'id_user' => 5,
                'username' => '123456789',
                'password' => '$2y$10$Cqvyc5M9evLEk9qGsP8KN.31EOXuoa82vsiWx8u1JBOn3ZrJr95Qy',
                'nama_lengkap' => 'Budi',
                'prodi' => 'S1 Teknik Informatika',
                'role' => 'asisten_dosen',
                'created_at' => '2026-11-08 11:26:25',
            ],
            [
                'id_user' => 6,
                'username' => '12345678910',
                'password' => '$2y$10$k2f9RjjinmScpejqjJrilean1DugJEX420F4Y879ruMEft0BbhN3e',
                'nama_lengkap' => 'Nu',
                'prodi' => null,
                'role' => 'dosen',
                'created_at' => '2026-11-08 11:27:12',
            ],
            [
                'id_user' => 7,
                'username' => '0987654321',
                'password' => '$2y$10$lwOgeIpFv8eKTTDg.K3ldeLMA0S5r5QsYlndfswRij31UNn6EKp4S',
                'nama_lengkap' => 'NANU',
                'prodi' => null,
                'role' => 'dosen',
                'created_at' => '2026-11-08 11:27:40',
            ],
            [
                'id_user' => 8,
                'username' => '24170520',
                'password' => '$2y$10$oSEDfc4nyW/Z.I2je54CE.e.3cdzvL3IA9MdOxDgRs3BuqsZU.Kr.',
                'nama_lengkap' => 'Budiono',
                'prodi' => null,
                'role' => 'dosen',
                'created_at' => '2026-11-08 11:40:12',
            ],
        ];
        foreach ($users as $u) {
            User::updateOrCreate(['id_user' => $u['id_user']], $u);
        }

        // 2. Seed Mata Kuliah
        $matkuls = [
            [
                'id_matkul' => 1,
                'kode_matkul' => 'PRAK-PBO-01',
                'nama_matkul' => 'Praktikum Pemrograman Berorientasi Objek',
                'sks' => 3,
                'kode_gabung' => 'PBO-2026-A',
                'id_dosen' => 1,
                'deskripsi' => 'Praktikum PBO kelas A dengan Java & JavaFX',
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_matkul' => 3,
                'kode_matkul' => 'SI24-SBD',
                'nama_matkul' => 'SISTEM BASISS',
                'sks' => 3,
                'kode_gabung' => 'SIS-F5LP',
                'id_dosen' => 1,
                'deskripsi' => '',
                'created_at' => '2026-11-08 11:17:43',
            ],
            [
                'id_matkul' => 4,
                'kode_matkul' => 'SIS24',
                'nama_matkul' => 'ASNAJSA',
                'sks' => 3,
                'kode_gabung' => 'ASN-SXCX',
                'id_dosen' => 1,
                'deskripsi' => 'ASAS',
                'created_at' => '2026-11-08 11:22:40',
            ],
            [
                'id_matkul' => 6,
                'kode_matkul' => 'PRAK-PWL-SI24',
                'nama_matkul' => 'WEB WEB WEB',
                'sks' => 2,
                'kode_gabung' => 'PRAK-PWL-SI24',
                'id_dosen' => 8,
                'deskripsi' => '',
                'created_at' => '2026-11-08 11:41:04',
            ],
        ];
        foreach ($matkuls as $m) {
            MataKuliah::updateOrCreate(['id_matkul' => $m['id_matkul']], $m);
        }

        // 3. Seed Asisten Matkul
        $asistens = [
            ['id_asisten_matkul' => 1, 'id_matkul' => 1, 'id_asisten' => 2, 'status' => 'approved', 'requested_at' => '2026-11-08 11:32:39'],
            ['id_asisten_matkul' => 4, 'id_matkul' => 6, 'id_asisten' => 2, 'status' => 'approved', 'requested_at' => '2026-11-08 11:41:56'],
        ];
        foreach ($asistens as $a) {
            AsistenMatkul::updateOrCreate(['id_asisten_matkul' => $a['id_asisten_matkul']], $a);
        }

        // 4. Seed Peserta Praktikum
        $pesertas = [
            ['id_peserta' => 1, 'id_matkul' => 1, 'id_mahasiswa' => 3, 'joined_at' => '2026-11-03 13:36:48'],
            ['id_peserta' => 3, 'id_matkul' => 4, 'id_mahasiswa' => 3, 'joined_at' => '2026-11-08 11:22:56'],
            ['id_peserta' => 4, 'id_matkul' => 6, 'id_mahasiswa' => 3, 'joined_at' => '2026-11-08 11:41:44'],
        ];
        foreach ($pesertas as $p) {
            PesertaPraktikum::updateOrCreate(['id_peserta' => $p['id_peserta']], $p);
        }

        // 5. Seed Persentase Matkul
        $bobots = [
            ['id_persentase' => 1, 'id_matkul' => 1, 'persentase_absensi' => 10, 'persentase_tugas' => 25, 'persentase_quiz' => 15, 'persentase_uts' => 25, 'persentase_uas' => 25],
            ['id_persentase' => 3, 'id_matkul' => 3, 'persentase_absensi' => 10, 'persentase_tugas' => 25, 'persentase_quiz' => 15, 'persentase_uts' => 25, 'persentase_uas' => 25],
            ['id_persentase' => 4, 'id_matkul' => 4, 'persentase_absensi' => 10, 'persentase_tugas' => 25, 'persentase_quiz' => 15, 'persentase_uts' => 25, 'persentase_uas' => 25],
            ['id_persentase' => 6, 'id_matkul' => 6, 'persentase_absensi' => 0,  'persentase_tugas' => 25, 'persentase_quiz' => 15, 'persentase_uts' => 25, 'persentase_uas' => 35],
        ];
        foreach ($bobots as $b) {
            PersentaseMatkul::updateOrCreate(['id_persentase' => $b['id_persentase']], $b);
        }

        // 6. Seed Forum Postingan
        $posts = [
            [
                'id_post' => 1,
                'id_matkul' => 1,
                'id_author' => 2,
                'tipe_post' => 'materi',
                'judul' => 'Modul Pertemuan 1 - Konsep Class dan Object',
                'isi' => 'Silakan unduh modul praktikum berikut untuk panduan pertemuan 1.',
                'file_lampiran' => 'modul_pertemuan_1.pdf',
                'deadline' => null,
                'is_closed' => false,
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_post' => 2,
                'id_matkul' => 1,
                'id_author' => 2,
                'tipe_post' => 'tugas',
                'judul' => 'Tugas Praktikum 1 - Membuat Program Kasir OOP',
                'isi' => 'Kerjakan latihan pada modul halaman 15 dan kumpulkan source code .zip sebelum tenggat waktu.',
                'file_lampiran' => null,
                'deadline' => '2026-11-10 23:59:00',
                'is_closed' => true,
                'created_at' => '2026-11-03 13:36:48',
            ],
            [
                'id_post' => 6,
                'id_matkul' => 1,
                'id_author' => 1,
                'tipe_post' => 'tugas',
                'judul' => 'jbajsbja',
                'isi' => 'anas',
                'file_lampiran' => null,
                'deadline' => '2026-11-10 21:54:00',
                'is_closed' => true,
                'created_at' => '2026-11-03 14:55:04',
            ],
            [
                'id_post' => 7,
                'id_matkul' => 6,
                'id_author' => 2,
                'tipe_post' => 'tugas',
                'judul' => 'DDF MDF M',
                'isi' => 'D, F,D',
                'file_lampiran' => null,
                'deadline' => '2026-11-11 18:43:00',
                'is_closed' => false,
                'created_at' => '2026-11-08 11:43:59',
            ],
        ];
        foreach ($posts as $po) {
            ForumPostingan::updateOrCreate(['id_post' => $po['id_post']], $po);
        }

        // 7. Seed Pengumpulan Tugas
        PengumpulanTugas::updateOrCreate(
            ['id_pengumpulan' => 2],
            [
                'id_post' => 7,
                'id_mahasiswa' => 3,
                'file_path' => 'TUGAS_7_230101001_1794138256_10.jpeg',
                'catatan_mahasiswa' => '',
                'submitted_at' => '2026-11-08 11:44:16',
                'status_submit' => 'tepat_waktu',
                'nilai' => 80.00,
                'catatan_asprak' => '',
            ]
        );

        // 8. Seed Supervisi Dosen
        $supervisis = [
            [
                'id_supervisi' => 1,
                'id_matkul' => 1,
                'id_dosen' => 1,
                'judul' => 'Uji Coba Catatan Supervisi',
                'catatan' => 'Harap periksa pengumpulan tugas praktikum',
                'status' => 'selesai',
                'tanggapan_asdos' => 'Siap Pak Dosen, tugas telah dikoreksi dan status telah selesai.',
                'created_at' => '2026-11-08 11:28:14',
                'updated_at' => '2026-11-08 11:28:14',
            ],
            [
                'id_supervisi' => 2,
                'id_matkul' => 1,
                'id_dosen' => 1,
                'judul' => 'Uji Coba Catatan Supervisi',
                'catatan' => 'Harap periksa pengumpulan tugas praktikum',
                'status' => 'selesai',
                'tanggapan_asdos' => 'Siap Pak Dosen, tugas telah dikoreksi dan status telah selesai.',
                'created_at' => '2026-11-08 11:28:50',
                'updated_at' => '2026-11-08 11:28:50',
            ],
        ];
        foreach ($supervisis as $s) {
            SupervisiDosen::updateOrCreate(['id_supervisi' => $s['id_supervisi']], $s);
        }

        // 9. Seed Log Aktivitas
        $logs = [
            ['id_log' => 1, 'id_matkul' => 1, 'id_asdos' => 2, 'aktivitas' => 'Uji Coba Sistem', 'keterangan' => 'Testing automated activity log', 'created_at' => '2026-11-08 11:28:14'],
            ['id_log' => 2, 'id_matkul' => 1, 'id_asdos' => 2, 'aktivitas' => 'Uji Coba Sistem', 'keterangan' => 'Testing automated activity log', 'created_at' => '2026-11-08 11:28:50'],
            ['id_log' => 3, 'id_matkul' => 6, 'id_asdos' => 2, 'aktivitas' => 'Perubahan Bobot Nilai', 'keterangan' => 'Memperbarui bobot penilaian: Absen 0%, Tugas 25%, Quiz 15%, UTS 25%, UAS 35%', 'created_at' => '2026-11-08 11:42:34'],
            ['id_log' => 4, 'id_matkul' => 6, 'id_asdos' => 2, 'aktivitas' => 'Publikasi Konten Forum', 'keterangan' => 'Menerbitkan Tugas: DDF MDF M', 'created_at' => '2026-11-08 11:43:59'],
            ['id_log' => 5, 'id_matkul' => 6, 'id_asdos' => 2, 'aktivitas' => 'Penilaian Tugas Forum', 'keterangan' => 'Menilai tugas \'DDF MDF M\' (ID Pengumpulan #2) dengan nilai 80', 'created_at' => '2026-11-08 11:45:40'],
        ];
        foreach ($logs as $l) {
            LogAktivitasAsdos::updateOrCreate(['id_log' => $l['id_log']], $l);
        }

        // 10. Sync PostgreSQL Sequences so new auto-increment inserts don't collide
        if (DB::connection()->getDriverName() === 'pgsql') {
            $sequences = [
                ['users', 'id_user', 'users_id_user_seq'],
                ['mata_kuliah', 'id_matkul', 'mata_kuliah_id_matkul_seq'],
                ['asisten_matkul', 'id_asisten_matkul', 'asisten_matkul_id_asisten_matkul_seq'],
                ['peserta_praktikum', 'id_peserta', 'peserta_praktikum_id_peserta_seq'],
                ['persentase_matkul', 'id_persentase', 'persentase_matkul_id_persentase_seq'],
                ['forum_postingan', 'id_post', 'forum_postingan_id_post_seq'],
                ['forum_komentar', 'id_komentar', 'forum_komentar_id_komentar_seq'],
                ['pengumpulan_tugas', 'id_pengumpulan', 'pengumpulan_tugas_id_pengumpulan_seq'],
                ['supervisi_dosen', 'id_supervisi', 'supervisi_dosen_id_supervisi_seq'],
                ['log_aktivitas_asdos', 'id_log', 'log_aktivitas_asdos_id_log_seq'],
                ['absensi', 'id_absensi', 'absensi_id_absensi_seq'],
                ['nilai_tugas', 'id_tugas', 'nilai_tugas_id_tugas_seq'],
                ['nilai_quiz', 'id_quiz', 'nilai_quiz_id_quiz_seq'],
                ['nilai_uts', 'id_uts', 'nilai_uts_id_uts_seq'],
                ['nilai_uas', 'id_uas', 'nilai_uas_id_uas_seq'],
                ['nilai_akhir_mahasiswa', 'id_nilai', 'nilai_akhir_mahasiswa_id_nilai_seq'],
            ];

            foreach ($sequences as [$table, $col, $seq]) {
                DB::statement("SELECT setval('{$seq}', COALESCE((SELECT MAX({$col}) FROM {$table}), 1))");
            }
        }
    }
}
