<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Er ging iets mis — De Harmonie</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,700;6..12,800;6..12,900&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            background: #fbfaf9;
            color: #2c2826;
            font-family: 'Source Sans 3', sans-serif;
            font-size: 18px;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            padding: 10vh 2rem 4rem 10vw;
        }
        @media (max-width: 600px) {
            body { padding: 3.5rem 1.5rem; }
        }
        a { color: inherit; }
    </style>
</head>
<body>
    <div style="max-width: 36rem;">

        {{-- NL: primary language --}}
        <p style="font-family: 'Nunito Sans', sans-serif; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #81b59c; margin: 0 0 1.25rem;">De Harmonie</p>

        <h1 style="font-family: 'Nunito Sans', sans-serif; font-size: clamp(1.75rem, 4vw, 2.25rem); font-weight: 900; line-height: 1.1; color: #2c2826; margin: 0 0 0.75rem;">
            Er ging iets mis
        </h1>
        <p style="color: #706662; margin: 0 0 1.75rem;">
            Onze excuses. Probeer het straks opnieuw, of bel ons gerust.
        </p>

        <a href="tel:0220328048"
           style="display: inline-flex; align-items: center; gap: 0.5rem; font-family: 'Nunito Sans', sans-serif; font-size: 1.5rem; font-weight: 900; color: #2c2826; text-decoration: none; margin-bottom: 2rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4679bc" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.71 3.39 2 2 0 0 1 3.68 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.64a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            02/203.28.48
        </a>

        <div>
            <a href="/"
               style="display: inline-block; background: #4679bc; color: white; font-family: 'Nunito Sans', sans-serif; font-weight: 700; font-size: 1rem; padding: 0.875rem 2rem; border-radius: 0.5rem; text-decoration: none;">
                Startpagina
            </a>
        </div>

        {{-- Divider --}}
        <div style="border-top: 1px solid #d8d3d2; margin: 2.5rem 0;"></div>

        {{-- FR: secondary language --}}
        <p style="font-family: 'Nunito Sans', sans-serif; font-size: 1.125rem; font-weight: 700; color: #2c2826; margin: 0 0 0.375rem;">
            Quelque chose a mal tourné
        </p>
        <p style="color: #706662; font-size: 1rem; margin: 0 0 1rem;">
            Nos excuses. Veuillez réessayer plus tard ou appelez-nous.
        </p>
        <a href="tel:0220328048" style="color: #4679bc; font-size: 1rem; font-weight: 600; text-decoration: none;">02/203.28.48</a>

        {{-- Error code: demoted to a tiny muted label --}}
        <p style="font-family: 'Nunito Sans', sans-serif; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #c0bbb9; margin: 3rem 0 0;">
            Fout 500
        </p>

    </div>
</body>
</html>
