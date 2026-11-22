<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Postingan Forum
        Schema::create('forum_postingan', function (Blueprint $table) {
            $table->bigIncrements('id_post');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_author');
            $table->string('tipe_post', 20); // 'pengumuman', 'materi', 'tugas'
            $table->string('judul', 200);
            $table->text('isi');
            $table->string('file_lampiran', 255)->nullable();
            $table->timestamp('deadline')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_author')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 2. Komentar Forum
        Schema::create('forum_komentar', function (Blueprint $table) {
            $table->bigIncrements('id_komentar');
            $table->unsignedBigInteger('id_post');
            $table->unsignedBigInteger('id_user');
            $table->text('isi_komentar');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_post')->references('id_post')->on('forum_postingan')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 3. Pengumpulan Tugas Mahasiswa
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->bigIncrements('id_pengumpulan');
            $table->unsignedBigInteger('id_post');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->string('file_path', 255);
            $table->text('catatan_mahasiswa')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('status_submit', 20); // 'tepat_waktu', 'terlambat'
            $table->decimal('nilai', 5, 2)->nullable();
            $table->text('catatan_asprak')->nullable();

            $table->foreign('id_post')->references('id_post')->on('forum_postingan')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_post', 'id_mahasiswa'], 'uniq_submission_post_mhs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
        Schema::dropIfExists('forum_komentar');
        Schema::dropIfExists('forum_postingan');
    }
};
