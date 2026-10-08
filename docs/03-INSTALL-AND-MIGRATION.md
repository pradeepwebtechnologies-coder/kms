# Install and migration guide

Plan for about half a day: two hours of setup, then a careful page-by-page check. **Do it on a Hostinger staging copy first** (hPanel → Websites → *krishnamusicschool.com* → WordPress → Staging). Push to live only after the checklist at the end passes.

## 0. Before you start

1. Take a full backup (hPanel → Files → Backups, or your backup plugin).
2. Export the Rank Math settings (Rank Math → Status & Tools → Import & Export). Also export redirections if you use them.
3. Record the current traffic baseline: Google Search Console → Performance → last 3 months → export. You need it to judge the blog merges in the content plan.

## 1. Install the plugin and the theme

1. Zip the two folders, or use `dist/kms-core.zip` and `dist/kms-theme.zip` (built by `bin/build.sh`).
2. **Plugins → Add New → Upload** `kms-core.zip` → Activate.
3. **Appearance → Themes → Add New → Upload** `kms-theme.zip`. **Don't activate yet.**

> **Divi warning.** The moment Divi stops being the active theme, pages built with the Divi Builder stop rendering through Divi. KMS Core handles this with its **legacy fallback**: it strips the `[et_pb_*]` shortcodes and keeps the HTML inside them. The site's pages keep almost all their content in Divi *Code* and *Text* modules, so they keep working. If your Elegant Themes licence includes the **Divi Builder plugin**, you can install that instead and the fallback switches itself off. Either way, rebuild pages one at a time later (step 8).

## 2. Enter the facts (the most important step)

**Settings → KMS Facts.** Go through [`02-FACTS-TO-CONFIRM.md`](02-FACTS-TO-CONFIRM.md) with the school first. Then:

- Fix the **PIN code** (305022) and the **founder name** spelling, and add the **TripAdvisor listing URL**.
- Set the founder photo, logo and default share image to Media Library URLs.
- Update ratings and their "last checked" month.
- Review the **retreat batches** and add the 2027 Himalaya dates when they are known. Past batches hide automatically. **Delete a batch as soon as it is full**: every batch on the list is shown as open for booking, on the site and to Google.
- Leave every box under **Search and AI engines** ticked.

Click **Run checks** at the top of the page. It tests 404 handling, `/llms.txt`, robots.txt, facts and unused plugins.

## 3. Import the starter content

**Tools → KMS Starter Content**:

- Leave "publish immediately" **unticked**. Classes, FAQs and reviews are created as drafts.
- Tick "Replace the content of the existing About, Reviews and Contact pages". The old versions stay in each page's *Revisions*, so nothing is lost.
- Click Import.

Then review and publish:

1. **Classes** (Harmonium, Singing, Bhajan & Kirtan, Indian Classical Vocal, Tabla). Check every price and claim, add a featured image (a real photo of a class, 1200×800), then publish. Prices are in the **Prices** box in the editor's right sidebar. Everything else (who it's for, outcomes, formats, the answer-first summary) is in **Class details**, inside the *Meta Boxes* panel at the bottom of the editor.
2. **FAQs.** Read each answer and publish.
3. **Reviews.** Match each one against TripAdvisor, tick **Verified**, and add the direct review link if you have it. Publish.
4. **Pages.** Pricing, FAQ, Refund policy (fill in the bracketed points first), About, Reviews and Contact.

## 4. Activate the theme and set up the site

1. **Appearance → Themes → Krishna Music School → Activate.**
2. **Appearance → Customize → Site Identity**: upload the logo. The current logo has a black background; the new header is dark, so it fits. Set the site icon too.
3. **Appearance → Customize → Krishna Music School**: homepage hero texts and image, plus the announcement bar (leave it empty to show the next retreat automatically).
4. **Settings → Reading**: the homepage stays the static page it is now; set **Posts page = Blog**.
5. **Appearance → Menus**: you can skip this. With no menu assigned, the theme builds the recommended menu automatically (Online classes ▸ each class + Prices · Retreats · Performances · About ▸ About, Reviews, FAQ, Blog). If you build your own, put **Online classes first** and use real URLs for parent items, not `#`.
6. **Settings → Permalinks → Save** (refreshes `/online-classes/`).

## 5. Rank Math settings

1. **Titles & Meta → Local SEO**: set to *Organization*, name "Krishna Music School", logo. KMS Core replaces Rank Math's JSON-LD while *Output the KMS structured-data graph* is ticked, but this keeps both consistent.
2. **Titles & Meta → Global → OpenGraph thumbnail**: set the hero photo. KMS Core also fills it in automatically.
3. **Sitemap → General**: exclude the *Cart / Checkout / My account / Shop* pages if WooCommerce stays active. After step 6 they no longer exist.
4. **Titles & Meta**: write new titles and descriptions for the key pages using the table in [`04-CONTENT-AND-AI-PLAN.md`](04-CONTENT-AND-AI-PLAN.md) §2.
5. Remove the per-page Rank Math **schema** you added manually (Course, Event, MusicGroup with ratings). It is no longer printed, but keeping it is misleading for the next editor.

## 6. Clean up

| Do this | Why |
|---|---|
| **Turn off the "404 → homepage" redirect** (an "All 404 Redirect to Homepage" type plugin, or Rank Math → Redirections → "Redirect 404s") | Every missing URL currently returns a 301 to `/`, which Google treats as a soft 404. KMS *Run checks* confirms when it is fixed. |
| Add the redirects from [`redirects.htaccess`](redirects.htaccess) at the top of `.htaccess`, or import [`redirects.csv`](redirects.csv) into the *Redirection* plugin | Moves old, duplicate and junk URLs to the right new pages. Section A is required now; section B after checking Search Console. |
| Delete `/courses/`, `/events/`, `/shop-now/` (Divi movie-theatre demo with lorem ipsum), `/home/` (old duplicate homepage) and `/pushkar-retreat-guidelines-terms-of-participation-2/` | Junk and duplicate pages that are indexed today. |
| **Deactivate WooCommerce** (nothing is sold), then delete Shop, Cart, Checkout and My account | Removes CSS/JS from every page and the cart icon. |
| **Deactivate Elementor** (no page uses it) | Removes unused CSS/JS. |
| Delete the old JSON-LD from the homepage's Divi code module and from `/online-harmonium-lessons-usa/` | Already hidden by KMS Core (*Remove JSON-LD pasted inside content*), but delete it at the source too. |
| Remove the invented testimonials listed in the facts worksheet from every page | Legal and trust risk. Use the Reviews system instead. |
| Site Kit → Sign in with Google: turn off One Tap for visitors | It loads `accounts.google.com` on every page for nobody. |

## 7. Hostinger

1. **Allow GPTBot.** During the audit the Hostinger CDN answered OpenAI's crawler (user agent `GPTBot`) with **HTTP 429** on every attempt, while other AI and search crawlers got 200. Look in hPanel → *Websites → krishnamusicschool.com →* **CDN** / **Security** for bot protection, AI-crawler blocking or a rate-limit rule, and allow GPTBot. If you can't find it, send Hostinger support:
   > "Your CDN (hcdn) returns HTTP 429 to the GPTBot user agent on every request to krishnamusicschool.com, while other crawlers get 200. Please allow OpenAI's crawlers (GPTBot, OAI-SearchBot, ChatGPT-User) for this site."

   Test afterwards: `curl -I -A "Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)" https://krishnamusicschool.com/` should return 200. *(The audit test used the GPTBot user-agent string from a non-OpenAI IP, so confirm in the CDN analytics or logs too.)*
2. **Page cache.** Enable Hostinger's cache or LiteSpeed Cache. TTFB was 0.8–1.4 s with no cache. The enquiry form is safe on cached pages: it doesn't rely on WordPress nonces.
3. **Email.** Install an SMTP plugin (e.g. WP Mail SMTP) with a real mailbox, so enquiry emails don't land in spam. Enquiries are also always saved under **Enquiries** in WordPress. After SMTP works, you can turn on the automatic confirmation email in KMS Facts.
4. **Redirect chain.** Make `http://www` go straight to `https://krishnamusicschool.com` in one hop.

## 8. Rebuild the old Divi pages, one at a time

Retreat and performance pages render through the legacy fallback. For each one:

1. Open it in the block editor. The Divi shortcodes show as text inside a Classic/Shortcode block.
2. Rebuild it with normal blocks plus KMS shortcodes, for example `[kms_retreats]`, `[kms_faqs group="retreats"]`, `[kms_button]` and `[kms_whatsapp text="Hi! I'd like to book the winter retreat"]`.
3. Make sure the page has **one H1** (the title; the theme prints it) and **no** old `<style>` or `<script type="application/ld+json">` blocks.
4. When no page is left on Divi, untick **Keep `<style>` blocks inside old page content** in KMS Facts.

Order: the **winter retreat page** first (it is selling now), then the summer retreat, then the performance pages. The winter retreat page also has a copy of the old site footer pasted into its content (it shows as a second footer under the new theme), and its Register buttons need the real form link.

## 9. Go-live checklist

- [ ] **Settings → KMS Facts → Run checks**: everything ✅ except items you have consciously accepted.
- [ ] `https://krishnamusicschool.com/llms.txt` shows the summary with the right facts and prices.
- [ ] Google **Rich Results Test** on the homepage, one class page and the winter retreat page: no errors (warnings for optional fields are fine).
- [ ] Send a test enquiry from a class page on your phone. It appears under **Enquiries**, the email arrives, and the WhatsApp continue button works.
- [ ] Mobile: the menu opens and closes, the sticky WhatsApp/consultation bar appears after scrolling, and prices show in your currency.
- [ ] `https://krishnamusicschool.com/this-does-not-exist/` shows the 404 page with status 404.
- [ ] Search Console: submit the sitemap again and request indexing for the homepage, `/online-classes/` and each class page.
- [ ] In GA4 (via Site Kit), mark `generate_lead` and `whatsapp_click` as **key events**. The theme sends both automatically.

## Updating later

- **Prices, phone, hours, retreat dates**: Settings → KMS Facts, and each class's *Class details* box.
- **A new class**: Classes → Add new. It appears on the homepage, the hub, the menu, the footer, the price table, the schema and `llms.txt` automatically.
- **A new review**: Reviews → Add new. Only genuine reviews, with the source link.
- **Retreat dates**: one line per batch in KMS Facts → Retreats. Delete a batch when it is full.
