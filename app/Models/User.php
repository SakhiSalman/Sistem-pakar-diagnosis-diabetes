<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Override notifikasi reset password bawaan Laravel supaya memakai
     * Mailable & template sendiri (konsisten dengan gaya email lain di app ini),
     * bukan notification channel bawaan.
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = route('admin.reset-password.form', [
            'token' => $token,
            'email' => $this->email,
        ]);

        try {
            Mail::to($this->email)->send(new ResetPasswordMail($this->name, $url));
        } catch (\Throwable $e) {
            // Jika SMTP gagal (mis. saat development), jangan sampai melempar
            // error ke pengguna. Cek storage/logs/laravel.log untuk detail.
        }
    }
}
