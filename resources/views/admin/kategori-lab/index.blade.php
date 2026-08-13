@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Dashboard admin</div>
  <div style="display:flex;align-items:center;gap:16px;font-size:14px;color:var(--ink-soft);">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--ink-soft);text-decoration:none;">Antrean</a>
    <a href="{{ route('admin.gejala.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Gejala</a>
    <a href="{{ route('admin.kategori-lab.index') }}" style="color:var(--teal-deep);text-decoration:none;font-weight:700;">Kelola Lab</a>
    <a href="{{ route('admin.rule-cf.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule CF</a>
    <a href="{{ route('admin.rule-cf-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule Lab</a>
    <a href="{{ route('admin.logout') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">Keluar</a>
  </div>
</header>
<main>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
    <h1 style="font-size:23px;margin:0;">Kelola Kategori Tes Lab</h1>
    <a href="{{ route('admin.kategori-lab.tambah') }}" class="btn btn-primary" style="width:auto;text-decoration:none;">+ Tambah Kategori</a>
  </div>
  <p class="sub" style="margin-bottom:18px;">Kategori di sini muncul sebagai baris di halaman <strong>Rule Lab</strong> (tempat isi nilai CF) dan dipakai untuk mengelompokkan hasil tes lab pasien. Label, keterangan, dan urutan bisa diedit bebas lewat halaman ini.</p>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

  <div class="panel" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <thead>
        <tr style="background:var(--mist);text-align:left;">
          <th style="padding:14px 18px;">Kode</th>
          <th style="padding:14px 18px;">Label</th>
          <th style="padding:14px 18px;">Sumber</th>
          <th style="padding:14px 18px;">Status</th>
          <th style="padding:14px 18px;width:230px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($daftar as $k)
          <tr style="border-top:1px solid var(--line);{{ !$k->aktif ? 'opacity:0.5;' : '' }}">
            <td style="padding:14px 18px;font-weight:700;color:var(--teal-deep);font-family:monospace;font-size:12.5px;">{{ $k->kode }}</td>
            <td style="padding:14px 18px;">
              {{ $k->label }}
              @if($k->keterangan)
                <div style="font-size:12px;color:var(--ink-soft);margin-top:2px;">{{ $k->keterangan }}</div>
              @endif
            </td>
            <td style="padding:14px 18px;font-size:12.5px;">
              @if($k->bawaan_sistem)
                <span style="background:var(--teal-tint);color:var(--teal-deep);padding:3px 9px;border-radius:20px;font-weight:700;">Bawaan sistem</span>
              @else
                <span style="background:var(--mist);color:var(--ink-soft);padding:3px 9px;border-radius:20px;font-weight:700;">Kustom admin</span>
              @endif
            </td>
            <td style="padding:14px 18px;font-size:12.5px;">
              @if($k->aktif)
                <span style="color:#0F6E56;font-weight:700;">Aktif</span>
              @else
                <span style="color:var(--ink-soft);font-weight:700;">Nonaktif</span>
              @endif
            </td>
            <td style="padding:14px 18px;font-size:13px;">
              <a href="{{ route('admin.kategori-lab.edit', $k->id) }}" style="color:var(--teal-deep);text-decoration:none;font-weight:700;">Edit</a>
              &nbsp;&middot;&nbsp;
              <form action="{{ route('admin.kategori-lab.toggle', $k->id) }}" method="post" style="display:inline;">
                @csrf
                <button type="submit" style="background:none;border:none;padding:0;color:{{ $k->aktif ? 'var(--coral-deep)' : '#0F6E56' }};font-weight:700;font-size:13px;cursor:pointer;">{{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}</button>
              </form>
              @if(!$k->bawaan_sistem)
                &nbsp;&middot;&nbsp;
                <a href="{{ route('admin.kategori-lab.hapus', $k->id) }}" style="color:var(--coral-deep);text-decoration:none;font-weight:700;" onclick="return confirm('Hapus kategori ini permanen beserta nilai CF-nya?')">Hapus</a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="disclaimer" style="margin-top:20px;">
    <strong>Catatan penting:</strong> kategori berlabel "Bawaan sistem" (HbA1c, Antibodi GAD, C-Peptide, TTGO, faktor usia) terhubung langsung ke logika perhitungan CF di kode program, jadi hanya bisa <em>dinonaktifkan</em>, bukan dihapus permanen — supaya sistem tidak rusak tanpa sengaja. Kategori baru yang Anda tambahkan sendiri ("Kustom admin") bisa dihapus bebas, tapi perlu diingat: kategori baru itu <u>tidak otomatis ikut dihitung</u> ke skor diagnosis kecuali developer menambahkan logika pemicunya di sistem terlebih dulu — jadi cocok dipakai untuk catatan/referensi tambahan, bukan langsung menghasilkan perhitungan baru.
  </div>
</main>
@endsection
