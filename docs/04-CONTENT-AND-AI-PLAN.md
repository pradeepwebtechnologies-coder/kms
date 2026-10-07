# Content, SEO and AI-engine plan

The goal is **enrolments in online classes**. Each recommendation below either makes the online offer easier to find and trust (for Google and AI assistants) or easier to say yes to.

## 1. Site structure

```
/                              Homepage — online first
/online-classes/               Hub: every class, how it works, prices, FAQ, enquiry form
  /online-classes/harmonium/
  /online-classes/singing/
  /online-classes/bhajan-kirtan/
  /online-classes/indian-classical-vocal/
  /online-classes/tabla/        ← new: the founder's main instrument; most TripAdvisor reviews are about tabla
/pricing/                      ← new (was a broken footer link)
/faq/                          ← new (was a broken footer link)
/refund-policy/                ← new (the site promised a money-back guarantee but had no policy)
/about-us/                     Rebuilt: founder entity page with a facts table
/hear-from-our-attendees/      Rebuilt: genuine reviews only, linked to the source
/contact-us/                   Rebuilt: enquiry form first
/8-day-winter-music-retreat-…/ Retreat (selling now — Nov 2026 to Feb 2027)
/summer-music-retreat-upper-bhagsu/
Performance pages              Unchanged URLs, grouped under "Performances"
/blog/                         ← new posts page (was a broken footer link)
/llms.txt                      ← new, generated
```

## 2. Titles and meta descriptions (enter in Rank Math)

Titles stay under 60 characters and descriptions under 155. None of them repeat prices, so they won't go stale.

| Page | SEO title | Meta description |
|---|---|---|
| Home | Online Indian Music Classes \| Krishna Music School | Live 1-to-1 harmonium, singing, kirtan, raga and tabla classes with Vini Devda in Pushkar, India. Beginners welcome. Book a free consultation. |
| /online-classes/ | Online Indian Music Classes: Harmonium, Singing, Tabla | Compare live one-to-one online classes in harmonium, Indian singing, bhajan & kirtan, raga and tabla. Prices in ₹ and US$; classes in your time zone. |
| Harmonium | Online Harmonium Classes – Live 1-to-1 from Pushkar | Learn harmonium online with Vini Devda. Live one-to-one classes for beginners, yoga teachers and kirtan leaders. Play 4–5 bhajans within 10 classes. |
| Singing | Online Indian Singing Classes – Live 1-to-1 Lessons | Learn Indian singing online, from Sa Re Ga Ma to ragas and bhajans, with Vini Devda. Live one-to-one lessons for beginners and experienced singers. |
| Bhajan & Kirtan | Online Bhajan & Kirtan Classes for Yoga Teachers | Learn to sing and lead kirtan online: mantras, bhajans, call-and-response and simple harmonium, taught live and one-to-one from Pushkar, India. |
| Indian classical vocal | Hindustani Classical Vocal Classes Online (Raga & Khayal) | Structured one-to-one Hindustani classical vocal training online: swar sadhana, ragas, alaap, bandish and taan with Vini Devda in Pushkar. |
| Tabla | Online Tabla Classes – Live 1-to-1 Lessons from India | Learn tabla online with Vini Devda: hand technique, bols, thekas like Teentaal and Keharwa, and your first compositions. Beginners welcome. |
| About | About Krishna Music School & Founder Vini Devda | Founded in Pushkar in 2008 by Vini Devda, a third-generation musician. Our story, teaching method and the school at the historic Rangji Temple. |
| Reviews | Student Reviews – Krishna Music School Pushkar | Reviews from students who learned tabla, singing, sitar and raga with Vini Devda in Pushkar, with links to the originals on TripAdvisor. |
| Pricing | Prices for Online Indian Music Classes | Prices for live one-to-one harmonium, singing, kirtan, raga and tabla classes, in rupees with approximate US$, € and £. First-class refund guarantee. |
| FAQ | FAQ – Online Indian Music Classes | What you need, class length, time zones, payment, refunds, children, certificates, retreats and live performances: quick answers. |
| Contact | Contact & Free Consultation – Krishna Music School | Book a free 15-minute consultation for online or in-person classes. WhatsApp +91 99286 58520, email us, or visit the school at Rangji Temple, Pushkar. |
| Winter retreat | Pushkar Music Retreat 2026–27: Singing & Harmonium | 8-day winter music retreat in Pushkar: singing, mantra chanting, kirtan, bhajan and harmonium. Batches from Nov 2026 to Feb 2027, max 10 students. |
| Summer retreat | Himalaya Music Retreat in Upper Bhagsu, Dharamshala | 10-day summer singing and harmonium retreat in Upper Bhagsu, Dharamshala. Small groups, beginners welcome. 2027 dates coming soon. |

Also fix these site-wide:

- **Posts.** Remove "\| Krishna Music School" from the 15 post H1s, cut titles to ≤ 60 characters, and set **author = Vini Devda**. Create a WordPress user with that display name, a bio and a photo, then reassign posts.
- **Featured images.** Every post and class needs one: a real photo from the school, 1200×675 or larger. 65 of 68 posts have none.
- **Categories.** Give each category page a 1–2 sentence description, or set them to noindex in Rank Math.

## 3. Blog: keep, merge, remove

Merging thin posts that compete with each other (cannibalisation) into one strong guide usually lifts the surviving page. **Before merging, check each URL's clicks in Search Console for the last 6 months.** Any post with meaningful clicks gets its best paragraphs moved into the target first. Redirects are ready in [`redirects.htaccess`](redirects.htaccess), section B.

| Action | Posts |
|---|---|
| **Merge into `/indian-classical-music-singing-exercises/`** | alankars-in-singing, how-to-practice-alankars-correctly, advanced-alankar-techniques, indian-classical-singing-basics, learn-indian-classical-music, indian-classical-singing-exercises, how-to-practice-palta, daily-palta-routines |
| **Merge into the kirtan guides** | what-is-kirtan and kirtan-for-beginners → what-is-kirtan-a-complete-guide-for-western-travelers; beginner-kirtan-tips → how-to-lead-kirtan-at-your-yoga-studio-complete-guide |
| **Merge duplicates** | advanced-harmonium-techniques-for-kirtan → …-leaders-professional-mastery-guide; spiritual-music-benefits → kirtan-meditation-devotional-music-for-mental-health; harmonium-in-bhajans → how-to-sing-bhajans-with-harmonium; breath-control-in-singing → how-to-improve-singing-voice-quality; swara-practice-for-beginners → basics-of-sa-re-ga-ma-in-indian-vocals |
| **Redirect to class pages** (sales pages written as blog posts; the country versions look like doorway pages) | harmonium-teacher-online-…, online-harmonium-lessons-usa, online-harmonium-lessons-uk, bhajan-and-kirtan-classes-online-uk, indian-classical-music-classes-online-uk, best-online-music-teachers-for-indian-classical-vocals, indian-music-classes-online-usa, best-online-music-lessons-uk, one-to-one-online-music-lessons-…, indian-music-school-online-with-reviews |
| **Remove (410)** — the school teaches Hindustani, not Carnatic music or violin | 10-tips-for-mastering-carnatic-vocal-techniques-for-beginners, how-to-choose-the-right-instrument-for-your-child-violin-vs-mridangam, learn-violin-online · learn-tala-online → 301 to understanding-tala-and-rhythm-in-indian-music |
| **Update every year** | pushkar-fair-2025-complete-guide and 15-amazing-things-to-do-at-pushkar-fair (change to an evergreen slug, e.g. `/pushkar-fair-guide/`, with a 301) |
| **Keep and improve** (add a photo or video, author box, class link, one-paragraph answer at the top) | how-to-sing-bhajans-with-harmonium, classical-bhajans, bhajan-vs-kirtan, harmonium-chords-and-scales-explained, harmonium-lessons-for-yoga-teachers, harmonium-for-jazz-musicians, master-raag-yaman…, raga-theory-for-non-musicians…, indian-classical-music-ragas, thaat-in-indian-classical-music, understanding-tala-and-rhythm-in-indian-music, basics-of-sa-re-ga-ma…, how-to-improve-singing-voice-quality, music-learning-tips-for-adults-and-beginners, kirtan-meditation…, mantra-chanting-for-beginners… |
| **Keep only if Search Console shows traffic** (travel posts that serve the Himalaya retreat, not online classes) | the ten Dharamshala / Upper Bhagsu guides |

**Stop** publishing generic, AI-written travel posts. Google's helpful-content systems and AI assistants both discount them. One genuine article a month, with Vini's own teaching, a photo or video and a class link, does more than ten generic ones.

## 4. Getting recommended by AI assistants (GEO / AEO)

AI assistants (ChatGPT, Gemini, Perplexity, Claude, Copilot) recommend a school when three things line up. They can **read** the site, the **facts agree** everywhere they look, and **other sources** confirm those facts. The theme and plugin cover the first two on the website. The rest is off-site work.

### 4.1 Done in this build

- `/llms.txt`: a plain-language summary of facts, classes, prices, retreats and policies, generated from the live data.
- `robots.txt`: explicit Allow rules for OAI-SearchBot, ChatGPT-User, GPTBot, ClaudeBot, PerplexityBot, Google-Extended, Applebot-Extended and others.
- One JSON-LD graph per page, linked by `@id`: Organization ↔ founder `Person` ↔ `Course` (with `offers`) ↔ `EducationEvent` ↔ `FAQPage` ↔ `BreadcrumbList`. There are no fake ratings.
- Answer-first class pages: a 2–3 sentence summary, then an "At a glance" table (format, length, level, teacher, language, ages, price, hours in your time zone), then FAQs.
- An "At a glance" facts table on the About page, written in full sentences that are easy to quote.
- Prices in one place, so an assistant can't find two different prices.

### 4.2 Off-site work, in order of impact

1. **Google Business Profile.** Same name, address (**PIN 305022**), phone and website as the site. Primary category *Music school*; add *Music instructor* if available. Add services (Online harmonium classes, Online singing classes and so on, linking to each class page), weekly photos, and questions and answers. Gemini and Google AI Overviews draw heavily on this profile.
2. **Reviews from online students.** The genuine reviews are all from in-person students in 2014–2020. Ask every online student who finishes a package for a Google review. Message template:
   > "Namaste {name}! It was a joy teaching you. If the classes helped you, would you share a few words on Google? It helps other students find us: {google_review_link} 🙏 — Vini"

   Paste the review link into KMS Facts → *Google "write a review" link*; it then also appears on the Reviews page. Never write, buy or reward reviews.
3. **Fix third-party listings that disagree.** threebestrated.in says "Laxmi Market", "since 2013" and "Vini Devra". Ask them to correct it. Make the same check on TripAdvisor (add the website link and the online classes), Justdial and Sulekha if listed, and every social bio.
4. **Instagram.** The handle is `@pushkarmusicretreat`. Put "Krishna Music School" in the profile name and the website in the bio so AI systems connect the two.
5. **YouTube.** Assistants, especially Gemini and Perplexity, cite videos. Publish 2–3 minute lessons ("How to pump harmonium bellows", "Your first kirtan: Om Namah Shivaya"). Use consistent titles, descriptions that link to the matching class page, and correct captions.
6. **Earned mentions.** Ask the London school where Vini ran workshops (see Elle A.'s review), and any yoga studios or retreat centres you work with, to mention and link to the school on their sites. A dozen genuine mentions from relevant sites beat any amount of on-site work.
7. **Wikidata (optional).** Create an item for the school only if you can cite independent sources, such as an event page or a press article. Don't create a Wikipedia article.

### 4.3 Content that answers what students ask AI assistants

Write each piece answer-first: the first paragraph answers the question in 2–3 sentences. Add a comparison table or checklist, Vini's own experience, and a link to the class.

1. How long does it take to learn harmonium? A realistic timeline.
2. Harmonium vs keyboard for kirtan: which should a beginner buy?
3. Buying a harmonium outside India: what to look for (USA, UK, Europe).
4. Can you learn Indian classical singing online? What works and what doesn't.
5. Hindustani vs Carnatic music: the difference, and which to learn.
6. How much do online harmonium and tabla lessons cost? Use the school's own prices plus what affects price.
7. Kirtan for yoga teachers: leading your first chant in five classes.
8. Online classes vs a retreat in Pushkar: which is right for you?

## 5. Measuring results

| What | Where |
|---|---|
| Enquiries per week, by class and by "How did you find us?" (includes **AI assistant**) | WordPress → Enquiries → Download CSV |
| `generate_lead` (form sent) and `whatsapp_click` | GA4 (Site Kit): mark both as key events |
| Visits from AI assistants | GA4 → Traffic acquisition → session source containing `chatgpt.com`, `perplexity.ai`, `gemini.google.com`, `copilot.microsoft.com`, `claude.ai` |
| Rankings and clicks for "online harmonium classes", "learn kirtan online", "online tabla classes", "music school Pushkar" | Search Console → Performance |
| AI answers | Once a month, ask ChatGPT, Gemini, Perplexity and Claude the same 8 questions (e.g. "best online harmonium classes for beginners", "learn kirtan online from India", "music school in Pushkar"). Record whether the school is named and whether the facts are right. |

## 6. 90-day plan

| Weeks | Work |
|---|---|
| 1–2 | Confirm facts → install on staging → import → review → go live. Hostinger: allow GPTBot, enable cache, SMTP. Section A redirects. |
| 3–4 | Google Business Profile clean-up and services. Fix threebestrated.in and social bios. Start asking online students for reviews. Rebuild the winter retreat page. |
| 5–8 | Blog merges (section B redirects) after the Search Console check. Featured images and author on the remaining posts. Two answer-first articles. Four YouTube lessons. |
| 9–12 | Rebuild the remaining Divi pages and switch off legacy styles. Two more articles. First monthly AI-answer check. Compare enquiries with the baseline. |
