<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Absensi
        Schema::create('absensi', function (Blueprint $table) {
            $table->bigIncrements('id_absensi');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->integer('jumlah_pertemuan')->default(16);
            $table->integer('jumlah_hadir')->default(0);
            $table->decimal('nilai_absensi', 5, 2)->default(0.00);
            $table->decimal('nilai_akhir_absensi', 5, 2)->default(0.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_absensi_mhs');
        });

        // 2. Nilai Tugas
        Schema::create('nilai_tugas', function (Blueprint $table) {
            $table->bigIncrements('id_tugas');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('rata_rata_tugas', 5, 2)->default(0.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_tugas_eval_mhs');
        });

        // 3. Nilai Quiz
        Schema::create('nilai_quiz', function (Blueprint $table) {
            $table->bigIncrements('id_quiz');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('rata_rata_quiz', 5, 2)->default(0.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_quiz_mhs');
        });

        // 4. Nilai UTS
        Schema::create('nilai_uts', function (Blueprint $table) {
            $table->bigIncrements('id_uts');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('nilai_uts', 5, 2)->default(0.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_uts_mhs');
        });

        // 5. Nilai UAS
        Schema::create('nilai_uas', function (Blueprint $table) {
            $table->bigIncrements('id_uas');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('nilai_uas', 5, 2)->default(0.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_uas_mhs');
        });

        // 6. Nilai Akhir Total Mahasiswa
        Schema::create('nilai_akhir_mahasiswa', function (Blueprint $table) {
            $table->bigIncrements('id_nilai');
            $table->unsignedBigInteger('id_matkul');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->decimal('nilai_akhir_total', 5, 2)->default(0.00);
            $table->string('huruf_matkul', 2);
            $table->decimal('bobot_matkul', 3, 2);
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_mahasiswa')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_mahasiswa'], 'uniq_nilai_akhir_mhs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_akhir_mahasiswa');
        Schema::dropIfExists('nilai_uas');
        Schema::dropIfExists('nilai_uts');
        Schema::dropIfExists('nilai_quiz');
        Schema::dropIfExists('nilai_tugas');
        Schema::dropIfExists('absensi');
    }
};
