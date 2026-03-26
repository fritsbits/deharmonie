<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivityOverzicht extends Component
{
    public int $monthOffset = 0;

    public function mount(): void
    {
        $first = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->first();

        if ($first) {
            $this->monthOffset = (int) now()->startOfMonth()->diffInMonths(
                $first->datum->copy()->startOfMonth()
            );
        }
    }

    #[Computed]
    public function activeMonth(): Carbon
    {
        return Carbon::now()->startOfMonth()->addMonths($this->monthOffset);
    }

    #[Computed]
    public function activiteiten(): Collection
    {
        $query = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereYear('datum', $this->activeMonth->year)
            ->whereMonth('datum', $this->activeMonth->month)
            ->orderBy('datum')
            ->orderBy('startuur');

        if ($this->monthOffset === 0) {
            $query->where('datum', '>=', now()->startOfDay());
        }

        return $query->get();
    }

    #[Computed]
    public function monthHeading(): string
    {
        return ucfirst(
            $this->activeMonth->locale(app()->getLocale())->translatedFormat('F Y')
        );
    }

    #[Computed]
    public function hasPrev(): bool
    {
        return $this->monthOffset > 0;
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextMonthStart = $this->activeMonth->copy()->addMonth();

        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $nextMonthStart)
            ->exists();
    }

    public function prevMonth(): void
    {
        if ($this->hasPrev) {
            $this->monthOffset--;
        }
    }

    public function nextMonth(): void
    {
        if ($this->hasNext) {
            $this->monthOffset++;
        }
    }

    public function render(): View
    {
        return view('livewire.activity-overzicht');
    }
}
