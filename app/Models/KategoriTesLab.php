<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriTesLab extends Model
{
    protected $table = 'kategori_tes_lab';
    protected $fillable = ['kode', 'label', 'keterangan', 'bawaan_sistem', 'aktif', 'urutan'];
    protected $casts = ['bawaan_sistem' => 'boolean', 'aktif' => 'boolean'];
}
