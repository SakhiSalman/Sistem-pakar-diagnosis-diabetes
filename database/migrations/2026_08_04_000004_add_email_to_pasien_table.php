<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kolom `email` sudah ada sejak migration awal pasien (dan sudah dibuat
     * nullable oleh migration no_hp sebelumnya). Migration ini hanya
     * menambahkan UNIQUE INDEX supaya email bisa dipakai sebagai identitas
     * unik untuk fitur lupa password.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Bersihkan string kosong peninggalan data lama.
            DB::statement("UPDATE pasien SET email = NULL WHERE email = ''");

            // Bersihkan EMAIL DUPLIKAT: kalau ada beberapa pasien memakai email
            // yang sama persis (data dummy lama), pertahankan hanya pada baris
            // id_pasien PALING KECIL, sisanya di-NULL-kan supaya tidak bentrok
            // saat dibuatkan UNIQUE INDEX.
            $duplikat = DB::table('pasien')
                ->select('email')
                ->whereNotNull('email')
                ->groupBy('email')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('email');

            foreach ($duplikat as $email) {
                $idPertama = DB::table('pasien')->where('email', $email)->min('id_pasien');
                DB::table('pasien')->where('email', $email)->where('id_pasien', '!=', $idPertama)->update(['email' => null]);
            }

            DB::statement('ALTER TABLE pasien ADD UNIQUE INDEX pasien_email_unique (email)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE pasien DROP INDEX pasien_email_unique');
        }
    }
};
