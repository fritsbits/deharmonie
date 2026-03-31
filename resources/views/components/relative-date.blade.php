@props(['datum'])
@php
    $diff = now()->startOfDay()->diffInDays($datum->startOfDay(), false);
    if ($diff == 0)      $label = __('activities.date_today');
    elseif ($diff == 1)  $label = __('activities.date_tomorrow');
    elseif ($diff <= 13) $label = ucfirst($datum->locale(app()->getLocale())->isoFormat('dddd'));
    else                 $label = ucfirst($datum->locale(app()->getLocale())->isoFormat('dddd D/M'));
@endphp
{{ $label }}
