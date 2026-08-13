@extends('layouts.app')
@section('content')
<main style="max-width:420px;padding-top:80px;">
  <div class="brand" style="margin-bottom:16px;justify-content:center;"><span class="dot"></span>Klinik Sehat Bersama</div>

  <div class="panel" style="padding:0;overflow:hidden;">
    <div style="display:flex;background:var(--mist);padding:6px;gap:6px;">
      <button type="button" id="tabLoginBtn" onclick="pilihTabAdmin('login')"
        style="flex:1;padding:12px;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;background:var(--teal);color:#fff;">Masuk</button>
      <button type="button" id="tabDaftarBtn" onclick="pilihTabAdmin('daftar')"
        style="flex:1;padding:12px;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;background:transparent;color:var(--ink-soft);">Daftar</button>
    </div>

    <div style="padding:30px;">
      <!-- ===== TAB LOGIN ===== -->
      <div id="tabLogin">
        <h2 style="margin-top:0;">Masuk Staf</h2>
        <p class="sub">Masuk ke dashboard staf untuk mengelola data diagnosis pasien.</p>

        @if(session('error'))
          <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="post">
          @csrf
          <div class="form-row"><label>Email</label><input type="email" name="email" placeholder="email@klinik.com" required></div>
          <div class="form-row"><label>Password</label><input type="password" name="password" required></div>
          <button type="submit" class="btn btn-primary">Masuk</button>
        </form>
        <p style="text-align:center;font-size:14px;margin-top:12px;">
          <a href="{{ route('admin.lupa-password') }}" style="color:var(--ink-soft);">Lupa password?</a>
        </p>
        <p style="text-align:center;font-size:14px;color:var(--ink-soft);margin-top:16px;">
          Belum punya akun? <a href="#" onclick="pilihTabAdmin('daftar');return false;" style="color:var(--teal-deep);font-weight:700;">Daftar sekarang</a>
        </p>
      </div>

      <!-- ===== TAB DAFTAR ===== -->
      <div id="tabDaftar" style="display:none;">
        <h2 style="margin-top:0;">Daftar Akun Staf</h2>
        <p class="sub">Buat akun baru untuk mengelola data diagnosis pasien.</p>

        @if($errors->any())
          <div class="alert-error">
            <ul style="margin:0;padding-left:18px;">
              @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('admin.register.post') }}" method="post">
          @csrf
          <div class="form-row"><label>Nama lengkap</label><input type="text" name="name" value="{{ old('name') }}" required></div>
          <div class="form-row"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="form-row"><label>Password</label><input type="password" name="password" minlength="6" required></div>
          <div class="form-row"><label>Ulangi password</label><input type="password" name="password_confirmation" required></div>
          <button type="submit" class="btn btn-primary">Daftar</button>
        </form>
        <p style="text-align:center;font-size:14px;color:var(--ink-soft);margin-top:16px;">
          Sudah punya akun? <a href="#" onclick="pilihTabAdmin('login');return false;" style="color:var(--teal-deep);font-weight:700;">Masuk sekarang</a>
        </p>
      </div>
    </div>
  </div>
</main>

<script>
  function pilihTabAdmin(nama) {
    const login = document.getElementById('tabLogin');
    const daftar = document.getElementById('tabDaftar');
    const btnLogin = document.getElementById('tabLoginBtn');
    const btnDaftar = document.getElementById('tabDaftarBtn');

    if (nama === 'login') {
      login.style.display = 'block'; daftar.style.display = 'none';
      btnLogin.style.background = 'var(--teal)'; btnLogin.style.color = '#fff';
      btnDaftar.style.background = 'transparent'; btnDaftar.style.color = 'var(--ink-soft)';
    } else {
      login.style.display = 'none'; daftar.style.display = 'block';
      btnDaftar.style.background = 'var(--teal)'; btnDaftar.style.color = '#fff';
      btnLogin.style.background = 'transparent'; btnLogin.style.color = 'var(--ink-soft)';
    }
  }
  @if($errors->any())
    pilihTabAdmin('daftar');
  @endif
</script>
@endsection
