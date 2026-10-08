# krishnamusicschool.com — Full Site Audit

**Audited:** 7 October 2026 · **Scope:** all 114 URLs in the XML sitemaps (68 posts, 38 pages, 8 categories) plus every internal link on the homepage, robots.txt, sitemaps, redirects, structured data, page speed, and AI-crawler access.
**Method:** automated crawl (HTML parsed page by page), JSON-LD validation, user-agent tests for each AI crawler, throttled mobile/desktop Chromium performance runs, manual reading of every money page, cross-checks against third-party listings.

> **Bottom line.** The biggest problem is not keywords. The site **contradicts itself about basic facts** (prices, founding year, founder's name, PIN code, student numbers, ratings). It also carries **structured data and testimonials that look fabricated**. Google and AI assistants both reward consistent, verifiable facts. Until the facts agree, no theme or keyword work will get the school recommended reliably. The online-class offer, which is the business goal, is also buried: it is not in the main menu, and it is the *secondary* button in the hero.


> **Update, 8 October 2026: fix this on the live site now.** Both registration buttons on the winter retreat page ("Register Now" and "Register Through Google Form") link to the placeholder text `GOOGLE_FORM_REGISTRATION_LINK_HERE`. The site redirects that to the homepage, so nobody can register from the page that is selling right now. Replace it with the real Google Form link in the page's Divi Code module. The same page also has a stray "```" and a copy of the old site footer pasted into its content. Found while building the Netlify preview; not caught in the 7 October crawl.

> **Update, 8 October 2026: the sales boxes inside every blog post.** The Ad Inserter plugin adds up to four boxes (code blocks 5–8) to all 68 posts. They claim "Only 3 trial slots open this month", "500+ students", a "4.9★ rating" and "17+ years". They also sell a €450 30-day intensive that the Raga page prices at ₹60,000–80,000, offer a "Free Trial" that costs €15 with a "100% Money-Back Guarantee… Zero questions asked", and quote an invented "James R., London | Former CEO". The boxes, and the "Hi Vini Devra" message every one of their WhatsApp buttons pre-fills, are the main source of that spelling (60 posts). Ad Inserter works with any theme, so the new theme does not remove them: disable blocks 5–7 and replace block 8 (install guide §6).

---

## 1. The 12 issues that matter most (fix in this order)

| # | Issue | Evidence | Impact |
|---|---|---|---|
| 1 | **Contradictory facts across the site** | Founding year 2007 / 2008 / 2013; founder "Vini Devda" / "Vini Devra"; 10-class package ₹6,500–8,000 / ₹13,500 / ₹13,950; 200+ students in 25+ countries vs 500+ in 30+ vs "over 50 countries". Full table in §2. | AI assistants avoid recommending entities whose facts conflict, or they quote the wrong price. Google's quality raters treat this as low trust. |
| 2 | **Homepage structured data is broken** | The main JSON-LD block starts with `"offers": [` and contains a nested `<script>` tag plus the leftover AI-tool citation markers `[web:2][file:28]`. JSON parser: *"Extra data: line 2 column 9"*. | The `EducationalOrganization`, `Course`, `FAQPage` and `LocalBusiness` data you intended to publish is **invisible to Google**. Only Rank Math's default graph is read, and it describes the school as a **Person**. |
| 3 | **Fabricated-looking review markup on 24 pages** | `AggregateRating` values like 4.9/85, 4.9/100, 5.0/200, 4.95/500 differ on every page, and none are backed by reviews visible on that page. "500 reviews" contradicts the 55+ TripAdvisor and 61+ Google reviews stated elsewhere. | This violates Google's review-snippet policy (self-serving reviews of your own business are not allowed, and ratings must match reviews shown on the page). It is a manual-action risk ("spammy structured markup"). |
| 4 | **Testimonials with signs of being invented** | The same "California yoga teacher who never sang" appears as *Lisa Thompson*, *Lisa M.*, *Lisa Kumar (San Diego)* and *Lisa K. (Germany)*. "Lucas Ferreira – **Brazil**" says the classes suited "my schedule in **Australia**". A Bay Area reviewer is labelled "Online Student – **India**". A reviewer who did an in-person Pushkar immersion is labelled "Online Student – Germany". | Legal exposure in your three main markets: the US FTC rule on fake reviews and testimonials (in force since 21 Oct 2024), the UK DMCC Act 2024 fake-review ban (April 2025), and India's CCPA misleading-advertising guidelines (2022). The genuine TripAdvisor reviews (2014–2020) are strong. Use only those. |
| 5 | **Every missing URL redirects to the homepage** | `/this-page-does-not-exist-xyz/` → 301 → `/`. The same happens to 9 footer links (`/pricing/`, `/faq/`, `/blog/`, `/refund-policy/`, `/terms-of-service/`, `/how-to-enroll/`, `/booking-process/`, `/tech-requirements/`, `/policies/`) and to `/llms.txt`. | Google reports these as soft 404s. Visitors who click "Pricing Guide" or "Refund Policy" land on the homepage. The site promises a money-back guarantee, yet **no refund policy exists**. Broken links are hidden from you. |
| 6 | **Hostinger CDN blocks OpenAI's GPTBot** | GPTBot user agent → **HTTP 429** on 3/3 attempts. ClaudeBot, PerplexityBot, OAI-SearchBot, ChatGPT-User, Googlebot, Bingbot, Applebot → 200. | OpenAI's training crawler can't read the site, which weakens ChatGPT's built-in knowledge of the school. Fix it in hPanel's CDN / bot-protection settings (see the install guide). |
| 7 | **Online classes are not the main path** | The main menu is *Home, Retreat, Testimonials, Contact Us, About Us, More*, with **no Classes item**. The hero's primary button is "Book Pushkar Workshop" and "Enroll in Online Class" is the outline button. Prices are in INR only (plus one "€15 USD" typo). | International learners, your target, can't find the offer, compare prices in their own currency, or see class times in their time zone. |
| 8 | **Indexed junk and duplicate pages** | `/courses/`, `/events/`, `/shop-now/` contain the **Divi "movie theatre" demo with lorem ipsum**. `/home/` is an old homepage with different facts. `/pushkar-retreat-guidelines-terms-of-participation/` and `-2/` are identical. `/shop/`, `/cart/`, `/checkout/` and `/my-account/` are WooCommerce leftovers (nothing is sold). | These dilute site quality and confuse crawlers. The "Courses" URL, the most valuable slug you own, shows a fake cinema page. |
| 9 | **Current retreat is invisible; past retreat is promoted** | The menu links only to "Summer Music Retreat 2026" (April–June 2026, already over). The Winter Retreat page (batches Nov 2026 → Feb 2027, ₹30,000) is **in no menu and linked from 0 of 68 posts**. Neither retreat page has an H1. | Lost bookings right now. |
| 10 | **Heavy, double-builder stack** | Divi 4.27.5 **and** Elementor 4.1.4 are both loaded (Elementor is used on 0 pages), WooCommerce 10.8 loads on every page, the homepage HTML is 472 KB (218 KB inline CSS + 195 KB inline JS), and there is no page cache (`x-hcdn-cache-status: DYNAMIC`). | Throttled mobile test: LCP 3.7 s, total blocking time 2.5 s, TTFB 1.4 s. Desktop is acceptable (LCP 1.7 s). |
| 11 | **Wrong postcode everywhere** | The site uses **305001** (Ajmer city). Pushkar is **305022** (India Post; Rangji Temple's listed PIN). A third-party listing (threebestrated.in) uses "Laxmi Market, Pushkar 305022". | NAP (name/address/phone) mismatches weaken local ranking and entity matching in Google Maps and AI answers. |
| 12 | **Non-functional newsletter on every page** | `<form action="#">` with an email input that has no `name` and no JavaScript handler. | Every signup is silently lost, and the form promises a "monthly newsletter" that can't exist. |

---

## 2. Fact consistency (the client must confirm one value for each)

These conflicts are the root cause of the trust and AI-visibility problems. The new theme stores each fact **once** (Settings → KMS Facts) and prints it everywhere: pages, schema, `llms.txt`. One correction then updates the whole site. The worksheet is in [`02-FACTS-TO-CONFIRM.md`](02-FACTS-TO-CONFIRM.md).

| Fact | Values found on the live site | Where |
|---|---|---|
| Founding year | **2008** · 2007 · 2013 | About page and footer ("Since 2008") · homepage JSON-LD and both retreat pages ("since 2007") · threebestrated.in |
| Founder name | **Vini Devda** · Vini Devra · Vini Dewra · Vinod Dewra · "vinod-dewra" | About, Reviews, schema · `/home/` page, 7 posts, the Ad Inserter sales boxes in 60 posts, and threebestrated.in · Raga page · concert page · image filename |
| Founder pronouns | "her father… she has spent 15 years" then "he believes… His teaching" | `/home/` page, consecutive paragraphs (the reviews use "he") |
| Temple name | **Rangji** · Ranji | About/Contact · `/home/`, Bhajan page |
| Postcode | 305001 (Ajmer city) · **305022** (Pushkar) | Site-wide · India Post, third-party listing |
| Street address | Rangji Temple · Laxmi Market | Site · threebestrated.in |
| Students / countries | 200+ / 25+ · 500+ / 30+ · "over 50 countries" | Home, About · Bhajan page · both retreat pages |
| Years teaching | 17+ · 15+ | Home · Singing and Bhajan pages (by 2026 it is 18+ if founded in 2008) |
| % beginners | 90% · 80% · 70% | Home hero · Harmonium page · Home FAQ/About |
| % international | 70% | Contact page (the same 70% is used for "beginners" elsewhere) |
| Single class | ₹1,500 · ₹800–1,200 · €15 "USD" · "$10" | Home and course pages · About and Contact ("trial class") · Bhajan page · testimonial on Bhajan page |
| 5-class package | ₹6,500 · ₹7,125 · €67.50 | Singing page and Home · Harmonium page · Bhajan page |
| 10-class package | ₹13,500 · ₹13,950 · ₹6,500–8,000 | Home · Singing and Harmonium pages · About |
| Pushkar immersion | ₹18,000 · ₹18,000–28,000 · from ₹15,000 · ₹8,900/week | Home · Singing · Multi-instrument · Harmonium |
| **Package logic** | At ₹6,500 for 5 classes and ₹13,500 for 10, the **10-class package costs more per class** (₹1,350) than the 5-class package (₹1,300). At ₹13,950 it is worse still. | Home, Singing and Harmonium pages. The new pricing table only labels a package "Best value" when it really is the cheapest per class. |
| Online class length | 40 min · 40–60 min | Harmonium and Bhajan pages · Singing page |
| Class size | 1-to-1 · max 4 · max 5 · max 8 | Harmonium · Singing (online) · Bhajan (offline) · Singing workshops |
| Ratings | 5.0 TripAdvisor (55+) and 4.9 Google (61+) · schema 4.8–5.0 with 3–500 reviews · 4.6 | On-page · `AggregateRating` on 24 pages · threebestrated.in |
| Hours | 9 AM–9 PM · 9 AM–8 PM | Footer and Contact (office) · class timings and threebestrated.in |
| Video platform | Zoom/Google Meet · Zoom/**Skype** | Singing · Harmonium (Microsoft shut Skype down in May 2025) |
| Copyright year | © 2025 | Footer (it is 2026) |

---

## 3. Structured data (schema)

| Finding | Pages | Fix |
|---|---|---|
| Unparseable JSON-LD (nested `<script>`, `"offers": [` fragment, `[web:2][file:28]` markers) | `/` | Delete the block; the KMS Core plugin outputs a validated graph |
| Literal `<?php` inside JSON-LD | `/online-harmonium-lessons-usa/` | Delete the block |
| Rank Math knowledge graph set to **Person** named "Krishna Music School", plus `Article` schema on the homepage, author `krishnamusicschool`, `@id: "#"` | all 104 pages | KMS Core replaces Rank Math's JSON-LD with an `EducationalOrganization` + `LocalBusiness` graph. If you keep Rank Math's schema instead, switch *Titles & Meta → Local SEO* to Organization |
| `@type: "MusicSchool"` (not a schema.org type; schema.org/MusicSchool returns 404) | 3 posts | Remove |
| `AggregateRating` without visible reviews, inconsistent counts | 24 pages | Remove all of it. Link to TripAdvisor/Google reviews instead |
| Logo URL `https://krishnamusicschool.com/logo.png` → 301 to homepage | homepage schema | Use the real logo `…/uploads/2023/11/Black-Logo.webp` |
| `primaryImageOfPage` declared 200×200 (the real image is 1600×900) | `/` | Generated from real attachment metadata |
| No `Person` entity for the founder; author link `#`; author archive redirects to the homepage | all posts | `Person` node for Vini Devda (degree, lineage, expertise), used as author of posts |
| No `Course` schema that Google can read (the only valid-looking one is in the broken block) | course pages | Generated per course from one price table |
| Retreat dates published only as text | both retreat pages | `EducationEvent` per batch (eligible for Google event results) |

---

## 4. Technical SEO

1. **Soft 404s.** A redirect rule sends every unknown URL to `/`. Remove it (usually an "All 404 Redirect to Homepage" plugin or a Rank Math → Redirections → 404 setting). Real 404s are needed so broken links can be found and fixed.
2. **Sitemap hygiene.** The sitemap lists `/cart/` and `/my-account/` (both `noindex`), `/checkout/` (302 → cart), `/shop/`, `/shop-now/`, `/courses/`, `/events/` and `/home/`. A sitemap should contain only canonical, indexable, 200 pages.
3. **Duplicate pages.** `/pushkar-retreat-guidelines-terms-of-participation/` and `/-2/` are byte-identical; `/retreat-guidelines-terms-of-participation/` is a third version. `/home/` duplicates the homepage with outdated facts.
4. **Redirect chain.** `http://www.` → `https://www.` → `https://` (2 hops). Minor; fix at Hostinger with one rule.
5. **Trailing-slash variants.** `/about-us` returns 200 instead of redirecting to `/about-us/`. The canonical tag covers it, but a 301 is cleaner.
6. **User enumeration.** `/wp-json/wp/v2/users` exposes the admin login name `krishnamusicschool`. Restrict it with a security plugin or filter.
7. **Unused plugins.** Elementor (0 pages use it) and WooCommerce (nothing for sale) load CSS/JS on every page, and WooCommerce adds the cart icon to the header. Deactivate both after migration.
8. **No page cache.** TTFB is 0.8–1.4 s. Enable LiteSpeed Cache (Hostinger runs LiteSpeed) or Hostinger's built-in cache.
9. **Images.** 65 of 68 posts have no images and no featured image, so 92 of 113 pages have no `og:image`. Social and AI link previews are blank. The founder photo `vinod-dewra.webp` is **1.36 MB**. The Divi demo images `cover-1…9.jpg` are referenced but deleted (301).
10. **Two contact forms** on `/contact-us/` (a custom Web3Forms form and a Divi form with a math captcha). Keep one.
11. **"Sign in with Google" / One Tap** (Site Kit) loads `accounts.google.com` for visitors who never log in. Disable it.

## 5. On-page SEO

| Check | Result (111 indexable URLs) |
|---|---|
| `<title>` longer than 60 characters (truncated in Google) | **62** (46 over 70) |
| Meta description missing / over 160 characters | **7** missing (all category pages) / **37** too long |
| Pages with **no H1** | **11**, including both retreat landing pages, `/courses/` and all categories |
| Pages with **two H1s** | **4**. Divi prints the comment count ("1 Comment") as an H1 |
| Heading levels skipped (H2 → H4) | **103** pages |
| Brand duplicated inside the H1 ("… \| Krishna Music School") | 15 posts |
| Placeholder `href="#"` links | **358** (menu parents "Retreat" and "More") |
| Emojis inside headings (🎓🎵🎭) | Homepage and most service pages |

**Slug ≠ content** (the URL promises one thing, the page delivers another):

| URL | Actual H1 |
|---|---|
| `/learn-violin-online/` | "5 Common Mistakes to Avoid When Learning the Violin" (violin is not taught) |
| `/learn-tala-online/` | "Why Learning Rhythm (Laya) is Crucial in **Carnatic** Music" |
| `/learn-indian-classical-music/` | "How to Use Alankars to Unlock Ragas" |
| `/indian-classical-singing-exercises/` | "Alankars vs. Palta: Which to Practice First?" |
| `/harmonium-in-bhajans/` | "Instruments in Bhajan Performances" |
| `/dharamshala-in-may-best-time-visit-dharamshala/` | "Upper Bhagsu May 2026…" |
| `/home/` | "Testimonials" |

## 6. Content and information architecture

- **Off-brand topics.** Carnatic vocals, violin and mridangam posts. The school teaches Hindustani classical and Rajasthani/devotional music. These dilute topical authority and may lead AI assistants to say the school teaches violin.
- **Keyword cannibalisation.** Many posts compete for the same query. See [`04-CONTENT-AND-AI-PLAN.md`](04-CONTENT-AND-AI-PLAN.md) for the merge and redirect plan. Main clusters:
  - *Alankars/paltas*: 8 short posts (~800–1,000 words each)
  - *Advanced harmonium for kirtan*: 2 near-identical posts
  - *What is kirtan*: 2 posts, plus 2 "beginner kirtan" posts
  - *Healing power of bhajans/sound*: 3 posts
  - *Online harmonium / Indian music lessons USA / UK*: 11 commercial posts. They compete with the class pages, and the country variants look like doorway pages
- **Outdated pages.** The summer retreat 2026 (still in the menu), `/music-classes-bhagsu-himachal/` (2025 retreat), two "Pushkar Fair 2025" guides, © 2025.
- **Missing pages promised in the footer.** Pricing, FAQ, Refund/Cancellation policy, Terms of Service, How to Enroll, Tech Requirements, Blog index.
- **No tabla class page.** About and reviews identify tabla as Vini's primary instrument (30+ years) and most TripAdvisor reviews are about tabla, yet the site has no tabla class page.
- **No authored content.** Posts are "by krishnamusicschool". Expert authorship by a named teacher with a bio is a key trust signal for Google (E-E-A-T) and for AI answer engines.

## 7. Conversion (online enrolment)

1. The main menu has no "Online Classes" entry. A visitor needs 2–3 clicks to find a class page.
2. Hero: the primary button is for Pushkar workshops, and the online class button is secondary.
3. No time-zone help. International students can't tell whether 9 AM–9 PM IST suits them (for London that is 4:30 AM–4:30 PM in summer; for New York, 11:30 PM–11:30 AM).
4. INR-only prices; one page mixes € and "USD".
5. Social proof on the online offer is the weakest on the site. The genuine reviews (TripAdvisor, 2014–2020) are all about **in-person** lessons; the "online" testimonials are the suspicious ones. Start collecting real online-student Google reviews now (the message template is in the content plan).
6. Forms: two forms on Contact, a broken newsletter on every page, and the enquiry form doesn't preselect the class the visitor was reading about.
7. The genuine strengths are under-used: one teacher across tabla, harmonium, vocals and sitar; a three-generation lineage; a degree in Indian classical vocal music from Government College, Ajmer; teaching from a historic temple; the Chokhi Vini Project band. These need to sit next to the "Enrol" button.

## 8. Performance (lab tests, Chromium)

| Metric | Mobile (Moto G4, slow 4G, 4× CPU) | Desktop |
|---|---|---|
| TTFB | 1,403 ms | 800 ms |
| First Contentful Paint | 3,148 ms | 1,132 ms |
| Largest Contentful Paint | **3,712 ms** (target < 2,500) | 1,692 ms |
| Total Blocking Time | **2,553 ms** (target < 200) | 59 ms |
| HTML size | 472 KB (218 KB inline CSS, 195 KB inline JS) | same |
| Transferred | 532 KB | 486 KB |

Field data (CrUX/Search Console) was not available to this audit. Check *Core Web Vitals* in Search Console for real-user numbers.

## 9. AI-engine optimisation (GEO/AEO) gaps

| Gap | Fix delivered |
|---|---|
| `/llms.txt` → 301 to homepage | KMS Core serves a generated `/llms.txt` (facts, classes, prices, retreats, policies) |
| GPTBot blocked (429) by Hostinger CDN | Settings change in hPanel (install guide, step 7); `robots.txt` explicitly allows AI search and assistant crawlers |
| Conflicting facts | One facts registry feeds pages, schema and `llms.txt` |
| No machine-readable entity | One `@graph`: Organization ↔ founder `Person` ↔ `Course` ↔ `Offer` ↔ `EducationEvent`, linked by `@id`, with `sameAs` to Google Business Profile, YouTube, Facebook, Instagram and TripAdvisor |
| Prices scattered in prose | Price table rendered from one source and repeated in Course `offers` and `llms.txt` |
| No answer-first blocks | Every class page opens with a one-paragraph answer plus an "At a glance" facts table, and ends with FAQs (the format AI answers quote most) |
| Third-party listings disagree | Off-site cleanup list in the content plan (threebestrated.in, Google Business Profile, TripAdvisor, social bios) |
| Instagram handle `@pushkarmusicretreat` ≠ brand | Keep it in `sameAs`, but add "Krishna Music School" to the profile name and bio so the entities connect |

## 10. Accessibility and security (quick findings)

- Social icon links in the footer have no accessible name. The cart icon link is empty.
- Emojis are used as icons in headings, so screen readers announce them ("graduation cap Start Your Musical Journey").
- `/wp-json/wp/v2/users` user enumeration (see §4).
- `xmlrpc.php` returns 403, which is good.

## 11. Results of the new build (tested on a local copy of WordPress 7.1 with the same content)

| Check | Old site | New theme + KMS Core |
|---|---|---|
| Homepage HTML (compressed) | 109 KB | 22 KB |
| Structured data | 1 broken block + 24 pages of fake ratings | 1 valid graph on every page type; 0 `AggregateRating` |
| `/llms.txt` | 301 → homepage | 200, generated from live data |
| Missing URL | 301 → homepage | 404 |
| Accessibility (axe-core, WCAG 2.1 AA, desktop and mobile, 8 page types) | not measured | 0 violations |
| Enquiry form | 2 forms on Contact, broken newsletter on every page | 1 form, saved and emailed, works on cached pages, WhatsApp hand-off, tested end to end |
| Old Divi pages after the theme switch | would show raw `[et_pb_*]` shortcodes | content rendered, embedded fake ratings removed |
| Rank Math | outputs a "Person" graph | keeps titles, meta and sitemaps; KMS Core supplies the single JSON-LD graph and a default share image |

## 12. What I could not check

Google Search Console data (clicks per URL, coverage, CWV field data), the Google Business Profile dashboard, the TripAdvisor listing (it blocks automated access), backlinks, and WordPress admin settings. The redirect plan says "check Search Console first" wherever traffic data should decide whether a post is kept.

---

## 13. What the new theme fixes automatically vs. what needs admin work

| Fixed by `kms-theme` + `kms-core` on activation | Needs a person in WP admin / hPanel |
|---|---|
| One H1 per page, correct heading order, comments heading as H2 | Confirm facts (worksheet) and enter them in **Settings → KMS Facts** |
| Valid schema graph (replaces Rank Math/Yoast JSON-LD, no fake ratings) | Remove the old JSON-LD from the Divi code modules (`/`, `/online-harmonium-lessons-usa/`) and remove `AggregateRating` from post content |
| Online-first homepage, class hub `/online-classes/`, class pages with price table, time-zone converter, currency switcher | Remove the "404 → homepage" redirect; add [`redirects.htaccess`](redirects.htaccess) (or import [`redirects.csv`](redirects.csv)) |
| Working enquiry form (leads saved in WP **and** emailed, with WhatsApp handoff); no fake newsletter | Delete the Divi demo pages, `/home/` and the duplicate retreat-terms page; deactivate WooCommerce and Elementor |
| `/llms.txt`, AI-crawler-friendly `robots.txt` | Hostinger: allow GPTBot, enable page cache |
| Breadcrumbs (visible and in schema), real 404 template | Featured images for posts; author = Vini Devda |
| Fast: one stylesheet (11 KB compressed), two small scripts (4 KB compressed), no jQuery, no page builder | Blog merges and rewrites (content plan) |
| Divi fallback: old Divi pages render their content instead of raw `[et_pb_…]` shortcodes | Check that each migrated page looks right |

---

## Appendix A — per-URL findings

| URL | Title chars | Desc chars | H1s | Words | Issues |
|---|---|---|---|---|---|
| `/` | 80 | 165 | 1 | 1161 | title 80ch, desc 165ch, no og:image, BROKEN JSON-LD |
| `/10-authentic-spiritual-experiences-in-dharamshala-that-arent-meditation/` | 74 | 226 | 1 | 3854 | title 74ch, desc 226ch, no og:image |
| `/10-tips-for-mastering-carnatic-vocal-techniques-for-beginners/` | 84 | 157 | 1 | 801 | title 84ch, no og:image |
| `/15-amazing-things-to-do-at-pushkar-fair/` | 67 | 164 | 1 | 2268 | title 67ch, desc 164ch, no og:image |
| `/30-days-in-dharamshala-a-digital-nomads-month-long-itinerary/` | 85 | 211 | 1 | 5125 | title 85ch, desc 211ch, no og:image |
| `/5-skills-you-can-learn-in-dharamshala-that-will-change-your-life/` | 89 | 161 | 1 | 3129 | title 89ch, desc 161ch, no og:image |
| `/7-days-in-dharamshala-perfect-spiritual-seeker-itinerary/` | 80 | 155 | 1 | 8893 | title 80ch, no og:image |
| `/8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium/` | 50 | 151 | 0 | 2193 | no H1 |
| `/about-us/` | 31 | 152 | 1 | 1639 | — |
| `/advanced-alankar-techniques/` | 112 | 155 | 1 | 967 | title 112ch, no og:image |
| `/advanced-harmonium-techniques-for-kirtan-leaders-professional-mastery-guide/` | 76 | 162 | 1 | 5573 | title 76ch, desc 162ch, no og:image |
| `/advanced-harmonium-techniques-for-kirtan/` | 70 | 155 | 1 | 4042 | title 70ch, no og:image |
| `/alankars-in-singing/` | 93 | 175 | 1 | 986 | title 93ch, desc 175ch, no og:image |
| `/basics-of-sa-re-ga-ma-in-indian-vocals/` | 72 | 182 | 1 | 3187 | title 72ch, desc 182ch, no og:image |
| `/beginner-kirtan-tips/` | 93 | 141 | 1 | 969 | title 93ch, no og:image |
| `/best-online-music-lessons-uk/` | 51 | 167 | 2 | 3109 | desc 167ch, 2×H1, no og:image |
| `/best-online-music-teachers-for-indian-classical-vocals/` | 71 | 180 | 1 | 3563 | title 71ch, desc 180ch, no og:image |
| `/bhajan-and-kirtan-classes-online-uk/` | 59 | 134 | 2 | 1851 | 2×H1, no og:image |
| `/bhajan-kirtan-devotional-music-nights-in-pushkar/` | 51 | 142 | 1 | 2009 | — |
| `/bhajan-kirtan-singing-workshops/` | 56 | 158 | 1 | 1633 | — |
| `/bhajan-vs-kirtan/` | 101 | 152 | 1 | 4290 | title 101ch, no og:image |
| `/bollywood-dance-music-shows-for-events-weddings/` | 51 | 161 | 1 | 2171 | desc 161ch |
| `/breath-control-in-singing/` | 104 | 169 | 1 | 955 | title 104ch, desc 169ch, no og:image |
| `/cart/` | 27 | 68 | 1 | 21 | no og:image, noindex but in sitemap, thin (21 words) |
| `/category/alankars-palta/` | 39 | 0 | 0 | 61 | no meta desc, no H1, no og:image, thin (61 words) |
| `/category/blog/` | 27 | 18 | 0 | 65 | no H1, no og:image, thin (65 words) |
| `/category/education/` | 32 | 0 | 0 | 64 | no meta desc, no H1, no og:image, thin (64 words) |
| `/category/guide/` | 28 | 0 | 0 | 71 | no meta desc, no H1, no og:image, thin (71 words) |
| `/category/kirtan-bhajan/` | 38 | 0 | 0 | 41 | no meta desc, no H1, no og:image, thin (41 words) |
| `/category/pushkar/` | 30 | 0 | 0 | 57 | no meta desc, no H1, no og:image, thin (57 words) |
| `/category/ragas-theory/` | 37 | 0 | 0 | 62 | no meta desc, no H1, no og:image, thin (62 words) |
| `/category/retreats/` | 31 | 0 | 0 | 60 | no meta desc, no H1, no og:image, thin (60 words) |
| `/classical-bhajans/` | 99 | 148 | 1 | 5874 | title 99ch, no og:image |
| `/contact-us/` | 33 | 117 | 1 | 585 | no og:image |
| `/corporate-musical-events-cultural-shows/` | 64 | 130 | 1 | 2661 | title 64ch |
| `/courses/` | 30 | 156 | 0 | 329 | no H1, no og:image |
| `/daily-palta-routines/` | 114 | 188 | 1 | 905 | title 114ch, desc 188ch, no og:image |
| `/dharamshala-in-may-best-time-visit-dharamshala/` | 79 | 151 | 1 | 7156 | title 79ch, no og:image |
| `/digital-nomads-guide-to-dharamshala-work-from-the-himalayas/` | 61 | 184 | 1 | 3998 | title 61ch, desc 184ch, no og:image |
| `/dinner-concerts-private-music-evenings/` | 63 | 106 | 1 | 3013 | title 63ch, no og:image |
| `/disclaimer/` | 33 | 157 | 1 | 277 | no og:image, thin (277 words) |
| `/events/` | 29 | 36 | 1 | 160 | no og:image, thin (160 words) |
| `/folk-music-band-chokhi-vini-project-pushkar/` | 69 | 125 | 1 | 1990 | title 69ch |
| `/from-tourist-to-student-transformative-learning-experiences-in-upper-bhagsu/` | 76 | 231 | 1 | 3679 | title 76ch, desc 231ch, no og:image |
| `/fusion-instrumental-groups/` | 49 | 130 | 1 | 2731 | — |
| `/fusion-jam-sessions/` | 51 | 154 | 1 | 2372 | — |
| `/grand-music-concerts-stage-programs/` | 60 | 146 | 1 | 3464 | — |
| `/harmonium-chords-and-scales-explained/` | 57 | 150 | 1 | 2592 | no og:image |
| `/harmonium-for-jazz-musicians/` | 86 | 162 | 1 | 3987 | title 86ch, desc 162ch, no og:image |
| `/harmonium-in-bhajans/` | 83 | 149 | 1 | 871 | title 83ch, no og:image |
| `/harmonium-lessons-for-yoga-teachers/` | 80 | 161 | 1 | 6002 | title 80ch, desc 161ch, no og:image |
| `/harmonium-teacher-online-for-personalized-lessons/` | 111 | 154 | 1 | 5211 | title 111ch, no og:image |
| `/hear-from-our-attendees/` | 46 | 139 | 1 | 1626 | no og:image |
| `/hidden-gems-in-upper-bhagsu-a-locals-guide-to-secret-spots/` | 60 | 241 | 1 | 3320 | desc 241ch, no og:image |
| `/home/` | 27 | 158 | 1 | 903 | no og:image |
| `/how-to-choose-the-right-instrument-for-your-child-violin-vs-mridangam/` | 94 | 152 | 1 | 741 | title 94ch, no og:image |
| `/how-to-improve-singing-voice-quality/` | 57 | 154 | 1 | 1866 | no og:image |
| `/how-to-lead-kirtan-at-your-yoga-studio-complete-guide/` | 54 | 171 | 1 | 3571 | desc 171ch, no og:image |
| `/how-to-practice-alankars-correctly/` | 96 | 161 | 1 | 915 | title 96ch, desc 161ch, no og:image |
| `/how-to-practice-palta/` | 106 | 177 | 1 | 744 | title 106ch, desc 177ch, no og:image |
| `/how-to-sing-bhajans-with-harmonium/` | 55 | 175 | 1 | 5940 | desc 175ch, no og:image |
| `/indian-classical-music-classes-online-uk/` | 40 | 184 | 1 | 2502 | desc 184ch, no og:image |
| `/indian-classical-music-performance/` | 102 | 157 | 1 | 1103 | title 102ch, no og:image |
| `/indian-classical-music-ragas/` | 103 | 216 | 1 | 2458 | title 103ch, desc 216ch, no og:image |
| `/indian-classical-music-singing-exercises/` | 66 | 158 | 1 | 4171 | title 66ch, no og:image |
| `/indian-classical-raga-khayal/` | 53 | 134 | 1 | 3613 | — |
| `/indian-classical-singing-basics/` | 109 | 189 | 1 | 837 | title 109ch, desc 189ch, no og:image |
| `/indian-classical-singing-exercises/` | 90 | 141 | 1 | 808 | title 90ch, no og:image |
| `/indian-music-classes-online-usa/` | 57 | 143 | 1 | 4020 | no og:image |
| `/indian-music-school-online-with-reviews/` | 82 | 203 | 1 | 3190 | title 82ch, desc 203ch, no og:image |
| `/kabali-sufi-musical-experience/` | 53 | 156 | 1 | 4595 | — |
| `/kirtan-for-beginners/` | 99 | 126 | 1 | 920 | title 99ch, no og:image |
| `/kirtan-meditation-devotional-music-for-mental-health/` | 55 | 160 | 1 | 3191 | no og:image |
| `/krishna-music-school-concert/` | 51 | 49 | 1 | 326 | — |
| `/learn-indian-classical-music/` | 113 | 161 | 1 | 992 | title 113ch, desc 161ch, no og:image |
| `/learn-tala-online/` | 78 | 157 | 1 | 743 | title 78ch, no og:image |
| `/learn-traditional-indian-arts-in-the-himalayas-a-complete-guide/` | 87 | 64 | 1 | 3578 | title 87ch, no og:image |
| `/learn-violin-online/` | 96 | 152 | 1 | 770 | title 96ch, no og:image |
| `/making-friends-while-traveling-social-activities-in-upper-bhagsu/` | 65 | 238 | 1 | 3661 | title 65ch, desc 238ch, no og:image |
| `/mantra-chanting-for-beginners-a-travelers-introduction/` | 69 | 150 | 1 | 3814 | title 69ch, no og:image |
| `/mantra-chanting-harmonium-workshops/` | 60 | 150 | 1 | 3755 | — |
| `/master-raag-yaman-the-most-important-raga-for-beginners/` | 56 | 253 | 1 | 5610 | desc 253ch, no og:image |
| `/multi-instrument-training/` | 48 | 148 | 1 | 4008 | — |
| `/music-classes-bhagsu-himachal/` | 110 | 159 | 1 | 789 | title 110ch |
| `/music-learning-tips-for-adults-and-beginners/` | 56 | 156 | 1 | 2233 | no og:image |
| `/music-retreats-vs-yoga-retreats-in-india/` | 65 | 164 | 1 | 4450 | title 65ch, desc 164ch, no og:image |
| `/my-account/` | 33 | 0 | 1 | 19 | no meta desc, no og:image, noindex but in sitemap, thin (19 words) |
| `/nagada-drum-percussion-workshops/` | 57 | 156 | 1 | 3870 | — |
| `/one-to-one-online-music-lessons-personalized-learning-for-all-levels/` | 69 | 164 | 1 | 4078 | title 69ch, desc 164ch, no og:image |
| `/online-harmonium-lessons-uk/` | 68 | 227 | 1 | 2701 | title 68ch, desc 227ch, no og:image |
| `/online-harmonium-lessons-usa/` | 64 | 148 | 1 | 3975 | title 64ch, no og:image, BROKEN JSON-LD |
| `/privacy-policy/` | 37 | 152 | 1 | 510 | no og:image |
| `/pushkar-fair-2025-complete-guide/` | 57 | 167 | 2 | 3786 | desc 167ch, 2×H1, no og:image |
| `/pushkar-retreat-guidelines-terms-of-participation-2/` | 74 | 58 | 1 | 1229 | title 74ch, no og:image |
| `/pushkar-retreat-guidelines-terms-of-participation/` | 74 | 58 | 1 | 1229 | title 74ch, no og:image |
| `/raga-theory-for-non-musicians-understand-indian-music-basics-in-2026/` | 69 | 169 | 1 | 4498 | title 69ch, desc 169ch, no og:image |
| `/rajasthani-cultural-concerts-festivals/` | 63 | 141 | 1 | 3258 | title 63ch |
| `/retreat-guidelines-terms-of-participation/` | 66 | 58 | 1 | 779 | title 66ch, no og:image |
| `/shop-now/` | 31 | 36 | 1 | 160 | no og:image, thin (160 words) |
| `/shop/` | 27 | 39 | 1 | 21 | no og:image, thin (21 words) |
| `/singing-workshops/` | 40 | 144 | 1 | 3430 | — |
| `/solo-artists/` | 35 | 148 | 1 | 3175 | — |
| `/solo-female-travelers-guide-to-upper-bhagsu-mcleod-ganj/` | 86 | 154 | 1 | 4723 | title 86ch, no og:image |
| `/spiritual-music-benefits/` | 106 | 146 | 2 | 886 | title 106ch, 2×H1, no og:image |
| `/summer-music-retreat-upper-bhagsu/` | 73 | 256 | 0 | 2079 | title 73ch, desc 256ch, no H1 |
| `/swara-practice-for-beginners/` | 80 | 158 | 1 | 825 | title 80ch, no og:image |
| `/terms-and-conditions/` | 43 | 147 | 1 | 1494 | no og:image |
| `/thaat-in-indian-classical-music/` | 93 | 219 | 1 | 1058 | title 93ch, desc 219ch, no og:image |
| `/the-healing-power-of-sound-music-spirituality-in-the-himalayas/` | 88 | 78 | 1 | 4291 | title 88ch, no og:image |
| `/understanding-tala-and-rhythm-in-indian-music/` | 97 | 157 | 1 | 4197 | title 97ch, no og:image |
| `/upper-bhagsu-travel-guide-things-to-do-where-to-stay/` | 60 | 154 | 1 | 6303 | no og:image |
| `/what-is-kirtan-a-complete-guide-for-western-travelers/` | 54 | 203 | 1 | 5705 | desc 203ch, no og:image |
| `/what-is-kirtan/` | 100 | 154 | 1 | 910 | title 100ch, no og:image |
