@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main style="max-width:480px;">
  <div class="panel">
    <h2 style="margin-top:0;">Lupa Password</h2>
    <p class="sub">Masukkan email yang Anda daftarkan. Kami akan kirim tautan untuk mengatur ulang password.</p>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif
    @if($errors->any())
      <div class="alert-error">
        <ul style="margin:0;padding-left:18px;">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('kuesioner.lupa-password.kirim') }}" method="post">
      @csrf
      <div class="form-row"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required></div>
      <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
    </form>
    <p style="text-align:center;font-size:14px;color:var(--ink-soft);margin-top:16px;">
      <a href="{{ route('kuesioner.index') }}" style="color:var(--teal-deep);font-weight:700;">&larr; Kembali</a>
    </p>
  </div>
</main>
@endsection
