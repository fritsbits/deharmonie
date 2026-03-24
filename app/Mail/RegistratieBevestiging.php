<?php

namespace App\Mail;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistratieBevestiging extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Deelnameverzoek $verzoek,
        public readonly Activiteit $activiteit,
        public readonly string $taal,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->taal === 'fr'
            ? 'Confirmation d\'inscription : ' . $this->activiteit->titel_fr
            : 'Bevestiging inschrijving: ' . $this->activiteit->titel_nl;

        return new Envelope(
            to: $this->verzoek->email,
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registratie-bevestiging');
    }
}
