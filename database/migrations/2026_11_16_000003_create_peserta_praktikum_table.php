<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_praktikum', function (Blueprint $table) {
            $table->bigIncrements('id_peserta');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->timestamp('joined_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_peserta_kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_praktikum');
    }
};
