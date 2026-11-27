<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asisten_matkul', function (Blueprint $table) {
            $table->increments('id_asisten_matkul');
            $table->unsignedInteger('id_matkul');
            $table->unsignedInteger('id_asisten');
            $table->string('status', 20)->default('approved'); // 'pending', 'approved', 'rejected'
            $table->timestamp('requested_at')->useCurrent();

            $table->foreign('id_matkul')->references('id_matkul')->on('mata_kuliah')->onDelete('cascade');
            $table->foreign('id_asisten')->references('id_user')->on('users')->onDelete('cascade');
            $table->unique(['id_matkul', 'id_asisten'], 'uniq_matkul_asisten');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asisten_matkul');
    }
};
