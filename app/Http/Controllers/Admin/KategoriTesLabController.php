<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriTesLab;
use App\Models\RuleCfLab;
use Illuminate\Http\Request;

class KategoriTesLabController extends Controller
{
    public function index()
    {
        return view('admin.kategori-lab.index', [
            'daftar' => KategoriTesLab::orderBy('urutan')->orderBy('id')->get(),
        ]);
    }

    public function tambah()
    {
        return view('admin.kategori-lab.form', ['mode' => 'tambah', 'data' => null]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'kode'       => 'required|string|max:40|alpha_dash|unique:kategori_tes_lab,kode',
            'label'      => 'required|string|max:150',
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ]);

        KategoriTesLab::create([
            'kode'          => $request->kode,
            'label'         => $request->label,
            'keterangan'    => $request->keterangan,
            'urutan'        => $request->urutan ?? 0,
            'bawaan_sistem' => false,
            'aktif'         => true,
        ]);

        return redirect()->route('admin.kategori-lab.index')
            ->with('success', 'Kategori baru berhasil ditambahkan. Catatan: kategori baru ini hanya tampil sebagai referensi di halaman Rule Lab -- supaya benar-benar dihitung ke skor CF, developer perlu menambahkan logika pemicunya di DiagnosisEngine terlebih dulu.');
    }

    public function edit(int $id)
    {
        return view('admin.kategori-lab.form', ['mode' => 'edit', 'data' => KategoriTesLab::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $kategori = KategoriTesLab::findOrFail($id);

        $request->validate([
            'label'      => 'required|string|max:150',
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ]);

        // kode sengaja tidak bisa diubah lewat form (baik bawaan sistem maupun
        // custom) karena kode dipakai sebagai kunci di tabel rule_cf_lab dan,
        // untuk kategori bawaan sistem, juga dicek langsung di kode PHP.
        $kategori->update([
            'label'      => $request->label,
            'keterangan' => $request->keterangan,
            'urutan'     => $request->urutan ?? $kategori->urutan,
        ]);

        return redirect()->route('admin.kategori-lab.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function toggleAktif(int $id)
    {
        $kategori = KategoriTesLab::findOrFail($id);
        $kategori->update(['aktif' => ! $kategori->aktif]);

        return back()->with('success', $kategori->aktif
            ? 'Kategori diaktifkan kembali.'
            : 'Kategori dinonaktifkan -- tidak akan tampil lagi di form pasien maupun halaman Rule Lab, tapi data lama tidak hilang.');
    }

    public function hapus(int $id)
    {
        $kategori = KategoriTesLab::findOrFail($id);

        if ($kategori->bawaan_sistem) {
            return back()->with('error', 'Kategori bawaan sistem ("' . $kategori->label . '") tidak bisa dihapus permanen karena logikanya terhubung ke perhitungan CF di kode program. Gunakan tombol "Nonaktifkan" untuk menyembunyikannya tanpa merusak sistem.');
        }

        RuleCfLab::where('kategori', $kategori->kode)->delete();
        $kategori->delete();

        return redirect()->route('admin.kategori-lab.index')->with('success', 'Kategori berhasil dihapus permanen.');
    }
}
