<x-mail::message>
@if ($taal === 'fr')
# Confirmation d'inscription

Bonjour {{ $verzoek->naam }},

Vous êtes inscrit(e) pour :

**{{ $activiteit->titel_fr }}**
{{ ucfirst($activiteit->datum->locale('fr')->isoFormat('dddd D MMMM YYYY')) }} · {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }}@endif
{{ $activiteit->locatie }}

Vous n'avez rien d'autre à faire — votre place est réservée.

Des questions ? Appelez-nous au **02 203 28 48** ou envoyez un e-mail à info@deharmonie.be.

À bientôt !<br>
De Harmonie
@else
# Bevestiging inschrijving

Hallo {{ $verzoek->naam }},

Je bent ingeschreven voor:

**{{ $activiteit->titel_nl }}**
{{ ucfirst($activiteit->datum->locale('nl')->isoFormat('dddd D MMMM YYYY')) }} · {{ substr($activiteit->startuur, 0, 5) }}@if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }}@endif
{{ $activiteit->locatie }}

Je hoeft niets meer te doen — je plaats is gereserveerd.

Vragen? Bel ons op **02 203 28 48** of mail naar info@deharmonie.be.

Tot dan!<br>
De Harmonie
@endif
</x-mail::message>
