<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $primaryKey = 'id_matkul';

    public $timestamps = false;

    protected $fillable = [
        'kode_matkul',
        'nama_matkul',
        'sks',
        'kode_gabung',
        'id_dosen',
        'deskripsi',
        'created_at',
    ];

    protected $casts = [
        'sks' => 'integer',
        'created_at' => 'datetime',
    ];

    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen', 'id_user');
    }

    public function asistenMatkul()
    {
        return $this->hasMany(AsistenMatkul::class, 'id_matkul', 'id_matkul');
    }

    public function asistenApproved()
    {
        return $this->belongsToMany(User::class, 'asisten_matkul', 'id_matkul', 'id_asisten')
            ->wherePivot('status', 'approved')
            ->withPivot('id_asisten_matkul', 'status', 'requested_at');
    }

    public function peserta()
    {
        return $this->belongsToMany(User::class, 'peserta_praktikum', 'id_matkul', 'id_mahasiswa')
            ->withPivot('id_peserta', 'joined_at');
    }

    public function persentase()
    {
        return $this->hasOne(PersentaseMatkul::class, 'id_matkul', 'id_matkul');
    }

    public function postingan()
    {
        return $this->hasMany(ForumPostingan::class, 'id_matkul', 'id_matkul')->orderBy('created_at', 'desc');
    }

    public function supervisi()
    {
        return $this->hasMany(SupervisiDosen::class, 'id_matkul', 'id_matkul')->orderBy('created_at', 'desc');
    }

    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitasAsdos::class, 'id_matkul', 'id_matkul')->orderBy('created_at', 'desc');
    }

    public function nilaiAkhir()
    {
        return $this->hasMany(NilaiAkhirMahasiswa::class, 'id_matkul', 'id_matkul');
    }
}
