<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaPraktikum extends Model
{
    protected $table = 'peserta_praktikum';

    protected $primaryKey = 'id_peserta';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
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
