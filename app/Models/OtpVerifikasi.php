<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerifikasi extends Model
{
    protected $table = 'otp_verifikasi';
    protected $primaryKey = 'id_otp';
    public $timestamps = false;
    protected $fillable = ['no_hp', 'kode_otp', 'status', 'percobaan', 'created_at', 'expired_at'];

    public static function buatOtp(string $noHp): string
    {
        $kode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        self::create([
            'no_hp'      => $noHp,
            'kode_otp'   => $kode,
            'status'     => 'terkirim',
            'percobaan'  => 0,
            'created_at' => now(),
            'expired_at' => now()->addMinutes(5),
        ]);

        return $kode;
    }

    public static function verifikasi(string $noHp, string $kode): bool
    {
        $row = self::where('no_hp', $noHp)
            ->where('status', 'terkirim')
            ->orderByDesc('id_otp')
            ->first();

        if (! $row) {
            return false;
        }

        if (now()->greaterThan($row->expired_at)) {
            $row->update(['status' => 'kedaluwarsa']);
            return false;
        }

        if ($row->kode_otp !== $kode) {
            $row->update(['percobaan' => $row->percobaan + 1]);
            return false;
        }

        $row->update(['status' => 'terverifikasi']);
        return true;
    }
}
