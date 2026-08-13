@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Klinik Sehat Bersama</div>
  <div class="badge">Sistem pakar diagnosis diabetes</div>
</header>
<main>
  <div class="hero" style="margin-bottom:22px;">
    <div class="eyebrow">Hasil indikasi</div>
    <h1 style="font-size:25px;">Tingkat keyakinan berdasarkan gejala Anda</h1>
  </div>

  @php $first = true; @endphp
  @foreach($hasil as $idJenis => $cf)
    @php $persen = round($cf * 100, 1); @endphp
    <div class="hasil-card {{ $first ? 'utama' : '' }}">
      @if($first)<div class="tag-utama">Indikasi tertinggi</div>@endif
      <div class="hasil-top">
        <span class="nama">{{ $jenisMap[$idJenis]->nama_jenis }}</span>
        <span class="persen">{{ $persen }}%</span>
      </div>
      <div class="meter"><div class="meter-fill" style="width:{{ $persen }}%;"></div></div>
    </div>
    @php $first = false; @endphp
  @endforeach

  <div class="panel" style="margin-top:22px;">
    <h2>Rekomendasi untuk {{ $jenisUtama->nama_jenis }}</h2>
    <p class="sub" style="margin-bottom:0;">{{ $jenisUtama->rekomendasi }}</p>
  </div>

  @if($sarankanTesLanjutan)
    <div class="disclaimer" style="margin-top:18px;">
      <strong>Saran pemeriksaan lanjutan:</strong> Gejala diabetes tipe 1 dan tipe 2 sulit dibedakan secara pasti hanya dari gejala. Untuk memastikan jenis diabetes Anda secara lebih akurat, disarankan melakukan salah satu dari 2 tes berikut (cukup salah satu saja, tidak harus keduanya):
      <ul style="margin:8px 0 0;padding-left:20px;">
        <li>Tes Antibodi (GAD Autoantibodies), atau</li>
        <li>Tes C-Peptide</li>
      </ul>
    </div>
  @endif

  <a href="{{ route('kuesioner.jadwal') }}" class="btn btn-primary" style="display:block;text-align:center;text-decoration:none;">Simpan hasil dan jadwalkan konsultasi</a>
  <a href="{{ route('kuesioner.gejala') }}" class="btn btn-outline" style="display:block;text-align:center;text-decoration:none;">Isi ulang kuesioner</a>
</main>
@endsection
