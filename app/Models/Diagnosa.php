<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    protected $table = 'diagnosa';
    protected $primaryKey = 'id_diagnosa';
    public $timestamps = false;
    protected $fillable = ['id_pasien', 'id_user_validator', 'tanggal_diagnosa', 'tanggal_kunjungan', 'status', 'catatan_admin'];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien', 'id_pasien');
    }

    public function detail()
    {
        return $this->hasMany(DiagnosaDetail::class, 'id_diagnosa', 'id_diagnosa');
    }

    public function gejalaTerpilih()
    {
        return $this->belongsToMany(Gejala::class, 'diagnosa_gejala', 'id_diagnosa', 'id_gejala');
    }

    public function tesLab()
    {
        return $this->hasOne(TesLab::class, 'id_diagnosa', 'id_diagnosa');
    }
}
