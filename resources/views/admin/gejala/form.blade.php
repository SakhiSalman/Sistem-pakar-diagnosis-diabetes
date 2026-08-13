@extends('layouts.app')
@section('content')
<header class="topbar"><div class="brand"><span class="dot"></span>Dashboard admin</div></header>
<main style="max-width:560px;">
  <a href="{{ route('admin.gejala.index') }}" class="back-link">&larr; Kembali ke daftar gejala</a>
  <div class="panel">
    <h2>{{ $mode === 'tambah' ? 'Tambah Gejala Baru' : 'Edit Gejala' }}</h2>
    @if($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

    <form action="{{ $mode === 'tambah' ? route('admin.gejala.simpan') : route('admin.gejala.update', $data->id_gejala) }}" method="post">
      @csrf
      <div class="form-row">
        <label>Kode Gejala</label>
        <input type="text" name="kode_gejala" value="{{ $data->kode_gejala ?? old('kode_gejala') }}" placeholder="G13" required>
      </div>
      <div class="form-row">
        <label>Nama Gejala</label>
        <input type="text" name="nama_gejala" value="{{ $data->nama_gejala ?? old('nama_gejala') }}" placeholder="Deskripsi gejala" required>
      </div>
      <button type="submit" class="btn btn-primary">{{ $mode === 'tambah' ? 'Simpan Gejala' : 'Perbarui Gejala' }}</button>
    </form>
  </div>
</main>
@endsection
