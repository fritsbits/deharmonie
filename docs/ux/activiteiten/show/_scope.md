# UX Scope — Activity Detail Page
_Status: Draft_

## In scope

### Content
- Back link ("← Alle activiteiten")
- Eyebrow: reeks name if `template_id` is set, otherwise generic "Activiteit"
- Activity title (NL or FR based on locale)
- Alternate-language title (muted, below main title)
- Cancellation notice — if `status === 'geannuleerd'`
- Logistics: date, time, price (with Gratis badge), location
- Description (rich text, 18px+)
- Registration form (only if `status === 'gepubliceerd'`)
- Inline registration success state (replaces form on submit)
- Contact info: phone + email
- Print action

### Interactions
- Sidebar "Schrijf je in →" CTA: smooth-scrolls to form anchor
- CTA hidden when activity is cancelled
- Mobile: sticky bottom CTA while form is off-screen

## Out of scope

- Activity photo / hero image (no photos used)
- Capacity display (max participants, spots remaining)
- "Activity full" / waitlist state
- Separate confirmation page after registration
- Related activities / "you might also like"
- Social sharing buttons
