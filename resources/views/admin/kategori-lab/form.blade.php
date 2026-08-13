@extends('layouts.app')
@section('content')
<header class="topbar"><div class="brand"><span class="dot"></span>Dashboard admin</div></header>
<main style="max-width:560px;">
  <a href="{{ route('admin.kategori-lab.index') }}" class="back-link">&larr; Kembali ke daftar kategori</a>
  <div class="panel">
    <h2>{{ $mode === 'tambah' ? 'Tambah Kategori Tes Lab' : 'Edit Kategori Tes Lab' }}</h2>
    @if($errors->any())
      <div class="alert-error">
        <ul style="margin:0;padding-left:18px;">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <form action="{{ $mode === 'tambah' ? route('admin.kategori-lab.simpan') : route('admin.kategori-lab.update', $data->id) }}" method="post">
      @csrf

      @if($mode === 'tambah')
        <div class="form-row">
          <label>Kode (unik, huruf kecil & underscore, tidak bisa diubah lagi setelah disimpan)</label>
          <input type="text" name="kode" value="{{ old('kode') }}" placeholder="contoh: skrining_baru_xyz" required pattern="[a-z0-9_]+">
        </div>
      @else
        <div class="form-row">
          <label>Kode</label>
          <input type="text" value="{{ $data->kode }}" readonly style="background:var(--teal-tint);font-family:monospace;">
        </div>
      @endif

      <div class="form-row">
        <label>Label (ditampilkan di halaman Rule Lab)</label>
        <input type="text" name="label" value="{{ $data->label ?? old('label') }}" placeholder="contoh: Tes Urine Glukosa: Positif" required>
      </div>

      <div class="form-row">
        <label>Keterangan (opsional)</label>
        <textarea name="keterangan" rows="3" placeholder="Penjelasan singkat untuk admin/dokter">{{ $data->keterangan ?? old('keterangan') }}</textarea>
      </div>

      <div class="form-row">
        <label>Urutan tampil (angka kecil tampil duluan)</label>
        <input type="number" name="urutan" value="{{ $data->urutan ?? old('urutan', 0) }}">
      </div>

      @if($mode === 'tambah')
        <div class="disclaimer" style="margin-bottom:18px;">Kategori baru ini akan muncul di halaman Rule Lab untuk diisi nilai CF-nya, tapi <strong>belum otomatis dihitung</strong> ke skor diagnosis kecuali developer menambahkan logika pemicunya (kapan kategori ini "menyala" untuk seorang pasien) di kode program terlebih dulu.</div>
      @endif

      <button type="submit" class="btn btn-primary">{{ $mode === 'tambah' ? 'Simpan Kategori' : 'Perbarui Kategori' }}</button>
    </form>
  </div>
</main>
@endsection
