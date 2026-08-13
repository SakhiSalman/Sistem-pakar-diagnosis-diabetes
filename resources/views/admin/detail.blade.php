@extends('layouts.app')
@section('content')
<header class="topbar">
  <div class="brand"><span class="dot"></span>Dashboard admin</div>
  <div style="display:flex;align-items:center;gap:16px;font-size:14px;color:var(--ink-soft);">
    <span class="badge">{{ auth()->user()->role }}</span>
    <a href="{{ route('admin.logout') }}" class="btn btn-outline btn-sm" style="text-decoration:none;">Keluar</a>
  </div>
</header>
<main>
  <a href="{{ route('admin.dashboard') }}" class="back-link">&larr; Kembali ke daftar</a>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="panel">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
      <div>
        <h2 style="margin-bottom:2px;">{{ $diagnosa->pasien->nama }}</h2>
        <p class="sub" style="margin:0;">{{ $diagnosa->pasien->no_registrasi }} &middot; KTP {{ $diagnosa->pasien->no_ktp }}</p>
      </div>
      <span class="status-pill status-{{ $diagnosa->status }}">
        {{ $diagnosa->status === 'menunggu_konsultasi' ? 'Menunggu konsultasi' : ($diagnosa->status === 'divalidasi' ? 'Divalidasi' : 'Selesai') }}
      </span>
    </div>

    <div class="info-grid">
      <div><span class="label">Nomor WhatsApp</span><span class="value">{{ $diagnosa->pasien->no_hp }}</span></div>
      <div><span class="label">Waktu pengisian (WIB)</span><span class="value">{{ \Carbon\Carbon::parse($diagnosa->tanggal_diagnosa)->translatedFormat('d F Y, H:i') }}</span></div>
      <div><span class="label">Jenis kelamin</span><span class="value">{{ $diagnosa->pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
      <div><span class="label">Rencana kunjungan</span><span class="value">{{ $diagnosa->tanggal_kunjungan ? \Carbon\Carbon::parse($diagnosa->tanggal_kunjungan)->translatedFormat('d F Y') : '-' }}</span></div>
    </div>

    <p class="section-title">Gejala yang dipilih pasien</p>
    <div class="gejala-tags">
      @foreach($diagnosa->gejalaTerpilih as $g)
        <span class="gejala-tag">{{ $g->nama_gejala }}</span>
      @endforeach
    </div>

    <p class="section-title">Riwayat tes lab yang diisi pasien</p>
    @if($diagnosa->tesLab)
      @php $tl = $diagnosa->tesLab; @endphp
      <div class="info-grid">
        <div><span class="label">Berat badan saat kunjungan</span><span class="value">{{ $tl->berat_badan ? $tl->berat_badan.' kg' : '-' }}</span></div>
        <div><span class="label">Usia saat isi kuesioner</span><span class="value">{{ $tl->tanggal_lahir_saat_isi ? \Carbon\Carbon::parse($tl->tanggal_lahir_saat_isi)->age.' tahun' : '-' }}</span></div>
        <div>
          <span class="label">HbA1c</span>
          <span class="value">{{ $tl->sudah_tes_hba1c ? $tl->hasil_hba1c.'%' : 'Belum tes' }}</span>
        </div>
        <div>
          <span class="label">Antibodi GAD</span>
          <span class="value">{{ $tl->sudah_tes_antibodi ? ucfirst($tl->hasil_antibodi) : 'Belum tes' }}</span>
        </div>
        <div>
          <span class="label">C-Peptide</span>
          <span class="value">{{ $tl->sudah_tes_cpeptide ? str_replace('_', '/', ucfirst($tl->hasil_cpeptide)) : 'Belum tes' }}</span>
        </div>
        <div>
          <span class="label">TTGO (Toleransi Glukosa Oral)</span>
          <span class="value">
            @if($tl->sudah_tes_ttgo)
              Puasa {{ $tl->ttgo_puasa }} &middot; 1 jam {{ $tl->ttgo_1jam }} &middot; 2 jam {{ $tl->ttgo_2jam }} mg/dL
              @php
                $memenuhi = ($tl->ttgo_puasa >= 92) || ($tl->ttgo_1jam >= 180) || ($tl->ttgo_2jam >= 153);
              @endphp
              <br><span style="font-size:12px;font-weight:700;color:{{ $memenuhi ? '#B45309' : '#0F6E56' }};">{{ $memenuhi ? 'Memenuhi kriteria diabetes gestasional' : 'Tidak memenuhi kriteria' }}</span>
            @else
              Belum tes
            @endif
          </span>
        </div>
      </div>
      @if(!$tl->sudah_tes_antibodi && !$tl->sudah_tes_cpeptide)
        <p style="font-size:13px;color:var(--ink-soft);margin-top:4px;">Pasien belum tes Antibodi GAD maupun C-Peptide &mdash; hasil di bawah ini sebagian mengandalkan estimasi usia sebagai fallback, bukan hasil lab definitif.</p>
      @endif
    @else
      <p class="sub">Data tes lab tidak ditemukan untuk kunjungan ini.</p>
    @endif

    <p class="section-title">Hasil Forward Chaining + Certainty Factor</p>
    @php $first = true; @endphp
    @foreach($diagnosa->detail->sortByDesc('nilai_cf_akhir') as $h)
      <div class="hasil-card {{ $first ? 'utama' : '' }}">
        @if($first)<div class="tag-utama">Indikasi tertinggi</div>@endif
        <div class="hasil-top">
          <span class="nama">{{ $h->jenis->nama_jenis }}</span>
          <span class="persen">{{ $h->persentase }}%</span>
        </div>
        <div class="meter"><div class="meter-fill" style="width:{{ $h->persentase }}%;"></div></div>
      </div>
      @php $first = false; @endphp
    @endforeach

    <form action="{{ route('admin.dashboard.validasi', $diagnosa->id_diagnosa) }}" method="post" style="margin-top:24px;padding-top:24px;border-top:2px solid var(--line);">
      @csrf
      <div class="form-row">
        <label>Catatan validasi staf</label>
        <textarea name="catatan" rows="4" placeholder="Tuliskan hasil pengecekan ulang gejala saat pasien datang...">{{ $diagnosa->catatan_admin }}</textarea>
      </div>
      <div style="display:flex;gap:10px;">
        <button type="submit" name="aksi" value="simpan" class="btn btn-outline" style="margin-top:0;">Simpan catatan</button>
        <button type="submit" name="aksi" value="teruskan" class="btn btn-forward" style="flex:1;border:none;border-radius:12px;font-weight:700;cursor:pointer;">Validasi & teruskan ke dokter</button>
      </div>
    </form>
  </div>
</main>
@endsection
