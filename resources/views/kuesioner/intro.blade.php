@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  <div class="hero">
    <div class="eyebrow">Deteksi dini mandiri</div>
    <h1>Kenali indikasi diabetes Anda sebelum bertemu dokter</h1>
    <p>Isi kuesioner gejala untuk mendapat indikasi awal jenis diabetes menggunakan Forward Chaining dan Certainty Factor.</p>
    <div class="disclaimer">Hasil ini adalah indikasi awal, bukan diagnosis final. Diagnosis resmi tetap ditentukan oleh dokter saat konsultasi langsung di klinik.</div>
  </div>

  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert-error">{{ $errors->first() }}</div>
  @endif

  <div class="panel" style="margin-top:26px;padding:0;overflow:hidden;">
    <div style="display:flex;background:var(--mist);padding:6px;gap:6px;">
      <button type="button" id="tabLoginBtn" onclick="pilihTab('login')"
        style="flex:1;padding:12px;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;background:var(--teal);color:#fff;">Masuk</button>
      <button type="button" id="tabDaftarBtn" onclick="pilihTab('daftar')"
        style="flex:1;padding:12px;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;background:transparent;color:var(--ink-soft);">Daftar</button>
    </div>

    <div style="padding:30px;">
      <!-- ===== TAB LOGIN (pasien lama) ===== -->
      <div id="tabLogin">
        <h2 style="margin-top:0;">Masuk</h2>
        <p class="sub">Sudah pernah mengisi sebelumnya? Masuk dengan email dan password yang Anda buat.</p>
        <form action="{{ route('kuesioner.login-lama') }}" method="post">
          @csrf
          <div class="form-row">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
          </div>
          <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" placeholder="Password Anda" required>
          </div>
          <button type="submit" class="btn btn-primary">Masuk & isi kuesioner baru</button>
        </form>
        <p style="text-align:center;font-size:14px;margin-top:12px;">
          <a href="{{ route('kuesioner.lupa-password') }}" style="color:var(--ink-soft);">Lupa password?</a>
        </p>
        <p style="text-align:center;font-size:14px;color:var(--ink-soft);margin-top:16px;">
          Belum punya akun? <a href="#" onclick="pilihTab('daftar');return false;" style="color:var(--teal-deep);font-weight:700;">Daftar sekarang</a>
        </p>
      </div>

      <!-- ===== TAB DAFTAR (pasien baru) ===== -->
      <div id="tabDaftar" style="display:none;">
        <h2 style="margin-top:0;">Daftar</h2>
        <p class="sub">Pengisian pertama kali. Buat email dan password untuk kunjungan berikutnya (email dipakai untuk reset password jika lupa). Nomor WhatsApp akan diminta belakangan saat menyimpan hasil.</p>
        <form action="{{ route('kuesioner.daftar') }}" method="post">
          @csrf
          <div class="form-row">
            <label>Nama lengkap</label>
            <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Nama Anda" required>
          </div>
          <div class="form-row">
            <label>Alamat email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
          </div>
          <div class="form-row">
            <label>Buat password</label>
            <input type="password" name="password" minlength="6" placeholder="Minimal 6 karakter" required>
          </div>
          <div class="form-row">
            <label>Ulangi password</label>
            <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
          </div>
          <button type="submit" class="btn btn-primary">Daftar & mulai kuesioner</button>
        </form>
        <p style="text-align:center;font-size:14px;color:var(--ink-soft);margin-top:16px;">
          Sudah punya akun? <a href="#" onclick="pilihTab('login');return false;" style="color:var(--teal-deep);font-weight:700;">Masuk sekarang</a>
        </p>
      </div>
    </div>
  </div>

  <p class="dummy-note">Data gejala dan nilai Certainty Factor pada sistem ini masih data dummy, belum divalidasi oleh dokter.</p>
</main>

<script>
  function pilihTab(nama) {
    const login = document.getElementById('tabLogin');
    const daftar = document.getElementById('tabDaftar');
    const btnLogin = document.getElementById('tabLoginBtn');
    const btnDaftar = document.getElementById('tabDaftarBtn');

    if (nama === 'login') {
      login.style.display = 'block';
      daftar.style.display = 'none';
      btnLogin.style.background = 'var(--teal)';
      btnLogin.style.color = '#fff';
      btnDaftar.style.background = 'transparent';
      btnDaftar.style.color = 'var(--ink-soft)';
    } else {
      login.style.display = 'none';
      daftar.style.display = 'block';
      btnDaftar.style.background = 'var(--teal)';
      btnDaftar.style.color = '#fff';
      btnLogin.style.background = 'transparent';
      btnLogin.style.color = 'var(--ink-soft)';
    }
  }
  @if(old('nama') !== null)
    pilihTab('daftar');
  @endif
</script>
@endsection
