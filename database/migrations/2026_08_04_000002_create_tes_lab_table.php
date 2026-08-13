<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tes_lab', function (Blueprint $table) {
            $table->id('id_tes_lab');
            $table->unsignedBigInteger('id_diagnosa');
            $table->boolean('sudah_tes_hba1c')->default(false);
            $table->decimal('hasil_hba1c', 4, 1)->nullable()->comment('dalam persen (%)');
            $table->boolean('sudah_tes_antibodi')->default(false);
            $table->enum('hasil_antibodi', ['positif', 'negatif'])->nullable()->comment('GAD Autoantibodies');
            $table->boolean('sudah_tes_cpeptide')->default(false);
            $table->enum('hasil_cpeptide', ['rendah', 'normal_tinggi'])->nullable();
            $table->decimal('berat_badan', 5, 1)->nullable()->comment('kg, dicatat tiap kunjungan');
            $table->date('tanggal_lahir_saat_isi')->nullable()->comment('dipakai untuk hitung umur saat kuesioner, khusus fallback saat hanya HbA1c yang dites');
            $table->timestamp('created_at')->nullable();

            $table->foreign('id_diagnosa')->references('id_diagnosa')->on('diagnosa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tes_lab');
    }
};
