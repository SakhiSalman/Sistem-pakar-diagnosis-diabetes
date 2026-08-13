<?php

namespace App\Http\Controllers\Kuesioner;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Diagnosa;
use App\Models\DiagnosaDetail;
use App\Models\Gejala;
use App\Models\JenisDiabetes;
use App\Models\Pasien;
use App\Models\RuleCf;
use App\Models\RuleCfLab;
use App\Models\TesLab;
use App\Services\DiagnosisEngine;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KuesionerController extends Controller
{
    public function index()
    {
        return view('kuesioner.intro');
    }

    // ===== PASIEN LAMA: login dengan email + password =====
    public function loginLama(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $pasien = Pasien::where('email', $request->email)->whereNotNull('password')->first();

        if (! $pasien || ! Hash::check($request->password, $pasien->password)) {
            return back()->with('error', 'Email atau password salah. Jika ini kunjungan pertama Anda, silakan daftar terlebih dahulu.')->withInput();
        }

        session([
            'pasien_id'   => $pasien->id_pasien,
            'pasien_nama' => $pasien->nama,
        ]);

        return redirect()->route('kuesioner.gejala');
    }

    // ===== PASIEN BARU: daftar dengan nama + email + password =====
    public function daftar(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:pasien,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan menu Login jika Anda sudah pernah berkunjung sebelumnya.',
        ]);

        session([
            'pasien_nama'     => $request->nama,
            'pasien_email'    => $request->email,
            'pasien_password' => Hash::make($request->password),
            'pasien_baru'     => true,
        ]);

        return redirect()->route('kuesioner.gejala');
    }

    private function sudahMasuk(): bool
    {
        return (bool) (session('pasien_baru') || session('pasien_id'));
    }

    // ===== Kuesioner gejala =====
    public function gejala()
    {
        if (! $this->sudahMasuk()) {
            return redirect()->route('kuesioner.index');
        }

        return view('kuesioner.gejala', [
            'daftarGejala' => Gejala::orderBy('id_gejala')->get(),
        ]);
    }

    public function proses(Request $request)
    {
        if (! $this->sudahMasuk()) {
            return redirect()->route('kuesioner.index');
        }

        $gejalaTerpilih = $request->input('gejala', []);
        if (empty($gejalaTerpilih)) {
            return back()->with('error', 'Pilih minimal satu gejala.');
        }

        session(['gejala_terpilih' => $gejalaTerpilih]);

        return redirect()->route('kuesioner.tes-lab');
    }

    // ===== Riwayat tes lab (HbA1c / Antibodi GAD / C-Peptide) + usia & berat badan =====
    public function formTesLab()
    {
        if (! session('gejala_terpilih')) {
            return redirect()->route('kuesioner.index');
        }

        $pasienLama    = (bool) session('pasien_id');
        $tanggalLahir  = null;
        if ($pasienLama) {
            $tanggalLahir = Pasien::find(session('pasien_id'))->tanggal_lahir ?? null;
        }

        return view('kuesioner.tes_lab', [
            'pasienLama'   => $pasienLama,
            'tanggalLahir' => $tanggalLahir,
        ]);
    }

    public function simpanTesLab(Request $request)
    {
        if (! session('gejala_terpilih')) {
            return redirect()->route('kuesioner.index');
        }

        $isPasienLama = (bool) session('pasien_id');

        $rules = [
            'berat_badan'        => 'required|numeric|min:1|max:400',
            'sudah_tes_hba1c'    => 'nullable|boolean',
            'hasil_hba1c'        => 'required_if:sudah_tes_hba1c,1|nullable|numeric|min:3|max:20',
            'sudah_tes_antibodi' => 'nullable|boolean',
            'hasil_antibodi'     => 'required_if:sudah_tes_antibodi,1|nullable|in:positif,negatif',
            'sudah_tes_cpeptide' => 'nullable|boolean',
            'hasil_cpeptide'     => 'required_if:sudah_tes_cpeptide,1|nullable|in:rendah,normal_tinggi',
            'sudah_tes_ttgo'     => 'nullable|boolean',
            'ttgo_puasa'         => 'required_if:sudah_tes_ttgo,1|nullable|numeric|min:20|max:400',
            'ttgo_1jam'          => 'required_if:sudah_tes_ttgo,1|nullable|numeric|min:20|max:500',
            'ttgo_2jam'          => 'required_if:sudah_tes_ttgo,1|nullable|numeric|min:20|max:500',
        ];
        if (! $isPasienLama) {
            $rules['tanggal_lahir'] = 'required|date|before:today';
        }

        $request->validate($rules, [
            'hasil_hba1c.required_if'    => 'Isi hasil angka HbA1c Anda (dalam persen).',
            'hasil_antibodi.required_if' => 'Pilih hasil tes Antibodi GAD Anda.',
            'hasil_cpeptide.required_if' => 'Pilih hasil tes C-Peptide Anda.',
        ]);

        $tanggalLahir = $isPasienLama
            ? (Pasien::find(session('pasien_id'))->tanggal_lahir ?? null)
            : $request->tanggal_lahir;

        $inputTesLab = [
            'sudah_tes_hba1c'    => (bool) $request->boolean('sudah_tes_hba1c'),
            'hasil_hba1c'        => $request->hasil_hba1c,
            'sudah_tes_antibodi' => (bool) $request->boolean('sudah_tes_antibodi'),
            'hasil_antibodi'     => $request->hasil_antibodi,
            'sudah_tes_cpeptide' => (bool) $request->boolean('sudah_tes_cpeptide'),
            'hasil_cpeptide'     => $request->hasil_cpeptide,
            'sudah_tes_ttgo'     => (bool) $request->boolean('sudah_tes_ttgo'),
            'ttgo_puasa'         => $request->ttgo_puasa,
            'ttgo_1jam'          => $request->ttgo_1jam,
            'ttgo_2jam'          => $request->ttgo_2jam,
            'berat_badan'        => $request->berat_badan,
            'tanggal_lahir'      => $tanggalLahir,
        ];

        $idGejalaObesitas = Gejala::where('kode_gejala', 'G15')->value('id_gejala');
        $gejalaObesitasDipilih = $idGejalaObesitas && in_array($idGejalaObesitas, session('gejala_terpilih', []));

        $engine   = new DiagnosisEngine();
        $ruleMap  = RuleCf::getRuleMap();
        $jenisIds = JenisDiabetes::pluck('id_jenis')->toArray();
        $ruleCfLabMap = RuleCfLab::getRuleMap();

        $faktorTambahan = $engine->faktorDariLabDanUsia($ruleCfLabMap, $inputTesLab, $gejalaObesitasDipilih);
        $hasil = $engine->hitung($ruleMap, session('gejala_terpilih'), $jenisIds, $faktorTambahan);

        session([
            'hasil_diagnosis'      => $hasil,
            'tes_lab_input'        => $inputTesLab,
            'pasien_tanggal_lahir' => $isPasienLama ? null : $request->tanggal_lahir,
        ]);

        return redirect()->route('kuesioner.hasil');
    }

    public function hasil()
    {
        $hasil = session('hasil_diagnosis');
        if (! $hasil) {
            return redirect()->route('kuesioner.index');
        }

        $jenisMap = JenisDiabetes::all()->keyBy('id_jenis');
        $idUtama  = array_key_first($hasil);
        $tesLab   = session('tes_lab_input', []);

        $sarankanTesLanjutan = empty($tesLab['sudah_tes_antibodi']) && empty($tesLab['sudah_tes_cpeptide']);

        return view('kuesioner.hasil', [
            'hasil'                => $hasil,
            'jenisMap'             => $jenisMap,
            'jenisUtama'           => $jenisMap[$idUtama],
            'sarankanTesLanjutan'  => $sarankanTesLanjutan,
        ]);
    }

    // ===== Data diri + no. WhatsApp =====
    public function formJadwal()
    {
        if (! session('hasil_diagnosis')) {
            return redirect()->route('kuesioner.index');
        }

        $pasienLama   = (bool) session('pasien_id');
        $noHpSekarang = null;
        if ($pasienLama) {
            $noHpSekarang = Pasien::find(session('pasien_id'))->no_hp ?? null;
        }

        return view('kuesioner.jadwal', [
            'nama'       => session('pasien_nama'),
            'pasienLama' => $pasienLama,
            'noHp'       => $noHpSekarang,
        ]);
    }

    public function simpanJadwal(Request $request)
    {
        $isPasienLama = (bool) session('pasien_id');

        if ($request->filled('no_hp')) {
            $request->merge(['no_hp' => WhatsAppService::formatNomor($request->no_hp)]);
        }

        $rules = [
            'tanggal_kunjungan' => 'required|date|after:today',
            'no_hp'             => [
                'required',
                'regex:/^(0|62)8[0-9]{8,12}$/',
                $isPasienLama
                    ? Rule::unique('pasien', 'no_hp')->ignore(session('pasien_id'), 'id_pasien')
                    : Rule::unique('pasien', 'no_hp'),
            ],
        ];

        if (! $isPasienLama) {
            $rules['no_ktp']        = 'required|digits_between:1,16|regex:/^[0-9]+$/';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        $request->validate($rules, [
            'no_ktp.regex'            => 'Nomor KTP hanya boleh berisi angka.',
            'no_ktp.digits_between'   => 'Nomor KTP maksimal 16 digit angka.',
            'tanggal_kunjungan.after' => 'Tanggal kunjungan tidak boleh hari ini, pilih besok atau seterusnya.',
            'no_hp.regex'             => 'Nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx atau 628xxxxxxxxxx.',
            'no_hp.unique'            => 'Nomor WhatsApp ini sudah terdaftar pada pasien lain.',
        ]);

        if ($isPasienLama) {
            $pasien = Pasien::findOrFail(session('pasien_id'));
            $pasien->update(['no_hp' => $request->no_hp]);
        } else {
            $pasien = Pasien::create([
                'no_registrasi' => Pasien::generateNoRegistrasi(),
                'no_ktp'        => $request->no_ktp,
                'no_hp'         => $request->no_hp,
                'nama'          => session('pasien_nama'),
                'email'         => session('pasien_email'),
                'password'      => session('pasien_password'),
                'jenis_kelamin' => $request->jenis_kelamin,
                'tanggal_lahir' => session('pasien_tanggal_lahir'),
                'created_at'    => now(),
            ]);
        }

        $diagnosa = Diagnosa::create([
            'id_pasien'         => $pasien->id_pasien,
            'tanggal_diagnosa'  => now(),
            'tanggal_kunjungan' => $request->tanggal_kunjungan,
            'status'            => 'menunggu_konsultasi',
        ]);

        $diagnosa->gejalaTerpilih()->attach(session('gejala_terpilih'));

        $tesLab = session('tes_lab_input', []);
        TesLab::create([
            'id_diagnosa'            => $diagnosa->id_diagnosa,
            'sudah_tes_hba1c'        => $tesLab['sudah_tes_hba1c'] ?? false,
            'hasil_hba1c'            => $tesLab['hasil_hba1c'] ?? null,
            'sudah_tes_antibodi'     => $tesLab['sudah_tes_antibodi'] ?? false,
            'hasil_antibodi'         => $tesLab['hasil_antibodi'] ?? null,
            'sudah_tes_cpeptide'     => $tesLab['sudah_tes_cpeptide'] ?? false,
            'hasil_cpeptide'         => $tesLab['hasil_cpeptide'] ?? null,
            'sudah_tes_ttgo'         => $tesLab['sudah_tes_ttgo'] ?? false,
            'ttgo_puasa'             => $tesLab['ttgo_puasa'] ?? null,
            'ttgo_1jam'              => $tesLab['ttgo_1jam'] ?? null,
            'ttgo_2jam'              => $tesLab['ttgo_2jam'] ?? null,
            'berat_badan'            => $tesLab['berat_badan'] ?? null,
            'tanggal_lahir_saat_isi' => $tesLab['tanggal_lahir'] ?? null,
            'created_at'             => now(),
        ]);

        $hasil   = session('hasil_diagnosis');
        $idUtama = array_key_first($hasil);
        foreach ($hasil as $idJenis => $cf) {
            DiagnosaDetail::create([
                'id_diagnosa'    => $diagnosa->id_diagnosa,
                'id_jenis'       => $idJenis,
                'nilai_cf_akhir' => $cf,
                'persentase'     => round($cf * 100, 2),
                'is_hasil_utama' => $idJenis === $idUtama,
            ]);
        }

        $noReg = $pasien->no_registrasi;

        session()->forget([
            'pasien_id', 'pasien_nama', 'pasien_email', 'pasien_password', 'pasien_baru',
            'hasil_diagnosis', 'gejala_terpilih', 'tes_lab_input', 'pasien_tanggal_lahir',
        ]);

        return view('kuesioner.konfirmasi', ['noRegistrasi' => $noReg]);
    }

    // ===== Lupa password pasien (via email) =====
    public function formLupaPassword()
    {
        return view('kuesioner.lupa_password');
    }

    public function kirimLinkReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pasien = Pasien::where('email', $request->email)->whereNotNull('password')->first();

        if ($pasien) {
            $token = Str::random(64);

            DB::table('pasien_password_resets')->where('email', $pasien->email)->delete();
            DB::table('pasien_password_resets')->insert([
                'email'      => $pasien->email,
                'token'      => Hash::make($token),
                'created_at' => now(),
            ]);

            $url = route('kuesioner.reset-password.form', ['token' => $token, 'email' => $pasien->email]);

            try {
                Mail::to($pasien->email)->send(new ResetPasswordMail($pasien->nama, $url));
            } catch (\Throwable $e) {
                // gagal kirim mail tidak menghentikan proses; cek storage/logs/laravel.log
            }
        }

        return back()->with('success', 'Jika email terdaftar, tautan reset password telah dikirim. Silakan cek inbox (atau folder spam).');
    }

    public function formResetPassword(Request $request, string $token)
    {
        return view('kuesioner.reset_password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function prosesResetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $row = DB::table('pasien_password_resets')->where('email', $request->email)->first();

        if (! $row || ! Hash::check($request->token, $row->token) || now()->diffInMinutes($row->created_at) > 60) {
            return back()->with('error', 'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.');
        }

        Pasien::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('pasien_password_resets')->where('email', $request->email)->delete();

        return redirect()->route('kuesioner.index')->with('success', 'Password berhasil diganti, silakan login dengan password baru.');
    }
}
