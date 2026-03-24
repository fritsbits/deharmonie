<?php

namespace App\Livewire;

use App\Mail\RegistratieBevestiging;
use App\Mail\RegistratieNotificatie;
use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Activiteit $activiteit;

    #[Validate('required|min:2|max:255')]
    public string $naam = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|max:50')]
    public string $telefoon = '';

    #[Validate('nullable|max:1000')]
    public string $bericht = '';

    public string $honeypot = '';  // must remain empty

    public bool $submitted = false;

    public function submit(): void
    {
        // Honeypot check — silently abort for bots
        if ($this->honeypot !== '') {
            return;
        }

        // Rate limiting
        $key = 'registration:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', __('forms.rate_limit'));
            return;
        }
        RateLimiter::hit($key, 60);

        $this->validate();

        // Capacity check
        if (! $this->activiteit->isBeschikbaar()) {
            return;
        }

        $verzoek = Deelnameverzoek::create([
            'activiteit_id' => $this->activiteit->id,
            'naam' => $this->naam,
            'email' => $this->email,
            'telefoon' => $this->telefoon ?: null,
            'bericht' => $this->bericht ?: null,
            'status' => 'te_contacteren',
        ]);

        $locale = app()->getLocale();

        Mail::send(new RegistratieNotificatie($verzoek, $this->activiteit));
        Mail::send(new RegistratieBevestiging($verzoek, $this->activiteit, $locale));

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.registration-form');
    }
}
