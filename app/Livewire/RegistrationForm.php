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

    protected function messages(): array
    {
        return [
            'naam.required' => __('forms.naam_required'),
            'naam.min' => __('forms.naam_min'),
            'email.required' => __('forms.email_required'),
            'email.email' => __('forms.invalid_email'),
        ];
    }

    public function submit(): void
    {
        if ($this->honeypot !== '') {
            return;
        }

        $key = 'registration:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', __('forms.rate_limit'));

            return;
        }
        RateLimiter::hit($key, 60);

        $this->validate();

        $verzoek = Deelnameverzoek::create([
            'activiteit_id' => $this->activiteit->id,
            'naam' => $this->naam,
            'email' => $this->email,
            'telefoon' => $this->telefoon ?: null,
            'bericht' => $this->bericht ?: null,
        ]);

        $locale = app()->getLocale();

        Mail::send(new RegistratieBevestiging($verzoek, $this->activiteit, $locale));
        Mail::later(now()->addSeconds(2), new RegistratieNotificatie($verzoek, $this->activiteit));

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.registration-form');
    }
}
