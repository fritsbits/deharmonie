<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Collection;

class ActivityFilter extends Component
{
    public int $year;
    public int $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    #[Computed]
    public function activiteiten(): Collection
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereYear('datum', $this->year)
            ->whereMonth('datum', $this->month)
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get();
    }

    #[Computed]
    public function monthLabel(): string
    {
        return Carbon::create($this->year, $this->month, 1)
            ->locale(app()->getLocale())
            ->isoFormat('MMMM YYYY');
    }

    public function render()
    {
        return view('livewire.activity-filter');
    }
}
