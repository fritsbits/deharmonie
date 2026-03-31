# UX Structure — Activity Detail Page
_Status: Draft_

## Information architecture

Single page, no sub-navigation. Content flows top to bottom:

```
Navigation
  └─ Back link → activiteiten overview

Header band (white)
  ├─ Eyebrow (reeks name or "Activiteit")
  ├─ Title + alt-language title
  ├─ Cancellation notice (conditional)
  └─ Logistics strip: date · time · price · location

Main content (green-tint bg)
  ├─ Left column (2/3)
  │   ├─ Description
  │   └─ Registration form → inline success state
  └─ Right column (1/3, sticky)
      ├─ "Schrijf je in" CTA → scrolls to form (if gepubliceerd)
      ├─ Contact (phone + email)
      └─ Print action

Footer
```

## Interaction flows

### Registration flow
1. User sees "Schrijf je in →" in sidebar → clicks → page scrolls to form
2. User fills form → submits
3. Form replaced by inline success message (green check, clear confirmation text)
4. No redirect, no separate page

### Cancellation state
- Cancellation notice replaces the form area
- "Schrijf je in" CTA absent from sidebar
- Everything else remains visible (description, logistics, contact)

## Conditional states

| Status | Form shown | Sidebar CTA | Notice banner |
|--------|-----------|-------------|---------------|
| `gepubliceerd` | Yes | Yes | No |
| `geannuleerd` | No | No | Yes (orange) |
