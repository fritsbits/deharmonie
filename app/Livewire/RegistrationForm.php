<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Activiteit $activiteit;

    public function render()
    {
        if (! $this->activiteit->isBeschikbaar()) {
            return view('livewire.registration-form-full');
        }
        return view('livewire.registration-form');
    }
}
