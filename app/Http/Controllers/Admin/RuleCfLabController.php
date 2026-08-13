<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisDiabetes;
use App\Models\KategoriTesLab;
use App\Models\RuleCfLab;
use Illuminate\Http\Request;

class RuleCfLabController extends Controller
{
    public function index()
    {
        $daftarJenis    = JenisDiabetes::orderBy('kode_jenis')->get();
        $daftarKategori = KategoriTesLab::where('aktif', true)->orderBy('urutan')->orderBy('id')->get();

        $matriks = [];
        foreach (RuleCfLab::all() as $r) {
            $matriks[$r->kategori][$r->id_jenis] = $r;
        }

        return view('admin.rule-lab.index', [
            'daftarKategori' => $daftarKategori,
            'daftarJenis'    => $daftarJenis,
            'matriks'        => $matriks,
        ]);
    }

    public function simpan(Request $request)
    {
        $kodeValid = KategoriTesLab::pluck('kode')->all();
        $input = $request->input('rule', []);

        foreach ($input as $kategori => $jenisList) {
            if (! in_array($kategori, $kodeValid)) {
                continue;
            }
            foreach ($jenisList as $idJenis => $nilai) {
                $mb = (float) ($nilai['mb'] ?? 0);
                $md = (float) ($nilai['md'] ?? 0);
                $cf = $mb - $md;

                $existing = RuleCfLab::where('kategori', $kategori)->where('id_jenis', $idJenis)->first();

                if ($existing) {
                    $existing->update(['nilai_mb' => $mb, 'nilai_md' => $md, 'nilai_cf' => $cf]);
                } elseif ($mb != 0 || $md != 0) {
                    RuleCfLab::create([
                        'kategori' => $kategori,
                        'id_jenis' => $idJenis,
                        'nilai_mb' => $mb,
                        'nilai_md' => $md,
                        'nilai_cf' => $cf,
                    ]);
                }
            }
        }

        return redirect()->route('admin.rule-cf-lab.index')->with('success', 'Basis pengetahuan tes lab & usia berhasil diperbarui.');
    }
}
