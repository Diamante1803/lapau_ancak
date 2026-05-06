<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $magicUrl;
    public string $namaPembeli;

    public function __construct(string $magicUrl, string $namaPembeli)
    {
        $this->magicUrl     = $magicUrl;
        $this->namaPembeli  = $namaPembeli;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔑 Link Verifikasi Lelang - Lapau Ancak',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.magic-link',
            with: [
                'magicUrl'    => $this->magicUrl,
                'namaPembeli' => $this->namaPembeli,
            ],
        );
    }
}