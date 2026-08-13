@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main style="max-width:480px;">
  <div class="panel">
    <h2 style="margin-top:0;">Atur Ulang Password</h2>
    <p class="sub">Buat password baru untuk akun Anda.</p>

    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif
    @if($errors->any())
      <div class="alert-error">
        <ul style="margin:0;padding-left:18px;">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('kuesioner.reset-password.simpan') }}" method="post">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-row"><label>Email</label><input type="email" name="email" value="{{ $email }}" readonly style="background:var(--teal-tint);"></div>
      <div class="form-row"><label>Password baru</label><input type="password" name="password" minlength="6" required></div>
      <div class="form-row"><label>Ulangi password baru</label><input type="password" name="password_confirmation" required></div>
      <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
    </form>
  </div>
</main>
@endsection
