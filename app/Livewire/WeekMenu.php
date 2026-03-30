<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeekMenu extends Component
{
    public int $weekOffset = 0;

    public function mount(): void
    {
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);
        $now = Carbon::now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        foreach ($data['days'] as $day) {
            if (! $day['closed'] && $day['date'] >= $candidate->toDateString()) {
                $highlightedWeekStart = Carbon::parse($day['date'])->startOfWeek(Carbon::MONDAY);
                $currentWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
                $this->weekOffset = (int) ($currentWeekStart->diffInDays($highlightedWeekStart) / 7);
                break;
            }
        }
    }

    private function allDays(): array
    {
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

        return $data['days'];
    }

    private function weekStart(int $offset): Carbon
    {
        return Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($offset);
    }

    #[Computed]
    public function days(): array
    {
        $weekStart = $this->weekStart($this->weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return array_values(array_filter(
            $this->allDays(),
            fn ($day) => ! $day['closed']
                && $day['date'] >= $weekStart->toDateString()
                && $day['date'] <= $weekEnd->toDateString()
        ));
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

        if (empty($open)) {
            $ws = $this->weekStart($this->weekOffset);

            return $ws->locale($locale)->isoFormat('D MMM')
                .' – '
                .$ws->copy()->endOfWeek(Carbon::SUNDAY)->locale($locale)->isoFormat('D MMM YYYY');
        }

        $first = Carbon::parse($open[0]['date'])->locale($locale);
        $last = Carbon::parse(end($open)['date'])->locale($locale);

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

        foreach ($this->allDays() as $day) {
            if (! $day['closed'] && $day['date'] >= $candidate->toDateString()) {
                return $day['date'];
            }
        }

        return null;
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

        foreach ($this->allDays() as $day) {
            if (! $day['closed']
                && $day['date'] >= $prevStart->toDateString()
                && $day['date'] <= $prevEnd->toDateString()) {
                return true;
            }
        }

        return false;
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextStart = $this->weekStart($this->weekOffset + 1);
        $nextEnd = $nextStart->copy()->endOfWeek(Carbon::SUNDAY);

        foreach ($this->allDays() as $day) {
            if (! $day['closed']
                && $day['date'] >= $nextStart->toDateString()
                && $day['date'] <= $nextEnd->toDateString()) {
                return true;
            }
        }

        return false;
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
