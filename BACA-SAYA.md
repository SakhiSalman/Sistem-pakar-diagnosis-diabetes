# Sistem Pakar Diagnosis Diabetes — Laravel 13

Dibangun dengan **Laravel 13 + PHP 8.2/8.3** menggunakan metode **Forward
Chaining + Certainty Factor**. Kode aplikasi (Controllers, Models, Views,
Migration, Seeder) sudah lengkap. Karena environment pembuatan kode ini
tidak memiliki PHP/Composer/MySQL, instalasi dependency inti (folder
`vendor/`) perlu dilakukan di Laragon Anda.

## 1. PENTING — Upgrade PHP di Laragon dulu
PHP 8.1.10 Anda **tidak cukup** untuk Laravel 13 (minimal PHP 8.2). Di
Laragon: klik kanan tray icon Laragon → **PHP** → pilih/download versi 8.2
atau 8.3 → jadikan default. Cek dengan `php -v` di terminal Laragon.

## 2. Instalasi

```bash
# Di folder www Laragon:
composer create-project laravel/laravel nama-project "13.*"
cd nama-project
```

Salin **seluruh isi** folder `app/`, `database/`, `resources/`, dan
`routes/` dari paket ini ke folder project Laravel yang baru dibuat
(timpa file yang sama, terutama `routes/web.php` dan
`app/Models/User.php`).

## 3. Konfigurasi Database

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_diagnosa_diabetes
DB_USERNAME=root
DB_PASSWORD=
```
Buat database kosong `db_diagnosa_diabetes` di HeidiSQL/phpMyAdmin (Laragon).

### Konfigurasi Email (untuk OTP)
Untuk demo cepat tanpa SMTP asli, di `.env` set:
```
MAIL_MAILER=log
```
Kode OTP akan tercatat di `storage/logs/laravel.log` (dan tetap
ditampilkan langsung di halaman verifikasi untuk kebutuhan demo/proposal).

Untuk email sungguhan nanti (opsional), ganti ke SMTP Gmail/Mailtrap:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email_anda@gmail.com
MAIL_PASSWORD=app_password_gmail
MAIL_ENCRYPTION=tls
```

## 4. Migrasi & Seeder

```bash
php artisan migrate
php artisan db:seed
```

## 5. Jalankan Server

```bash
php artisan serve
```

- **Kuesioner pasien (publik):** http://localhost:8000/kuesioner
- **Dashboard admin/staf:** http://localhost:8000/admin/login
  - Login staf: `staf@klinik.test` / `password`
  - Login dokter: `dokter@klinik.test` / `password`

## Struktur Fitur

| Fitur | Lokasi Kode |
|---|---|
| Mesin Forward Chaining + Certainty Factor | `app/Services/DiagnosisEngine.php` |
| Kuesioner publik + login nama/email + OTP email | `app/Http/Controllers/Kuesioner/KuesionerController.php` |
| Email OTP | `app/Mail/OtpMail.php` + `resources/views/emails/otp.blade.php` |
| Dashboard admin | `app/Http/Controllers/Admin/DashboardController.php` |
| Login staf/dokter (Laravel Auth bawaan) | `app/Http/Controllers/Admin/AuthController.php` |
| Kelola gejala (CRUD) | `app/Http/Controllers/Admin/GejalaController.php` |
| Kelola rule Forward Chaining + CF | `app/Http/Controllers/Admin/RuleCfController.php` |
| Struktur database | `database/migrations/` |
| Data dummy | `database/seeders/DatabaseSeeder.php` |

## Update Terbaru (v2)

1. **Timezone sudah WIB** (`config/app.php` → `Asia/Jakarta`).
2. **Pasien lama** bisa login pakai nama + password (tanpa OTP lagi) di halaman utama kuesioner.
3. **Pasien baru**: nama + email → OTP email → isi gejala → isi KTP (wajib 16 digit angka), jenis kelamin, tanggal lahir, tanggal kunjungan (wajib besok atau setelahnya, tidak bisa hari ini), dan buat password untuk kunjungan berikutnya.
4. Halaman verifikasi OTP: ada **tombol kembali** (ganti nama/email) dan **timer 5 menit**.
5. **Staf mendaftar akun sendiri** di `/admin/register` (bukan akun dummy lagi). Akun dokter dihapus dari seeder — cukup 1 role `admin_staf`.
6. Saat staf klik **"Validasi & teruskan ke dokter"**, sistem otomatis mengirim **email notifikasi** ke pasien (lihat `app/Mail/DiagnosaDivalidasiMail.php`).

**Migration tambahan yang perlu dijalankan** (kalau sebelumnya sudah pernah migrate):
```bash
php artisan migrate
```
Ini akan menambah kolom `password` di tabel `pasien` dan `tanggal_kunjungan` di tabel `diagnosa`.

Jika database lama sudah berisi data dan ingin mulai bersih, bisa juga jalankan:
```bash
php artisan migrate:fresh --seed
```
(perhatian: ini akan menghapus semua data lama)


1. **Data gejala & nilai CF masih dummy** — wajib diganti setelah
   wawancara dokter, lewat halaman **Kelola Gejala** dan **Rule CF** di
   dashboard admin (tidak perlu edit kode).
2. Login pasien memakai **nama + email**, tanpa password — verifikasi
   lewat kode OTP 6 digit yang dikirim ke email (lihat `MAIL_MAILER` di
   atas untuk pengaturan pengiriman).
3. Login staf/dokter memakai sistem Auth bawaan Laravel (email + password
   tabel `users`, sudah ditambah kolom `role`).
4. Sebelum sidang, jalankan aplikasi ini di laptop Anda agar bisa
   didemonstrasikan langsung ke dosen pembimbing/dokter.
