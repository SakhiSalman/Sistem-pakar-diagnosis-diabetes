<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DiagnosaDivalidasiMail;
use App\Models\Diagnosa;
use App\Models\Gejala;
use App\Models\JenisDiabetes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('q', '');

        $query = Diagnosa::with('pasien')->orderByDesc('tanggal_diagnosa');

        if ($keyword !== '') {
            $query->whereHas('pasien', function ($q) use ($keyword) {
                $q->where('no_registrasi', 'like', "%{$keyword}%")
                  ->orWhere('no_ktp', 'like', "%{$keyword}%")
                  ->orWhere('nama', 'like', "%{$keyword}%");
            });
        }

        return view('admin.dashboard', [
            'daftar'  => $query->paginate(20)->withQueryString(),
            'keyword' => $keyword,
        ]);
    }

    public function detail(int $id)
    {
        $diagnosa = Diagnosa::with(['pasien', 'detail.jenis', 'gejalaTerpilih', 'tesLab'])->findOrFail($id);

        return view('admin.detail', ['diagnosa' => $diagnosa]);
    }

    public function validasi(Request $request, int $id)
    {
        $diagnosa = Diagnosa::with('pasien')->findOrFail($id);
        $status = $request->input('aksi') === 'teruskan' ? 'selesai' : 'divalidasi';

        $diagnosa->update([
            'catatan_admin'     => $request->input('catatan'),
            'status'            => $status,
            'id_user_validator' => Auth::id(),
        ]);

        // Kirim notifikasi email otomatis ke pasien saat diteruskan ke dokter
        $emailTerkirim = false;
        if ($status === 'selesai' && ! empty($diagnosa->pasien->email)) {
            try {
                Mail::to($diagnosa->pasien->email)->send(new DiagnosaDivalidasiMail(
                    $diagnosa->pasien->nama,
                    $diagnosa->pasien->no_registrasi,
                    $diagnosa->tanggal_kunjungan,
                    $diagnosa->catatan_admin
                ));
                $emailTerkirim = true;
            } catch (\Throwable $e) {
                // gagal kirim mail tidak menghentikan proses; cek storage/logs/laravel.log
            }
        }

        $pesanSukses = 'Catatan berhasil disimpan.';
        if ($status === 'selesai') {
            $pesanSukses = $emailTerkirim
                ? 'Data diteruskan ke dokter dan notifikasi email telah dikirim ke pasien.'
                : 'Data diteruskan ke dokter, namun notifikasi email gagal terkirim (cek email pasien / pengaturan SMTP).';
        }

        return redirect()->route('admin.dashboard.detail', $id)
            ->with('success', $pesanSukses);
    }
}
