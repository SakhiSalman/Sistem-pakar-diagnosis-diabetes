<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->unique()->after('no_ktp');
        });

        // Email tidak lagi wajib karena registrasi/OTP sekarang memakai nomor WhatsApp.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE pasien MODIFY email VARCHAR(100) NULL');
        }
    }

    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pasien MODIFY email VARCHAR(100) NOT NULL DEFAULT ''");
        }
    }
};
