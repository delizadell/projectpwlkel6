<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persentase_matkul', function (Blueprint $table) {
            $table->bigIncrements('id_persentase');
            $table->unsignedBigInteger('id_matkul')->unique();
            $table->decimal('persentase_absensi', 5, 2)->default(10.00);
            $table->decimal('persentase_tugas', 5, 2)->default(25.00);
            $table->decimal('persentase_quiz', 5, 2)->default(15.00);
            $table->decimal('persentase_uts', 5, 2)->default(25.00);
            $table->decimal('persentase_uas', 5, 2)->default(25.00);

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persentase_matkul');
    }
};
