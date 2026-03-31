# De Harmonie — Organization Research Report
**Prepared for:** Frederik Vincx / Impact Studio
**Purpose:** Website redesign (Webflow/Airtable → Laravel)
**Date:** March 25, 2026

---

## 1. Organization Identity

**Full name:** Lokaal Dienstencentrum De Harmonie
**Legal entity:** VZW Buurtwerk Noordwijk
**Enterprise number (KBO):** BE 0415.797.230
**Founded:** March 1, 1975 (VZW Buurtwerk Noordwijk)
**Recognized as LDC:** 1985 — the oldest lokaal dienstencentrum in Brussels
**Tagline (NL):** "Activiteiten & diensten in ons centrum en bij u thuis"
**Tagline (FR):** "Services & activités chez nous et chez vous"
**Coordinator:** Cynthia Spijker

**What they are:** A lokaal dienstencentrum (local service center) and sociaal restaurant serving primarily seniors in the Noordwijk (North Quarter) of Brussels. They help elderly and care-dependent residents live independently at home as long as possible, break isolation, and maintain social contact with neighbors.

**Heritage claim:** "Al 50 jaar is De Harmonie een vertrouwd adres in de Noordwijk" — they've been active for 50 years in the neighborhood.

---

## 1b. Old Website (Wayback Machine, Jan 2022)

The previous site (before Frederik's Webflow rebuild) was a hand-coded HTML site built by Hein van den Brempt using PageBreeze Free HTML Editor. It was a simple, static site with a NL/FR language splash page.

**Old site navigation (NL):** Laatste Nieuws | Diensten | Personeel | Menu | Activiteiten | Agenda | Maandblad | Tarieven | Foto's | Archieven | FR versie

**Key differences vs. current site:**
- **Old address was Antwerpsestwg 356** (current is Antwerpsesteenweg 24) — confirms they moved to the new building
- **Opening hours were longer:** Mon–Fri 9:00–17:00, Sat 9:30–15:00 (now Mon–Fri 10:00–16:30, Sat 10:00–14:00)
- **Had a fax number:** 02/203 47 60
- **Had a "Maandblad" (monthly newsletter)** — a dedicated section that no longer exists on the new site
- **Had "Tarieven" (pricing)** — transparent pricing page, now gone
- **Had "Archieven" (archives)** — historical content, now gone
- **Had "Foto's" (photos)** — gallery section, now gone
- **Had "Laatste Nieuws" (latest news)** — news section, now gone

**Old board of directors (2022):**
- Jan Vandekerckhove (Voorzitter/President) — still on current Buurtraad
- Maarten Janssens (Lid) — still on current Buurtraad
- Mohamed El Morabit (Lid) — still on current Buurtraad
- Carine Haelemeersch (Penningmeester) — still on current Buurtraad
- Léopold Vodak (Lid) — still on current Buurtraad
- Bert d'Hondt (Lid) — no longer listed
- Christiane Holsbeeks (Secretaris) — no longer listed
- Dirk Wauters (Lid) — no longer listed

**FR version mission statement (richer than current site):** The FR homepage contained a detailed paragraph about their philosophy: users are at the heart of the center's functioning, with a focus on empowerment and leveraging users' own capabilities. This kind of mission content is absent from the current site.

**Wayback Machine coverage:** Only the splash page, index_nl.htm, and index_fr.htm were archived. The subpages (diensten, personeel, menu, activiteiten, agenda, maandblad, tarieven, foto's, archieven) were NOT captured — meaning that content is lost unless De Harmonie has local copies.

**Implication for new site:** Several content types that existed on the old site have been dropped in the Webflow version: the monthly newsletter, pricing transparency, photo gallery, news section, and archives. Consider whether these should be revived in the Laravel rebuild.

---

## 2. Location & Contact

**Primary address:** Antwerpsesteenweg 24, 1000 Brussel
**Alternative address listed:** Harmoniestraat 1, 1000 Brussel (used on some third-party sites and Facebook)
**Phone:** 02/203.28.48
**Email:** info@deharmonie.be (general) / diensten@deharmonie.be (services)
**Website:** www.deharmonie.be
**Facebook:** facebook.com/deharmoniebrussel (519 followers, 2 reviews)

**Opening hours:**
- Monday–Friday: 10:00–16:30
- Saturday: 10:00–14:00

---

## 3. Building & Physical Space

The original location was an old auction hall ("veilinghal"), where only the ground floor could be used due to fire safety regulations. The building was poorly insulated and unsuitable for expansion.

**New building (2012 decision):** In collaboration with Kenniscentrum WWZ (Knowledge Centre for Welfare, Housing and Care), a plan was developed to construct a new building featuring:
- The renewed dienstencentrum on the ground floor
- 9 apartments for seniors above the service center
- Including 1 group housing unit for 3 persons
- LDC Wood was involved in the interior/furnishing (wooden elements)

---

## 4. Governance & Team

### Board of Directors (Bestuursorgaan VZW Buurtwerk Noordwijk)
- Steven Gibens
- Ilse Van der Veken
- Grieke Forceville
- Joeri Colson

### Buurtraad Noordwijk (Neighborhood Council)
- Jan Vandekerckhove
- Maarten Janssens
- Karen De Cooman
- Mohamed El Morabit
- Carine Haelemeers
- Bianca Laurino
- Peter Vandenbempt
- Léopold Vodak

### Staff (~13.2 FTE)
- **Coordination:** Cynthia Spijker
- **Reception & Animation:** Deborah Monfils, Arnaud Petit, Nicolas Van den Eede, Peter Kern
- **Chefs / Kitchen Instructors:** Claude Muaka, Pernelle Mbawu
- **Bar / Hall Instructor:** Gonard Matondo
- **Kitchen & Hall Staff:** Agnes Kalonda-Mbiye, Hassna Boumediane, Japhet Mawanda Nzukum, Mohamed Dahmani, Mohammad Malikzai Lal, Rapten Tenzin, Sahara Ahmed, Shafahat Mallakhel, Tarakhel Kefayatullah
- **Transport & Repairs:** Omid Arabzai, Eduardo Manzoangani
- **Household Help:** Nadine Abeng Evouna, John Saquee
- **Bookkeeping & Admin:** Nancy Jacobs

**Notable:** The team is extremely diverse — reflecting the multicultural Noordwijk neighborhood. This is clearly part of their DNA and a strength to highlight on the website.

---

## 5. Services Offered

### At the Center
1. **Sociaal Restaurant** — Dagschotels aan verminderd tarief voor senioren (daily dishes at reduced rates). Takeaway and home delivery available.
2. **Catering & Venue Rental** — For neighborhood residents and organizations
3. **Activities & Outings** — Creative, relaxing, cultural, educational, informational, and sporty
4. **Sociale Infopunt** — Social information point (biweekly)
5. **Tweedehands Klerenwinkel & Retouches** — Second-hand clothing shop + alterations

### At Home
6. **Boodschappendienst** — Shopping service
7. **Vervoersdienst** — Transport service
8. **Poetsdienst** — Cleaning service (including window washing)
9. **Klusjesdienst** — Handyman/repair service
10. **Wassen en strijken** — Laundry and ironing
11. **Maaltijden aan huis** — Meal delivery
12. **Project Grote Kuis** — Deep cleaning project: a combined service where cleaners and handymen overhaul a senior's home (oven cleaning, tap repair, window washing, carpet cleaning, hood installation, curtain washing, toilet painting, administrative help)

### Social / Care Role
13. **Eerstelijnszorgnetwerk** — Partner in the primary care network of the Noordwijk
14. **Wegwijs in socio-cultureel Brussel** — Helping people navigate Brussels' socio-cultural landscape

---

## 6. Activities Program

The site is fully bilingual (NL/FR). Activities include:

**Language & Conversation Tables:**
- Spanish (Thursday 10:00–12:00)
- Dutch (Friday 10:30–11:30)
- Italian (Monday 11:30–12:30)
- English (Tuesday 10:30)

**Movement & Sports:**
- Country Line Dance (Thursday 14:00–16:00)
- Zumba (Friday 14:00–15:00)

**Creative & Workshop:**
- Sewing workshop / Naaiworkshop (Wednesday 13:30–16:00)
- Diamond Painting with Nadia (biweekly Friday 14:00)
- Creativity workshop / Creativiteit workshop (biweekly Monday 14:00–16:00)

**Mind & Memory:**
- Geheugenatelier / Memory workshop (Monday 13:30–15:15)
- Digital workshop / Digitale workshop (Wednesday 14:00–16:00)

**Social & Entertainment:**
- BINGO! (Wednesday 13:30–16:00)
- Sociale Infopunt (biweekly Wednesday 11:00–14:00)

**Cultural Outings:**
- Theater visits (e.g., "Le Mariage de Figaro" at Théâtre du Parc)
- Museum visits (e.g., Expo Pompei at Tour et Taxis, Lego Exhibition in Liège)
- City outings (e.g., Zomerbal van Stad Brussel)

---

## 7. Financial Overview

**Gross margin:** €631,136.44 (most recent annual accounts)
**FTE:** 13.2
**Last annual accounts filed:** June 16, 2025 (balance year 2024)
**Legal form:** VZW (non-profit association)
**NACE code:** Other social action without accommodation (88.999)

**Funding sources identified:**
- Vlaamse Gemeenschapscommissie (VGC) — recognized and subsidized LDC
- Crowdfunding via Growfunding (civic crowdfunding platform) for the social restaurant
- Footer of website mentions "Met steun van:" (with support from) — logos of supporters present

---

## 8. Network & Partnerships

- **RESOO** — Network of the 19 Brussels lokale dienstencentra (launched shared website December 2024 at resoo.brussels). De Harmonie is one of these 19 centers.
- **Kenniscentrum WWZ** — Collaborated on the building renovation/new construction project
- **Radio Harmonie / Radio Panik (105.4 FM)** — Community radio program produced at De Harmonie, collaboration between VZW Buurtwerk Noordwijk, Bruxelles Nous Appartient–Brussel Behoort Ons Toe, and Radio Panik
- **Globe Aroma & Kaaitheater** — BXL-NORD socio-artistic film project, with De Harmonie as a location/partner
- **Brussels Platform Geestelijke Gezondheid** — Listed as a resource
- **11.11.11** — De Harmonie is listed as a member organization
- **Born in Brussels** — Listed in their directory
- **sociaal.brussels** — Listed in the Brussels social services directory

---

## 9. Digital Presence Assessment

### Current Website (Webflow + Airtable)
**Pages found:**
- Homepage (/)
- Activiteiten (/activiteiten) — NL agenda
- Activités (/activites) — FR agenda (same content, translated)
- Diensten (/diensten)
- Weekmenu de la Semaine — **currently returns 404!**
- Wie is Wie (/wie-is-wie)
- Contact (linked in nav)
- Individual activity pages (/activiteit/...)
- Print overview (/activiteiten-print-fr)

**Navigation items:** Activiteiten, Diensten, Weekmenu de la Semaine, Contact

**Observations:**
- Bilingual NL/FR (mixed on some pages, separate on others — inconsistent)
- The weekmenu page is broken (404)
- Activities are all showing as "Geannuleerd" (cancelled) — unclear if this is a data issue or they're genuinely all cancelled
- Footer links: Diensten, Wie is wie
- Footer mentions "Met steun van:" with supporter logos
- Simple, flat structure — no deep hierarchy
- Old website still accessible at deharmonie.be/index_nl.htm

### Facebook
- 519 followers, 12 following
- 2 reviews
- Low engagement visible
- Posts about cultural outings, activities

### Not found:
- No Instagram account detected
- No LinkedIn presence
- No YouTube or video content
- No newsletter/mailing list visible on the site

---

## 10. Jaarverslag (Annual Report)

**Finding:** No public jaarverslag or werkingsverslag was found online. The annual accounts (jaarrekening) are filed with the Balanscentrale (National Bank) and can be accessed via:
- **CompanyWeb:** companyweb.be/nl/0415797230/buurtwerk-noordwijk
- **Bizzy:** bizzy.ai/nl/be/0415797230/buurtwerk-noordwijk
- **CoBRHA Viewer (Dept. Zorg):** publiek.departementzorg.be (registration WVG_VAZG/2791)

These are financial filings, not narrative annual reports. If De Harmonie produces a jaarverslag with stories, photos, and impact data, it is not publicly available online. This could be something to ask the organization for directly — and could become a feature of the new website (publishing their annual report digitally).

---

## 11. Opportunities for the New Website

Based on this research, here are areas where the new Laravel site could significantly improve:

1. **Fix the bilingual experience** — Current NL/FR handling is inconsistent. Laravel with proper i18n routing could offer clean /nl/ and /fr/ URL prefixes.

2. **Weekmenu as a first-class feature** — This is clearly central to their offering (social restaurant) but the current page 404s. Could be a dynamic, easily-updatable module.

3. **Activity management** — Currently powered by Airtable. The new system should make it dead simple for staff to add/edit/cancel activities and have them appear correctly in both languages.

4. **The "Grote Kuis" project** — This is a unique, compelling service that deserves its own landing page with before/after stories, testimonials, and a clear CTA.

5. **Team diversity as a story** — The multicultural team reflects the neighborhood. This could be told visually and narratively on the Wie is Wie page.

6. **Digital jaarverslag** — Publishing an annual impact report on the website would strengthen their case for funding and community trust.

7. **RESOO integration** — Consider how the site relates to resoo.brussels. Could pull shared data or link intelligently.

8. **Radio Harmonie content** — Embedding or linking to their community radio program would add richness.

9. **Social proof** — Crowdfunding success (Growfunding), 50-year heritage, partnerships with Kaaitheater/Globe Aroma — none of this is on the current website.

10. **SEO & directory consistency** — Address listed differently across platforms (Antwerpsesteenweg 24 vs. Harmoniestraat 1). The new site should establish one canonical address.

---

## 12. Key Sources

| Source | URL |
|--------|-----|
| Current website | deharmonie.be |
| Facebook | facebook.com/deharmoniebrussel |
| CompanyWeb (KBO) | companyweb.be/nl/0415797230/buurtwerk-noordwijk |
| Bizzy | bizzy.ai/nl/be/0415797230/buurtwerk-noordwijk |
| RESOO network | resoo.brussels/en/19-centers/de-harmonie |
| Kenniscentrum WWZ (building) | kenniscentrumwwz.be/kennisbank/portfolio-bouwprojecten/de-harmonie |
| VGC listing | vgc.be/locaties/lokaal-dienstencentrum-de-harmonie |
| sociaal.brussels | sociaal.brussels/organisation/4120 |
| Born in Brussels | bornin.brussels/nl/verenigingen/lokaal-dienstencentrum-de-harmonie-2 |
| Growfunding campaign | growfunding.be/en/projects/harmonie |
| Radio Harmonie | radiopanik.org/emissions/radio-harmonie |
| BXL-NORD film (Demos) | demos.be/kenniscentrum/praktijk/bxl-nord |
| CoBRHA (Dept. Zorg) | publiek.departementzorg.be/Cobrha/Institutions/Institution/WVG_VAZG/2791 |
| LDC Wood (interior) | ldcwood.com/en/cases/de-harmonie-brussels |
| 11.11.11 listing | 11.be/organisaties/vzw-buurtwerk-noordwijk |
| Brussels Platform GGZ | platformbxl.brussels/nl/node/38690 |
| Old website | deharmonie.be/index_nl.htm |
