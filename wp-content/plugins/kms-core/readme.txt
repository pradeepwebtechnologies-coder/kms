=== KMS Core — Krishna Music School ===
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Classes, reviews, FAQs, enquiry form, structured data, llms.txt and the single facts registry for krishnamusicschool.com.

== Description ==

* Settings → KMS Facts: one place for every business fact, used by pages, JSON-LD and /llms.txt. Includes a "Run checks" panel.
* Content types: Classes (/online-classes/), Reviews (verified flag + source link), FAQs (groups), Enquiries (private; CSV export; privacy export/erase).
* Enquiry form saved in WordPress and emailed, with WhatsApp hand-off. Cache-safe spam protection (honeypot, JS check, time trap, rate limit).
* One connected JSON-LD graph per page; silences Rank Math / Yoast / AIOSEO JSON-LD and strips JSON-LD pasted into content.
* /llms.txt and explicit robots.txt rules for AI search and assistant crawlers.
* Renders old Divi Builder content when Divi is no longer active.
* Starter content (Tools → KMS Starter Content, or `wp kms import`).

== WP-CLI ==

    wp kms import [--publish] [--replace-pages]
    wp kms llms
    wp kms health

== Changelog ==

= 1.0.0 =
* First release.
