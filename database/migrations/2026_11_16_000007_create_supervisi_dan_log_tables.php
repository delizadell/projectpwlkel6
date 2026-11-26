<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Supervisi Dosen
        Schema::create('supervisi_dosen', function (Blueprint $table) {
            $table->bigIncrements('id_supervisi');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_dosen');
            $table->string('judul', 150);
            $table->text('catatan');
            $table->string('status', 20)->default('terkirim'); // 'terkirim', 'dibaca', 'selesai'
            $table->text('tanggapan_asdos')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_dosen')->references('id_user')->on('users')->onDelete('cascade');
        });

        // 2. Log Aktivitas Asdos
        Schema::create('log_aktivitas_asdos', function (Blueprint $table) {
            $table->bigIncrements('id_log');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_asdos');
            $table->string('aktivitas', 255);
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_asdos')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas_asdos');
        Schema::dropIfExists('supervisi_dosen');
    }
};
