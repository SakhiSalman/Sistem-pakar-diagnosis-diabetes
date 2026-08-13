@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Dashboard admin</div>
  <div style="display:flex;align-items:center;gap:16px;font-size:14px;color:var(--ink-soft);">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--ink-soft);text-decoration:none;">Antrean</a>
    <a href="{{ route('admin.gejala.index') }}" style="color:var(--teal-deep);text-decoration:none;font-weight:700;">Kelola Gejala</a>
    <a href="{{ route('admin.kategori-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Lab</a>
    <a href="{{ route('admin.rule-cf.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule CF</a>
    <a href="{{ route('admin.rule-cf-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule Lab</a>
    <a href="{{ route('admin.logout') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">Keluar</a>
  </div>
</header>
<main>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
    <h1 style="font-size:23px;margin:0;">Kelola Data Gejala</h1>
    <a href="{{ route('admin.gejala.tambah') }}" class="btn btn-primary" style="width:auto;text-decoration:none;">+ Tambah Gejala</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <div class="panel" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <thead>
        <tr style="background:var(--mist);text-align:left;">
          <th style="padding:14px 18px;">Kode</th>
          <th style="padding:14px 18px;">Nama Gejala</th>
          <th style="padding:14px 18px;width:150px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($daftar as $g)
          <tr style="border-top:1px solid var(--line);">
            <td style="padding:14px 18px;font-weight:700;color:var(--teal-deep);">{{ $g->kode_gejala }}</td>
            <td style="padding:14px 18px;">{{ $g->nama_gejala }}</td>
            <td style="padding:14px 18px;">
              <a href="{{ route('admin.gejala.edit', $g->id_gejala) }}" style="color:var(--teal-deep);text-decoration:none;font-weight:700;font-size:13px;">Edit</a>
              &nbsp;&middot;&nbsp;
              <a href="{{ route('admin.gejala.hapus', $g->id_gejala) }}" style="color:var(--coral-deep);text-decoration:none;font-weight:700;font-size:13px;" onclick="return confirm('Hapus gejala ini beserta rule terkait?')">Hapus</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</main>
@endsection
