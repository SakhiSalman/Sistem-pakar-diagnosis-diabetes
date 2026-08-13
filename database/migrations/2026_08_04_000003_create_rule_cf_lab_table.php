<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rule_cf_lab', function (Blueprint $table) {
            $table->id('id_rule_lab');
            $table->string('kategori', 40)->comment('kode faktor, lihat App\\Services\\DiagnosisEngine::KATEGORI_LAB');
            $table->unsignedBigInteger('id_jenis');
            $table->decimal('nilai_mb', 3, 2)->default(0);
            $table->decimal('nilai_md', 3, 2)->default(0);
            $table->decimal('nilai_cf', 4, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_jenis')->references('id_jenis')->on('jenis_diabetes')->onDelete('cascade');
            $table->unique(['kategori', 'id_jenis']);
        });

        // Nilai default (dummy) - WAJIB divalidasi ulang oleh dokter/pakar,
        // sama seperti rule_cf gejala. Diisi lewat halaman /admin/rule-cf-lab.
        $now = now();
        $jenisMap = DB::table('jenis_diabetes')->pluck('id_jenis', 'kode_jenis');
        // P01 = Tipe 1, P02 = Tipe 2, P03 = Gestasional
        $tipe1 = $jenisMap['P01'] ?? 1;
        $tipe2 = $jenisMap['P02'] ?? 2;
        $gestasional = $jenisMap['P03'] ?? 3;

        $rows = [
            // HbA1c: <5.7% normal (tidak menambah CF ke jenis manapun, jadi tidak perlu baris)
            ['kategori' => 'hba1c_prediabetes',   'id_jenis' => $tipe2, 'nilai_mb' => 0.30, 'nilai_md' => 0.00],
            ['kategori' => 'hba1c_diabetes',      'id_jenis' => $tipe1, 'nilai_mb' => 0.30, 'nilai_md' => 0.00],
            ['kategori' => 'hba1c_diabetes',      'id_jenis' => $tipe2, 'nilai_mb' => 0.30, 'nilai_md' => 0.00],
            ['kategori' => 'hba1c_diabetes',      'id_jenis' => $gestasional, 'nilai_mb' => 0.20, 'nilai_md' => 0.00],

            // Tes Antibodi GAD - indikator kuat autoimun (khas Tipe 1)
            ['kategori' => 'antibodi_positif',    'id_jenis' => $tipe1, 'nilai_mb' => 0.90, 'nilai_md' => 0.00],
            ['kategori' => 'antibodi_negatif',    'id_jenis' => $tipe2, 'nilai_mb' => 0.50, 'nilai_md' => 0.00],

            // Tes C-Peptide - rendah = produksi insulin sangat kurang (khas Tipe 1),
            // normal/tinggi = resistensi insulin (khas Tipe 2)
            ['kategori' => 'cpeptide_rendah',        'id_jenis' => $tipe1, 'nilai_mb' => 0.80, 'nilai_md' => 0.00],
            ['kategori' => 'cpeptide_normal_tinggi', 'id_jenis' => $tipe2, 'nilai_mb' => 0.70, 'nilai_md' => 0.00],

            // Fallback usia (HANYA dipakai jika pasien belum tes antibodi/C-Peptide)
            ['kategori' => 'usia_anak_remaja',       'id_jenis' => $tipe1, 'nilai_mb' => 0.40, 'nilai_md' => 0.00],
            ['kategori' => 'usia_dewasa_40plus',     'id_jenis' => $tipe2, 'nilai_mb' => 0.40, 'nilai_md' => 0.00],
            // Anak/remaja + gejala obesitas dipilih -> tetap bisa condong ke Tipe 2
            ['kategori' => 'obesitas_usia_muda',     'id_jenis' => $tipe2, 'nilai_mb' => 0.50, 'nilai_md' => 0.00],
        ];

        foreach ($rows as $r) {
            $r['nilai_cf'] = round($r['nilai_mb'] - $r['nilai_md'], 2);
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
            DB::table('rule_cf_lab')->insert($r);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rule_cf_lab');
    }
};
