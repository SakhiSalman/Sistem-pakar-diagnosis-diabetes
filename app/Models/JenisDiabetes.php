<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisDiabetes extends Model
{
    protected $table = 'jenis_diabetes';
    protected $primaryKey = 'id_jenis';
    public $timestamps = false;
    protected $fillable = ['kode_jenis', 'nama_jenis', 'deskripsi', 'rekomendasi'];
}
