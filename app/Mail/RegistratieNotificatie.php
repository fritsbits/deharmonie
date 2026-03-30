<?php

namespace App\Mail;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
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
        $subject = 'Nieuwe inschrijving: '
            .$this->activiteit->titel_nl
            .' — '
            .$this->activiteit->datum->locale('nl')->isoFormat('dddd D MMMM');

        return new Envelope(
            to: [new Address(config('mail.admin_address', 'animatie@deharmonie.be'))],
            replyTo: [new Address($this->verzoek->email, $this->verzoek->naam)],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registratie-notificatie');
    }
}
