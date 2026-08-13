<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#F1F8F5;padding:30px;">
  <div style="max-width:460px;margin:0 auto;background:#fff;border-radius:14px;padding:32px;border:1px solid #D8E8E1;">
    <h2 style="color:#085041;">Klinik Sehat Bersama</h2>
    <p>Halo {{ $namaPasien }},</p>
    <p>Hasil kuesioner diagnosis mandiri Anda (No. Registrasi: <strong>{{ $noRegistrasi }}</strong>) telah <strong>diperiksa dan divalidasi</strong> oleh staf klinik, dan diteruskan ke dokter untuk konsultasi lebih lanjut.</p>
    @if($tanggalKunjungan)
      <p>Jadwal kunjungan Anda: <strong>{{ \Carbon\Carbon::parse($tanggalKunjungan)->translatedFormat('d F Y') }}</strong></p>
    @endif
    @if($catatanAdmin)
      <p style="background:#E1F5EE;padding:14px;border-radius:10px;font-size:14px;color:#085041;">Catatan staf: {{ $catatanAdmin }}</p>
    @endif
    <p style="font-size:13px;color:#3E5850;">Mohon datang tepat waktu dan tunjukkan nomor registrasi Anda ke staf klinik.</p>
  </div>
</body>
</html>
