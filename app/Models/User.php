<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'prodi',
        'role',
        'created_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Mengambil role dasar yang tersimpan di database.
     */
    public function getBaseRole(): string
    {
        $raw = $this->attributes['role'] ?? 'mahasiswa';
        if ($raw === 'dosen') {
            return 'dosen';
        }

        if (str_contains($raw, 'mahasiswa')) {
            return 'mahasiswa';
        }

        if (str_contains($raw, 'asisten_dosen')) {
            return 'asisten_dosen';
        }

        return 'mahasiswa';
    }

    /**
     * Mengambil role aktif saat ini (memperhitungkan session active_role untuk non-dosen).
     */
    public function getActiveRole(): string
    {
        if ($this->getBaseRole() === 'dosen') {
            return 'dosen';
        }

        if (Auth::check() && Auth::id() === $this->id_user && session()->has('active_role')) {
            $activeRole = session('active_role');
            if (in_array($activeRole, ['mahasiswa', 'asisten_dosen'], true)) {
                return $activeRole;
            }
        }

        return $this->getBaseRole();
    }

    /**
     * Dynamic attribute accessor for $user->role.
     * Mengembalikan active_role untuk user yang sedang login agar kompatibel dengan seluruh controller & view.
     */
    public function getRoleAttribute($value): string
    {
        if (Auth::check() && Auth::id() === $this->id_user && session()->has('active_role')) {
            $activeRole = session('active_role');
            if ($value !== 'dosen' && in_array($activeRole, ['mahasiswa', 'asisten_dosen'], true)) {
                return $activeRole;
            }
        }

        if ($value === 'dosen') {
            return 'dosen';
        }

        if (str_contains((string) $value, 'mahasiswa')) {
            return 'mahasiswa';
        }

        if (str_contains((string) $value, 'asisten_dosen')) {
            return 'asisten_dosen';
        }

        return $value ?? 'mahasiswa';
    }

    /**
     * Mengambil nilai mentah kolom role langsung dari database.
     */
    public function getRawRole(): string
    {
        return $this->attributes['role'] ?? 'mahasiswa';
    }

    /**
     * Mengambil daftar peran dalam bentuk array PHP.
     */
    public function getRolesList(): array
    {
        $raw = $this->getRawRole();
        if ($raw === 'dosen') {
            return ['dosen'];
        }

        if (str_contains($raw, 'mahasiswa') && str_contains($raw, 'asisten_dosen')) {
            return ['mahasiswa', 'asisten_dosen'];
        }

        if (str_contains($raw, 'asisten_dosen')) {
            return ['asisten_dosen'];
        }

        return ['mahasiswa'];
    }

    /**
     * Memeriksa apakah user dapat menggunakan fitur switch role (semua non-dosen).
     */
    public function canSwitchRole(): bool
    {
        return $this->getBaseRole() !== 'dosen';
    }

    /**
     * Memeriksa apakah user benar-benar memiliki status ganda di data akademik
     * (terdaftar sebagai asisten di asisten_matkul dan sebagai mahasiswa praktikan di peserta_praktikum,
     * atau memiliki role ganda '(mahasiswa, asisten_dosen)' di database).
     */
    public function hasDualRole(): bool
    {
        if (! $this->canSwitchRole()) {
            return false;
        }

        $raw = $this->getRawRole();
        if (str_contains($raw, 'mahasiswa') && str_contains($raw, 'asisten_dosen')) {
            return true;
        }

        $hasAsdosRecord = $this->kelasAsdos()->exists() || $this->getBaseRole() === 'asisten_dosen';
        $hasMhsRecord = $this->kelasPraktikan()->exists() || $this->getBaseRole() === 'mahasiswa';

        return $hasAsdosRecord && $hasMhsRecord;
    }

    public function isDosen(): bool
    {
        return $this->getActiveRole() === 'dosen';
    }

    public function isAsdos(): bool
    {
        return $this->getActiveRole() === 'asisten_dosen';
    }

    public function isMahasiswa(): bool
    {
        return $this->getActiveRole() === 'mahasiswa';
    }

    public function getDashboardRoute(): string
    {
        return match ($this->getActiveRole()) {
            'dosen' => 'dosen.dashboard',
            'asisten_dosen' => 'asprak.dashboard',
            'mahasiswa' => 'mahasiswa.dashboard',
            default => 'login',
        };
    }

    public function mataKuliahDosen()
    {
        return $this->hasMany(MataKuliah::class, 'id_dosen', 'id_user');
    }

    public function kelasAsdos()
    {
        return $this->belongsToMany(MataKuliah::class, 'asisten_matkul', 'id_asisten', 'id_matkul')
            ->withPivot('id_asisten_matkul', 'status', 'requested_at');
    }

    public function kelasPraktikan()
    {
        return $this->belongsToMany(MataKuliah::class, 'peserta_praktikum', 'id_mahasiswa', 'id_matkul')
            ->withPivot('id_peserta', 'joined_at');
    }
}
