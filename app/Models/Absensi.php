<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $primaryKey = 'id_absensi';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'jumlah_pertemuan',
        'jumlah_hadir',
        'nilai_absensi',
        'nilai_akhir_absensi',
    ];

    protected $casts = [
        'jumlah_pertemuan' => 'integer',
        'jumlah_hadir' => 'integer',
        'nilai_absensi' => 'float',
        'nilai_akhir_absensi' => 'float',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id_user');
    }
}