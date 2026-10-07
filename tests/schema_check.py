"""One valid JSON-LD graph per page type: required properties, resolvable @ids, no ratings, no unfilled placeholders.

Usage: python3 tests/schema_check.py [comma-separated paths]   (KMS_BASE env var for another host)
"""
import json, os, re, sys, urllib.request
BASE = os.environ.get('KMS_BASE', 'http://127.0.0.1:8080')
paths = (sys.argv[1] if len(sys.argv) > 1 else '/,/online-classes/,/online-classes/harmonium/,/about-us/,/pricing/,/contact-us/,/hear-from-our-attendees/,/faq/,/blog/,/how-to-sing-bhajans-with-harmonium/,/8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium/,/category/kirtan-bhajan/').split(',')
problems = 0
for p in paths:
    html = urllib.request.urlopen(BASE + p).read().decode('utf-8')
    blocks = re.findall(r'<script type="application/ld\+json"[^>]*>(.*?)</script>', html, re.S)
    if len(blocks) != 1:
        print(f'!! {p}: {len(blocks)} JSON-LD blocks'); problems += 1
    for b in blocks:
        try:
            d = json.loads(b)
        except Exception as e:
            print(f'!! {p}: invalid JSON {e}'); problems += 1; continue
        graph = d.get('@graph', [])
        ids = {n.get('@id') for n in graph if isinstance(n, dict)}
        types = []
        for n in graph:
            t = n.get('@type'); types.append(t if isinstance(t, str) else '+'.join(t))
        # unresolved fragment references
        def refs(o):
            if isinstance(o, dict):
                if set(o.keys()) == {'@id'}: yield o['@id']
                for v in o.values(): yield from refs(v)
            elif isinstance(o, list):
                for v in o: yield from refs(v)
        unresolved = [r for r in refs(d) if r not in ids and '#' in r and not r.endswith('#course')]
        txt = json.dumps(d)
        issues = []
        if 'AggregateRating' in txt: issues.append('AggregateRating present')
        if unresolved: issues.append(f'unresolved refs {unresolved[:3]}')
        for n in graph:
            t = n.get('@type')
            if t == 'Course':
                for k in ['name', 'description', 'provider', 'offers', 'hasCourseInstance']:
                    if k not in n: issues.append(f'Course missing {k}')
                for o in n.get('offers', []):
                    for k in ['price', 'priceCurrency', 'category']:
                        if k not in o: issues.append(f'Offer missing {k}')
                for ci in n.get('hasCourseInstance', []):
                    if 'courseMode' not in ci or not ('courseSchedule' in ci or 'courseWorkload' in ci): issues.append('CourseInstance incomplete')
            if t == 'BreadcrumbList':
                items = n['itemListElement']
                for i, it in enumerate(items):
                    if it['position'] != i + 1: issues.append('breadcrumb position')
                    if i < len(items) - 1 and 'item' not in it: issues.append('breadcrumb item missing')
            if t == 'FAQPage':
                for q in n['mainEntity']:
                    if not q.get('acceptedAnswer', {}).get('text'): issues.append('FAQ answer empty')
                    if '{' in q['name'] or '{' in q['acceptedAnswer']['text']: issues.append('unfilled placeholder in FAQ: ' + q['name'])
            if t == 'EducationEvent':
                for k in ['name', 'startDate', 'location', 'eventAttendanceMode', 'offers']:
                    if k not in n: issues.append(f'Event missing {k}')
        if '{founder}' in txt or '{price_' in txt or '{name}' in txt: issues.append('unfilled placeholder')
        print(('OK ' if not issues else '!! ') + p, types, issues)
        problems += bool(issues)
print('problems:', problems)
sys.exit(1 if problems else 0)
