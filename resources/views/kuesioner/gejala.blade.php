@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  @if(session('error'))
    <div class="alert-error">{{ session('error') }}</div>
  @endif

  <form action="{{ route('kuesioner.proses') }}" method="post">
    @csrf
    <div class="panel">
      <h2>Pilih gejala yang Anda alami</h2>
      <p class="sub">Centang semua gejala yang sesuai dengan kondisi Anda saat ini.</p>

      @foreach($daftarGejala as $g)
        <div class="gejala-item">
          <input type="checkbox" id="g{{ $g->id_gejala }}" name="gejala[]" value="{{ $g->id_gejala }}">
          <label for="g{{ $g->id_gejala }}">{{ $g->nama_gejala }}</label>
        </div>
      @endforeach
    </div>
    <button type="submit" class="btn btn-primary">Lihat hasil diagnosis</button>
  </form>
</main>
@endsection
