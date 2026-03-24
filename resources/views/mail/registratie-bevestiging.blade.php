<x-mail::message>
@if ($taal === 'fr')
# Confirmation d'inscription

Bonjour {{ $verzoek->naam }},

Nous avons bien reçu votre inscription pour **{{ $activiteit->titel_fr }}**.

**Date :** {{ $activiteit->datum->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
**Heure :** {{ substr($activiteit->startuur, 0, 5) }}
**Lieu :** {{ $activiteit->locatie }}

Nous vous contacterons bientôt pour confirmer votre participation.

Cordialement,<br>
De Harmonie
@else
# Bevestiging inschrijving

Hallo {{ $verzoek->naam }},

We hebben je inschrijving ontvangen voor **{{ $activiteit->titel_nl }}**.

**Datum:** {{ $activiteit->datum->locale('nl')->isoFormat('dddd D MMMM YYYY') }}
**Uur:** {{ substr($activiteit->startuur, 0, 5) }}
**Locatie:** {{ $activiteit->locatie }}

We nemen snel contact met je op om je deelname te bevestigen.

Met vriendelijke groeten,<br>
De Harmonie
@endif
</x-mail::message>
