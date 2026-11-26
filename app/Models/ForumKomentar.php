<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    protected $table = 'forum_komentar';

    protected $primaryKey = 'id_komentar';

    public $timestamps = false;

    protected $fillable = [
        'id_post',
        'id_user',
        'isi_komentar',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function postingan()
    {
        return $this->belongsTo(ForumPostingan::class, 'id_post', 'id_post');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
