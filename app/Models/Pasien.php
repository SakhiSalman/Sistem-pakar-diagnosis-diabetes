<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    public $timestamps = false;
    protected $fillable = ['no_registrasi', 'no_ktp', 'no_hp', 'nama', 'email', 'password', 'jenis_kelamin', 'tanggal_lahir', 'created_at'];
    protected $hidden = ['password'];

    public static function generateNoRegistrasi(): string
    {
        return 'REG-' . random_int(100000, 999999);
    }
}
