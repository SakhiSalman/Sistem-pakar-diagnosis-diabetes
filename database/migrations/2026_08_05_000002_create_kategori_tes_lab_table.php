<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sebelumnya daftar kategori tes lab (HbA1c, Antibodi, C-Peptide, dst) hardcode
     * di kode (RuleCfLabController::KATEGORI). Sekarang dipindah ke tabel supaya
     * label & keterangannya bisa dikelola admin lewat website tanpa ubah kode.
     *
     * PENTING (keterbatasan yang jujur perlu diketahui): kolom `kode` di sini
     * TETAP terikat ke logika perhitungan CF di app/Services/DiagnosisEngine.php
     * (mis. kode 'hba1c_diabetes' dicek langsung di kode PHP). Admin BISA
     * mengubah label/keterangan/urutan, dan BISA menonaktifkan (bukan hapus
     * permanen) kategori bawaan sistem supaya tidak sengaja merusak
     * perhitungan. Kategori baru yang dibuat admin lewat website hanya akan
     * tampil sebagai referensi/catatan -- tidak otomatis ikut dihitung ke CF
     * kecuali developer menambahkan logikanya di DiagnosisEngine.
     */
    public function up(): void
    {
        Schema::create('kategori_tes_lab', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 40)->unique();
            $table->string('label', 150);
            $table->text('keterangan')->nullable();
            $table->boolean('bawaan_sistem')->default(false)->comment('true = terhubung ke logika DiagnosisEngine, tidak boleh dihapus permanen');
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['kode' => 'hba1c_prediabetes',      'label' => 'HbA1c 5,7% – 6,4% (Prediabetes)', 'urutan' => 1],
            ['kode' => 'hba1c_diabetes',         'label' => 'HbA1c ≥ 6,5% (Diabetes)', 'urutan' => 2],
            ['kode' => 'antibodi_positif',       'label' => 'Tes Antibodi GAD: Positif', 'urutan' => 3],
            ['kode' => 'antibodi_negatif',       'label' => 'Tes Antibodi GAD: Negatif', 'urutan' => 4],
            ['kode' => 'cpeptide_rendah',        'label' => 'Tes C-Peptide: Rendah', 'urutan' => 5],
            ['kode' => 'cpeptide_normal_tinggi', 'label' => 'Tes C-Peptide: Normal / Tinggi', 'urutan' => 6],
            ['kode' => 'ttgo_positif',           'label' => 'TTGO: Memenuhi kriteria diabetes gestasional (salah satu ambang terpenuhi)', 'urutan' => 7,
                'keterangan' => 'Skrining diabetes gestasional dengan TTGO glukosa 75 gram. Terpenuhi bila salah satu: glukosa puasa \u2265 92 mg/dL, glukosa 1 jam \u2265 180 mg/dL, atau glukosa 2 jam \u2265 153 mg/dL.'],
            ['kode' => 'usia_anak_remaja',       'label' => 'Fallback usia: 6\u201318 tahun (anak/remaja)', 'urutan' => 8,
                'keterangan' => 'Hanya dipakai jika pasien belum tes Antibodi GAD maupun C-Peptide.'],
            ['kode' => 'usia_dewasa_40plus',     'label' => 'Fallback usia: \u2265 40 tahun', 'urutan' => 9,
                'keterangan' => 'Hanya dipakai jika pasien belum tes Antibodi GAD maupun C-Peptide.'],
            ['kode' => 'obesitas_usia_muda',     'label' => 'Fallback: gejala obesitas dipilih pada usia < 40 tahun', 'urutan' => 10,
                'keterangan' => 'Hanya dipakai jika pasien belum tes Antibodi GAD maupun C-Peptide.'],
        ];

        foreach ($rows as $r) {
            DB::table('kategori_tes_lab')->insert([
                'kode'          => $r['kode'],
                'label'         => $r['label'],
                'keterangan'    => $r['keterangan'] ?? null,
                'bawaan_sistem' => true,
                'aktif'         => true,
                'urutan'        => $r['urutan'],
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        // Tambahkan rule CF default untuk ttgo_positif -> Gestasional (dummy, wajib divalidasi pakar)
        $idGestasional = DB::table('jenis_diabetes')->where('kode_jenis', 'P03')->value('id_jenis');
        if ($idGestasional) {
            DB::table('rule_cf_lab')->insert([
                'kategori'   => 'ttgo_positif',
                'id_jenis'   => $idGestasional,
                'nilai_mb'   => 0.85,
                'nilai_md'   => 0.00,
                'nilai_cf'   => 0.85,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_tes_lab');
    }
};
