<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosa_gejala', function (Blueprint $table) {
            $table->foreignId('id_diagnosa')->constrained('diagnosa', 'id_diagnosa')->cascadeOnDelete();
            $table->foreignId('id_gejala')->constrained('gejala', 'id_gejala')->cascadeOnDelete();
            $table->primary(['id_diagnosa', 'id_gejala']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosa_gejala');
    }
};
