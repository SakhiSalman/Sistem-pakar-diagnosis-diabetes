<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_verifikasi', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->after('id_otp');
        });

        // OTP sekarang dikirim ke WhatsApp, bukan email lagi.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE otp_verifikasi MODIFY email VARCHAR(100) NULL');
        }
    }

    public function down(): void
    {
        Schema::table('otp_verifikasi', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });
    }
};
