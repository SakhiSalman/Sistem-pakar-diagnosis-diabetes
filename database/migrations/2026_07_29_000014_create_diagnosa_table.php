<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosa', function (Blueprint $table) {
            $table->id('id_diagnosa');
            $table->foreignId('id_pasien')->constrained('pasien', 'id_pasien')->cascadeOnDelete();
            $table->foreignId('id_user_validator')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->dateTime('tanggal_diagnosa');
            $table->enum('status', ['menunggu_konsultasi', 'divalidasi', 'selesai'])->default('menunggu_konsultasi');
            $table->text('catatan_admin')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosa');
    }
};
