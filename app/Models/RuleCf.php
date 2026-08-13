<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RuleCf extends Model
{
    protected $table = 'rule_cf';
    protected $primaryKey = 'id_rule';
    public $timestamps = false;
    protected $fillable = ['id_gejala', 'id_jenis', 'nilai_mb', 'nilai_md', 'nilai_cf'];

    /**
     * Ambil seluruh rule dalam bentuk map: [id_gejala => [id_jenis => nilai_cf]]
     * Dipakai oleh DiagnosisEngine (Forward Chaining + Certainty Factor).
     */
    public static function getRuleMap(): array
    {
        $map = [];
        foreach (self::all() as $row) {
            $map[$row->id_gejala][$row->id_jenis] = (float) $row->nilai_cf;
        }
        return $map;
    }
}
