#!/usr/bin/env python3
"""Export the local preview WordPress as a static folder for Netlify (for review only).

    python3 bin/preview/export-static.py [--base http://127.0.0.1:8091] [--out dist/preview]

Compared with what WordPress serves:
- Links between pages are root-relative, so the preview works on any Netlify address.
- Canonical URLs, structured data, robots.txt and llms.txt keep https://krishnamusicschool.com,
  so they show what production will publish.
- Every page is noindex (meta tag and Netlify _headers), so the preview never competes with the real site.
- A banner says it is a preview, and the enquiry form is switched off: it needs WordPress.
"""

import argparse
import html
import os
import re
import shutil
import sys
import urllib.error
import urllib.parse
import urllib.request
from xml.etree import ElementTree

PROD = 'https://krishnamusicschool.com'
MAX_FILES = 5000
SKIP = re.compile(r'^/(wp-admin|wp-login\.php|wp-json|xmlrpc\.php|wp-cron\.php|wp-sitemap|author/|comments/feed)|/(feed|embed|trackback)/?$')
URL_ATTR = re.compile(r'''\b(href|src|srcset|imagesrcset|poster|data-src|data-srcset)\s*=\s*(["'])(.*?)\2''', re.I | re.S)
CSS_URL = re.compile(r'''url\(\s*(["']?)([^"')]+)\1\s*\)''', re.I)
TEXT_FILES = ('.html', '.css', '.js', '.txt', '.xml', '.json', '.svg')

# Left as production URLs: structured data, canonical/alternate links and meta tags.
PROTECT = re.compile(
    r'<script[^>]*application/ld\+json[^>]*>.*?</script>'
    r'''|<link\b[^>]*\brel=["'](?:canonical|alternate|shortlink|EditURI|https://api\.w\.org/|pingback)["'][^>]*>'''
    r'|<meta\b[^>]*>',
    re.I | re.S,
)
PROD_URL = re.compile(re.escape(PROD) + r'''(/[^\s"'<>(),]*)?''')

HEADERS = """/*
  X-Robots-Tag: noindex, nofollow
/llms.txt
  Content-Type: text/plain; charset=utf-8
/robots.txt
  Content-Type: text/plain; charset=utf-8
"""

BANNER = (
    '<div role="region" aria-label="Preview notice" style="background:#fff4d6;color:#3d2c00;'
    "font:600 13px/1.4 system-ui,-apple-system,'Segoe UI',sans-serif;padding:8px 16px;"
    'text-align:center;border-bottom:1px solid #e5c56b">'
    'Preview of the new site, not the live one. The enquiry form is switched off here.</div>'
)

FORM_OFF = """<script>
document.addEventListener('submit', function (event) {
	var form = event.target;
	if (!form.hasAttribute || !form.hasAttribute('data-km-form')) {
		return;
	}
	event.preventDefault();
	event.stopPropagation();
	var note = form.querySelector('.kms-preview-form-note');
	if (!note) {
		note = document.createElement('p');
		note.className = 'kms-preview-form-note';
		note.setAttribute('role', 'alert');
		note.style.cssText = 'margin-top:12px;padding:10px 12px;background:#fff4d6;color:#3d2c00;border-radius:6px;font-weight:600';
		form.appendChild(note);
	}
	note.textContent = 'Preview only: this form is not connected here, and nothing was sent. On the real site it emails the school and saves the enquiry in WordPress.';
}, true);
</script>"""


def fetch(opener, url):
    try:
        with opener.open(url, timeout=120) as res:
            return res.status, res.headers.get_content_type(), res.read()
    except urllib.error.HTTPError as err:
        return err.code, err.headers.get_content_type(), err.read()


def quoted(path):
    return urllib.parse.quote(path, safe="/%:@!$&'()*+,;=-._~")


def output_name(path, ctype):
    if path.endswith('/'):
        return path + 'index.html'
    if ctype == 'text/html' and '.' not in path.rsplit('/', 1)[-1]:
        return path + '/index.html'
    return path


def sitemap_paths(opener, base):
    paths, todo = [], [base + '/wp-sitemap.xml']
    while todo:
        status, _, body = fetch(opener, todo.pop())
        if status != 200:
            continue
        try:
            root = ElementTree.fromstring(body)
        except ElementTree.ParseError:
            continue
        for loc in root.iter('{http://www.sitemaps.org/schemas/sitemap/0.9}loc'):
            url = (loc.text or '').strip()
            if '/wp-sitemap' in url:
                todo.append(url)
            elif url:
                paths.append(urllib.parse.urlsplit(url).path or '/')
    return paths


def links(body, ctype, doc_url, host):
    """Same-site paths referenced by an HTML or CSS document."""
    if ctype not in ('text/html', 'text/css'):
        return []
    text = body.decode('utf-8', 'replace')
    found = []
    if ctype == 'text/html':
        for name, _, value in URL_ATTR.findall(text):
            if 'srcset' in name.lower():
                found += [part.split()[0] for part in value.split(',') if part.strip()]
            else:
                found.append(value)
    found += [m[1] for m in CSS_URL.findall(text)]

    paths = []
    for value in found:
        value = html.unescape(value.strip())
        if not value or value.startswith(('data:', 'mailto:', 'tel:', 'javascript:', '#')):
            continue
        parts = urllib.parse.urlsplit(urllib.parse.urljoin(doc_url, value))
        path = urllib.parse.unquote(parts.path) or '/'
        if parts.scheme in ('http', 'https') and parts.netloc == host and not SKIP.search(path):
            paths.append(path)
    return paths


def crawl(base):
    opener = urllib.request.build_opener(urllib.request.ProxyHandler({}))  # A local site: never through a proxy.
    host = urllib.parse.urlsplit(base).netloc
    seeds = ['/', '/online-classes/', '/llms.txt', '/robots.txt'] + sitemap_paths(opener, base)
    queue = [p for p in dict.fromkeys(seeds) if not SKIP.search(p)]
    seen, files = set(queue), {}
    while queue and len(files) < MAX_FILES:
        path = queue.pop(0)
        status, ctype, body = fetch(opener, base + quoted(path))
        if status != 200:
            print(f'warning: {status} {path}', file=sys.stderr)
            continue
        files[output_name(path, ctype)] = body
        for link in links(body, ctype, base + quoted(path), host):
            if link not in seen:
                seen.add(link)
                queue.append(link)
    status, _, body = fetch(opener, base + '/kms-preview-missing-page/')
    if status == 404:
        files['/404.html'] = body
    return files


def outside(text, pattern, change):
    """Apply change() to the parts of text that do not match pattern."""
    out, pos = [], 0
    for m in pattern.finditer(text):
        out.append(change(text[pos:m.start()]))
        out.append(m.group(0))
        pos = m.end()
    out.append(change(text[pos:]))
    return ''.join(out)


def preview_html(text, exported):
    def relative(m):
        rest = m.group(1) or '/'
        path = urllib.parse.unquote(urllib.parse.urlsplit(rest).path) or '/'
        return rest if (path in exported or path + '/' in exported) else m.group(0)

    text = outside(text, PROTECT, lambda part: PROD_URL.sub(relative, part))
    text = re.sub(r'''<meta\s+name=["']robots["'][^>]*>\s*''', '', text, flags=re.I)
    text = text.replace('</head>', '<meta name="robots" content="noindex, nofollow">\n</head>', 1)
    text = re.sub(r'(<body\b[^>]*>)', lambda m: m.group(1) + BANNER, text, count=1)
    text = re.sub(r'(<form\b[^>]*\bdata-km-form\b[^>]*\baction=")[^"]*(")', r'\1#enquire\2', text)
    return text.replace('</body>', FORM_OFF + '\n</body>', 1)


def write(out, name, body):
    path = os.path.join(out, name.lstrip('/'))
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'wb') as fh:
        fh.write(body)


def main():
    parser = argparse.ArgumentParser(description='Export the local preview WordPress as a static Netlify folder.')
    parser.add_argument('--base', default='http://127.0.0.1:8091', help='local WordPress address')
    parser.add_argument('--out', default='dist/preview', help='output folder (replaced)')
    args = parser.parse_args()
    base = args.base.rstrip('/')

    if os.path.isdir(args.out) and os.listdir(args.out):
        if not os.path.exists(os.path.join(args.out, '_headers')):
            sys.exit(f'{args.out} is not empty and is not an earlier preview export; choose another --out.')
        shutil.rmtree(args.out)

    files = crawl(base)
    exported = set(files) | {name[: -len('index.html')] for name in files if name.endswith('/index.html')}
    origins = [
        (base, PROD),
        (base.replace('/', '\\/'), PROD.replace('/', '\\/')),
        (urllib.parse.quote(base, safe=''), urllib.parse.quote(PROD, safe='')),
    ]
    for name, body in files.items():
        if name.endswith(TEXT_FILES):
            text = body.decode('utf-8', 'replace')
            for local, prod in origins:
                text = text.replace(local, prod)
            if name.endswith('.html'):
                text = preview_html(text, exported)
            body = text.encode('utf-8')
        write(args.out, name, body)
    write(args.out, '/_headers', HEADERS.encode('utf-8'))

    pages = sum(1 for name in files if name.endswith('.html'))
    print(f'{pages} pages and {len(files) - pages} other files written to {args.out}')


if __name__ == '__main__':
    main()
