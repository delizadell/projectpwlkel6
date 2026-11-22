<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiUts extends Model
{
    protected $table = 'nilai_uts';

    protected $primaryKey = 'id_uts';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'nilai_uts',
    ];

    protected $casts = [
        'nilai_uts' => 'float',
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