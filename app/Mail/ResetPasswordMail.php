<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $namaTujuan;
    public string $resetUrl;

    public function __construct(string $namaTujuan, string $resetUrl)
    {
        $this->namaTujuan = $namaTujuan;
        $this->resetUrl = $resetUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Reset Password - Klinik Sehat Bersama');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reset_password');
    }
}
