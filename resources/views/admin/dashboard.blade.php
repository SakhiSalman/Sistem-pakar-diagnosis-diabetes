@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Dashboard admin</div>
  <div style="display:flex;align-items:center;gap:16px;font-size:14px;color:var(--ink-soft);">
    <a href="{{ route('admin.gejala.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Gejala</a>
    <a href="{{ route('admin.kategori-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Lab</a>
    <a href="{{ route('admin.rule-cf.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule CF</a>
    <a href="{{ route('admin.rule-cf-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule Lab</a>
    <span class="badge">{{ auth()->user()->role }}</span>
    <span>{{ auth()->user()->name }}</span>
    <a href="{{ route('admin.logout') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">Keluar</a>
  </div>
</header>
<main>
  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form class="search-panel" method="get" action="{{ route('admin.dashboard') }}">
    <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari berdasarkan nomor registrasi, KTP, atau nama...">
    <button type="submit" class="btn btn-primary" style="width:auto;">Cari</button>
  </form>

  <p class="section-title" style="margin-top:0;">Antrean pasien</p>

  @forelse($daftar as $d)
    <a href="{{ route('admin.dashboard.detail', $d->id_diagnosa) }}" class="queue-item">
      <div>
        <span class="qi-nama">{{ $d->pasien->nama }}</span>
        <span class="qi-meta">{{ $d->pasien->no_registrasi }} &middot; {{ \Carbon\Carbon::parse($d->tanggal_diagnosa)->translatedFormat('d M Y, H:i') }} WIB</span>
      </div>
      <span class="status-pill status-{{ $d->status }}">
        {{ $d->status === 'menunggu_konsultasi' ? 'Menunggu konsultasi' : ($d->status === 'divalidasi' ? 'Divalidasi' : 'Selesai') }}
      </span>
    </a>
  @empty
    <p style="color:var(--ink-soft);font-size:14px;text-align:center;padding:30px 0;">Tidak ada data pasien ditemukan.</p>
  @endforelse

  @if($daftar->hasPages())
    <div style="display:flex;justify-content:center;gap:6px;margin-top:22px;flex-wrap:wrap;">
      {{ $daftar->onEachSide(1)->links() }}
    </div>
  @endif

  <p class="dummy-note">Data pasien di dashboard ini tersimpan sungguhan dari kuesioner publik, namun basis pengetahuan gejala/CF masih perlu divalidasi pakar.</p>
</main>
@endsection
