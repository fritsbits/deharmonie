# De Harmonie — Competitive & Strategic Research
**Prepared for:** Frederik Vincx / Impact Studio
**Purpose:** Website redesign — comparative analysis, SEO, patterns
**Date:** March 25, 2026

---

## 1. RESOO Peer Website Audit

We audited 8 of the 19 Brussels lokale dienstencentra to identify patterns, best practices, and gaps.

### Comparison Matrix

| Feature | De Harmonie | De Kaai | Chambéry | Het Anker (LD3) | Cosmos | Aksent | Ellips | Warlandis | Lotus |
|---------|-------------|---------|----------|-----------------|--------|--------|--------|-----------|-------|
| **Platform** | Webflow+Airtable | Modern CMS | Custom | WordPress | Custom (frames) | Custom | WordPress | Custom | Unknown |
| **Bilingual approach** | Mixed/inconsistent | Embedded NL+FR on same page | Toggle (separate pages) | Embedded mixed | Toggle (separate) | Side-by-side NL/FR | Button grid menus | — | — |
| **Weekly menu** | 404 (broken) | Monthly calendar table with prices | Weekly table with solidarity pricing | Linked externally | Hidden in submenus | Cafetaria page | PDF button grid by week | — | — |
| **Activity calendar** | Flat list (all "cancelled") | Detailed weekly table, categorized | Integrated with menu | Grid calendar | Not prominent | Agenda page | — | — | — |
| **Jaarverslag online** | No | No | No | No | No | **YES (PDF)** | No | No | No |
| **Donate/support CTA** | No | No | **Yes ("Steun Ons")** | No | No | **Yes ("Steun Ons!")** | No | No | No |
| **News/blog** | No | Leadership message | Possibly | **Yes (Nieuws)** | Likely | No | No | No | No |
| **Volunteer recruitment** | No | **Yes (Vacature)** | No | **Yes (dedicated page)** | Possible | **Yes (Vacatures)** | No | No | No |
| **Photo gallery** | No | No | No | **Yes (Filmpjes/video)** | No | **Yes (Multimedia)** | No | No | No |
| **Testimonials/stories** | No | No | No | **Yes (Portretten)** | No | No | No | No | No |
| **Drinks menu** | No | **Yes (Drankenkaart)** | No | No | No | No | No | No | No |
| **Room rental** | Mentioned | No | No | No | **Yes (Zaalverhuur)** | No | No | No | No |

### Standout Sites

**De Kaai (dekaaivzw.be)** — Best overall
- 11 navigation items covering everything users need
- Monthly menu calendar with full meal descriptions, pricing tiers, delivery options
- "Woord van de Centrumleider" — personal leadership voice (unique)
- Dedicated drinks menu (Drankenkaart)
- Clean, modern design with illustrated branding

**Chambéry (chambery.be)** — Best branding & pricing transparency
- Distinctive illustrated visual identity (not stock-photo dependent)
- Clear solidarity pricing tiers: Solidariteit €10 / 64+ varied / Eisene €7 / Omnio €5
- "Steun Ons" (Support Us) fundraising CTA
- Green/red color palette with hand-drawn feel — warm and approachable
- Diensten split cleanly into: Klussen en Renovatie + Restaurant en Catering

**Aksent (aksentvzw.be)** — Best bilingual + transparency
- Side-by-side NL/FR text throughout (not toggled, not mixed)
- **Only LDC found publishing a jaarverslag online (PDF, 2020)**
- Multi-location (Schaarbeek + Evere) with unified brand
- "Steun Ons / Soutenez-Nous" donation ask
- Multimedia section + organizational chart published

**Het Anker / LD3 (ld3.be)** — Best content richness
- "Portretten" — user stories/testimonials (unique among all sites)
- Active news section with recent posts
- Video content section
- Volunteer recruitment page
- Newsletter subscription

---

## 2. Features De Harmonie Is Missing (vs. Best Peers)

Based on the audit, De Harmonie's current site lacks these features that peers have:

| Missing Feature | Who Does It Well | Priority for New Site |
|----------------|-----------------|----------------------|
| Working weekly menu | De Kaai, Chambéry | **Critical** — it's in the nav but 404s |
| Solidarity/tiered pricing display | Chambéry | **High** — builds trust and transparency |
| Jaarverslag/annual report | Aksent | **High** — funding credibility |
| Donate/support CTA | Chambéry, Aksent | **Medium** — revenue opportunity |
| Volunteer recruitment page | Het Anker, Aksent, De Kaai | **Medium** — operational need |
| News/updates section | Het Anker | **Medium** — shows the center is alive |
| User stories/testimonials | Het Anker (Portretten) | **Medium** — emotional connection |
| Photo/video gallery | Het Anker, Aksent | **Medium** — shows atmosphere |
| Leadership message/voice | De Kaai | **Low** — nice personality touch |
| Drinks menu | De Kaai | **Low** — completeness |

---

## 3. SEO & Discovery Analysis

### How People Search for Services Like De Harmonie

| Search Query (NL) | What Ranks | De Harmonie Visible? |
|-------------------|-----------|---------------------|
| "sociaal restaurant Brussel" | Google Maps (De Harmonie 4.4★, 38 reviews), Sociaal Brussel, Born in Brussel | **Yes — Maps + organic** |
| "activiteiten senioren Brussel" | Brussels Ouderenplatform, be.brussels, Sociaal Brussel | **No** |
| "hulp ouderen thuis Brussel" | Iriscare, be.brussels, OCMW sites | **No** |
| "lokaal dienstencentrum Brussel" | Google Maps (De Harmonie 4.4★), Resoo.brussels | **Yes — Maps** |

| Search Query (FR) | What Ranks | De Harmonie Visible? |
|-------------------|-----------|---------------------|
| "restaurant social Bruxelles seniors" | Restaurant Mimosa, Born in Brussel, Sociaal Brussel, deharmonie.be | **Yes** |
| "aide à domicile personnes âgées Bruxelles" | Ville de Bruxelles, Iriscare, be.brussels, OCMW | **No** |
| "centre de services local Bruxelles" | Resoo.brussels (dominant), Google knowledge panel | **No** |

### Key SEO Findings

1. **Resoo.brussels dominates** institutional search terms — De Harmonie benefits from being in the network but should also rank independently.
2. **Google Maps is crucial** — De Harmonie already has a solid profile (4.4★, 38 reviews) but the listing needs consistent NAP (Name, Address, Phone).
3. **De Harmonie is invisible for home services queries** — the "hulp aan huis" offering (cleaning, shopping, transport, repairs) has zero search visibility.
4. **Directories matter** — Born in Brussels, Sociaal Brussel, be.brussels all rank well. De Harmonie should ensure listings are current and consistent.
5. **The address inconsistency hurts** — "Antwerpsesteenweg 24" vs "Harmoniestraat 1" across different platforms confuses Google.

### SEO Opportunities for the New Laravel Site

- **Create dedicated landing pages** for each service (poetsdienst, vervoersdienst, boodschappendienst, klusjesdienst, Grote Kuis) — these can rank for specific home-care queries
- **Structured data (Schema.org)** — LocalBusiness, Restaurant, Event markup
- **Google Business Profile** — unify address, add photos, post weekly menu updates
- **Hreflang tags** — proper NL/FR implementation (see bilingual section)
- **Blog/news section** — regular content creation to build domain authority

---

## 4. Bilingual Website Patterns

### How Brussels Organizations Handle NL/FR

| Organization | Approach | URL Structure | Switcher |
|-------------|----------|---------------|----------|
| Resoo.brussels | URL prefix routing | /nl/, /fr/, /en/ | Dropdown selector |
| be.brussels | URL prefix routing | /nl/, /fr/, /en/ | Button in top nav |
| VGC | Primarily Dutch | Limited FR | Language link |
| De Kaai | Embedded both languages on same page | Single URL | None needed |
| Chambéry | Toggle (separate pages) | Separate URLs | NL/FR button |
| Aksent | Side-by-side NL/FR text | Single URL | None needed |

### Three Viable Approaches for De Harmonie's Laravel Build

**Option A: URL Prefix Routing (Recommended)**
- Pattern: `deharmonie.be/nl/diensten` / `deharmonie.be/fr/services`
- Pros: Best for SEO (separate indexable URLs), clean, follows Resoo pattern
- Cons: All content must be translated, more content to maintain
- Laravel implementation: Route::prefix('{locale}') with middleware, hreflang tags

**Option B: Embedded Bilingual (De Kaai model)**
- Pattern: `deharmonie.be/diensten` with NL text above, FR text below
- Pros: Single page to maintain, no language toggle needed, inclusive
- Cons: Long pages, poor SEO (Google can't distinguish language), harder to read

**Option C: Toggle with Shared URLs (Chambéry model)**
- Pattern: `deharmonie.be/diensten?lang=fr` or cookie-based
- Pros: Simpler content management
- Cons: Weaker SEO, potential caching issues, relies on user action

### Laravel Implementation Notes (Option A)

- Use `Route::prefix('{locale}')` with `where('locale', 'nl|fr')` constraint
- Middleware to extract locale from URL segment, set `app()->setLocale()`
- UI strings: file-based translation (`resources/lang/nl/`, `resources/lang/fr/`)
- Dynamic content (activities, menus, services): database with translatable columns
- **Critical SEO**: Every page needs `<link rel="alternate" hreflang="nl">` and `hreflang="fr"` plus `hreflang="x-default"`
- Canonical tags: self-referencing per language version

---

## 5. Weekly Menu Presentation Patterns

### How Social Restaurants Present Menus Online

| Organization | Format | Pros | Cons |
|-------------|--------|------|------|
| **De Kaai** | Monthly calendar table on web page | Visual, familiar, printable, bilingual | Large table, monthly updates needed |
| **Opcura** | Structured daily listing (web page) | Clean, mobile-friendly, always current | Only shows current week |
| **Ellips** | Button grid linking to weekly PDFs | Organized by week, bilingual labels | PDFs not mobile-friendly, not indexable |
| **Merchtem** | PDF download per week | Professional, shareable, printable | Not searchable, requires app to view |
| **Chambéry** | Weekly table with dates + solidarity pricing | Transparent pricing, clear dates | Needs weekly manual update |

### Recommended Approach for De Harmonie

**Primary: Structured web page (Opcura model)**
- Current week displayed by default
- Daily breakdown: Soep → Dagschotel → Dessert → Prijs
- Bilingual (NL above, FR below per day)
- Auto-advances to next week
- Schema.org Menu markup for Google

**Secondary: Printable/shareable version**
- PDF auto-generated from same data
- WhatsApp/email share button
- Seniors often print menus or share with family

**Admin interface (Laravel):**
- Simple form: select date, enter soup/main/dessert in NL+FR
- Copy from previous week template
- Publish/unpublish toggle
- This replaces Airtable and makes weekly updates trivial for staff

---

## 6. Accessibility for Elderly Users

### Beyond Standard WCAG — Senior-Specific Guidelines

| Guideline | Standard WCAG | Recommended for Seniors | Source |
|-----------|--------------|------------------------|--------|
| **Body font size** | 16px minimum | 18-20px minimum | PMC systematic review |
| **Color contrast** | 4.5:1 (AA) | 7:1 (AAA) | W3C WAI |
| **Touch targets** | 44×44px | 50-60px | Smashing Magazine |
| **Text scaling** | Up to 200% | Up to 200% without horizontal scroll | WCAG 1.4.4 |

### Key Design Principles for De Harmonie's Audience

1. **Font**: Sans-serif (Verdana, Arial), 18px+ body text, generous line-height (1.6+)
2. **Navigation**: Maximum 6-7 main items, consistent placement, breadcrumbs on all pages
3. **Links**: Always underlined, color-distinct, never "click here"
4. **Buttons**: 50-60px touch targets, high contrast, clear labels
5. **Cognitive load**: Progressive disclosure, one CTA per section, no auto-rotating carousels
6. **Print**: Print-friendly stylesheets — many seniors print pages to read or share
7. **Error handling**: Forgiving forms, clear error messages in plain language
8. **Skip links**: "Skip to main content" for keyboard navigation
9. **High contrast mode**: Consider offering an enhanced contrast toggle

---

## 7. Key Recommendations Summary

### Must-Have for New Site (based on all research)

1. **Working weekly menu** — structured web page + printable PDF, with simple admin
2. **URL-prefix bilingual routing** (/nl/, /fr/) with proper hreflang
3. **Senior-optimized design** — 18px+ fonts, AAA contrast, 50px+ touch targets
4. **Service landing pages** — one per service for SEO (poetsdienst, vervoer, boodschappen, etc.)
5. **Activity calendar** — filterable, categorized, easy admin for staff
6. **Consistent address** — pick one (Antwerpsesteenweg 24), update all directories

### Should-Have

7. **Donate/support page** — Chambéry and Aksent both do this well
8. **Jaarverslag section** — follow Aksent's lead, publish PDF + web summary
9. **Solidarity pricing display** — follow Chambéry's model of transparent tiers
10. **Volunteer/vacatures page** — 3 of 8 peers have this
11. **Google Business Profile optimization** — weekly menu posts, photos, consistent NAP

### Nice-to-Have

12. **User stories / Portretten** — follow Het Anker's lead
13. **News/blog section** — builds SEO authority, shows the center is active
14. **Photo/video gallery** — shows atmosphere, builds trust
15. **Leadership message** — follow De Kaai's personal touch
16. **Newsletter signup** — Het Anker has this, good for community building

---

## 8. Sources

### Peer Websites Audited
| Center | URL | Municipality |
|--------|-----|-------------|
| De Kaai | dekaaivzw.be | Anderlecht |
| Chambéry | chambery.be | Etterbeek |
| Het Anker (LD3) | ld3.be | Brussel-Stad |
| Cosmos | cosmosvzw.be | Anderlecht |
| Aksent | aksentvzw.be | Evere + Schaarbeek |
| Ellips | ldc-ellips.com | Berchem-Sainte-Agathe |
| Warlandis | warlandis.be | Jette |
| Lotus | ldclotus.be | Ukkel |

### Menu Pattern Examples
- Opcura: opcura.be/zorgaanbod/sociaal-restaurant/weekmenu
- De Kaai: dekaaivzw.be/kalender
- Ellips: ldc-ellips.com/menus

### Bilingual Patterns
- Resoo: resoo.brussels (Drupal, /nl/ /fr/ /en/ prefixes)
- be.brussels (government portal, prefix routing)

### Accessibility Research
- W3C WAI: w3.org/WAI/older-users/
- PMC Font Size Study: pmc.ncbi.nlm.nih.gov/articles/PMC9376262/
- Smashing Magazine Target Sizes: smashingmagazine.com/2023/04/accessible-tap-target-sizes-rage-taps-clicks/

### Laravel Bilingual Implementation
- Laravel Daily: laraveldaily.com/post/multi-language-routes-and-locales-with-auth
- Chris Talks: christalks.dev/post/setting-up-locale-based-routing-in-laravel-with-middleware
- Lokalise: lokalise.com/blog/laravel-localization-step-by-step/

### SEO
- Google Search Central: developers.google.com/search/docs/specialty/international/managing-multi-regional-sites
- CLICKTRUST Belgium bilingual SEO: clicktrust.be/blog/seo/how-to-get-international-seo-right-in-bilingual-countries/
