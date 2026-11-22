<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitasAsdos extends Model
{
    protected $table = 'log_aktivitas_asdos';

    protected $primaryKey = 'id_log';

    public $timestamps = false;

    protected $fillable = [
        'id_matkul',
        'id_asdos',
        'aktivitas',
        'keterangan',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'id_matkul');
    }

    public function asdos()
    {
        return $this->belongsTo(User::class, 'id_asdos', 'id_user');
    }

    public static function log(int $idMatkul, int $idAsdos, string $aktivitas, ?string $keterangan = null)
    {
        return self::create([
            'id_matkul' => $idMatkul,
            'id_asdos' => $idAsdos,
            'aktivitas' => $aktivitas,
            'keterangan' => $keterangan,
            'created_at' => now(),
        ]);
    }
}
