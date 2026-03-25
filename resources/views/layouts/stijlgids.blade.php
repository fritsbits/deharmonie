<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Stijlgids — De Harmonie</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,300;6..12,400;6..12,600;6..12,700;6..12,800&family=Source+Sans+3:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body style="background-color: var(--color-brand-bg); color: var(--color-brand-dark); font-family: var(--font-body); font-size: 18px;">
    @yield('content')
</body>
</html>
