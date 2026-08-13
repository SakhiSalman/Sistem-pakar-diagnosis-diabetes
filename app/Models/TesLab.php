<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TesLab extends Model
{
    protected $table = 'tes_lab';
    protected $primaryKey = 'id_tes_lab';
    public $timestamps = false;

    protected $fillable = [
        'id_diagnosa',
        'sudah_tes_hba1c', 'hasil_hba1c',
        'sudah_tes_antibodi', 'hasil_antibodi',
        'sudah_tes_cpeptide', 'hasil_cpeptide',
        'sudah_tes_ttgo', 'ttgo_puasa', 'ttgo_1jam', 'ttgo_2jam',
        'berat_badan', 'tanggal_lahir_saat_isi',
        'created_at',
    ];

    protected $casts = [
        'sudah_tes_hba1c'    => 'boolean',
        'sudah_tes_antibodi' => 'boolean',
        'sudah_tes_cpeptide' => 'boolean',
        'sudah_tes_ttgo'     => 'boolean',
    ];

    public function diagnosa()
    {
        return $this->belongsTo(Diagnosa::class, 'id_diagnosa', 'id_diagnosa');
    }
}
