<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $ip,
        public string $browser,
        public string $platform,
        public string $location,
        public string $loginTime
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '⚠️ New Device Login Alert');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.suspicious-login');
    }
}
