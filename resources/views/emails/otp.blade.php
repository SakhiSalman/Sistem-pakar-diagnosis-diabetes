<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#F1F8F5;padding:30px;">
  <div style="max-width:420px;margin:0 auto;background:#fff;border-radius:14px;padding:32px;border:1px solid #D8E8E1;">
    <h2 style="color:#085041;">Klinik Sehat Bersama</h2>
    <p>Halo {{ $namaPasien }},</p>
    <p>Berikut kode verifikasi untuk mengisi kuesioner diagnosis mandiri diabetes:</p>
    <p style="font-size:28px;font-weight:700;letter-spacing:6px;color:#0F6E56;text-align:center;padding:16px;background:#E1F5EE;border-radius:10px;">{{ $kodeOtp }}</p>
    <p style="font-size:13px;color:#3E5850;">Kode berlaku 5 menit. Jangan bagikan kode ini kepada siapa pun.</p>
  </div>
</body>
</html>
