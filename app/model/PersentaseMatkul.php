<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersentaseMatkul extends Model
{
    protected $table = 'persentase_matkul';

    protected $primaryKey = 'id_persentase';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'persentase_absensi',
        'persentase_tugas',
        'persentase_quiz',
        'persentase_uts',
        'persentase_uas',
    ];

    protected $casts = [
        'persentase_absensi' => 'float',
        'persentase_tugas' => 'float',
        'persentase_quiz' => 'float',
        'persentase_uts' => 'float',
        'persentase_uas' => 'float',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }
}
