<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosa_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_diagnosa')->constrained('diagnosa', 'id_diagnosa')->cascadeOnDelete();
            $table->foreignId('id_jenis')->constrained('jenis_diabetes', 'id_jenis')->cascadeOnDelete();
            $table->decimal('nilai_cf_akhir', 6, 4);
            $table->decimal('persentase', 5, 2);
            $table->boolean('is_hasil_utama')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosa_detail');
    }
};
