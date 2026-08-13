<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gejala', function (Blueprint $table) {
            $table->id('id_gejala');
            $table->string('kode_gejala', 10)->unique();
            $table->string('nama_gejala', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gejala');
    }
};
