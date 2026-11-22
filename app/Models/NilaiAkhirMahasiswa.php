<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiAkhirMahasiswa extends Model
{
    protected $table = 'nilai_akhir_mahasiswa';

    protected $primaryKey = 'id_nilai';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'nilai_akhir_total',
        'huruf_matkul',
        'bobot_matkul',
        'updated_at',
    ];

    protected $casts = [
        'nilai_akhir_total' => 'float',
        'bobot_matkul' => 'float',
        'updated_at' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id_user');
    }

    /**
     * Konversi Nilai Angka ke Huruf Mutu
     */
    public static function konversiHuruf(float $nilai): string
    {
        if ($nilai >= 76) {
            return 'A';
        }
        if ($nilai >= 71) {
            return 'B+';
        }
        if ($nilai >= 66) {
            return 'B';
        }
        if ($nilai >= 61) {
            return 'C+';
        }
        if ($nilai >= 56) {
            return 'C';
        }
        if ($nilai >= 45) {
            return 'D';
        }

        return 'E';
    }

    /**
     * Konversi Nilai Angka ke Angka Bobot Mutu (0.0 - 4.0)
     */
    public static function konversiBobot(float $nilai): float
    {
        if ($nilai >= 76) {
            return 4.0;
        }
        if ($nilai >= 71) {
            return 3.5;
        }
        if ($nilai >= 66) {
            return 3.0;
        }
        if ($nilai >= 61) {
            return 2.5;
        }
        if ($nilai >= 56) {
            return 2.0;
        }
        if ($nilai >= 45) {
            return 1.0;
        }

        return 0.0;
    }
}