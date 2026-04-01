<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivityFilter extends Component
{
    #[Computed]
    public function activiteiten(): Collection
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->orderBy('startuur')
            ->limit(5)
            ->with('media')
            ->get();
    }

    public function render()
    {
        return view('livewire.activity-filter');
    }
}
