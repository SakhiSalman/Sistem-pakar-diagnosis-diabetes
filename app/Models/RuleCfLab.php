<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleCfLab extends Model
{
    protected $table = 'rule_cf_lab';
    protected $primaryKey = 'id_rule_lab';
    protected $fillable = ['kategori', 'id_jenis', 'nilai_mb', 'nilai_md', 'nilai_cf'];

    /**
     * Ambil seluruh rule dalam bentuk map: [kategori => [id_jenis => nilai_cf]]
     * Dipakai oleh DiagnosisEngine untuk menggabungkan hasil tes lab & faktor usia.
     */
    public static function getRuleMap(): array
    {
        $map = [];
        foreach (self::all() as $row) {
            $map[$row->kategori][$row->id_jenis] = (float) $row->nilai_cf;
        }
        return $map;
    }
}
