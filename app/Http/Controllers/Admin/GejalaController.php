<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\RuleCf;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    public function index()
    {
        return view('admin.gejala.index', [
            'daftar' => Gejala::orderBy('kode_gejala')->get(),
        ]);
    }

    public function tambah()
    {
        return view('admin.gejala.form', ['mode' => 'tambah', 'data' => null]);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'kode_gejala' => 'required|string|max:10',
            'nama_gejala' => 'required|string|max:255',
        ]);

        Gejala::create($request->only('kode_gejala', 'nama_gejala'));

        return redirect()->route('admin.gejala.index')->with('success', 'Gejala baru berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        return view('admin.gejala.form', ['mode' => 'edit', 'data' => Gejala::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        Gejala::findOrFail($id)->update($request->only('kode_gejala', 'nama_gejala'));
        return redirect()->route('admin.gejala.index')->with('success', 'Gejala berhasil diperbarui.');
    }

    public function hapus(int $id)
    {
        RuleCf::where('id_gejala', $id)->delete();
        Gejala::findOrFail($id)->delete();
        return redirect()->route('admin.gejala.index')->with('success', 'Gejala dan rule terkait berhasil dihapus.');
    }
}
