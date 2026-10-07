/**
 * End-to-end checks against a running site (tests/local-wp.sh).
 * Usage: node tests/e2e.js   (KMS_BASE=https://staging.example node tests/e2e.js)
 */
// Playwright: local install, or the global one.
let pw;
try { pw = require('playwright'); } catch (e) { pw = require('/opt/node22/lib/node_modules/playwright'); }
const { chromium, devices } = pw;
const base = process.env.KMS_BASE || 'http://127.0.0.1:8080';
const assert = (c, m) => { if (!c) { console.log('FAIL: ' + m); process.exitCode = 1; } else console.log('ok: ' + m); };
(async () => {
  const browser = await chromium.launch();
  // 1. Enquiry form via fetch, preselected course from ?course=
  let ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, timezoneId: 'America/Los_Angeles' });
  let page = await ctx.newPage();
  await page.goto(base + '/contact-us/?course=tabla#enquire', { waitUntil: 'networkidle' });
  assert(await page.$eval('[data-km-interest]', el => el.value) === 'tabla', 'course preselected from ?course=tabla');
  assert(await page.$eval('[data-km-tzfield]', el => el.value) === 'America/Los_Angeles', 'time zone captured');
  await page.fill('input[name=kms_name]', 'Test Student');
  await page.fill('input[name=kms_email]', 'student@example.com');
  await page.fill('input[name=kms_phone]', '+1 555 123 4567');
  await page.fill('input[name=kms_country]', 'Seattle, USA');
  await page.selectOption('select[name=kms_level]', 'beginner');
  await page.fill('input[name=kms_when]', 'weekday evenings');
  await page.waitForTimeout(3200); // time trap
  await page.click('[data-km-submit]');
  await page.waitForSelector('[data-km-done]:not([hidden])', { timeout: 10000 });
  assert(await page.isVisible('[data-km-done]'), 'success message shown after fetch submit');
  assert(!(await page.isVisible('[data-km-form]')), 'form hidden after success');
  // local time conversion
  const local = await page.$eval('[data-km-tz-local]', el => el.textContent);
  console.log('   local time text:', local);
  assert(/PDT|PST|GMT-7|GMT-8/.test(local), 'time converted to Pacific time');
  await ctx.close();

  // 2. Currency switch persists
  ctx = await browser.newContext({ viewport: { width: 1280, height: 900 }, timezoneId: 'Europe/Berlin' });
  page = await ctx.newPage();
  await page.goto(base + '/online-classes/harmonium/', { waitUntil: 'networkidle' });
  const alt = await page.$eval('.km-plan .km-price__alt', el => el.textContent);
  assert(alt.includes('€'), 'Berlin visitor sees euros: ' + alt);
  await page.click('.km-currency__btn[data-cur="INR"]');
  assert(await page.$eval('.km-plan .km-price__alt', el => el.hidden), 'INR hides the conversion');
  await page.reload({ waitUntil: 'networkidle' });
  assert(await page.$eval('.km-currency__btn[data-cur="INR"]', el => el.getAttribute('aria-pressed')) === 'true', 'currency choice remembered');
  await ctx.close();

  // 3. Mobile menu and submenu
  ctx = await browser.newContext(devices['iPhone 13']);
  page = await ctx.newPage();
  await page.goto(base + '/', { waitUntil: 'networkidle' });
  assert(!(await page.isVisible('#km-menu')), 'menu hidden on mobile initially');
  await page.click('[data-km-nav-toggle]');
  assert(await page.isVisible('#km-menu'), 'menu opens');
  assert(await page.$eval('[data-km-nav-toggle]', el => el.getAttribute('aria-expanded')) === 'true', 'aria-expanded true');
  const sub = await page.$('.km-sub-toggle');
  await sub.click();
  assert(await page.isVisible('.menu-item-has-children.is-open .sub-menu'), 'submenu opens');
  await page.keyboard.press('Escape');
  assert(!(await page.isVisible('#km-menu')), 'Escape closes menu');
  // sticky bar appears after scrolling and is not focusable when hidden
  assert(await page.$eval('[data-km-sticky]', el => getComputedStyle(el).visibility) === 'hidden', 'sticky bar hidden at top');
  await page.evaluate(() => window.scrollTo(0, 1500));
  await page.waitForTimeout(500);
  assert(await page.$eval('[data-km-sticky]', el => el.classList.contains('is-visible')), 'sticky bar visible after scroll');
  await ctx.close();

  // 4. Keyboard: skip link
  ctx = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  page = await ctx.newPage();
  await page.goto(base + '/', { waitUntil: 'networkidle' });
  await page.keyboard.press('Tab');
  assert((await page.evaluate(() => document.activeElement.className)).includes('km-skip'), 'first Tab focuses skip link');
  await ctx.close();
  await browser.close();
})().catch(e => { console.error(e); process.exit(1); });
