@extends('layouts.app')
@section('content')
<main style="max-width:420px;padding-top:80px;">
  <div class="panel">
    <div class="brand" style="margin-bottom:8px;"><span class="dot"></span>Klinik Sehat Bersama</div>
    <p class="sub">Buat akun staf untuk mengelola data diagnosis pasien.</p>

    @if($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('admin.register.post') }}" method="post">
      @csrf
      <div class="form-row"><label>Nama lengkap</label><input type="text" name="name" value="{{ old('name') }}" required></div>
      <div class="form-row"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
      <div class="form-row"><label>Password</label><input type="password" name="password" minlength="6" required></div>
      <div class="form-row"><label>Ulangi password</label><input type="password" name="password_confirmation" required></div>
      <button type="submit" class="btn btn-primary">Daftar</button>
    </form>
    <a href="{{ route('admin.login') }}" class="btn btn-outline" style="display:block;text-align:center;text-decoration:none;">Sudah punya akun? Masuk</a>
  </div>
</main>
@endsection
