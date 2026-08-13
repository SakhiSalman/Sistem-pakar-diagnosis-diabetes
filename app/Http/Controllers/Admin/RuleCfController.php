<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\JenisDiabetes;
use App\Models\RuleCf;
use Illuminate\Http\Request;

class RuleCfController extends Controller
{
    public function index()
    {
        $daftarGejala = Gejala::orderBy('kode_gejala')->get();
        $daftarJenis  = JenisDiabetes::orderBy('kode_jenis')->get();

        $matriks = [];
        foreach (RuleCf::all() as $r) {
            $matriks[$r->id_gejala][$r->id_jenis] = $r;
        }

        return view('admin.rule.index', compact('daftarGejala', 'daftarJenis', 'matriks'));
    }

    public function simpan(Request $request)
    {
        $input = $request->input('rule', []);

        foreach ($input as $idGejala => $jenisList) {
            foreach ($jenisList as $idJenis => $nilai) {
                $mb = (float) ($nilai['mb'] ?? 0);
                $md = (float) ($nilai['md'] ?? 0);
                $cf = $mb - $md;

                $existing = RuleCf::where('id_gejala', $idGejala)->where('id_jenis', $idJenis)->first();

                if ($existing) {
                    $existing->update(['nilai_mb' => $mb, 'nilai_md' => $md, 'nilai_cf' => $cf]);
                } elseif ($mb != 0 || $md != 0) {
                    RuleCf::create([
                        'id_gejala' => $idGejala,
                        'id_jenis'  => $idJenis,
                        'nilai_mb'  => $mb,
                        'nilai_md'  => $md,
                        'nilai_cf'  => $cf,
                    ]);
                }
            }
        }

        return redirect()->route('admin.rule-cf.index')->with('success', 'Basis pengetahuan (rule) berhasil diperbarui.');
    }
}
