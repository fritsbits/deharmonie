# UX Skeleton — Inschrijvingsflow

## Activity detail page — form section (available)

```
┌─────────────────────────────────────────────────┐
│  [description text]                             │
│                                                 │
│  ─────────────────────────────────────────────  │
│                                                 │
│  [💬 icon]  Schrijf je in via het formulier     │
│             of bel 02 203 28 48                 │
│                                                 │
│  ┌───────────────────────────────────────────┐  │
│  │  NAAM *          [________________]       │  │
│  │  E-MAIL          [________________]       │  │
│  │  TELEFOON        [________________]       │  │
│  │  BERICHT         [________________]       │  │
│  │                  [________________]       │  │
│  │                                           │  │
│  │  [ Inschrijven →              (green) ]   │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

---

## Activity detail page — fully booked (replaces entire section)

```
┌─────────────────────────────────────────────────┐
│  [description text]                             │
│                                                 │
│  ─────────────────────────────────────────────  │
│                                                 │
│  ┌───────────────────────────────────────────┐  │
│  │  Deze activiteit is volgeboekt.           │  │
│  │  Cette activité est complète.             │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
```

*Minimal. No phone number, no call to action. Just honest feedback.*

---

## Activity detail page — success state (form replaced inline)

```
┌───────────────────────────────────────────────┐
│                                               │
│               ✓  (green checkmark)            │
│                                               │
│        Je bent ingeschreven!                  │
│                                               │
│   Koken met kruiden                           │
│   Vrijdag 18 april · 14:00 – 16:00            │
│   De Harmonie, Parkstraat 4                   │
│                                               │
│   Je ontvangt een bevestiging per e-mail.     │
│                                               │
└───────────────────────────────────────────────┘
```

---

## Visitor confirmation email (plain markdown)

```
  Bevestiging inschrijving: Koken met kruiden
  ─────────────────────────────────────────────

  Hallo Marie,

  Je bent ingeschreven voor:

  Koken met kruiden
  Vrijdag 18 april 2025 · 14:00 – 16:00
  De Harmonie, Parkstraat 4

  Je hoeft niets meer te doen — je plaats is gereserveerd.

  Vragen? Bel ons op 02 203 28 48 of mail naar
  info@deharmonie.be.

  Tot dan!
  De Harmonie
```

---

## Staff notification email (plain markdown, reply-to: registrant)

```
  Nieuwe inschrijving: Koken met kruiden — vrijdag 18 april
  ─────────────────────────────────────────────

  Koken met kruiden
  Vrijdag 18 april 2025 · 14:00

  ─────────────────────────────────────────────

  Naam:      Marie Dupont
  E-mail:    marie@example.com
  Telefoon:  0472 12 34 56
  Bericht:   Ik kom met mijn zus mee.

  [ Bekijk in admin ]

  De Harmonie
```

*Reply-to is set to the registrant's email — staff hits reply to respond directly.*

---

## Admin — registrations list (read-only)

```
┌──────────────────────────────────────────────────────────┐
│  Inschrijvingen                                          │
├────────────┬──────────────────┬────────────┬────────────┤
│  Naam      │  Activiteit      │  Datum     │  Ontvangen │
├────────────┼──────────────────┼────────────┼────────────┤
│  Marie D.  │  Koken met...    │  18/04     │  12/04 14u │
│  Jan V.    │  Italiaans...    │  22/04     │  11/04 09u │
└────────────┴──────────────────┴────────────┴────────────┘
```

*Clicking a row opens the side panel. No actions, no status toggle.*

---

## Admin — side panel (slide-over on row click)

```
┌──────────────────────────────────────┐
│  Marie Dupont            [✕ sluiten] │
│  Ontvangen: 12 apr 2025 om 14:32     │
│  ──────────────────────────────────  │
│  E-mail     marie@example.com        │
│  Telefoon   0472 12 34 56            │
│  Bericht    Ik kom met mijn zus mee. │
│                                      │
│  ──────────────────────────────────  │
│  Activiteit  Koken met kruiden       │
│  Datum       Vrijdag 18 april 2025   │
│  Tijdstip    14:00 – 16:00           │
└──────────────────────────────────────┘
```
