/**
 * Accessibility audit (axe-core, WCAG 2.1 AA + best practice) on desktop and mobile.
 * Usage: node tests/axe.js [comma-separated paths]
 */
// Playwright: local install, or the global one.
let pw;
try { pw = require('playwright'); } catch (e) { pw = require('/opt/node22/lib/node_modules/playwright'); }
const { chromium, devices } = pw;
const base = process.env.KMS_BASE || 'http://127.0.0.1:8080';
const AXE = 'https://cdn.jsdelivr.net/npm/axe-core@4.10.2/axe.min.js';
const pages = (process.argv[2] || '/,/online-classes/,/online-classes/harmonium/,/about-us/,/pricing/,/faq/,/contact-us/,/hear-from-our-attendees/,/blog/,/no-such-page/').split(',');
(async () => {
  const browser = await chromium.launch();
  for (const [name, opts] of [['desktop', { viewport: { width: 1366, height: 900 } }], ['mobile', devices['iPhone 13']]]) {
    const ctx = await browser.newContext(opts);
    const page = await ctx.newPage();
    for (const p of pages) {
      await page.goto(base + p, { waitUntil: 'networkidle' });
      await page.addScriptTag({ url: AXE });
      const res = await page.evaluate(async () => await axe.run(document, { runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21aa', 'best-practice'] } }));
      const v = res.violations.map(x => `${x.impact} ${x.id} (${x.nodes.length}): ${x.help} :: ${x.nodes.slice(0,2).map(n => n.target.join(' ')).join(' | ')}`);
      console.log(`${name} ${p}: ${res.violations.length} violations`);
      if (res.violations.length) process.exitCode = 1;
      v.forEach(l => console.log('   ' + l));
    }
    await ctx.close();
  }
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
