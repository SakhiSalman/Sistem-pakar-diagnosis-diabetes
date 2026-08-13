@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  @if($errors->any())
    <div class="alert-error">
      <ul style="margin:0;padding-left:18px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <div class="panel">
    <h2 style="margin-top:0;">Riwayat Pemeriksaan Laboratorium</h2>
    <p class="sub">Gejala diabetes tipe 1 dan tipe 2 seringkali mirip dan sulit dibedakan hanya dari gejala. Kalau Anda sudah pernah melakukan salah satu tes berikut, isikan hasilnya di bawah ini supaya hasil diagnosis lebih akurat. Kalau belum pernah tes, boleh dilewati saja.</p>

    <form action="{{ route('kuesioner.tes-lab.simpan') }}" method="post">
      @csrf

      @if(!$pasienLama)
        <div class="form-row">
          <label>Tanggal lahir</label>
          <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
        </div>
      @else
        <input type="hidden" name="tanggal_lahir_lama" value="1">
        <div class="disclaimer" style="margin:0 0 18px;">Tanggal lahir Anda sudah tersimpan dari pendaftaran sebelumnya, tidak perlu diisi ulang.</div>
      @endif

      <div class="form-row">
        <label>Berat badan saat ini (kg)</label>
        <input type="number" step="0.1" min="1" max="400" name="berat_badan" value="{{ old('berat_badan') }}" placeholder="contoh: 65" required>
      </div>

      <hr style="border:none;border-top:1px solid var(--line);margin:22px 0;">

      <!-- HbA1c -->
      <div class="form-row" style="margin-bottom:10px;">
        <label style="display:flex;align-items:center;gap:10px;font-weight:700;">
          <input type="checkbox" id="cbHba1c" name="sudah_tes_hba1c" value="1" onchange="toggleBox('boxHba1c', this.checked)" {{ old('sudah_tes_hba1c') ? 'checked' : '' }}>
          Sudah tes HbA1c
        </label>
      </div>
      <div id="boxHba1c" style="display:{{ old('sudah_tes_hba1c') ? 'block' : 'none' }};margin:0 0 20px 28px;">
        <label style="font-size:14px;font-weight:600;">Hasil HbA1c (%)</label>
        <input type="number" step="0.1" min="3" max="20" name="hasil_hba1c" value="{{ old('hasil_hba1c') }}" placeholder="contoh: 6.8">
        <p style="font-size:12.5px;color:var(--ink-soft);margin:6px 0 0;">Normal: &lt;5,7% &middot; Prediabetes: 5,7&ndash;6,4% &middot; Diabetes: &ge;6,5%</p>
      </div>

      <!-- Antibodi GAD -->
      <div class="form-row" style="margin-bottom:10px;">
        <label style="display:flex;align-items:center;gap:10px;font-weight:700;">
          <input type="checkbox" id="cbAntibodi" name="sudah_tes_antibodi" value="1" onchange="toggleBox('boxAntibodi', this.checked)" {{ old('sudah_tes_antibodi') ? 'checked' : '' }}>
          Sudah tes Antibodi (GAD Autoantibodies)
        </label>
      </div>
      <div id="boxAntibodi" style="display:{{ old('sudah_tes_antibodi') ? 'block' : 'none' }};margin:0 0 20px 28px;">
        <label style="font-size:14px;font-weight:600;">Hasil tes Antibodi GAD</label>
        <select name="hasil_antibodi">
          <option value="">Pilih hasil</option>
          <option value="positif" {{ old('hasil_antibodi')=='positif'?'selected':'' }}>Positif</option>
          <option value="negatif" {{ old('hasil_antibodi')=='negatif'?'selected':'' }}>Negatif</option>
        </select>
      </div>

      <!-- C-Peptide -->
      <div class="form-row" style="margin-bottom:10px;">
        <label style="display:flex;align-items:center;gap:10px;font-weight:700;">
          <input type="checkbox" id="cbCpeptide" name="sudah_tes_cpeptide" value="1" onchange="toggleBox('boxCpeptide', this.checked)" {{ old('sudah_tes_cpeptide') ? 'checked' : '' }}>
          Sudah tes C-Peptide
        </label>
      </div>
      <div id="boxCpeptide" style="display:{{ old('sudah_tes_cpeptide') ? 'block' : 'none' }};margin:0 0 20px 28px;">
        <label style="font-size:14px;font-weight:600;">Hasil tes C-Peptide</label>
        <select name="hasil_cpeptide">
          <option value="">Pilih hasil</option>
          <option value="rendah" {{ old('hasil_cpeptide')=='rendah'?'selected':'' }}>Rendah</option>
          <option value="normal_tinggi" {{ old('hasil_cpeptide')=='normal_tinggi'?'selected':'' }}>Normal / Tinggi</option>
        </select>
        <p style="font-size:12.5px;color:var(--ink-soft);margin:6px 0 0;">Kalau tidak tahu kategorinya, tanyakan ke petugas lab atau lihat catatan "rendah/normal/tinggi" pada hasil tes Anda.</p>
      </div>

      <!-- TTGO -->
      <div class="form-row" style="margin-bottom:10px;">
        <label style="display:flex;align-items:center;gap:10px;font-weight:700;">
          <input type="checkbox" id="cbTtgo" name="sudah_tes_ttgo" value="1" onchange="toggleBox('boxTtgo', this.checked)" {{ old('sudah_tes_ttgo') ? 'checked' : '' }}>
          Sudah tes TTGO (Tes Toleransi Glukosa Oral)
        </label>
      </div>
      <div id="boxTtgo" style="display:{{ old('sudah_tes_ttgo') ? 'block' : 'none' }};margin:0 0 8px 28px;">
        <p style="font-size:12.5px;color:var(--ink-soft);margin:0 0 10px;">Khusus skrining diabetes gestasional (tes dengan glukosa 75 gram). Isi hasil yang tertera pada kertas hasil lab Anda, dalam satuan mg/dL.</p>
        <label style="font-size:14px;font-weight:600;">Glukosa darah puasa (mg/dL)</label>
        <input type="number" step="0.1" min="20" max="400" name="ttgo_puasa" value="{{ old('ttgo_puasa') }}" placeholder="contoh: 88" style="margin-bottom:12px;">
        <label style="font-size:14px;font-weight:600;">Glukosa darah setelah 1 jam (mg/dL)</label>
        <input type="number" step="0.1" min="20" max="500" name="ttgo_1jam" value="{{ old('ttgo_1jam') }}" placeholder="contoh: 165" style="margin-bottom:12px;">
        <label style="font-size:14px;font-weight:600;">Glukosa darah setelah 2 jam (mg/dL)</label>
        <input type="number" step="0.1" min="20" max="500" name="ttgo_2jam" value="{{ old('ttgo_2jam') }}" placeholder="contoh: 140">
        <p style="font-size:12.5px;color:var(--ink-soft);margin:6px 0 0;">Ambang diagnosis diabetes gestasional: puasa &ge;92 mg/dL, atau 1 jam &ge;180 mg/dL, atau 2 jam &ge;153 mg/dL (cukup salah satu terpenuhi).</p>
      </div>

      <p style="font-size:13px;color:var(--ink-soft);margin:18px 0 22px;">Cukup centang tes yang benar-benar sudah Anda lakukan. Kalau belum tes sama sekali, langsung klik lanjut &mdash; sistem tetap bisa memberi indikasi awal dari gejala, dan Anda akan diberi saran tes lanjutan di halaman hasil.</p>

      <button type="submit" class="btn btn-primary">Lanjut Lihat Hasil</button>
    </form>
  </div>
</main>

<script>
  function toggleBox(id, show) {
    document.getElementById(id).style.display = show ? 'block' : 'none';
  }
</script>
@endsection
