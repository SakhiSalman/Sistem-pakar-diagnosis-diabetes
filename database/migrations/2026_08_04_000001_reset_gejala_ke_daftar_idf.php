<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mengganti daftar gejala lama dengan daftar yang dikonsolidasi dari
     * IDF (International Diabetes Federation) - idf.org/about-diabetes/what-is-diabetes.
     *
     * PERINGATAN: ini akan MENGHAPUS seluruh rule_cf & tautan diagnosa_gejala
     * yang lama, karena gejala lama diganti total (bukan ditambah). Sesuai
     * permintaan pengguna. Rule CF wajib diisi ulang lewat /admin/rule-cf
     * dan divalidasi ulang oleh dokter/pakar.
     */
    public function up(): void
    {
        DB::table('diagnosa_gejala')->delete();
        DB::table('rule_cf')->delete();
        DB::table('gejala')->delete();

        $now = now();
        DB::table('gejala')->insert([
            ['id_gejala' => 1,  'kode_gejala' => 'G01', 'nama_gejala' => 'Rasa haus berlebihan & mulut kering', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 2,  'kode_gejala' => 'G02', 'nama_gejala' => 'Sering buang air kecil', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 3,  'kode_gejala' => 'G03', 'nama_gejala' => 'Kurang energi / mudah lelah', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 4,  'kode_gejala' => 'G04', 'nama_gejala' => 'Rasa lapar yang terus-menerus', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 5,  'kode_gejala' => 'G05', 'nama_gejala' => 'Penurunan berat badan secara tiba-tiba', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 6,  'kode_gejala' => 'G06', 'nama_gejala' => 'Penglihatan kabur', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 7,  'kode_gejala' => 'G07', 'nama_gejala' => 'Mengompol (pada anak yang biasanya tidak mengompol)', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 8,  'kode_gejala' => 'G08', 'nama_gejala' => 'Mual, muntah, nyeri perut, napas cepat/bau aseton (gejala DKA/ketoasidosis)', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 9,  'kode_gejala' => 'G09', 'nama_gejala' => 'Luka yang lambat sembuh', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 10, 'kode_gejala' => 'G10', 'nama_gejala' => 'Infeksi kulit berulang', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 11, 'kode_gejala' => 'G11', 'nama_gejala' => 'Kesemutan atau mati rasa di tangan/kaki', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 12, 'kode_gejala' => 'G12', 'nama_gejala' => 'Mual', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 13, 'kode_gejala' => 'G13', 'nama_gejala' => 'Infeksi berulang, contohnya infeksi jamur', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 14, 'kode_gejala' => 'G14', 'nama_gejala' => 'Sedang hamil', 'created_at' => $now, 'updated_at' => $now],
            ['id_gejala' => 15, 'kode_gejala' => 'G15', 'nama_gejala' => 'Kegemukan / obesitas (IMT \u2265 25)', 'created_at' => $now, 'updated_at' => $now],
        ]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE gejala AUTO_INCREMENT = 16');
        }
    }

    public function down(): void
    {
        // Sengaja tidak reversible - data lama sudah dihapus permanen.
    }
};
