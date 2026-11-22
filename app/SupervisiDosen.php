<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisiDosen extends Model
{
    protected $table = 'supervisi_dosen';

    protected $primaryKey = 'id_supervisi';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_dosen',
        'judul',
        'catatan',
        'status',
        'tanggapan_asdos',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id_user');
    }
}
