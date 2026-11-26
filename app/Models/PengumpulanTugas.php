<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected $primaryKey = 'id_pengumpulan';

    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_mahasiswa',
        'file_path',
        'catatan_mahasiswa',
        'submitted_at',
        'status_submit',
        'nilai',
        'catatan_asprak',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'nilai' => 'float',
    ];

    public function postingan()
    {
        return $this->belongsTo(ForumPostingan::class, 'id_post', 'id_post');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id_user');
    }
}
