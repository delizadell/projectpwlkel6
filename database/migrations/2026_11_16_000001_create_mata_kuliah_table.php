<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->increments('id_matkul');
            $table->string('kode_matkul', 20)->unique();
            $table->string('nama_matkul', 100);
            $table->smallInteger('sks');
            $table->string('kode_gabung', 50)->unique();
            $table->unsignedInteger('id_dosen');
            $table->text('deskripsi')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_dosen')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};
