<?php

namespace App\Livewire;

use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeekMenu extends Component
{
    public int $weekOffset = 0;

    public function mount(): void
    {
        $now = Carbon::now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        $day = WeekMenuDag::where('closed', false)
            ->where('date', '>=', $candidate->toDateString())
            ->orderBy('date')
            ->first();

        if ($day) {
            $highlightedWeekStart = $day->date->startOfWeek(Carbon::MONDAY);
            $currentWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            $this->weekOffset = (int) ($currentWeekStart->diffInDays($highlightedWeekStart) / 7);
        }
    }

    private function weekStart(int $offset): Carbon
    {
        return Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($offset);
    }

    #[Computed]
    public function days(): Collection
    {
        $weekStart = $this->weekStart($this->weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date')
            ->get();
    }

    #[Computed]
    public function weekHeading(): string
    {
        if ($this->weekOffset === 0) {
            return __('weekmenu.this_week');
        }
        if ($this->weekOffset === 1) {
            return __('weekmenu.next_week');
        }
        if ($this->weekOffset === -1) {
            return __('weekmenu.prev_week');
        }

        return $this->weekLabel;
    }

    #[Computed]
    public function weekLabel(): string
    {
        $locale = app()->getLocale();
        $open = $this->days;

        if ($open->isEmpty()) {
            $ws = $this->weekStart($this->weekOffset);

            return $ws->locale($locale)->isoFormat('D MMM')
                .' – '
                .$ws->copy()->endOfWeek(Carbon::SUNDAY)->locale($locale)->isoFormat('D MMM YYYY');
        }

        $first = $open->first()->date->locale($locale);
        $last = $open->last()->date->locale($locale);

        if ($first->month === $last->month) {
            return $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY');
        }

        return $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY');
    }

    #[Computed]
    public function highlightedDate(): ?string
    {
        $now = Carbon::now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        $day = WeekMenuDag::where('closed', false)
            ->where('date', '>=', $candidate->toDateString())
            ->orderBy('date')
            ->first();

        return $day?->date->toDateString();
    }

    #[Computed]
    public function highlightedIsToday(): bool
    {
        return $this->highlightedDate !== null
            && $this->highlightedDate === Carbon::now()->toDateString();
    }

    #[Computed]
    public function highlightedIsTomorrow(): bool
    {
        return $this->highlightedDate !== null
            && $this->highlightedDate === Carbon::now()->addDay()->toDateString();
    }

    #[Computed]
    public function hasPrev(): bool
    {
        $prevStart = $this->weekStart($this->weekOffset - 1);
        $prevEnd = $prevStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->exists();
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextStart = $this->weekStart($this->weekOffset + 1);
        $nextEnd = $nextStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$nextStart->toDateString(), $nextEnd->toDateString()])
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
        return view('livewire.week-menu');
    }
}
