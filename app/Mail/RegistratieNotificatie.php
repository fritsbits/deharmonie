<?php

namespace App\Mail;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class RegistratieNotificatie extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Deelnameverzoek $verzoek,
        public readonly Activiteit $activiteit,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address(config('mail.admin_address', 'animatie@deharmonie.be'))],
            subject: 'Nieuwe inschrijving: ' . $this->activiteit->titel_nl,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registratie-notificatie');
    }
}
