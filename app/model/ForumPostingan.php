<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPostingan extends Model
{
    protected $table = 'forum_postingan';

    protected $primaryKey = 'id_post';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_author',
        'tipe_post',
        'judul',
        'isi',
        'file_lampiran',
        'deadline',
        'is_closed',
        'created_at',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'deadline' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'id_author', 'id_user');
    }

    public function komentar()
    {
        return $this->hasMany(ForumKomentar::class, 'id_post', 'id_post')->orderBy('created_at', 'asc');
    }

    public function pengumpulan()
    {
        return $this->hasMany(PengumpulanTugas::class, 'id_post', 'id_post')->orderBy('submitted_at', 'desc');
    }

    public function submissionMhs(int $idMahasiswa)
    {
        return $this->pengumpulan()->where('id_mahasiswa', $idMahasiswa)->first();
    }
}
