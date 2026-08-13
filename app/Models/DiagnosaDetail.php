<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosaDetail extends Model
{
    protected $table = 'diagnosa_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;
    protected $fillable = ['id_diagnosa', 'id_jenis', 'nilai_cf_akhir', 'persentase', 'is_hasil_utama'];

    public function jenis()
    {
        return $this->belongsTo(JenisDiabetes::class, 'id_jenis', 'id_jenis');
    }
}
