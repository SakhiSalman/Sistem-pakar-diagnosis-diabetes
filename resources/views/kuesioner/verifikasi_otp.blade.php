@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  <a href="{{ route('kuesioner.index') }}" class="back-link">&larr; Kembali, salah nama/email</a>

  <div class="panel">
    <h2>Masukkan kode verifikasi</h2>
    <p class="sub">Kode 6 digit telah dikirim ke email <strong>{{ $email }}</strong>. Cek juga folder Spam jika belum masuk.</p>

    @if(session('error'))
      <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div id="timerBox" style="text-align:center;margin-bottom:18px;font-size:15px;color:var(--ink-soft);">
      Kode berlaku selama <strong id="timerText" style="color:var(--coral-deep);">05:00</strong>
    </div>

    <form action="{{ route('kuesioner.verifikasi-otp.post') }}" method="post">
      @csrf
      <div class="form-row">
        <label>Kode OTP</label>
        <input type="text" name="kode_otp" maxlength="6" placeholder="123456" style="letter-spacing:5px;font-size:20px;text-align:center;" required>
      </div>
      <button type="submit" class="btn btn-primary">Verifikasi</button>
    </form>
    <form action="{{ route('kuesioner.kirim-otp') }}" method="post" style="margin-top:6px;">
      @csrf
      <input type="hidden" name="nama" value="{{ $nama }}">
      <input type="hidden" name="email" value="{{ $email }}">
      <button type="submit" class="btn btn-outline">Kirim ulang kode</button>
    </form>
  </div>
</main>

<script>
  let sisaDetik = 5 * 60;
  const timerText = document.getElementById('timerText');
  const interval = setInterval(() => {
    sisaDetik--;
    if (sisaDetik <= 0) {
      clearInterval(interval);
      timerText.textContent = 'Kedaluwarsa';
      timerText.parentElement.innerHTML = '<span style="color:var(--coral-deep);">Kode sudah kedaluwarsa, silakan klik "Kirim ulang kode".</span>';
      return;
    }
    const menit = String(Math.floor(sisaDetik / 60)).padStart(2, '0');
    const detik = String(sisaDetik % 60).padStart(2, '0');
    timerText.textContent = `${menit}:${detik}`;
  }, 1000);
</script>
@endsection
