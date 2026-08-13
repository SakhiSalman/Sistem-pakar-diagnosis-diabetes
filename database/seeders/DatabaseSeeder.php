<?php

namespace Database\Seeders;

use App\Models\Gejala;
use App\Models\JenisDiabetes;
use App\Models\RuleCf;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun staf dibuat mandiri lewat halaman /admin/register, tidak ada akun dummy lagi.

        // ===== Gejala =====
        $gejalaData = [
            'G01' => 'Sering buang air kecil (poliuria)',
            'G02' => 'Sering merasa haus (polidipsia)',
            'G03' => 'Sering merasa lapar berlebihan (polifagia)',
            'G04' => 'Penurunan berat badan tanpa sebab jelas',
            'G05' => 'Mudah lelah / lemas',
            'G06' => 'Penglihatan kabur',
            'G07' => 'Luka yang lambat sembuh',
            'G08' => 'Kesemutan pada tangan atau kaki',
            'G09' => 'Kulit kering dan gatal',
            'G10' => 'Riwayat diabetes dalam keluarga',
            'G11' => 'Kelebihan berat badan / obesitas',
            'G12' => 'Sedang hamil (trimester 2 atau 3)',
        ];
        foreach ($gejalaData as $kode => $nama) {
            Gejala::create(['kode_gejala' => $kode, 'nama_gejala' => $nama]);
        }

        // ===== Jenis Diabetes =====
        JenisDiabetes::create([
            'kode_jenis' => 'P01', 'nama_jenis' => 'Diabetes Tipe 1',
            'deskripsi' => 'Disebabkan kerusakan sel beta pankreas akibat reaksi autoimun.',
            'rekomendasi' => 'Segera konsultasikan ke dokter untuk pemeriksaan gula darah dan kemungkinan terapi insulin.',
        ]);
        JenisDiabetes::create([
            'kode_jenis' => 'P02', 'nama_jenis' => 'Diabetes Tipe 2',
            'deskripsi' => 'Disebabkan resistensi insulin, berkaitan dengan pola hidup, usia, dan obesitas.',
            'rekomendasi' => 'Disarankan pemeriksaan gula darah puasa dan evaluasi pola hidup bersama dokter.',
        ]);
        JenisDiabetes::create([
            'kode_jenis' => 'P03', 'nama_jenis' => 'Diabetes Gestasional',
            'deskripsi' => 'Terjadi pada masa kehamilan akibat perubahan hormon.',
            'rekomendasi' => 'Disarankan pemeriksaan toleransi glukosa (TTGO) bersama dokter kandungan/umum.',
        ]);

        // ===== Rule Forward Chaining + CF (DATA DUMMY, wajib diganti hasil wawancara pakar) =====
        $ruleDummy = [
            'G01' => ['P01' => 0.70, 'P02' => 0.60, 'P03' => 0.50],
            'G02' => ['P01' => 0.70, 'P02' => 0.60, 'P03' => 0.40],
            'G03' => ['P01' => 0.50, 'P02' => 0.50, 'P03' => 0.20],
            'G04' => ['P01' => 0.80, 'P02' => 0.30, 'P03' => 0.10],
            'G05' => ['P01' => 0.40, 'P02' => 0.50, 'P03' => 0.40],
            'G06' => ['P01' => 0.30, 'P02' => 0.60, 'P03' => 0.20],
            'G07' => ['P01' => 0.20, 'P02' => 0.70, 'P03' => 0.10],
            'G08' => ['P01' => 0.20, 'P02' => 0.60, 'P03' => 0.10],
            'G09' => ['P01' => 0.30, 'P02' => 0.40, 'P03' => 0.10],
            'G10' => ['P01' => 0.40, 'P02' => 0.70, 'P03' => 0.50],
            'G11' => ['P01' => 0.10, 'P02' => 0.80, 'P03' => 0.60],
            'G12' => ['P01' => 0.00, 'P02' => 0.10, 'P03' => 0.90],
        ];

        $gejalaMap = Gejala::pluck('id_gejala', 'kode_gejala');
        $jenisMap  = JenisDiabetes::pluck('id_jenis', 'kode_jenis');

        foreach ($ruleDummy as $kodeGejala => $jenisList) {
            foreach ($jenisList as $kodeJenis => $mb) {
                RuleCf::create([
                    'id_gejala' => $gejalaMap[$kodeGejala],
                    'id_jenis'  => $jenisMap[$kodeJenis],
                    'nilai_mb'  => $mb,
                    'nilai_md'  => 0.00,
                    'nilai_cf'  => $mb,
                ]);
            }
        }
    }
}
