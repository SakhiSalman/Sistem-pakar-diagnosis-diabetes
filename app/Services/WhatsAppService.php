<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Layanan pengiriman pesan WhatsApp menggunakan gateway Fonnte.
 *
 * Kenapa Fonnte? Karena paling umum dipakai untuk skripsi/proyek kecil di
 * Indonesia: cukup satu API token, tanpa perlu approval WhatsApp Business
 * resmi dari Meta. Daftar & dapatkan token di https://fonnte.com
 *
 * Cara pakai:
 *   1. Daftar akun di fonnte.com, scan QR untuk hubungkan nomor WA pengirim.
 *   2. Salin "Device Token" dari dashboard Fonnte.
 *   3. Isi di file .env:
 *        FONNTE_TOKEN=isi_token_anda
 *
 * Jika suatu saat ingin ganti provider lain (Wablas, Watzap, WA Cloud API
 * resmi Meta, dll), cukup ubah isi method kirim() di file ini saja — bagian
 * lain aplikasi (Controller, dsb) tidak perlu diubah karena hanya memanggil
 * WhatsAppService::kirim($noHp, $pesan).
 */
class WhatsAppService
{
    protected string $apiUrl;
    protected ?string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $this->token  = config('services.fonnte.token');
    }

    /**
     * Kirim pesan WhatsApp ke satu nomor tujuan.
     *
     * @return bool true jika berhasil dikirim (atau berhasil di-log saat token belum diisi)
     */
    public function kirim(string $noHp, string $pesan): bool
    {
        $noHpFormat = self::formatNomor($noHp);

        // Token belum disetel (misal saat pengembangan lokal) -> jangan gagalkan proses,
        // cukup catat ke log supaya developer masih bisa lihat isi pesan & OTP.
        if (empty($this->token)) {
            Log::info("[WhatsAppService] FONNTE_TOKEN belum disetel. Pesan untuk {$noHpFormat}:\n{$pesan}");
            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->post($this->apiUrl, [
                'target'  => $noHpFormat,
                'message' => $pesan,
                'countryCode' => '62',
            ]);

            if (! $response->successful()) {
                Log::warning("[WhatsAppService] Gagal kirim WA ke {$noHpFormat}. HTTP {$response->status()}: {$response->body()}");
                return false;
            }

            $body = $response->json();
            if (isset($body['status']) && $body['status'] === false) {
                Log::warning("[WhatsAppService] Fonnte menolak pesan ke {$noHpFormat}: " . json_encode($body));
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("[WhatsAppService] Exception saat kirim WA ke {$noHpFormat}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim kode OTP dengan template pesan baku.
     */
    public function kirimOtp(string $noHp, string $namaPasien, string $kodeOtp): bool
    {
        $pesan = "Halo {$namaPasien},\n\n"
            . "Kode verifikasi Anda untuk Sistem Pakar Diagnosis Diabetes - Klinik ABC:\n\n"
            . "*{$kodeOtp}*\n\n"
            . "Kode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun, termasuk pihak yang mengaku dari klinik.\n\n"
            . "Abaikan pesan ini jika Anda tidak merasa melakukan pendaftaran.";

        return $this->kirim($noHp, $pesan);
    }

    /**
     * Kirim notifikasi saat hasil diagnosa pasien sudah divalidasi admin.
     */
    public function kirimNotifikasiValidasi(string $noHp, string $namaPasien, string $noRegistrasi, ?string $tanggalKunjungan, ?string $catatanAdmin): bool
    {
        $tanggal = $tanggalKunjungan ? \Illuminate\Support\Carbon::parse($tanggalKunjungan)->translatedFormat('d F Y') : '-';

        $pesan = "Halo {$namaPasien},\n\n"
            . "Data diagnosis Anda dengan nomor registrasi *{$noRegistrasi}* telah divalidasi oleh admin Klinik ABC dan diteruskan ke dokter.\n\n"
            . "Jadwal kunjungan Anda: *{$tanggal}*\n";

        if (! empty($catatanAdmin)) {
            $pesan .= "\nCatatan dari admin:\n{$catatanAdmin}\n";
        }

        $pesan .= "\nMohon datang tepat waktu sesuai jadwal di atas. Terima kasih.";

        return $this->kirim($noHp, $pesan);
    }

    /**
     * Normalisasi nomor HP Indonesia ke format 62xxxxxxxxxx (tanpa spasi/strip/plus)
     * agar sesuai format yang diminta gateway WA.
     */
    public static function formatNomor(string $noHp): string
    {
        $angka = preg_replace('/\D/', '', $noHp);

        if (str_starts_with($angka, '0')) {
            $angka = '62' . substr($angka, 1);
        } elseif (str_starts_with($angka, '8')) {
            $angka = '62' . $angka;
        }

        return $angka;
    }
}
