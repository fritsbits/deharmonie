@php
    // Each categorie offers a few icon candidates. The first listed is the
    // currently-active choice in App\Support\CategorieIcons. Tell Frederik
    // which option per category to use, then update CategorieIcons accordingly.
    $sectionColors = [
        'beweeg' => ['bg' => 'var(--color-brand-orange)', 'icon' => '#b34a2d'],
        'maak_leer' => ['bg' => 'var(--color-brand-green)', 'icon' => '#5a8a74'],
        'ontmoet_beleef' => ['bg' => 'var(--color-brand-blue)', 'icon' => '#2f5490'],
    ];

    $catalog = [
        ['cat' => 'sport_beweging', 'label' => 'Sport & beweging', 'section' => 'beweeg', 'variants' => [
            ['id' => 'bolt', 'name' => 'bolt (huidig)', 'heroicon' => 'heroicon-s-bolt'],
            ['id' => 'fire', 'name' => 'fire', 'heroicon' => 'heroicon-s-fire'],
            ['id' => 'arrow-trending-up', 'name' => 'arrow-trending-up', 'heroicon' => 'heroicon-s-arrow-trending-up'],
            ['id' => 'rocket-launch', 'name' => 'rocket-launch', 'heroicon' => 'heroicon-s-rocket-launch'],
            ['id' => 'play', 'name' => 'play', 'heroicon' => 'heroicon-s-play'],
        ]],
        ['cat' => 'creatief', 'label' => 'Creatief', 'section' => 'maak_leer', 'variants' => [
            ['id' => 'sparkles', 'name' => 'sparkles (huidig)', 'heroicon' => 'heroicon-s-sparkles'],
            ['id' => 'paint-brush', 'name' => 'paint-brush', 'heroicon' => 'heroicon-s-paint-brush'],
            ['id' => 'swatch', 'name' => 'swatch', 'heroicon' => 'heroicon-s-swatch'],
            ['id' => 'scissors', 'name' => 'scissors', 'heroicon' => 'heroicon-s-scissors'],
            ['id' => 'pencil-square', 'name' => 'pencil-square', 'heroicon' => 'heroicon-s-pencil-square'],
        ]],
        ['cat' => 'bijleren', 'label' => 'Bijleren', 'section' => 'maak_leer', 'variants' => [
            ['id' => 'light-bulb', 'name' => 'light-bulb (huidig)', 'heroicon' => 'heroicon-s-light-bulb'],
            ['id' => 'academic-cap', 'name' => 'academic-cap', 'heroicon' => 'heroicon-s-academic-cap'],
            ['id' => 'book-open', 'name' => 'book-open', 'heroicon' => 'heroicon-s-book-open'],
            ['id' => 'puzzle-piece', 'name' => 'puzzle-piece', 'heroicon' => 'heroicon-s-puzzle-piece'],
            ['id' => 'language', 'name' => 'language', 'heroicon' => 'heroicon-s-language'],
        ]],
        ['cat' => 'ontmoeting', 'label' => 'Ontmoeting', 'section' => 'ontmoet_beleef', 'variants' => [
            ['id' => 'chat-bubble-oval-left', 'name' => 'chat-bubble-oval-left (huidig)', 'heroicon' => 'heroicon-s-chat-bubble-oval-left'],
            ['id' => 'chat-bubble-left-right', 'name' => 'chat-bubble-left-right', 'heroicon' => 'heroicon-s-chat-bubble-left-right'],
            ['id' => 'user-group', 'name' => 'user-group', 'heroicon' => 'heroicon-s-user-group'],
            ['id' => 'users', 'name' => 'users', 'heroicon' => 'heroicon-s-users'],
            ['id' => 'hand-raised', 'name' => 'hand-raised', 'heroicon' => 'heroicon-s-hand-raised'],
        ]],
        ['cat' => 'spelletjes', 'label' => 'Spelletjes', 'section' => 'ontmoet_beleef', 'variants' => [
            ['id' => 'squares-2x2', 'name' => 'squares-2x2 (huidig)', 'heroicon' => 'heroicon-s-squares-2x2'],
            ['id' => 'puzzle-piece', 'name' => 'puzzle-piece', 'heroicon' => 'heroicon-s-puzzle-piece'],
            ['id' => 'trophy', 'name' => 'trophy', 'heroicon' => 'heroicon-s-trophy'],
            ['id' => 'cube', 'name' => 'cube', 'heroicon' => 'heroicon-s-cube'],
            ['id' => 'sparkles', 'name' => 'sparkles', 'heroicon' => 'heroicon-s-sparkles'],
        ]],
        ['cat' => 'culinair', 'label' => 'Culinair', 'section' => 'ontmoet_beleef', 'variants' => [
            ['id' => 'cake', 'name' => 'cake (huidig)', 'heroicon' => 'heroicon-s-cake'],
            ['id' => 'fire', 'name' => 'fire', 'heroicon' => 'heroicon-s-fire'],
            ['id' => 'beaker', 'name' => 'beaker', 'heroicon' => 'heroicon-s-beaker'],
            ['id' => 'gift', 'name' => 'gift', 'heroicon' => 'heroicon-s-gift'],
            ['id' => 'heart', 'name' => 'heart', 'heroicon' => 'heroicon-s-heart'],
        ]],
        ['cat' => 'film_muziek', 'label' => 'Film & muziek', 'section' => 'ontmoet_beleef', 'variants' => [
            ['id' => 'musical-note', 'name' => 'musical-note (huidig)', 'heroicon' => 'heroicon-s-musical-note'],
            ['id' => 'film', 'name' => 'film', 'heroicon' => 'heroicon-s-film'],
            ['id' => 'play-circle', 'name' => 'play-circle', 'heroicon' => 'heroicon-s-play-circle'],
            ['id' => 'speaker-wave', 'name' => 'speaker-wave', 'heroicon' => 'heroicon-s-speaker-wave'],
            ['id' => 'microphone', 'name' => 'microphone', 'heroicon' => 'heroicon-s-microphone'],
        ]],
        ['cat' => 'op_uitstap', 'label' => 'Op uitstap', 'section' => 'ontmoet_beleef', 'variants' => [
            ['id' => 'map-pin', 'name' => 'map-pin (huidig)', 'heroicon' => 'heroicon-s-map-pin'],
            ['id' => 'map', 'name' => 'map', 'heroicon' => 'heroicon-s-map'],
            ['id' => 'globe-europe-africa', 'name' => 'globe-europe-africa', 'heroicon' => 'heroicon-s-globe-europe-africa'],
            ['id' => 'paper-airplane', 'name' => 'paper-airplane', 'heroicon' => 'heroicon-s-paper-airplane'],
            ['id' => 'sun', 'name' => 'sun', 'heroicon' => 'heroicon-s-sun'],
        ]],
    ];
@endphp

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Icon variants — De Harmonie</title>
    <link rel="stylesheet" href="{{ asset(Vite::asset('resources/css/app.css')) }}">
    <style>
        body { font-family: var(--font-body, system-ui), sans-serif; background: var(--color-brand-bg, #fbfaf9); padding: 2rem; max-width: 1200px; margin: 0 auto; color: #2c2826; }
        h1 { font-family: var(--font-sans), system-ui; font-weight: 900; font-size: 2rem; margin-bottom: 0.5rem; }
        h2 { font-family: var(--font-sans), system-ui; font-weight: 800; font-size: 1.25rem; margin-top: 2.5rem; margin-bottom: 0.5rem; }
        .lead { color: #706662; margin-bottom: 2rem; }
        .row { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1rem; }
        .variant { background: white; border-radius: 8px; padding: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .block { position: relative; height: 60px; border-radius: 6px; overflow: hidden; margin-bottom: 0.5rem; }
        .block svg.icon { position: absolute; bottom: -4px; right: 4px; width: 45px; height: 45px; transform: rotate(12deg); pointer-events: none; }
        .name { font-family: var(--font-sans), system-ui; font-weight: 600; font-size: 0.7rem; color: #706662; text-align: center; word-break: break-word; }
        .id { font-family: ui-monospace, monospace; font-size: 0.65rem; color: #999; text-align: center; margin-top: 0.15rem; }
    </style>
</head>
<body>
    <h1>Icon variants per categorie</h1>
    <p class="lead">Telkens ~5 opties per categorie, op exact hetzelfde formaat als in de agenda. Geef per categorie aan welk ID je wilt — bv. <code>sport_beweging=fire, creatief=paint-brush, …</code></p>

    @foreach ($catalog as $entry)
        @php
            $sc = $sectionColors[$entry['section']];
        @endphp
        <h2>{{ $entry['label'] }} <span style="font-family: ui-monospace, monospace; font-weight: normal; color: #999; font-size: 0.85rem;">({{ $entry['cat'] }})</span></h2>
        <div class="row">
            @foreach ($entry['variants'] as $v)
                <div class="variant">
                    <div class="block" style="background: {{ $sc['bg'] }};">
                        @svg($v['heroicon'], 'icon', ['fill' => $sc['icon']])
                    </div>
                    <div class="name">{{ $v['name'] }}</div>
                    <div class="id">{{ $v['id'] }}</div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
