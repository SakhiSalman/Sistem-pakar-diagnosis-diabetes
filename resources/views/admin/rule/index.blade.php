@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Dashboard admin</div>
  <div style="display:flex;align-items:center;gap:16px;font-size:14px;color:var(--ink-soft);">
    <a href="{{ route('admin.dashboard') }}" style="color:var(--ink-soft);text-decoration:none;">Antrean</a>
    <a href="{{ route('admin.gejala.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Gejala</a>
    <a href="{{ route('admin.kategori-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Kelola Lab</a>
    <a href="{{ route('admin.rule-cf.index') }}" style="color:var(--teal-deep);text-decoration:none;font-weight:700;">Rule CF</a>
    <a href="{{ route('admin.rule-cf-lab.index') }}" style="color:var(--ink-soft);text-decoration:none;">Rule Lab</a>
    <a href="{{ route('admin.logout') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">Keluar</a>
  </div>
</header>
<main style="max-width:100%;padding-left:24px;padding-right:24px;">
  <h1 style="font-size:23px;">Kelola Rule Forward Chaining + Certainty Factor</h1>
  <p class="sub" style="margin-bottom:20px;">Isi nilai <strong>MB</strong> dan <strong>MD</strong> hasil wawancara dokter untuk tiap kombinasi gejala &times; jenis diabetes. Nilai CF dihitung otomatis (CF = MB &minus; MD).</p>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <form action="{{ route('admin.rule-cf.simpan') }}" method="post">
    @csrf
    <div class="panel" style="overflow-x:auto;padding:0;">
      <table style="border-collapse:collapse;font-size:13px;min-width:720px;width:100%;">
        <thead>
          <tr style="background:var(--mist);">
            <th style="padding:14px;text-align:left;position:sticky;left:0;background:var(--mist);min-width:220px;">Gejala</th>
            @foreach($daftarJenis as $j)
              <th style="padding:14px;text-align:center;min-width:160px;">{{ $j->nama_jenis }}</th>
            @endforeach
          </tr>
          <tr style="background:var(--mist);border-bottom:1px solid var(--line);">
            <th style="position:sticky;left:0;background:var(--mist);"></th>
            @foreach($daftarJenis as $j)
              <th style="padding:0 14px 12px;font-weight:500;color:var(--ink-soft);">
                <span style="display:inline-block;width:48%;text-align:center;">MB</span>
                <span style="display:inline-block;width:48%;text-align:center;">MD</span>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($daftarGejala as $g)
            <tr style="border-top:1px solid var(--line);">
              <td style="padding:11px 14px;font-weight:700;position:sticky;left:0;background:var(--panel);">
                <span style="color:var(--teal-deep);">{{ $g->kode_gejala }}</span> — {{ $g->nama_gejala }}
              </td>
              @foreach($daftarJenis as $j)
                @php $cell = $matriks[$g->id_gejala][$j->id_jenis] ?? null; @endphp
                <td style="padding:9px 14px;text-align:center;">
                  <input type="number" step="0.01" min="0" max="1"
                    name="rule[{{ $g->id_gejala }}][{{ $j->id_jenis }}][mb]"
                    value="{{ $cell->nilai_mb ?? '' }}" placeholder="0.00"
                    style="width:62px;padding:7px;border:1px solid var(--line);border-radius:6px;text-align:center;font-size:13px;">
                  <input type="number" step="0.01" min="0" max="1"
                    name="rule[{{ $g->id_gejala }}][{{ $j->id_jenis }}][md]"
                    value="{{ $cell->nilai_md ?? '' }}" placeholder="0.00"
                    style="width:62px;padding:7px;border:1px solid var(--line);border-radius:6px;text-align:center;font-size:13px;">
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <button type="submit" class="btn btn-primary" style="width:auto;padding:16px 34px;position:sticky;bottom:16px;box-shadow:0 4px 16px rgba(0,0,0,0.18);">Simpan Seluruh Rule</button>
  </form>
</main>
@endsection
