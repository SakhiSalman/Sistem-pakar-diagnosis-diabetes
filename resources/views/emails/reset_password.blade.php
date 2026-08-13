<!DOCTYPE html>
<html>
<body style="font-family:sans-serif;background:#F1F8F5;padding:30px;">
  <div style="max-width:420px;margin:0 auto;background:#fff;border-radius:14px;padding:32px;border:1px solid #D8E8E1;">
    <h2 style="color:#085041;">Klinik Sehat Bersama</h2>
    <p>Halo {{ $namaTujuan }},</p>
    <p>Kami menerima permintaan untuk mengatur ulang password akun Anda. Klik tombol di bawah untuk membuat password baru:</p>
    <p style="text-align:center;margin:28px 0;">
      <a href="{{ $resetUrl }}" style="display:inline-block;background:#0F6E56;color:#fff;text-decoration:none;padding:14px 28px;border-radius:10px;font-weight:700;">Atur Ulang Password</a>
    </p>
    <p style="font-size:13px;color:#3E5850;">Link ini berlaku selama 60 menit. Jika Anda tidak merasa meminta reset password, abaikan saja email ini &mdash; password Anda tidak akan berubah.</p>
    <p style="font-size:12px;color:#7C8F88;word-break:break-all;">Atau salin tautan berikut ke browser: {{ $resetUrl }}</p>
  </div>
</body>
</html>
