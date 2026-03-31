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
    public int $weekOffset = 0;

    public function mount(): void
    {
        $first = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->first();

        if ($first) {
            $this->weekOffset = (int) now()->startOfWeek()->diffInWeeks(
                $first->datum->copy()->startOfWeek()
            );
        }
    }

    #[Computed]
    public function activeWeekStart(): Carbon
    {
        return Carbon::now()->startOfWeek()->addWeeks($this->weekOffset);
    }

    #[Computed]
    public function activeWeekEnd(): Carbon
    {
        return $this->activeWeekStart->copy()->endOfWeek();
    }

    #[Computed]
    public function activiteiten(): Collection
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereBetween('datum', [$this->activeWeekStart, $this->activeWeekEnd])
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get()
            ->groupBy(fn (Activiteit $a) => $a->datum->toDateString());
    }

    #[Computed]
    public function weekHeading(): string
    {
        $start = $this->activeWeekStart;
        $end = $this->activeWeekEnd;
        $locale = app()->getLocale();

        if ($start->month === $end->month) {
            return "{$start->day}–{$end->day} ".$start->locale($locale)->isoFormat('MMMM YYYY');
        }

        return $start->locale($locale)->isoFormat('D MMMM').' – '.$end->locale($locale)->isoFormat('D MMMM YYYY');
    }

    #[Computed]
    public function hasPrev(): bool
    {
        return $this->weekOffset > 0;
    }

    #[Computed]
    public function hasNext(): bool
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>', $this->activeWeekEnd)
            ->exists();
    }

    public function prevWeek(): void
    {
        if ($this->hasPrev) {
            $this->weekOffset--;
        }
    }

    public function nextWeek(): void
    {
        if ($this->hasNext) {
            $this->weekOffset++;
        }
    }

    public function render(): View
    {
        return view('livewire.activity-overzicht');
    }
}
