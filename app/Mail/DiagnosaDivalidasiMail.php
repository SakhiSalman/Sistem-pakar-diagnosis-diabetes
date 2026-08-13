<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DiagnosaDivalidasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $namaPasien;
    public string $noRegistrasi;
    public ?string $tanggalKunjungan;
    public ?string $catatanAdmin;

    public function __construct(string $namaPasien, string $noRegistrasi, ?string $tanggalKunjungan, ?string $catatanAdmin)
    {
        $this->namaPasien = $namaPasien;
        $this->noRegistrasi = $noRegistrasi;
        $this->tanggalKunjungan = $tanggalKunjungan;
        $this->catatanAdmin = $catatanAdmin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Data Anda Telah Divalidasi - Klinik Sehat Bersama');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.diagnosa_divalidasi');
    }
}
