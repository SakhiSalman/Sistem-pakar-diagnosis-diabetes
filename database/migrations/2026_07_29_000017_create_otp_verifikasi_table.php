<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifikasi', function (Blueprint $table) {
            $table->id('id_otp');
            $table->string('email', 100);
            $table->string('kode_otp', 6);
            $table->enum('status', ['terkirim', 'terverifikasi', 'kedaluwarsa'])->default('terkirim');
            $table->integer('percobaan')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifikasi');
    }
};
