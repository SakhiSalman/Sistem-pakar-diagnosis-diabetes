@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  <div class="panel confirm-box">
    <div class="confirm-icon">&#10003;</div>
    <h2>Data berhasil disimpan</h2>
    <p class="sub">Nomor registrasi Anda: <strong>{{ $noRegistrasi }}</strong><br>Tunjukkan nomor ini ke staf klinik saat Anda datang berkonsultasi.</p>
  </div>
  <a href="{{ route('kuesioner.index') }}" class="btn btn-outline" style="display:block;text-align:center;text-decoration:none;">Isi kuesioner baru</a>
</main>
@endsection
