<?php

namespace App\Services;

/**
 * DiagnosisEngine
 * 1. Forward Chaining: cocokkan gejala terpilih (fakta) dengan rule yang ada.
 *    Hanya rule yang gejalanya ada di fakta yang "menyala".
 * 2. Certainty Factor: gabungkan nilai CF dari rule yang menyala untuk
 *    hipotesis (jenis diabetes) yang sama.
 *
 * Sejak update ini, CF dari GEJALA digabung dengan CF dari HASIL TES LAB
 * (HbA1c / Antibodi GAD / C-Peptide) dan faktor usia (fallback jika pasien
 * belum tes antibodi/C-Peptide) memakai rumus kombinasi CF yang sama:
 *   CF_combine(CF1, CF2) = CF1 + CF2 * (1 - CF1)
 * diterapkan berurutan untuk semua CF yang "menyala" pada satu jenis diabetes,
 * baik itu asalnya dari gejala maupun dari faktor lab/usia. Forward chaining
 * tetap berperan sebagai penyaring: hanya faktor yang benar-benar terjadi pada
 * pasien (gejala dipilih / hasil lab tertentu / kategori usia tertentu) yang
 * ikut dihitung.
 */
class DiagnosisEngine
{
    /**
     * @param array $ruleMap     [id_gejala => [id_jenis => cf]] dari RuleCf::getRuleMap()
     * @param array $gejalaTerpilih  id_gejala yang dicentang pasien
     * @param array $jenisIds    semua id_jenis yang mungkin
     * @param array $faktorTambahan [id_jenis => [cf1, cf2, ...]] CF dari luar gejala
     *                              (mis. hasil lab, faktor usia). Opsional.
     */
    public function hitung(array $ruleMap, array $gejalaTerpilih, array $jenisIds, array $faktorTambahan = []): array
    {
        $hasil = [];

        foreach ($jenisIds as $idJenis) {
            $rulesAktif = [];

            foreach ($gejalaTerpilih as $idGejala) {
                if (isset($ruleMap[$idGejala][$idJenis])) {
                    $rulesAktif[] = $ruleMap[$idGejala][$idJenis];
                }
            }

            foreach (($faktorTambahan[$idJenis] ?? []) as $cfLuar) {
                $rulesAktif[] = $cfLuar;
            }

            if (empty($rulesAktif)) {
                $hasil[$idJenis] = 0.0;
                continue;
            }

            $cfCombine = 0.0;
            foreach ($rulesAktif as $cf) {
                $cfCombine = $cfCombine + $cf * (1 - $cfCombine);
            }

            $hasil[$idJenis] = round($cfCombine, 4);
        }

        arsort($hasil);
        return $hasil;
    }

    /**
     * Bangun array faktor tambahan (CF dari lab + usia) untuk digabung ke hitung().
     *
     * @param array $ruleCfLabMap   [kategori => [id_jenis => cf]] dari RuleCfLab::getRuleMap()
     * @param array $inputTesLab    hasil form tes lab, lihat KuesionerController::simpanTesLab()
     * @param bool  $gejalaObesitasDipilih  apakah pasien mencentang gejala "kegemukan/obesitas"
     * @return array [id_jenis => [cf, ...]]
     */
    public function faktorDariLabDanUsia(array $ruleCfLabMap, array $inputTesLab, bool $gejalaObesitasDipilih): array
    {
        $faktor = [];
        $tambah = function (string $kategori) use (&$faktor, $ruleCfLabMap) {
            foreach (($ruleCfLabMap[$kategori] ?? []) as $idJenis => $cf) {
                $faktor[$idJenis][] = $cf;
            }
        };

        // --- HbA1c ---
        if (! empty($inputTesLab['sudah_tes_hba1c']) && isset($inputTesLab['hasil_hba1c'])) {
            $nilai = (float) $inputTesLab['hasil_hba1c'];
            if ($nilai >= 6.5) {
                $tambah('hba1c_diabetes');
            } elseif ($nilai >= 5.7) {
                $tambah('hba1c_prediabetes');
            }
            // < 5.7% (normal) -> tidak menambah CF ke jenis manapun.
        }

        // --- Antibodi GAD ---
        $sudahAntibodiAtauCpeptide = ! empty($inputTesLab['sudah_tes_antibodi']) || ! empty($inputTesLab['sudah_tes_cpeptide']);

        if (! empty($inputTesLab['sudah_tes_antibodi'])) {
            if (($inputTesLab['hasil_antibodi'] ?? null) === 'positif') {
                $tambah('antibodi_positif');
            } elseif (($inputTesLab['hasil_antibodi'] ?? null) === 'negatif') {
                $tambah('antibodi_negatif');
            }
        }

        // --- C-Peptide ---
        if (! empty($inputTesLab['sudah_tes_cpeptide'])) {
            if (($inputTesLab['hasil_cpeptide'] ?? null) === 'rendah') {
                $tambah('cpeptide_rendah');
            } elseif (($inputTesLab['hasil_cpeptide'] ?? null) === 'normal_tinggi') {
                $tambah('cpeptide_normal_tinggi');
            }
        }

        // --- TTGO (khusus skrining diabetes gestasional, glukosa 75 gram) ---
        if (! empty($inputTesLab['sudah_tes_ttgo'])) {
            $puasa = isset($inputTesLab['ttgo_puasa']) ? (float) $inputTesLab['ttgo_puasa'] : null;
            $jam1  = isset($inputTesLab['ttgo_1jam']) ? (float) $inputTesLab['ttgo_1jam'] : null;
            $jam2  = isset($inputTesLab['ttgo_2jam']) ? (float) $inputTesLab['ttgo_2jam'] : null;

            $memenuhiKriteria = ($puasa !== null && $puasa >= 92)
                || ($jam1 !== null && $jam1 >= 180)
                || ($jam2 !== null && $jam2 >= 153);

            if ($memenuhiKriteria) {
                $tambah('ttgo_positif');
            }
        }

        // --- Fallback usia: HANYA jika pasien belum tes antibodi maupun C-Peptide ---
        if (! $sudahAntibodiAtauCpeptide && ! empty($inputTesLab['tanggal_lahir'])) {
            $umur = \Carbon\Carbon::parse($inputTesLab['tanggal_lahir'])->age;

            if ($umur >= 6 && $umur <= 18) {
                $tambah('usia_anak_remaja');
                if ($gejalaObesitasDipilih) {
                    $tambah('obesitas_usia_muda');
                }
            } elseif ($umur >= 40) {
                $tambah('usia_dewasa_40plus');
            } elseif ($gejalaObesitasDipilih) {
                // 19-39 tahun: usia tidak dipakai sebagai faktor (di luar kategori
                // yang diminta), tapi obesitas pada usia berapa pun tetap dihitung.
                $tambah('obesitas_usia_muda');
            }
        }

        return $faktor;
    }
}
