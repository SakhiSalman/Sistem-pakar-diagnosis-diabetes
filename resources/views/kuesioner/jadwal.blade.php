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

  <form action="{{ route('kuesioner.simpan-jadwal') }}" method="post">
    @csrf
    <div class="panel">
      <h2>Simpan hasil dan jadwalkan konsultasi</h2>
      <p class="sub">Lengkapi data berikut agar staf klinik dapat memanggil data Anda saat datang berkonsultasi, dan mengirim konfirmasi lewat WhatsApp.</p>

      <div class="form-row"><label>Nama lengkap</label><input type="text" value="{{ $nama }}" readonly style="background:var(--teal-tint);color:var(--teal-deep);font-weight:700;"></div>

      <div class="form-row">
        <label>Nomor WhatsApp aktif</label>
        <input type="tel" name="no_hp" inputmode="numeric" maxlength="15"
          value="{{ old('no_hp', $noHp) }}" placeholder="08xxxxxxxxxx" pattern="[0-9]*"
          oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
        <p style="font-size:12.5px;color:var(--ink-soft);margin:6px 0 0;">Pastikan nomor ini aktif. Admin klinik akan mengirim konfirmasi jadwal & hasil validasi lewat WhatsApp ke nomor ini.</p>
      </div>

      @if(!$pasienLama)
        <div class="form-row">
          <label>Nomor KTP (16 digit angka)</label>
          <input type="text" name="no_ktp" inputmode="numeric" maxlength="16" pattern="[0-9]*"
            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
            value="{{ old('no_ktp') }}" placeholder="16 digit angka" required>
        </div>

        <div class="form-row">
          <label>Jenis kelamin</label>
          <select name="jenis_kelamin" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="L" {{ old('jenis_kelamin')=='L'?'selected':'' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin')=='P'?'selected':'' }}>Perempuan</option>
          </select>
        </div>
      @else
        <div class="disclaimer" style="margin:0 0 18px;">Data KTP, jenis kelamin, dan tanggal lahir Anda sudah tersimpan dari pendaftaran sebelumnya, tidak perlu diisi ulang.</div>
      @endif

      <div class="form-row">
        <label>Tanggal rencana kunjungan ke klinik</label>
        <input type="date" name="tanggal_kunjungan" id="tanggalKunjungan" value="{{ old('tanggal_kunjungan') }}" required>
        <p style="font-size:12.5px;color:var(--ink-soft);margin:6px 0 0;">Tidak bisa memilih hari ini, minimal besok.</p>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan data</button>
  </form>
  <a href="{{ route('kuesioner.hasil') }}" class="btn btn-outline" style="display:block;text-align:center;text-decoration:none;">Kembali ke hasil</a>
</main>

<script>
  const inp = document.getElementById('tanggalKunjungan');
  const besok = new Date();
  besok.setDate(besok.getDate() + 1);
  inp.min = besok.toISOString().split('T')[0];
</script>
@endsection
