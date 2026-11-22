<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiUas extends Model
{
    protected $table = 'nilai_uas';

    protected $primaryKey = 'id_uas';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'nilai_uas',
    ];

    protected $casts = [
        'nilai_uas' => 'float',
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