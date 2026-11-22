<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiTugas extends Model
{
    protected $table = 'nilai_tugas';

    protected $primaryKey = 'id_tugas';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'rata_rata_tugas',
    ];

    protected $casts = [
        'rata_rata_tugas' => 'float',
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