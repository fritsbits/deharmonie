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
            ->where('datum', '>=', now()->toDateString())
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
        if ($this->weekOffset <= 0) {
            return false;
        }

        $currentWeekStart = Carbon::now()->startOfWeek();
        $activeStart = $currentWeekStart->copy()->addWeeks($this->weekOffset);

        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $currentWeekStart)
            ->where('datum', '<', $activeStart)
            ->exists();
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextWeekStart = Carbon::now()->startOfWeek()->addWeeks($this->weekOffset + 1);

        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $nextWeekStart)
            ->exists();
    }

    public function prevWeek(): void
    {
        if (! $this->hasPrev) {
            return;
        }

        $currentWeekStart = Carbon::now()->startOfWeek();
        $activeStart = $currentWeekStart->copy()->addWeeks($this->weekOffset);

        $prev = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $currentWeekStart)
            ->where('datum', '<', $activeStart)
            ->orderByDesc('datum')
            ->first();

        if ($prev) {
            $this->weekOffset = (int) $currentWeekStart->diffInWeeks(
                $prev->datum->copy()->startOfWeek()
            );
        }
    }

    public function nextWeek(): void
    {
        if (! $this->hasNext) {
            return;
        }

        $currentWeekStart = Carbon::now()->startOfWeek();
        $nextWeekStart = $currentWeekStart->copy()->addWeeks($this->weekOffset + 1);

        $next = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $nextWeekStart)
            ->orderBy('datum')
            ->first();

        if ($next) {
            $this->weekOffset = (int) $currentWeekStart->diffInWeeks(
                $next->datum->copy()->startOfWeek()
            );
        }
    }

    public function render(): View
    {
        return view('livewire.activity-overzicht');
    }
}
