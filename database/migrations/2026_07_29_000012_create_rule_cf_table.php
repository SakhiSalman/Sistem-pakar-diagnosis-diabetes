<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_cf', function (Blueprint $table) {
            $table->id('id_rule');
            $table->foreignId('id_gejala')->constrained('gejala', 'id_gejala')->cascadeOnDelete();
            $table->foreignId('id_jenis')->constrained('jenis_diabetes', 'id_jenis')->cascadeOnDelete();
            $table->decimal('nilai_mb', 3, 2);
            $table->decimal('nilai_md', 3, 2);
            $table->decimal('nilai_cf', 3, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_cf');
    }
};
