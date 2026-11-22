<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun contoh untuk pengujian. Nomor id_user sengaja dibuat tetap
     * karena seeder anggota lain merujuk ke nomor ini.
     * (1 = Dosen, 2 = Asdos, 3-5 = Mahasiswa)
     */
    public function run(): void
    {
        $users = [
            [
                'id_user' => 1,
                'username' => 'dosen1',
                'password' => Hash::make('admin'),
                'nama_lengkap' => 'Dr. Hendra, M.Kom.',
                'prodi' => null,
                'role' => 'dosen',
            ],
            [
                'id_user' => 2,
                'username' => 'asprak1',
                'password' => Hash::make('asprak'),
                'nama_lengkap' => 'Rian Hidayat',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'asisten_dosen',
            ],
            [
                'id_user' => 3,
                'username' => '230101001',
                'password' => Hash::make('mhs'),
                'nama_lengkap' => 'Budi Santoso',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'mahasiswa',
            ],
            [
                'id_user' => 4,
                'username' => '230101002',
                'password' => Hash::make('mhs'),
                'nama_lengkap' => 'Siti Rahma',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'mahasiswa',
            ],
            [
                'id_user' => 5,
                'username' => '230101003',
                'password' => Hash::make('mhs'),
                'nama_lengkap' => 'Andi Pratama',
                'prodi' => 'S1 Sistem Informasi',
                'role' => 'mahasiswa',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['id_user' => $user['id_user']], $user);
        }

        // PostgreSQL tidak menaikkan nomor urut otomatis saat id diisi manual,
        // jadi disinkronkan agar pendaftaran akun baru tidak bentrok (duplicate key).
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("SELECT setval('users_id_user_seq', COALESCE((SELECT MAX(id_user) FROM users), 1))");
        }
    }
}