<x-mail::message>
# Nieuwe inschrijving

Er is een nieuwe inschrijving ontvangen voor **{{ $activiteit->titel_nl }}**.

**Activiteit:** {{ $activiteit->titel_nl }}
**Datum:** {{ $activiteit->datum->format('d/m/Y') }} om {{ substr($activiteit->startuur, 0, 5) }}

---

**Naam:** {{ $verzoek->naam }}
**E-mail:** {{ $verzoek->email }}
**Telefoon:** {{ $verzoek->telefoon ?? '—' }}
**Bericht:** {{ $verzoek->bericht ?? '—' }}

<x-mail::button url="{{ config('app.url') }}/admin/deelnameverzoeken">
Bekijk in admin
</x-mail::button>

Met vriendelijke groeten,<br>
De Harmonie
</x-mail::message>
