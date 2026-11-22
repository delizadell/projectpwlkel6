<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiQuiz extends Model
{
    protected $table = 'nilai_quiz';

    protected $primaryKey = 'id_quiz';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_mahasiswa',
        'rata_rata_quiz',
    ];

    protected $casts = [
        'rata_rata_quiz' => 'float',
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